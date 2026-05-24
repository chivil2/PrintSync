<?php

namespace Tests\Feature\Owner;

use App\Models\Quote;
use App\Models\QuoteLineItem;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotesManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_owner_can_view_quotes_page(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $response = $this->actingAs($owner)->get(route('owner.quotes'));

        $response->assertOk();
    }

    public function test_owner_quotes_page_shows_quotes(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $customer = User::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);
        $customer->assignRole('customer');

        $quote = Quote::create([
            'quote_number' => 'QTE-001',
            'customer_id' => $customer->id,
            'date' => '2026-05-25',
            'status' => 'pending',
            'currency' => 'PHP',
            'subtotal' => 1000,
            'tax' => 120,
            'discount' => 0,
            'total' => 1120,
            'terms' => 'Payment due within 30 days.',
            'notes' => null,
            'employee_id' => null,
        ]);

        QuoteLineItem::create([
            'quote_id' => $quote->id,
            'item_name' => 'Brochure Printing',
            'description' => 'Full color, A5',
            'quantity' => 500,
            'unit_price' => 2,
            'line_total' => 1000,
        ]);

        $response = $this->actingAs($owner)->get(route('owner.quotes'));

        $response->assertOk()
            ->assertSee('QTE-001')
            ->assertSee('John Doe')
            ->assertSee('1,120.00');
    }

    public function test_owner_cannot_view_quotes_page_as_guest(): void
    {
        $response = $this->get(route('owner.quotes'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_owner_cannot_view_quotes_page(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $response = $this->actingAs($customer)->get(route('owner.quotes'));

        $response->assertForbidden();
    }
}
