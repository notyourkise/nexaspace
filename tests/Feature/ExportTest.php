<?php

namespace Tests\Feature;

use App\Models\Billing;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    // ── /export/billing ───────────────────────────────────────────────────────

    /** Tamu tidak bisa mengakses export billing. */
    public function test_guest_cannot_export_billing(): void
    {
        $this->get(route('export.billing'))->assertStatus(403);
    }

    /** Anak kos tidak bisa mengakses export billing (403). */
    public function test_tenant_cannot_export_billing(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);

        $this->actingAs($tenant)
            ->get(route('export.billing'))
            ->assertStatus(403);
    }

    /** Juragan bisa export CSV tagihan dan mendapat header yang benar. */
    public function test_juragan_can_export_billing_csv_with_correct_headers(): void
    {
        $juragan = User::factory()->juragan()->create();
        $tenant  = User::factory()->create(['role' => 'tenant', 'juragan_id' => $juragan->id]);
        Billing::factory()->create([
            'user_id'       => $tenant->id,
            'amount'        => 250000,
            'billing_month' => '2026-06-01',
            'due_date'      => '2026-06-10',
            'status'        => 'unpaid',
        ]);

        $response = $this->actingAs($juragan)
            ->get(route('export.billing'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Nama Anak Kos', $content);
        $this->assertStringContainsString('Nomor Kamar', $content);
        $this->assertStringContainsString('Bulan Tagihan', $content);
        $this->assertStringContainsString('Jatuh Tempo', $content);
        $this->assertStringContainsString('Nominal (Rp)', $content);
        $this->assertStringContainsString('Status', $content);
    }

    /** CSV hanya berisi data anak kos milik juragan sendiri (isolasi data). */
    public function test_juragan_export_billing_only_contains_own_tenants(): void
    {
        $juraganA = User::factory()->juragan()->create();
        $juraganB = User::factory()->juragan()->create();

        $tenantA = User::factory()->create(['role' => 'tenant', 'juragan_id' => $juraganA->id, 'name' => 'Anak Kos A']);
        $tenantB = User::factory()->create(['role' => 'tenant', 'juragan_id' => $juraganB->id, 'name' => 'Anak Kos B']);

        Billing::factory()->create(['user_id' => $tenantA->id, 'status' => 'paid']);
        Billing::factory()->create(['user_id' => $tenantB->id, 'status' => 'unpaid']);

        $response = $this->actingAs($juraganA)
            ->get(route('export.billing'))
            ->assertOk();

        $content = $response->streamedContent();
        $this->assertStringContainsString('Anak Kos A', $content);
        $this->assertStringNotContainsString('Anak Kos B', $content);
    }

    /** Developer melihat semua tagihan semua juragan. */
    public function test_developer_export_billing_sees_all_tenants(): void
    {
        $developer = User::factory()->developer()->create();

        $juraganA = User::factory()->juragan()->create();
        $juraganB = User::factory()->juragan()->create();

        $tenantA = User::factory()->create(['role' => 'tenant', 'juragan_id' => $juraganA->id, 'name' => 'Budi']);
        $tenantB = User::factory()->create(['role' => 'tenant', 'juragan_id' => $juraganB->id, 'name' => 'Sari']);

        Billing::factory()->create(['user_id' => $tenantA->id]);
        Billing::factory()->create(['user_id' => $tenantB->id]);

        $response = $this->actingAs($developer)
            ->get(route('export.billing'))
            ->assertOk();

        $content = $response->streamedContent();
        $this->assertStringContainsString('Budi', $content);
        $this->assertStringContainsString('Sari', $content);
    }

    /** Filter ?month=YYYY-MM hanya menampilkan tagihan bulan tersebut. */
    public function test_billing_export_month_filter(): void
    {
        $juragan = User::factory()->juragan()->create();
        $tenant  = User::factory()->create(['role' => 'tenant', 'juragan_id' => $juragan->id, 'name' => 'Budi']);

        Billing::factory()->create(['user_id' => $tenant->id, 'billing_month' => '2026-06-01', 'status' => 'unpaid']);
        Billing::factory()->create(['user_id' => $tenant->id, 'billing_month' => '2026-05-01', 'status' => 'paid']);

        $response = $this->actingAs($juragan)
            ->get(route('export.billing', ['month' => '2026-06']))
            ->assertOk();

        $content = $response->streamedContent();
        $this->assertStringContainsString('Jun 2026', $content);
        $this->assertStringNotContainsString('May 2026', $content);
    }

    /** Status billing diterjemahkan ke Bahasa Indonesia di CSV. */
    public function test_billing_export_status_is_translated(): void
    {
        $juragan = User::factory()->juragan()->create();
        $tenant  = User::factory()->create(['role' => 'tenant', 'juragan_id' => $juragan->id]);

        Billing::factory()->create(['user_id' => $tenant->id, 'status' => 'paid']);
        Billing::factory()->create(['user_id' => $tenant->id, 'status' => 'unpaid']);
        Billing::factory()->create(['user_id' => $tenant->id, 'status' => 'throttled']);

        $response = $this->actingAs($juragan)->get(route('export.billing'))->assertOk();
        $content  = $response->streamedContent();

        $this->assertStringContainsString('Lunas', $content);
        $this->assertStringContainsString('Belum Lunas', $content);
        $this->assertStringContainsString('Dibatasi', $content);
    }

    // ── /export/subscription ──────────────────────────────────────────────────

    /** Tamu tidak bisa export subscription. */
    public function test_guest_cannot_export_subscription(): void
    {
        $this->get(route('export.subscription'))->assertStatus(403);
    }

    /** Anak kos tidak bisa export subscription (403). */
    public function test_tenant_cannot_export_subscription(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);

        $this->actingAs($tenant)
            ->get(route('export.subscription'))
            ->assertStatus(403);
    }

    /** Juragan hanya melihat subscription miliknya sendiri. */
    public function test_juragan_export_subscription_only_contains_own_data(): void
    {
        $juraganA = User::factory()->juragan()->create(['kos_name' => 'Kos Alpha']);
        $juraganB = User::factory()->juragan()->create(['kos_name' => 'Kos Beta']);

        Subscription::factory()->create([
            'juragan_id'         => $juraganA->id,
            'subscription_month' => '2026-06-01',
        ]);
        Subscription::factory()->create([
            'juragan_id'         => $juraganB->id,
            'subscription_month' => '2026-06-01',
        ]);

        $response = $this->actingAs($juraganA)
            ->get(route('export.subscription'))
            ->assertOk();

        $content = $response->streamedContent();
        $this->assertStringContainsString('Kos Alpha', $content);
        $this->assertStringNotContainsString('Kos Beta', $content);
    }

    /** Developer melihat semua subscription semua juragan. */
    public function test_developer_export_subscription_sees_all(): void
    {
        $developer = User::factory()->developer()->create();

        $juraganA = User::factory()->juragan()->create(['kos_name' => 'Kos Alpha']);
        $juraganB = User::factory()->juragan()->create(['kos_name' => 'Kos Beta']);

        Subscription::factory()->create(['juragan_id' => $juraganA->id]);
        Subscription::factory()->create(['juragan_id' => $juraganB->id]);

        $response = $this->actingAs($developer)
            ->get(route('export.subscription'))
            ->assertOk();

        $content = $response->streamedContent();
        $this->assertStringContainsString('Kos Alpha', $content);
        $this->assertStringContainsString('Kos Beta', $content);
    }

    /** CSV subscription memiliki header kolom yang benar. */
    public function test_subscription_export_has_correct_headers(): void
    {
        $developer = User::factory()->developer()->create();

        $response = $this->actingAs($developer)
            ->get(route('export.subscription'))
            ->assertOk();

        $content = $response->streamedContent();
        $this->assertStringContainsString('Juragan', $content);
        $this->assertStringContainsString('Nama Kos', $content);
        $this->assertStringContainsString('Paket', $content);
        $this->assertStringContainsString('Bulan', $content);
        $this->assertStringContainsString('Jatuh Tempo', $content);
        $this->assertStringContainsString('Nominal (Rp)', $content);
        $this->assertStringContainsString('Status', $content);
    }
}
