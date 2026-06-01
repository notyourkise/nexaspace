<?php

namespace Database\Factories;

use App\Models\Device;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Device>
 */
class DeviceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'device_name' => fake()->randomElement(['iPhone', 'Samsung', 'Laptop', 'iPad', 'Android']) . ' ' . fake()->word(),
            'mac_address' => fake()->macAddress(),
            'status' => fake()->randomElement(['active', 'active', 'active', 'throttled', 'blocked']),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'active']);
    }

    public function throttled(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'throttled']);
    }
}
