<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    // ── Admin panel ───────────────────────────────────────────────────────────

    /** Admin dapat mengakses dashboard admin. */
    public function test_admin_can_access_admin_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk();
    }

    /** Tenant yang sudah login dilarang masuk ke admin panel — dapat 403. */
    public function test_tenant_cannot_access_admin_panel(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);

        $this->actingAs($tenant)
            ->get('/admin')
            ->assertForbidden();
    }

    /** Guest tanpa login diredirect ke login admin. */
    public function test_guest_cannot_access_admin_panel(): void
    {
        $this->get('/admin')->assertRedirect();
    }

    // ── Tenant panel ──────────────────────────────────────────────────────────

    /** Tenant dapat mengakses dashboard tenant. */
    public function test_tenant_can_access_tenant_panel(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);

        $this->actingAs($tenant)
            ->get('/tenant')
            ->assertOk();
    }

    /** Admin yang sudah login dilarang masuk ke tenant panel — dapat 403. */
    public function test_admin_cannot_access_tenant_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/tenant')
            ->assertForbidden();
    }

    /** Guest tanpa login diredirect ke login tenant. */
    public function test_guest_cannot_access_tenant_panel(): void
    {
        $this->get('/tenant')->assertRedirect();
    }

    // ── Login pages ───────────────────────────────────────────────────────────

    /** Halaman login admin dapat diakses tanpa autentikasi. */
    public function test_admin_login_page_is_accessible(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    /** Halaman login tenant dapat diakses tanpa autentikasi. */
    public function test_tenant_login_page_is_accessible(): void
    {
        $this->get('/tenant/login')->assertOk();
    }

    // ── Suspended juragan ─────────────────────────────────────────────────────

    /** Juragan yang di-suspend tidak bisa mengakses admin panel. */
    public function test_suspended_juragan_cannot_access_admin_panel(): void
    {
        $juragan = User::factory()->juragan()->create([
            'suspended_at' => Carbon::now(),
        ]);

        $this->actingAs($juragan)
            ->get('/admin')
            ->assertForbidden();
    }

    /** Juragan yang tidak di-suspend bisa mengakses admin panel. */
    public function test_active_juragan_can_access_admin_panel(): void
    {
        $juragan = User::factory()->juragan()->create([
            'suspended_at' => null,
        ]);

        $this->actingAs($juragan)
            ->get('/admin')
            ->assertOk();
    }

    /** Developer tidak pernah bisa di-suspend — tetap bisa akses admin panel. */
    public function test_developer_is_never_blocked_by_suspension(): void
    {
        $developer = User::factory()->developer()->create([
            'suspended_at' => Carbon::now(), // seharusnya tidak pernah terjadi, tapi dicek pula
        ]);

        $this->actingAs($developer)
            ->get('/admin')
            ->assertOk();
    }

    /** Anak kos kehilangan akses tenant panel ketika juragan-nya di-suspend. */
    public function test_tenant_cannot_access_panel_when_juragan_is_suspended(): void
    {
        $juragan = User::factory()->juragan()->create([
            'suspended_at' => Carbon::now(),
        ]);

        $tenant = User::factory()->create([
            'role'       => 'tenant',
            'juragan_id' => $juragan->id,
        ]);

        $this->actingAs($tenant)
            ->get('/tenant')
            ->assertForbidden();
    }

    /** Anak kos bisa akses tenant panel ketika juragan-nya aktif (tidak di-suspend). */
    public function test_tenant_can_access_panel_when_juragan_is_active(): void
    {
        $juragan = User::factory()->juragan()->create([
            'suspended_at' => null,
        ]);

        $tenant = User::factory()->create([
            'role'       => 'tenant',
            'juragan_id' => $juragan->id,
        ]);

        $this->actingAs($tenant)
            ->get('/tenant')
            ->assertOk();
    }
}
