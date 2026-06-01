<?php

namespace Database\Seeders;

use App\Models\Billing;
use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Static admin ──────────────────────────────────────────────────────
        User::factory()->admin()->create([
            'name'     => 'Haikal',
            'email'    => 'admin@nexaspace.site',
            'password' => Hash::make('password'),
        ]);

        // ── Static test tenants ───────────────────────────────────────────────
        $staticTenants = [
            ['name' => 'Penyewa Utama',  'email' => 'tenant1@nexaspace.site'],
            ['name' => 'Penyewa Kedua',  'email' => 'tenant2@nexaspace.site'],
        ];

        foreach ($staticTenants as $data) {
            $tenant = User::factory()->create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make('password'),
            ]);

            $this->seedTenantData($tenant);
        }

        // ── Random tenants (Indonesian locale, @nexaspace.site emails) ────────
        User::factory(8)->create()->each(fn (User $tenant) => $this->seedTenantData($tenant));
    }

    private function seedTenantData(User $tenant): void
    {
        Device::factory(fake()->numberBetween(1, 3))->create([
            'user_id' => $tenant->id,
        ]);

        foreach (range(0, 2) as $monthsAgo) {
            $month = Carbon::now()->subMonths($monthsAgo)->startOfMonth();

            Billing::factory()->forMonth($month)->create([
                'user_id' => $tenant->id,
                'status'  => $monthsAgo === 0 ? 'unpaid' : 'paid',
            ]);
        }
    }
}
