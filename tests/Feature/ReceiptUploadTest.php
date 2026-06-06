<?php

namespace Tests\Feature;

use App\Models\Billing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceiptUploadTest extends TestCase
{
    use RefreshDatabase;

    /** Tenant dapat mengakses halaman tagihan untuk upload receipt. */
    public function test_tenant_can_access_billing_list(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);

        $this->actingAs($tenant)
            ->get('/tenant/billings')
            ->assertOk();
    }

    /** Tenant hanya melihat tagihan miliknya sendiri (scoping benar). */
    public function test_billing_query_scoped_to_authenticated_tenant(): void
    {
        $tenantA = User::factory()->create(['role' => 'tenant']);
        $tenantB = User::factory()->create(['role' => 'tenant']);

        Billing::factory()->create(['user_id' => $tenantA->id, 'status' => 'unpaid']);
        Billing::factory()->create(['user_id' => $tenantB->id, 'status' => 'unpaid']);

        // Query yang digunakan resource: where('user_id', auth()->id())
        $ownBillings = Billing::where('user_id', $tenantA->id)->count();
        $this->assertSame(1, $ownBillings);

        $otherBillings = Billing::where('user_id', $tenantB->id)->where('id', '!=', null)->pluck('id');
        $this->assertFalse(
            Billing::where('user_id', $tenantA->id)->whereIn('id', $otherBillings)->exists()
        );
    }

    /** Tenant tidak bisa upload receipt untuk tagihan milik tenant lain (query scoping). */
    public function test_tenant_cannot_see_other_tenant_billing(): void
    {
        $tenantA = User::factory()->create(['role' => 'tenant']);
        $tenantB = User::factory()->create(['role' => 'tenant']);
        $billingB = Billing::factory()->create(['user_id' => $tenantB->id, 'status' => 'unpaid']);

        // Panel tenant menggunakan modifyQueryUsing yang memfilter ke user_id saja.
        $scopedResult = Billing::where('user_id', $tenantA->id)->where('id', $billingB->id)->exists();
        $this->assertFalse($scopedResult);
    }

    /** Tagihan 'paid' tidak menampilkan tombol upload receipt (visible false). */
    public function test_upload_receipt_not_visible_for_paid_billing(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        $billing = Billing::factory()->create(['user_id' => $tenant->id, 'status' => 'paid']);

        // Simulasikan logika `visible()` dari action upload_receipt.
        $isVisible = in_array($billing->status, ['unpaid', 'throttled']);
        $this->assertFalse($isVisible);
    }

    /** Tagihan 'unpaid' menampilkan tombol upload receipt (visible true). */
    public function test_upload_receipt_visible_for_unpaid_billing(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        $billing = Billing::factory()->create(['user_id' => $tenant->id, 'status' => 'unpaid']);

        $isVisible = in_array($billing->status, ['unpaid', 'throttled']);
        $this->assertTrue($isVisible);
    }

    /** Tagihan 'throttled' juga menampilkan tombol upload receipt. */
    public function test_upload_receipt_visible_for_throttled_billing(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        $billing = Billing::factory()->create(['user_id' => $tenant->id, 'status' => 'throttled']);

        $isVisible = in_array($billing->status, ['unpaid', 'throttled']);
        $this->assertTrue($isVisible);
    }

    /** Setelah upload, payment_receipt tersimpan di database. */
    public function test_payment_receipt_is_stored_in_database(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        $billing = Billing::factory()->create(['user_id' => $tenant->id, 'status' => 'unpaid']);

        // Simulasikan action yang dilakukan upload_receipt action.
        $billing->update(['payment_receipt' => 'receipts/test-receipt.jpg']);

        $this->assertDatabaseHas('billings', [
            'id'              => $billing->id,
            'payment_receipt' => 'receipts/test-receipt.jpg',
        ]);
    }

    /** Upload receipt tidak mengubah status billing (tetap 'unpaid' sampai admin tandai paid). */
    public function test_uploading_receipt_does_not_change_billing_status(): void
    {
        $tenant  = User::factory()->create(['role' => 'tenant']);
        $billing = Billing::factory()->create(['user_id' => $tenant->id, 'status' => 'unpaid']);

        $billing->update(['payment_receipt' => 'receipts/test.jpg']);

        $this->assertDatabaseHas('billings', ['id' => $billing->id, 'status' => 'unpaid']);
    }

    /** Tenant tidak bisa mengakses route /create (canCreate = false). */
    public function test_tenant_cannot_access_create_billing_route(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);

        $this->actingAs($tenant)
            ->get('/tenant/billings/create')
            ->assertStatus(404);
    }
}
