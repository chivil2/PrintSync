<?php

namespace Tests\Feature\Chat;

use App\Models\Conversation;
use App\Models\PrintingService;
use App\Models\Quote;
use App\Models\ServiceJob;
use App\Models\User;
use App\Notifications\NewChatMessageNotification;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OwnerChatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_owner_can_view_chat_index(): void
    {
        [$customer, $owner] = $this->makeCustomerAndOwner();
        Conversation::create([
            'quote_id' => null,
            'customer_id' => $customer->id,
            'owner_id' => $owner->id,
        ]);

        $response = $this->actingAs($owner)->get(route('owner.chat.index'));

        $response->assertOk();
        $response->assertSee('Messages');
    }

    public function test_owner_can_view_their_conversation(): void
    {
        [$customer, $owner] = $this->makeCustomerAndOwner();
        $conversation = $this->makeConversation($customer, $owner);

        $response = $this->actingAs($owner)->get(route('owner.chat.show', $conversation));

        $response->assertOk();
        $this->assertNotNull($conversation->fresh()->owner_last_read_at);
    }

    public function test_owner_cannot_view_a_conversation_they_do_not_own(): void
    {
        [$customer, $ownerA] = $this->makeCustomerAndOwner();
        $ownerB = $this->makeOwner();
        $conversation = Conversation::create([
            'quote_id' => null,
            'customer_id' => $customer->id,
            'owner_id' => $ownerA->id,
        ]);

        $response = $this->actingAs($ownerB)->get(route('owner.chat.show', $conversation));

        $response->assertForbidden();
    }

    public function test_owner_can_send_a_message_to_customer(): void
    {
        Notification::fake();
        [$customer, $owner] = $this->makeCustomerAndOwner();
        $conversation = $this->makeConversation($customer, $owner);

        $response = $this->actingAs($owner)->postJson(
            route('owner.chat.send', $conversation),
            ['body' => 'Thanks for your message!']
        );

        $response->assertCreated();
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $owner->id,
            'body' => 'Thanks for your message!',
        ]);

        Notification::assertSentTo($customer, NewChatMessageNotification::class);
    }

    public function test_owner_can_poll_for_new_messages(): void
    {
        [$customer, $owner] = $this->makeCustomerAndOwner();
        $conversation = $this->makeConversation($customer, $owner);
        $conversation->messages()->create([
            'sender_id' => $customer->id,
            'body' => 'Any updates?',
        ]);

        $response = $this->actingAs($owner)
            ->getJson(route('owner.chat.poll', $conversation));

        $response->assertOk();
        $response->assertJsonPath('messages.0.body', 'Any updates?');
    }

    public function test_owner_can_mark_conversation_read(): void
    {
        [$customer, $owner] = $this->makeCustomerAndOwner();
        $conversation = $this->makeConversation($customer, $owner);

        $response = $this->actingAs($owner)
            ->postJson(route('owner.chat.read', $conversation));

        $response->assertOk();
        $this->assertNotNull($conversation->fresh()->owner_last_read_at);
    }

    public function test_sending_empty_message_is_rejected(): void
    {
        [$customer, $owner] = $this->makeCustomerAndOwner();
        $conversation = $this->makeConversation($customer, $owner);

        $response = $this->actingAs($owner)->postJson(
            route('owner.chat.send', $conversation),
            ['body' => '']
        );

        $response->assertStatus(422);
    }

    /**
     * @return array{0: User, 1: User}
     */
    private function makeCustomerAndOwner(): array
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $owner = User::factory()->create();
        $owner->assignRole('owner');

        return [$customer, $owner];
    }

    private function makeOwner(): User
    {
        $owner = User::factory()->create();
        $owner->assignRole('owner');

        return $owner;
    }

    private function makeQuoteFor(User $customer): Quote
    {
        $printingService = PrintingService::factory()->create();
        $serviceJob = ServiceJob::create([
            'name' => $printingService->name,
            'description' => $printingService->description,
            'type' => 'printing',
            'customer_id' => $customer->id,
            'service_id' => $printingService->id,
            'service_type' => 'printing_service',
            'status' => 'pending',
        ]);

        return Quote::factory()->create([
            'customer_id' => $customer->id,
            'service_job_id' => $serviceJob->id,
        ]);
    }

    private function makeConversation(User $customer, User $owner, ?Quote $quote = null): Conversation
    {
        return Conversation::create([
            'quote_id' => $quote?->id,
            'customer_id' => $customer->id,
            'owner_id' => $owner->id,
        ]);
    }
}
