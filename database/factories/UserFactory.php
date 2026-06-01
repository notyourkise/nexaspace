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

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role'        => 'admin',
            'room_number' => null,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
