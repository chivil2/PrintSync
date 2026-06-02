<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Quote;
use App\Models\ServiceJob;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'quote_id' => Quote::factory(),
            'service_job_id' => ServiceJob::factory(),
            'customer_id' => User::factory(),
            'method' => 'gcash',
            'amount' => 1500.00,
            'reference_no' => fake()->numerify('##########'),
            'status' => Payment::STATUS_PENDING,
            'notes' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => Payment::STATUS_PENDING]);
    }

    public function verified(): static
    {
        return $this->state(fn () => [
            'status' => Payment::STATUS_VERIFIED,
            'verified_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => Payment::STATUS_REJECTED,
            'rejection_reason' => 'Reference number could not be verified.',
        ]);
    }
}
