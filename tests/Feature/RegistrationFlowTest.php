<?php

namespace Tests\Feature;

use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class RegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name'       => 'Pak Budi',
            'kos_name'   => 'Kos Mutiara',
            'email'      => 'budi@example.com',
            'phone'      => '08123456789',
            'room_count' => 25,
            'plan'       => 'pro',
            'message'    => 'Mohon info lebih lanjut.',
        ], $overrides);
    }

    public function test_valid_registration_is_stored_as_pending(): void
    {
        $response = $this->postJson(route('registration.store'), $this->validPayload());

        $response->assertOk();

        $this->assertDatabaseHas('registrations', [
            'name'     => 'Pak Budi',
            'kos_name' => 'Kos Mutiara',
            'email'    => 'budi@example.com',
            'plan'     => 'pro',
            'status'   => 'pending',
        ]);
    }

    public function test_non_custom_plan_response_contains_manual_payment_url(): void
    {
        $response = $this->postJson(route('registration.store'), $this->validPayload(['plan' => 'pro']));

        $response->assertOk()->assertJsonStructure(['payment_url', 'wa_url']);
        $this->assertStringContainsString('/daftar/pembayaran/', $response->json('payment_url'));
        $this->assertNull($response->json('wa_url'));
    }

    public function test_manual_payment_page_shows_bank_options_and_confirmation_link(): void
    {
        $registration = Registration::create($this->validPayload(['plan' => 'pro']) + ['status' => 'pending']);

        $response = $this->get(URL::signedRoute('daftar.payment', $registration));

        $response->assertOk();
        $response->assertSee('Pembayaran Manual');
        $response->assertSee('Bank Central Asia');
        $response->assertSee('Rp 499.000');
        $response->assertSee('https://wa.me/6285249678700', false);
        $response->assertDontSee('Catatan rekening');
        $response->assertDontSee('Nomor rekening di halaman ini masih fiktif');
    }

    public function test_manual_payment_page_requires_valid_signature(): void
    {
        $registration = Registration::create($this->validPayload(['plan' => 'pro']) + ['status' => 'pending']);

        $this->get(route('daftar.payment', $registration))->assertForbidden();
    }

    public function test_custom_plan_response_returns_whatsapp_url_with_details(): void
    {
        $response = $this->postJson(route('registration.store'), $this->validPayload(['plan' => 'custom']));

        $response->assertOk()->assertJsonStructure(['wa_url']);

        $waUrl = $response->json('wa_url');
        $this->assertStringContainsString('https://wa.me/6285249678700', $waUrl);

        $decoded = urldecode($waUrl);
        $this->assertStringContainsString('Kos Mutiara', $decoded);
        $this->assertStringContainsString('budi@example.com', $decoded);
    }

    public function test_email_is_required(): void
    {
        $response = $this->postJson(route('registration.store'), $this->validPayload(['email' => '']));

        $response->assertStatus(422);
        $this->assertArrayHasKey('email', $response->json('errors'));
        $this->assertDatabaseCount('registrations', 0);
    }

    public function test_kos_name_is_required(): void
    {
        $response = $this->postJson(route('registration.store'), $this->validPayload(['kos_name' => '']));

        $response->assertStatus(422);
        $this->assertArrayHasKey('kos_name', $response->json('errors'));
    }

    public function test_invalid_plan_is_rejected(): void
    {
        $response = $this->postJson(route('registration.store'), $this->validPayload(['plan' => 'enterprise']));

        $response->assertStatus(422);
        $this->assertArrayHasKey('plan', $response->json('errors'));
    }

    public function test_status_cannot_be_injected_by_client(): void
    {
        $this->postJson(route('registration.store'), $this->validPayload(['status' => 'active']));

        // Controller forces status=pending regardless of input.
        $this->assertDatabaseHas('registrations', [
            'email'  => 'budi@example.com',
            'status' => 'pending',
        ]);
        $this->assertDatabaseMissing('registrations', [
            'email'  => 'budi@example.com',
            'status' => 'active',
        ]);
    }
}
