<?php

namespace Tests\Feature;

use App\Filament\Resources\BillingResource;
use App\Filament\Resources\DeviceResource;
use App\Filament\Resources\RegistrationResource;
use App\Filament\Resources\UserResource;
use App\Models\Billing;
use App\Models\Device;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JuraganIsolationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Build two juragan, each with one anak kos that owns one device and one bill.
     *
     * @return array{0: User, 1: User, 2: User, 3: User} [juraganA, anakA, juraganB, anakB]
     */
    private function seedTwoJuragan(): array
    {
        $juraganA = User::factory()->juragan()->create();
        $juraganB = User::factory()->juragan()->create();

        $anakA = User::factory()->create(['role' => 'tenant', 'juragan_id' => $juraganA->id]);
        $anakB = User::factory()->create(['role' => 'tenant', 'juragan_id' => $juraganB->id]);

        Device::factory()->create(['user_id' => $anakA->id]);
        Device::factory()->create(['user_id' => $anakB->id]);

        Billing::factory()->create(['user_id' => $anakA->id]);
        Billing::factory()->create(['user_id' => $anakB->id]);

        return [$juraganA, $anakA, $juraganB, $anakB];
    }

    public function test_juragan_only_sees_own_anak_kos(): void
    {
        [$juraganA, $anakA, $juraganB, $anakB] = $this->seedTwoJuragan();

        $this->actingAs($juraganA);
        $ids = UserResource::getEloquentQuery()->pluck('id');

        $this->assertTrue($ids->contains($anakA->id));
        $this->assertFalse($ids->contains($anakB->id));
        $this->assertFalse($ids->contains($juraganB->id));
        $this->assertSame(1, $ids->count());
    }

    public function test_developer_sees_all_users(): void
    {
        [$juraganA, $anakA, $juraganB, $anakB] = $this->seedTwoJuragan();
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer);
        $count = UserResource::getEloquentQuery()->count();

        // 2 juragan + 2 anak kos + 1 developer
        $this->assertSame(5, $count);
    }

    public function test_juragan_only_sees_own_anak_kos_devices(): void
    {
        [$juraganA, $anakA, $juraganB, $anakB] = $this->seedTwoJuragan();

        $this->actingAs($juraganA);
        $deviceUserIds = DeviceResource::getEloquentQuery()->pluck('user_id');

        $this->assertTrue($deviceUserIds->contains($anakA->id));
        $this->assertFalse($deviceUserIds->contains($anakB->id));
        $this->assertSame(1, $deviceUserIds->count());
    }

    public function test_juragan_only_sees_own_anak_kos_billings(): void
    {
        [$juraganA, $anakA, $juraganB, $anakB] = $this->seedTwoJuragan();

        $this->actingAs($juraganA);
        $billingUserIds = BillingResource::getEloquentQuery()->pluck('user_id');

        $this->assertTrue($billingUserIds->contains($anakA->id));
        $this->assertFalse($billingUserIds->contains($anakB->id));
        $this->assertSame(1, $billingUserIds->count());
    }

    public function test_developer_billing_query_is_unscoped(): void
    {
        $this->seedTwoJuragan();
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer);

        $this->assertSame(2, BillingResource::getEloquentQuery()->count());
    }

    public function test_registration_resource_accessible_only_by_developer(): void
    {
        $developer = User::factory()->developer()->create();
        $juragan   = User::factory()->juragan()->create();

        $this->actingAs($developer);
        $this->assertTrue(RegistrationResource::canAccess());

        $this->actingAs($juragan);
        $this->assertFalse(RegistrationResource::canAccess());
        $this->assertFalse(RegistrationResource::shouldRegisterNavigation());
    }

    public function test_registration_hidden_from_juragan_via_navigation(): void
    {
        Registration::create([
            'name'       => 'Calon Juragan',
            'kos_name'   => 'Kos Anggrek',
            'email'      => 'calon@example.com',
            'phone'      => '08123456789',
            'room_count' => 10,
            'plan'       => 'pro',
            'status'     => 'pending',
        ]);

        $developer = User::factory()->developer()->create();
        $this->assertTrue(
            $this->actingAs($developer) && RegistrationResource::canViewAny()
        );

        $juragan = User::factory()->juragan()->create();
        $this->actingAs($juragan);
        $this->assertFalse(RegistrationResource::canViewAny());
    }
}
