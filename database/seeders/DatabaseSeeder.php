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
        // ── NexaSpace developer / super admin ─────────────────────────────────
        User::factory()->developer()->create([
            'name'     => 'Haikal',
            'email'    => 'admin@nexaspace.site',
            'password' => Hash::make('password'),
        ]);

        // ── Juragan (paying SaaS customers), each with their own kos ───────────
        // Login email is namespaced to the kos slug (e.g. owner@mutiara.com),
        // anak kos use roomN@<slug>.com.
        $juraganDefs = [
            ['name' => 'Pak Budi', 'kos_name' => 'Kos Mutiara', 'kos_slug' => 'mutiara', 'plan' => 'pro',  'room_quota' => 40, 'anak_kos' => 5],
            ['name' => 'Bu Sani',  'kos_name' => 'Kos Melati',  'kos_slug' => 'melati',  'plan' => 'lite', 'room_quota' => 20, 'anak_kos' => 4],
        ];

        foreach ($juraganDefs as $def) {
            $juragan = User::factory()->juragan()->create([
                'name'       => $def['name'],
                'email'      => "owner@{$def['kos_slug']}.com",
                'password'   => Hash::make('password'),
                'kos_name'   => $def['kos_name'],
                'kos_slug'   => $def['kos_slug'],
                'plan'       => $def['plan'],
                'room_quota' => $def['room_quota'],
            ]);

            $this->seedAnakKos($juragan, $def['kos_slug'], $def['anak_kos']);
        }
    }

    /**
     * Create anak kos accounts under a juragan, each namespaced to the kos slug,
     * plus their devices and three months of billing history.
     */
    private function seedAnakKos(User $juragan, string $kosSlug, int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            $anak = User::factory()->create([
                'email'        => "room{$i}@{$kosSlug}.com",
                'password'     => Hash::make('password'),
                'role'         => 'tenant',
                'juragan_id'   => $juragan->id,
                'room_number'  => (string) $i,
                'monthly_rate' => fake()->numberBetween(150, 350) * 1000,
            ]);

            Device::factory(fake()->numberBetween(1, 3))->create([
                'user_id' => $anak->id,
            ]);

            foreach (range(0, 2) as $monthsAgo) {
                $month = Carbon::now()->subMonths($monthsAgo)->startOfMonth();

                Billing::factory()->forMonth($month)->create([
                    'user_id' => $anak->id,
                    'status'  => $monthsAgo === 0 ? 'unpaid' : 'paid',
                ]);
            }
        }
    }
}
