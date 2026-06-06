<?php

namespace Tests\Feature;

use App\Jobs\RestoreDevicesJob;
use App\Models\Billing;
use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class BillingObserverTest extends TestCase
{
    use RefreshDatabase;

    /** Status → 'paid' harus men-dispatch RestoreDevicesJob. */
    public function test_marking_billing_paid_dispatches_restore_job(): void
    {
        Queue::fake();

        $tenant  = User::factory()->create(['role' => 'tenant']);
        $billing = Billing::factory()->create(['user_id' => $tenant->id, 'status' => 'unpaid']);

        $billing->update(['status' => 'paid']);

        Queue::assertPushed(RestoreDevicesJob::class, fn ($job) => $job->billing->is($billing));
    }

    /** Status → 'throttled' (bukan 'paid') TIDAK boleh men-dispatch job. */
    public function test_marking_billing_throttled_does_not_dispatch_restore_job(): void
    {
        Queue::fake();

        $tenant  = User::factory()->create(['role' => 'tenant']);
        $billing = Billing::factory()->create(['user_id' => $tenant->id, 'status' => 'unpaid']);

        $billing->update(['status' => 'throttled']);

        Queue::assertNothingPushed();
    }

    /** Update field selain status (misal: amount) TIDAK boleh men-dispatch job. */
    public function test_updating_non_status_field_does_not_dispatch_restore_job(): void
    {
        Queue::fake();

        $tenant  = User::factory()->create(['role' => 'tenant']);
        $billing = Billing::factory()->create(['user_id' => $tenant->id, 'status' => 'paid', 'amount' => 100000]);

        $billing->update(['amount' => 200000]);

        Queue::assertNothingPushed();
    }

    /**
     * Integrasi end-to-end (queue=sync): saat billing dibayar, device throttled
     * milik tenant harus langsung kembali active.
     */
    public function test_paying_billing_restores_throttled_device_end_to_end(): void
    {
        $tenant   = User::factory()->create(['role' => 'tenant']);
        $throttled = Device::factory()->create(['user_id' => $tenant->id, 'status' => 'throttled']);
        $billing  = Billing::factory()->create(['user_id' => $tenant->id, 'status' => 'throttled']);

        // Queue=sync di phpunit.xml — job langsung dieksekusi.
        // MikroTikService akan mencoba koneksi nyata, jadi kita mock-nya.
        $this->mock(\App\Services\MikroTikService::class)
            ->shouldReceive('unthrottleDevice')
            ->once()
            ->with($throttled->mac_address)
            ->andReturn(true);

        $billing->update(['status' => 'paid']);

        $this->assertDatabaseHas('devices', ['id' => $throttled->id, 'status' => 'active']);
    }

    /** Device 'blocked' milik tenant TIDAK berubah saat billing dibayar. */
    public function test_paying_billing_does_not_touch_blocked_device(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        $blocked = Device::factory()->create(['user_id' => $tenant->id, 'status' => 'blocked']);
        $billing = Billing::factory()->create(['user_id' => $tenant->id, 'status' => 'throttled']);

        $this->mock(\App\Services\MikroTikService::class)
            ->shouldNotReceive('unthrottleDevice');

        $billing->update(['status' => 'paid']);

        $this->assertDatabaseHas('devices', ['id' => $blocked->id, 'status' => 'blocked']);
    }

    /** Device 'throttled' milik tenant LAIN tidak ikut dipulihkan. */
    public function test_paying_billing_only_restores_own_tenant_devices(): void
    {
        $tenantA  = User::factory()->create(['role' => 'tenant']);
        $tenantB  = User::factory()->create(['role' => 'tenant']);
        $deviceA  = Device::factory()->create(['user_id' => $tenantA->id, 'status' => 'throttled']);
        $deviceB  = Device::factory()->create(['user_id' => $tenantB->id, 'status' => 'throttled']);
        $billing  = Billing::factory()->create(['user_id' => $tenantA->id, 'status' => 'throttled']);

        $this->mock(\App\Services\MikroTikService::class)
            ->shouldReceive('unthrottleDevice')
            ->once()
            ->with($deviceA->mac_address)
            ->andReturn(true);

        $billing->update(['status' => 'paid']);

        $this->assertDatabaseHas('devices', ['id' => $deviceB->id, 'status' => 'throttled']);
    }
}
