<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        $month = Carbon::now()->startOfMonth();

        return [
            'amount'             => 199_000,
            'subscription_month' => $month,
            'due_date'           => $month->copy()->addDays(9),
            'status'             => 'unpaid',
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => ['status' => 'paid']);
    }

    public function overdue(): static
    {
        $past = Carbon::now()->subMonth()->startOfMonth();

        return $this->state(fn () => [
            'subscription_month' => $past,
            'due_date'           => $past->copy()->addDays(9),
            'status'             => 'unpaid',
        ]);
    }

    public function forMonth(Carbon $month): static
    {
        return $this->state(fn () => [
            'subscription_month' => $month->copy()->startOfMonth(),
            'due_date'           => $month->copy()->startOfMonth()->addDays(9),
        ]);
    }
}
