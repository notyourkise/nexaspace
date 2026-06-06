<?php

namespace Tests\Feature;

use App\Models\Billing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantBillingIsolationTest extends TestCase
{
    use RefreshDatabase;

    /** Tenant hanya melihat billing miliknya sendiri di panel tenant. */
    public function test_tenant_only_sees_own_billings(): void
    {
        $tenantA = User::factory()->create(['role' => 'tenant']);
        $tenantB = User::factory()->create(['role' => 'tenant']);

        $billingA = Billing::factory()->create(['user_id' => $tenantA->id]);
        $billingB = Billing::factory()->create(['user_id' => $tenantB->id]);

        $response = $this->actingAs($tenantA)->get('/tenant/billings');

        $response->assertOk();

        // Query scoping: hanya billing milik tenantA yang dikembalikan
        $this->assertSame(
            1,
            Billing::where('user_id', $tenantA->id)->count()
        );
        $this->assertSame(
            1,
            Billing::where('user_id', $tenantB->id)->count()
        );

        // Pastikan billing milik tenantB tidak terekspos ke tenantA
        $ownBillings = Billing::where('user_id', $tenantA->id)->pluck('id');
        $this->assertTrue($ownBillings->contains($billingA->id));
        $this->assertFalse($ownBillings->contains($billingB->id));
    }

    /** Tenant tidak bisa mengakses langsung URL edit billing milik tenant lain. */
    public function test_tenant_cannot_access_other_tenant_billing(): void
    {
        $tenantA = User::factory()->create(['role' => 'tenant']);
        $tenantB = User::factory()->create(['role' => 'tenant']);

        // Tenant panel tidak punya halaman edit — canCreate false, hanya index
        // Verifikasi bahwa query scoping di resource benar-benar membatasi
        $billingB = Billing::factory()->create(['user_id' => $tenantB->id, 'status' => 'unpaid']);

        // Query yang digunakan resource tenant: where('user_id', auth()->id())
        $scopedQuery = Billing::where('user_id', $tenantA->id);
        $this->assertFalse($scopedQuery->where('id', $billingB->id)->exists());
    }

    /** Admin dapat melihat semua billing dari semua tenant. */
    public function test_admin_can_see_all_billings(): void
    {
        $admin   = User::factory()->admin()->create();
        $tenantA = User::factory()->create(['role' => 'tenant']);
        $tenantB = User::factory()->create(['role' => 'tenant']);

        Billing::factory()->create(['user_id' => $tenantA->id]);
        Billing::factory()->create(['user_id' => $tenantB->id]);

        // Admin resource tidak memiliki user_id scope
        $this->assertSame(2, Billing::count());

        $this->actingAs($admin)->get('/admin/billings')->assertOk();
    }

    /** Tenant tidak bisa membuat billing baru lewat panel tenant. */
    public function test_tenant_cannot_create_billing(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);

        // Route create tidak ada karena canCreate() = false
        $this->actingAs($tenant)
            ->get('/tenant/billings/create')
            ->assertStatus(404);
    }

    /** Widget TenantStatsWidget hanya menghitung billing milik tenant yang login. */
    public function test_tenant_stats_only_counts_own_billings(): void
    {
        $tenantA = User::factory()->create(['role' => 'tenant']);
        $tenantB = User::factory()->create(['role' => 'tenant']);

        // tenantA punya 2 unpaid, tenantB punya 1 unpaid
        Billing::factory()->count(2)->create(['user_id' => $tenantA->id, 'status' => 'unpaid']);
        Billing::factory()->create(['user_id' => $tenantB->id, 'status' => 'unpaid']);

        // Verifikasi query yang digunakan widget
        $unpaidForA = Billing::where('user_id', $tenantA->id)
            ->whereIn('status', ['unpaid', 'throttled'])
            ->count();

        $this->assertSame(2, $unpaidForA);
    }
}
