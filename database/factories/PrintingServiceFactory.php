<?php

namespace Database\Factories;

use App\Models\PrintingService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PrintingService>
 */
class PrintingServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Signage', 'Tarpaulin', 'Invitations', 'Brochure', 'T-Shirts', 'Mugs']),
            'description' => fake()->sentence(10),
            'price' => fake()->randomFloat(2, 50, 5000),
            'image' => null,
            'is_active' => true,
        ];
    }
}
