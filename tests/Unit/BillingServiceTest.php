<?php

namespace Tests\Unit;

use App\Models\Billing;
use App\Models\Device;
use App\Models\User;
use App\Services\BillingService;
use App\Services\MikroTikService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Mockery;
use Tests\TestCase;

class BillingServiceTest extends TestCase
{
    use RefreshDatabase;

    private BillingService $service;
    private MikroTikService $mikrotik;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mikrotik = Mockery::mock(MikroTikService::class);
        $this->service  = new BillingService($this->mikrotik);
    }

    // ── Grace period boundaries ───────────────────────────────────────────────

    /** Billing due today: masih dalam grace period, TIDAK boleh di-throttle. */
    public function test_billing_due_today_is_not_throttled(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        $device  = Device::factory()->create(['user_id' => $tenant->id, 'status' => 'active']);
        $billing = Billing::factory()->create([
            'user_id'  => $tenant->id,
            'status'   => 'unpaid',
            'due_date' => Carbon::today(),
        ]);

        $this->mikrotik->shouldNotReceive('throttleDevice');

        $this->service->checkAndThrottleOverdue();

        $this->assertDatabaseHas('billings', ['id' => $billing->id, 'status' => 'unpaid']);
        $this->assertDatabaseHas('devices',  ['id' => $device->id,  'status' => 'active']);
    }

    /** Billing due kemarin (H+1): masih grace period, TIDAK boleh di-throttle. */
    public function test_billing_due_yesterday_is_not_throttled(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        $device  = Device::factory()->create(['user_id' => $tenant->id, 'status' => 'active']);
        $billing = Billing::factory()->create([
            'user_id'  => $tenant->id,
            'status'   => 'unpaid',
            'due_date' => Carbon::yesterday(),
        ]);

        $this->mikrotik->shouldNotReceive('throttleDevice');

        $this->service->checkAndThrottleOverdue();

        $this->assertDatabaseHas('billings', ['id' => $billing->id, 'status' => 'unpaid']);
    }

    /** Billing due 2 hari lalu (H+2): masih batas grace, TIDAK boleh di-throttle. */
    public function test_billing_due_two_days_ago_is_not_throttled(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        Device::factory()->create(['user_id' => $tenant->id, 'status' => 'active']);
        $billing = Billing::factory()->create([
            'user_id'  => $tenant->id,
            'status'   => 'unpaid',
            'due_date' => Carbon::today()->subDays(2),
        ]);

        $this->mikrotik->shouldNotReceive('throttleDevice');

        $this->service->checkAndThrottleOverdue();

        $this->assertDatabaseHas('billings', ['id' => $billing->id, 'status' => 'unpaid']);
    }

    /** Billing due 3 hari lalu (H+3): grace period habis, HARUS di-throttle. */
    public function test_billing_due_three_days_ago_is_throttled(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        $device  = Device::factory()->create(['user_id' => $tenant->id, 'status' => 'active']);
        $billing = Billing::factory()->create([
            'user_id'  => $tenant->id,
            'status'   => 'unpaid',
            'due_date' => Carbon::today()->subDays(3),
        ]);

        $this->mikrotik
            ->shouldReceive('throttleDevice')
            ->once()
            ->with($device->mac_address)
            ->andReturn(true);

        $this->service->checkAndThrottleOverdue();

        $this->assertDatabaseHas('billings', ['id' => $billing->id, 'status' => 'throttled']);
        $this->assertDatabaseHas('devices',  ['id' => $device->id,  'status' => 'throttled']);
    }

    /** Billing yang sudah 'paid' tidak boleh diproses ulang. */
    public function test_paid_billing_is_skipped(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);
        $device = Device::factory()->create(['user_id' => $tenant->id, 'status' => 'active']);
        $billing = Billing::factory()->create([
            'user_id'  => $tenant->id,
            'status'   => 'paid',
            'due_date' => Carbon::today()->subDays(10),
        ]);

        $this->mikrotik->shouldNotReceive('throttleDevice');

        $this->service->checkAndThrottleOverdue();

        $this->assertDatabaseHas('billings', ['id' => $billing->id, 'status' => 'paid']);
        $this->assertDatabaseHas('devices', ['id' => $device->id, 'status' => 'active']);
    }

    /** Device 'blocked' tidak boleh diubah ke 'throttled' saat throttle berjalan. */
    public function test_blocked_device_is_untouched_during_throttle(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        $blocked = Device::factory()->create(['user_id' => $tenant->id, 'status' => 'blocked']);
        Billing::factory()->create([
            'user_id'  => $tenant->id,
            'status'   => 'unpaid',
            'due_date' => Carbon::today()->subDays(3),
        ]);

        $this->mikrotik->shouldNotReceive('throttleDevice');

        $this->service->checkAndThrottleOverdue();

        $this->assertDatabaseHas('devices', ['id' => $blocked->id, 'status' => 'blocked']);
    }

    /** Jika router down (throttleDevice false), billing & device DB tetap ter-update. */
    public function test_throttle_updates_db_even_if_mikrotik_fails(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        $device  = Device::factory()->create(['user_id' => $tenant->id, 'status' => 'active']);
        $billing = Billing::factory()->create([
            'user_id'  => $tenant->id,
            'status'   => 'unpaid',
            'due_date' => Carbon::today()->subDays(3),
        ]);

        $this->mikrotik
            ->shouldReceive('throttleDevice')
            ->once()
            ->andReturn(false);

        $this->service->checkAndThrottleOverdue();

        // DB harus tetap berubah meskipun MikroTik gagal
        $this->assertDatabaseHas('billings', ['id' => $billing->id, 'status' => 'throttled']);
        $this->assertDatabaseHas('devices',  ['id' => $device->id,  'status' => 'throttled']);
    }

    // ── Restore devices ───────────────────────────────────────────────────────

    /** Saat billing paid, device 'throttled' harus dikembalikan ke 'active'. */
    public function test_restore_unthrottles_throttled_devices(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        $device  = Device::factory()->create(['user_id' => $tenant->id, 'status' => 'throttled']);
        $billing = Billing::factory()->create(['user_id' => $tenant->id, 'status' => 'paid']);

        $this->mikrotik
            ->shouldReceive('unthrottleDevice')
            ->once()
            ->with($device->mac_address)
            ->andReturn(true);

        $this->service->restoreDevicesForBilling($billing);

        $this->assertDatabaseHas('devices', ['id' => $device->id, 'status' => 'active']);
    }

    /** Device 'blocked' tidak boleh diubah ke 'active' saat restore. */
    public function test_restore_does_not_touch_blocked_devices(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        $blocked = Device::factory()->create(['user_id' => $tenant->id, 'status' => 'blocked']);
        $billing = Billing::factory()->create(['user_id' => $tenant->id, 'status' => 'paid']);

        $this->mikrotik->shouldNotReceive('unthrottleDevice');

        $this->service->restoreDevicesForBilling($billing);

        $this->assertDatabaseHas('devices', ['id' => $blocked->id, 'status' => 'blocked']);
    }

    /** Device 'active' tidak disentuh saat restore (tidak ada double-call MikroTik). */
    public function test_restore_skips_already_active_devices(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        $device  = Device::factory()->create(['user_id' => $tenant->id, 'status' => 'active']);
        $billing = Billing::factory()->create(['user_id' => $tenant->id, 'status' => 'paid']);

        $this->mikrotik->shouldNotReceive('unthrottleDevice');

        $this->service->restoreDevicesForBilling($billing);

        $this->assertDatabaseHas('devices', ['id' => $device->id, 'status' => 'active']);
    }

    /** Restore hanya menyentuh device milik tenant dari billing tersebut. */
    public function test_restore_only_affects_devices_of_billing_tenant(): void
    {
        $tenantA = User::factory()->create(['role' => 'tenant']);
        $tenantB = User::factory()->create(['role' => 'tenant']);

        $deviceA  = Device::factory()->create(['user_id' => $tenantA->id, 'status' => 'throttled']);
        $deviceB  = Device::factory()->create(['user_id' => $tenantB->id, 'status' => 'throttled']);
        $billing  = Billing::factory()->create(['user_id' => $tenantA->id, 'status' => 'paid']);

        $this->mikrotik
            ->shouldReceive('unthrottleDevice')
            ->once()
            ->with($deviceA->mac_address)
            ->andReturn(true);

        $this->service->restoreDevicesForBilling($billing);

        $this->assertDatabaseHas('devices', ['id' => $deviceB->id, 'status' => 'throttled']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
