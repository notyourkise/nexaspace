<?php

namespace Database\Factories;

use App\Models\MaintenanceTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceTicket>
 */
class MaintenanceTicketFactory extends Factory
{
    protected $model = MaintenanceTicket::class;

    public function definition(): array
    {
        $juragan = User::factory()->juragan()->create();
        $tenant = User::factory()->create([
            'role' => 'tenant',
            'juragan_id' => $juragan->id,
        ]);

        return [
            'tenant_id' => $tenant->id,
            'juragan_id' => $juragan->id,
            'category' => fake()->randomElement(array_keys(MaintenanceTicket::categoryOptions())),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'evidence_path' => 'maintenance-reports/example.jpg',
            'status' => MaintenanceTicket::STATUS_OPEN,
            'progress_note' => null,
            'reviewed_at' => null,
            'resolved_at' => null,
        ];
    }
}
