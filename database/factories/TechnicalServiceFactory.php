<?php

namespace Database\Factories;

use App\Models\TechnicalService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TechnicalService>
 */
class TechnicalServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Installation', 'Repair']),
            'description' => fake()->sentence(10),
            'price' => fake()->randomFloat(2, 100, 3000),
            'image' => null,
            'is_active' => true,
        ];
    }
}
