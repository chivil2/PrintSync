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

class CustomerChatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_customer_can_view_their_chat_index(): void
    {
        [$customer, $owner] = $this->makeCustomerAndOwner();
        Conversation::create([
            'quote_id' => null,
            'customer_id' => $customer->id,
            'owner_id' => $owner->id,
        ]);

        $response = $this->actingAs($customer)->get(route('customer.chat.index'));

        $response->assertOk();
        $response->assertSee('Messages');
    }

    public function test_customer_can_view_their_conversation(): void
    {
        [$customer, $owner] = $this->makeCustomerAndOwner();
        $conversation = Conversation::create([
            'quote_id' => null,
            'customer_id' => $customer->id,
            'owner_id' => $owner->id,
        ]);

        $response = $this->actingAs($customer)->get(route('customer.chat.show', $conversation));

        $response->assertOk();
        $this->assertNotNull($conversation->fresh()->customer_last_read_at);
    }

    public function test_customer_cannot_view_another_customers_conversation(): void
    {
        [, $owner] = $this->makeCustomerAndOwner();
        $conversation = Conversation::create([
            'quote_id' => null,
            'customer_id' => $this->makeCustomer()->id,
            'owner_id' => $owner->id,
        ]);
        $otherCustomer = $this->makeCustomer();

        $response = $this->actingAs($otherCustomer)->get(route('customer.chat.show', $conversation));

        $response->assertForbidden();
    }

    public function test_customer_can_send_a_message(): void
    {
        Notification::fake();
        [$customer, $owner] = $this->makeCustomerAndOwner();
        $conversation = $this->makeConversation($customer, $owner);

        $response = $this->actingAs($customer)->postJson(
            route('customer.chat.send', $conversation),
            ['body' => 'Hello, can I get a discount?']
        );

        $response->assertCreated();
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $customer->id,
            'body' => 'Hello, can I get a discount?',
        ]);
        $this->assertNotNull($conversation->fresh()->last_message_at);

        Notification::assertSentTo($owner, NewChatMessageNotification::class);
    }

    public function test_customer_can_poll_for_new_messages(): void
    {
        [$customer, $owner] = $this->makeCustomerAndOwner();
        $conversation = $this->makeConversation($customer, $owner);
        $conversation->messages()->create([
            'sender_id' => $owner->id,
            'body' => 'Welcome!',
        ]);

        $response = $this->actingAs($customer)
            ->getJson(route('customer.chat.poll', $conversation));

        $response->assertOk();
        $response->assertJsonPath('messages.0.body', 'Welcome!');
    }

    public function test_poll_returns_304_when_if_modified_since_matches(): void
    {
        [$customer, $owner] = $this->makeCustomerAndOwner();
        $conversation = $this->makeConversation($customer, $owner);
        $conversation->messages()->create([
            'sender_id' => $owner->id,
            'body' => 'Hello',
        ]);

        $lastModified = $conversation->fresh()->updated_at?->toRfc7231String();

        $response = $this->actingAs($customer)
            ->withHeaders(['If-Modified-Since' => $lastModified])
            ->getJson(route('customer.chat.poll', $conversation));

        $response->assertStatus(304);
    }

    public function test_customer_can_mark_conversation_read(): void
    {
        [$customer, $owner] = $this->makeCustomerAndOwner();
        $conversation = $this->makeConversation($customer, $owner);

        $response = $this->actingAs($customer)
            ->postJson(route('customer.chat.read', $conversation));

        $response->assertOk();
        $this->assertNotNull($conversation->fresh()->customer_last_read_at);
    }

    public function test_customer_can_open_chat_from_their_quote(): void
    {
        [$customer, $owner] = $this->makeCustomerAndOwner();
        $quote = $this->makeQuoteFor($customer);

        $response = $this->actingAs($customer)
            ->get(route('customer.chat.open-for-quote', $quote));

        $response->assertRedirect();
        $this->assertDatabaseHas('conversations', [
            'quote_id' => $quote->id,
            'customer_id' => $customer->id,
            'owner_id' => $owner->id,
        ]);
    }

    public function test_customer_cannot_open_chat_from_someone_elses_quote(): void
    {
        $quote = $this->makeQuoteFor($this->makeCustomer());
        $otherCustomer = $this->makeCustomer();

        $response = $this->actingAs($otherCustomer)
            ->get(route('customer.chat.open-for-quote', $quote));

        $response->assertForbidden();
    }

    public function test_sending_empty_message_is_rejected(): void
    {
        [$customer, $owner] = $this->makeCustomerAndOwner();
        $conversation = $this->makeConversation($customer, $owner);

        $response = $this->actingAs($customer)->postJson(
            route('customer.chat.send', $conversation),
            ['body' => '']
        );

        $response->assertStatus(422);
    }

    /**
     * @return array{0: User, 1: User}
     */
    private function makeCustomerAndOwner(): array
    {
        return [$this->makeCustomer(), $this->makeOwner()];
    }

    private function makeCustomer(): User
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        return $customer;
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
