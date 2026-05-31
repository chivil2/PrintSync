<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\QuoteLineItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuoteLineItem>
 */
class QuoteLineItemFactory extends Factory
{
    protected $model = QuoteLineItem::class;

    public function definition(): array
    {
        return [
            'quote_id' => Quote::factory(),
            'item_name' => fake()->word().' Printing',
            'description' => fake()->sentence(),
            'quantity' => fake()->randomDigitNotNull(),
            'unit_price' => fake()->randomFloat(2, 1, 100),
            'line_total' => 0,
        ];
    }
}
