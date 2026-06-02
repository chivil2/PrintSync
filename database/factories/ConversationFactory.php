<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conversation>
 */
class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        $owner = User::factory()->create();
        $customer = User::factory()->create();

        return [
            'quote_id' => null,
            'customer_id' => $customer->id,
            'owner_id' => $owner->id,
            'last_message_at' => null,
            'customer_last_read_at' => null,
            'owner_last_read_at' => null,
        ];
    }
}
