<?php

namespace Tests\Feature;

use App\Jobs\ProvisionTenantJob;
use App\Mail\TenantProvisionedMail;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProvisioningTest extends TestCase
{
    use RefreshDatabase;

    private function registration(array $overrides = []): Registration
    {
        return Registration::create(array_merge([
            'name'       => 'Pak Mawar',
            'kos_name'   => 'Kos Mawar',
            'email'      => 'pakmawar@example.com',
            'phone'      => '08123456789',
            'room_count' => 30,
            'plan'       => 'pro',
            'status'     => 'pending',
        ], $overrides));
    }

    public function test_pro_plan_provisions_juragan_and_40_anak_kos(): void
    {
        Mail::fake();
        $reg = $this->registration(['plan' => 'pro']);

        ProvisionTenantJob::dispatchSync($reg);

        $juragan = User::where('role', 'juragan')->where('kos_slug', 'kos-mawar')->first();
        $this->assertNotNull($juragan);
        $this->assertSame('owner@kos-mawar.com', $juragan->email);
        $this->assertSame(40, $juragan->room_quota);
        $this->assertSame('pro', $juragan->plan);

        $this->assertSame(40, User::where('role', 'tenant')->where('juragan_id', $juragan->id)->count());
        $this->assertDatabaseHas('users', ['email' => 'room1@kos-mawar.com', 'juragan_id' => $juragan->id]);
        $this->assertDatabaseHas('users', ['email' => 'room40@kos-mawar.com', 'juragan_id' => $juragan->id]);

        $this->assertSame('active', $reg->fresh()->status);
    }

    public function test_lite_plan_provisions_20_anak_kos(): void
    {
        Mail::fake();
        $reg = $this->registration(['plan' => 'lite']);

        ProvisionTenantJob::dispatchSync($reg);

        $juragan = User::where('role', 'juragan')->first();
        $this->assertSame(20, $juragan->room_quota);
        $this->assertSame(20, User::where('role', 'tenant')->where('juragan_id', $juragan->id)->count());
    }

    public function test_custom_plan_uses_at_least_50_or_room_count(): void
    {
        Mail::fake();
        $reg = $this->registration(['plan' => 'custom', 'room_count' => 70]);

        ProvisionTenantJob::dispatchSync($reg);

        $juragan = User::where('role', 'juragan')->first();
        $this->assertSame(70, $juragan->room_quota);
        $this->assertSame(70, User::where('role', 'tenant')->where('juragan_id', $juragan->id)->count());
    }

    public function test_credentials_email_sent_to_juragan(): void
    {
        Mail::fake();
        $reg = $this->registration();

        ProvisionTenantJob::dispatchSync($reg);

        Mail::assertSent(TenantProvisionedMail::class, function (TenantProvisionedMail $mail) use ($reg) {
            return $mail->hasTo($reg->email)
                && $mail->juraganPassword === 'password'
                && count($mail->anakKos) === 40;
        });
    }

    public function test_passwords_are_hashed_in_database(): void
    {
        Mail::fake();
        $reg = $this->registration(['plan' => 'lite']);

        ProvisionTenantJob::dispatchSync($reg);

        $juragan = User::where('role', 'juragan')->first();
        $this->assertNotSame('', $juragan->password);
        $this->assertTrue(str_starts_with($juragan->password, '$'), 'Password should be a bcrypt hash');
        $this->assertTrue(Hash::check('password', $juragan->password));
        $this->assertFalse(Hash::check('', $juragan->password));

        $tenant = User::where('role', 'tenant')->first();
        $this->assertTrue(Hash::check('password', $tenant->password));
    }

    public function test_provisioning_is_idempotent(): void
    {
        Mail::fake();
        $reg = $this->registration(['plan' => 'lite']);

        ProvisionTenantJob::dispatchSync($reg);
        ProvisionTenantJob::dispatchSync($reg); // second run must be a no-op

        $this->assertSame(1, User::where('role', 'juragan')->count());
        $this->assertSame(20, User::where('role', 'tenant')->count());
        Mail::assertSent(TenantProvisionedMail::class, 1);
    }

    public function test_slug_collision_is_resolved(): void
    {
        Mail::fake();

        // Pre-existing kos with the same slug.
        User::factory()->juragan()->create(['kos_slug' => 'kos-mawar']);

        $reg = $this->registration(['plan' => 'lite']);
        ProvisionTenantJob::dispatchSync($reg);

        $this->assertDatabaseHas('users', ['email' => 'owner@kos-mawar-2.com', 'kos_slug' => 'kos-mawar-2']);
        $this->assertDatabaseHas('users', ['email' => 'room1@kos-mawar-2.com']);
    }
}
