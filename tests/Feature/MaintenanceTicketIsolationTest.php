<?php

namespace Tests\Feature;

use App\Filament\Resources\MaintenanceTicketResource as AdminMaintenanceTicketResource;
use App\Filament\Tenant\Resources\MaintenanceTicketResource as TenantMaintenanceTicketResource;
use App\Models\MaintenanceTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceTicketIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_only_sees_own_maintenance_tickets(): void
    {
        $juragan = User::factory()->juragan()->create();
        $tenantA = User::factory()->create(['role' => 'tenant', 'juragan_id' => $juragan->id]);
        $tenantB = User::factory()->create(['role' => 'tenant', 'juragan_id' => $juragan->id]);

        $ticketA = MaintenanceTicket::factory()->create([
            'tenant_id' => $tenantA->id,
            'juragan_id' => $juragan->id,
        ]);
        $ticketB = MaintenanceTicket::factory()->create([
            'tenant_id' => $tenantB->id,
            'juragan_id' => $juragan->id,
        ]);

        $this->actingAs($tenantA);

        $ids = TenantMaintenanceTicketResource::getEloquentQuery()->pluck('id');

        $this->assertTrue($ids->contains($ticketA->id));
        $this->assertFalse($ids->contains($ticketB->id));
    }

    public function test_juragan_only_sees_maintenance_tickets_from_their_tenants(): void
    {
        $juraganA = User::factory()->juragan()->create();
        $juraganB = User::factory()->juragan()->create();
        $tenantA = User::factory()->create(['role' => 'tenant', 'juragan_id' => $juraganA->id]);
        $tenantB = User::factory()->create(['role' => 'tenant', 'juragan_id' => $juraganB->id]);

        $ticketA = MaintenanceTicket::factory()->create([
            'tenant_id' => $tenantA->id,
            'juragan_id' => $juraganA->id,
        ]);
        $ticketB = MaintenanceTicket::factory()->create([
            'tenant_id' => $tenantB->id,
            'juragan_id' => $juraganB->id,
        ]);

        $this->actingAs($juraganA);

        $ids = AdminMaintenanceTicketResource::getEloquentQuery()->pluck('id');

        $this->assertTrue($ids->contains($ticketA->id));
        $this->assertFalse($ids->contains($ticketB->id));
    }

    public function test_developer_can_see_all_maintenance_tickets(): void
    {
        $developer = User::factory()->developer()->create();

        MaintenanceTicket::factory()->count(2)->create();

        $this->actingAs($developer);

        $this->assertSame(2, AdminMaintenanceTicketResource::getEloquentQuery()->count());
    }

    public function test_maintenance_ticket_pages_are_accessible_for_correct_roles(): void
    {
        $juragan = User::factory()->juragan()->create();
        $tenant = User::factory()->create(['role' => 'tenant', 'juragan_id' => $juragan->id]);

        $this->actingAs($tenant)
            ->get('/tenant/maintenance-tickets')
            ->assertOk();

        $this->actingAs($tenant)
            ->get('/tenant/maintenance-tickets/create')
            ->assertOk();

        $this->actingAs($juragan)
            ->get('/admin/maintenance-tickets')
            ->assertOk();
    }
}
