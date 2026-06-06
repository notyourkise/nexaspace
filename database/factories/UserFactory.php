<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $name = fake()->name();

        // Build a clean slug from the generated Indonesian name for a consistent email.
        $slug = Str::slug($name, '.');

        return [
            'name'               => $name,
            'email'              => fake()->unique()->userName() . '@nexaspace.site',
            'email_verified_at'  => now(),
            'password'           => static::$password ??= Hash::make('password'),
            'remember_token'     => Str::random(10),
            'role'               => 'tenant',
            'room_number'        => fake()->numerify('##'),
            'phone_number'       => '+62' . fake()->numerify('8##########'),
        ];
    }

    /** NexaSpace super admin (platform owner). */
    public function developer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role'        => 'developer',
            'room_number' => null,
            'monthly_rate' => 0,
        ]);
    }

    /** Backward-compatible alias — the former "admin" is now the developer role. */
    public function admin(): static
    {
        return $this->developer();
    }

    /** Boarding-house owner (a paying SaaS customer). */
    public function juragan(): static
    {
        return $this->state(function (array $attributes) {
            $kosName = 'Kos ' . fake()->firstName();
            $slug    = Str::slug($kosName);

            return [
                'role'          => 'juragan',
                'contact_email' => fake()->unique()->safeEmail(),
                'kos_name'      => $kosName,
                'kos_slug'      => $slug,
                'plan'          => fake()->randomElement(['lite', 'pro', 'custom']),
                'room_quota'    => fake()->randomElement([20, 40, 50]),
                'room_number'   => null,
                'monthly_rate'  => 0,
            ];
        });
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
