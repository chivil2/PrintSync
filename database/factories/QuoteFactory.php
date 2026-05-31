<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quote>
 */
class QuoteFactory extends Factory
{
    protected $model = Quote::class;

    public function definition(): array
    {
        return [
            'quote_number' => 'QT-'.now()->format('Ymd').'-'.str_pad((string) fake()->unique()->randomNumber(4), 4, '0', STR_PAD_LEFT),
            'customer_id' => User::factory(),
            'date' => now(),
            'status' => 'draft',
            'currency' => 'PHP',
            'subtotal' => 1000,
            'tax' => 120,
            'discount' => 0,
            'total' => 1120,
            'terms' => 'Payment due within 30 days.',
            'notes' => null,
            'employee_id' => null,
        ];
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}
