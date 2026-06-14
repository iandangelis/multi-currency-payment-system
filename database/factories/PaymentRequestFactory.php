<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentRequest>
 */
class PaymentRequestFactory extends Factory
{
    private const CURRENCIES = [
        'USD',
        'BRL',
        'GBP',
        'CAD',
        'AUD',
        'JPY',
        'CHF',
        'SEK',
        'NOK',
        'NZD',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requester_id' => User::factory(),
            'approver_id' => null,
            'amount' => fake()->randomFloat(2, 100, 10000),
            'currency' => $this->getRandomCurrencyCode(),
            'target_currency' => 'EUR',
            'converted_amount' => fake()->randomFloat(2, 10, 5000),
            'exchange_rate' => fake()->randomFloat(6, 0.1, 10),
            'exchange_rate_source' => 'exchange-rate-api',
            'exchange_rate_fetched_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'status' => PaymentStatus::Pending,
            'approved_at' => null,
            'rejected_at' => null,
            'expires_at' => fake()->dateTimeBetween('now', '+7 days'),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn() => [
            'status' => PaymentStatus::Approved,
            'approved_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn() => [
            'status' => PaymentStatus::Rejected,
            'rejected_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn() => [
            'status' => PaymentStatus::Expired,
            'expires_at' => now()->subHour(),
        ]);
    }

    private function getRandomCurrencyCode(): string
    {
        return fake()->randomElement(self::CURRENCIES);
    }
}
