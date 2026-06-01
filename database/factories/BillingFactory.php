<?php

namespace Database\Factories;

use App\Models\Billing;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Billing>
 */
class BillingFactory extends Factory
{
    public function definition(): array
    {
        $billingMonth = Carbon::now()->startOfMonth();

        return [
            'amount' => fake()->numberBetween(100000, 300000),
            'billing_month' => $billingMonth,
            'due_date' => $billingMonth->copy()->addDays(10),
            'status' => 'unpaid',
        ];
    }

    public function forMonth(Carbon $month): static
    {
        return $this->state(fn (array $attributes) => [
            'billing_month' => $month->copy()->startOfMonth(),
            'due_date' => $month->copy()->startOfMonth()->addDays(10),
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'paid']);
    }

    public function overdue(): static
    {
        $past = Carbon::now()->subMonth()->startOfMonth();

        return $this->state(fn (array $attributes) => [
            'billing_month' => $past,
            'due_date' => $past->copy()->addDays(10),
            'status' => 'unpaid',
        ]);
    }
}
