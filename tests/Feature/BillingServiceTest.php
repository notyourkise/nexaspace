<?php

namespace Tests\Feature;

use App\Models\Billing;
use App\Models\Device;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BillingServiceTest extends TestCase
{
    use RefreshDatabase;

    // ── Grace period boundaries ───────────────────────────────────────────────

    /** Tagihan yang jatuh tempo tepat hari ini TIDAK di-throttle (masih dalam grace). */
    public function test_billing_due_today_is_not_throttled(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        $billing = Billing::factory()->create([
            'user_id'  => $tenant->id,
            'status'   => 'unpaid',
            'due_date' => Carbon::today(),
        ]);

        $this->mock(\App\Services\MikroTikService::class)
            ->shouldNotReceive('throttleDevice');

        app(BillingService::class)->checkAndThrottleOverdue();

        $this->assertDatabaseHas('billings', ['id' => $billing->id, 'status' => 'unpaid']);
    }

    /** Tagihan yang jatuh tempo H+2 (grace hari terakhir) TIDAK di-throttle. */
    public function test_billing_due_two_days_ago_is_not_throttled(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        $billing = Billing::factory()->create([
            'user_id'  => $tenant->id,
            'status'   => 'unpaid',
            'due_date' => Carbon::today()->subDays(2),
        ]);

        $this->mock(\App\Services\MikroTikService::class)
            ->shouldNotReceive('throttleDevice');

        app(BillingService::class)->checkAndThrottleOverdue();

        $this->assertDatabaseHas('billings', ['id' => $billing->id, 'status' => 'unpaid']);
    }

    /** Tagihan yang jatuh tempo H+3 atau lebih harus di-throttle (grace sudah habis). */
    public function test_billing_due_three_days_ago_is_throttled(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        $device  = Device::factory()->create(['user_id' => $tenant->id, 'status' => 'active']);
        $billing = Billing::factory()->create([
            'user_id'  => $tenant->id,
            'status'   => 'unpaid',
            'due_date' => Carbon::today()->subDays(3),
        ]);

        $this->mock(\App\Services\MikroTikService::class)
            ->shouldReceive('throttleDevice')
            ->once()
            ->with($device->mac_address)
            ->andReturn(true);

        app(BillingService::class)->checkAndThrottleOverdue();

        $this->assertDatabaseHas('billings', ['id' => $billing->id, 'status' => 'throttled']);
        $this->assertDatabaseHas('devices', ['id' => $device->id, 'status' => 'throttled']);
    }

    /** Tagihan yang sudah 'throttled' tidak diproses ulang. */
    public function test_already_throttled_billing_is_skipped(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        $billing = Billing::factory()->create([
            'user_id'  => $tenant->id,
            'status'   => 'throttled',
            'due_date' => Carbon::today()->subDays(5),
        ]);

        $this->mock(\App\Services\MikroTikService::class)
            ->shouldNotReceive('throttleDevice');

        app(BillingService::class)->checkAndThrottleOverdue();

        // Status tidak berubah.
        $this->assertDatabaseHas('billings', ['id' => $billing->id, 'status' => 'throttled']);
    }

    /** Device 'blocked' tidak disentuh saat throttle diterapkan. */
    public function test_blocked_device_is_not_touched_during_throttle(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        $blocked = Device::factory()->create(['user_id' => $tenant->id, 'status' => 'blocked']);
        $billing = Billing::factory()->create([
            'user_id'  => $tenant->id,
            'status'   => 'unpaid',
            'due_date' => Carbon::today()->subDays(3),
        ]);

        $this->mock(\App\Services\MikroTikService::class)
            ->shouldNotReceive('throttleDevice');

        app(BillingService::class)->checkAndThrottleOverdue();

        $this->assertDatabaseHas('devices', ['id' => $blocked->id, 'status' => 'blocked']);
    }

    /** Hanya tenant A di-throttle, tenant B yang belum overdue tidak tersentuh. */
    public function test_throttle_only_affects_overdue_tenant(): void
    {
        $tenantA = User::factory()->create(['role' => 'tenant']);
        $tenantB = User::factory()->create(['role' => 'tenant']);

        $deviceA = Device::factory()->create(['user_id' => $tenantA->id, 'status' => 'active']);
        $deviceB = Device::factory()->create(['user_id' => $tenantB->id, 'status' => 'active']);

        // TenantA overdue, tenantB masih dalam grace.
        Billing::factory()->create(['user_id' => $tenantA->id, 'status' => 'unpaid', 'due_date' => Carbon::today()->subDays(3)]);
        Billing::factory()->create(['user_id' => $tenantB->id, 'status' => 'unpaid', 'due_date' => Carbon::today()]);

        $this->mock(\App\Services\MikroTikService::class)
            ->shouldReceive('throttleDevice')
            ->once()
            ->with($deviceA->mac_address)
            ->andReturn(true);

        app(BillingService::class)->checkAndThrottleOverdue();

        $this->assertDatabaseHas('devices', ['id' => $deviceA->id, 'status' => 'throttled']);
        $this->assertDatabaseHas('devices', ['id' => $deviceB->id, 'status' => 'active']);
    }

    // ── Generate monthly bills ────────────────────────────────────────────────

    /** generateMonthlyBills() membuat tagihan untuk tenant yang punya monthly_rate. */
    public function test_generate_monthly_bills_creates_billing_for_tenants(): void
    {
        $this->mock(\App\Services\MikroTikService::class);

        $tenant = User::factory()->create(['role' => 'tenant', 'monthly_rate' => 200000]);

        app(BillingService::class)->generateMonthlyBills();

        $this->assertDatabaseHas('billings', [
            'user_id' => $tenant->id,
            'amount'  => 200000,
            'status'  => 'unpaid',
        ]);
    }

    /** generateMonthlyBills() idempotent — tidak dobel untuk tenant yang sudah punya tagihan bulan ini. */
    public function test_generate_monthly_bills_is_idempotent(): void
    {
        $this->mock(\App\Services\MikroTikService::class);

        $tenant = User::factory()->create(['role' => 'tenant', 'monthly_rate' => 150000]);

        // Jalankan dua kali.
        app(BillingService::class)->generateMonthlyBills();
        app(BillingService::class)->generateMonthlyBills();

        $this->assertSame(1, Billing::where('user_id', $tenant->id)->count());
    }

    /** Tenant tanpa monthly_rate (0) tidak dibuatkan tagihan. */
    public function test_generate_monthly_bills_skips_tenant_without_rate(): void
    {
        $this->mock(\App\Services\MikroTikService::class);

        $tenant = User::factory()->create(['role' => 'tenant', 'monthly_rate' => 0]);

        app(BillingService::class)->generateMonthlyBills();

        $this->assertSame(0, Billing::where('user_id', $tenant->id)->count());
    }
}
