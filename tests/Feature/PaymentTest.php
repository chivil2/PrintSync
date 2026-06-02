<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\PrintingService;
use App\Models\Quote;
use App\Models\QuoteLineItem;
use App\Models\ServiceJob;
use App\Models\User;
use App\Notifications\PaymentRejectedNotification;
use App\Notifications\PaymentSubmittedNotification;
use App\Notifications\PaymentVerifiedNotification;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_customer_can_submit_payment_for_accepted_quote(): void
    {
        Notification::fake();
        Storage::fake('public');

        [$customer, $owner, $quote] = $this->makeAcceptedQuoteScenario();

        $response = $this->actingAs($customer)->post(
            route('customer.quotes.payments.store', $quote),
            [
                'reference_no' => '1234567890',
                'notes' => 'Paid via GCash app',
            ]
        );

        $response->assertRedirect(route('customer.quotes.show', $quote));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('payments', [
            'quote_id' => $quote->id,
            'customer_id' => $customer->id,
            'method' => 'gcash',
            'amount' => (string) $quote->total,
            'reference_no' => '1234567890',
            'status' => Payment::STATUS_PENDING,
        ]);

        Notification::assertSentTo(
            $owner,
            PaymentSubmittedNotification::class,
        );
    }

    public function test_owner_can_verify_payment_and_quote_is_marked_paid(): void
    {
        Notification::fake();

        [$customer, $owner, $quote] = $this->makeAcceptedQuoteScenario();
        $payment = Payment::factory()->create([
            'quote_id' => $quote->id,
            'service_job_id' => $quote->service_job_id,
            'customer_id' => $customer->id,
            'amount' => $quote->total,
            'status' => Payment::STATUS_PENDING,
        ]);

        $response = $this->actingAs($owner)->post(route('owner.payments.verify', $payment));

        $response->assertRedirect(route('owner.payments', ['status' => Payment::STATUS_PENDING]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => Payment::STATUS_VERIFIED,
            'verified_by' => $owner->id,
        ]);

        $this->assertNotNull($payment->fresh()->verified_at);
        $this->assertEquals('paid', $quote->fresh()->payment_status);

        Notification::assertSentTo($customer, PaymentVerifiedNotification::class);
    }

    public function test_owner_can_reject_payment_with_reason(): void
    {
        Notification::fake();

        [$customer, $owner, $quote] = $this->makeAcceptedQuoteScenario();
        $payment = Payment::factory()->create([
            'quote_id' => $quote->id,
            'service_job_id' => $quote->service_job_id,
            'customer_id' => $customer->id,
            'amount' => $quote->total,
            'status' => Payment::STATUS_PENDING,
        ]);

        $response = $this->actingAs($owner)->post(
            route('owner.payments.reject', $payment),
            ['rejection_reason' => 'Reference number not found in GCash transactions.']
        );

        $response->assertRedirect(route('owner.payments', ['status' => Payment::STATUS_PENDING]));

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => Payment::STATUS_REJECTED,
            'rejection_reason' => 'Reference number not found in GCash transactions.',
        ]);

        $this->assertEquals('unpaid', $quote->fresh()->payment_status);

        Notification::assertSentTo(
            $customer,
            PaymentRejectedNotification::class,
        );
    }

    public function test_customer_cannot_pay_a_draft_quote(): void
    {
        [$customer, $owner, $quote] = $this->makeAcceptedQuoteScenario();
        $quote->update(['status' => 'draft']);

        $response = $this->actingAs($customer)->get(route('customer.quotes.pay', $quote));

        $response->assertNotFound();
    }

    public function test_customer_cannot_pay_an_already_paid_quote(): void
    {
        [$customer, , $quote] = $this->makeAcceptedQuoteScenario();
        $quote->update(['payment_status' => 'paid']);

        $response = $this->actingAs($customer)->get(route('customer.quotes.pay', $quote));

        $response->assertNotFound();
    }

    public function test_customer_cannot_pay_quote_with_pending_payment(): void
    {
        [$customer, , $quote] = $this->makeAcceptedQuoteScenario();
        Payment::factory()->create([
            'quote_id' => $quote->id,
            'service_job_id' => $quote->service_job_id,
            'customer_id' => $customer->id,
            'amount' => $quote->total,
            'status' => Payment::STATUS_PENDING,
        ]);

        $response = $this->actingAs($customer)->get(route('customer.quotes.pay', $quote));

        $response->assertNotFound();
    }

    public function test_dashboard_to_pay_card_includes_only_unpaid_accepted_quotes(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $serviceJobA = $this->makeServiceJobFor($customer);
        $serviceJobB = $this->makeServiceJobFor($customer);
        $serviceJobC = $this->makeServiceJobFor($customer);
        $serviceJobD = $this->makeServiceJobFor($customer);

        $unpaidAccepted = $this->makeQuoteFor($serviceJobA, ['status' => 'accepted', 'payment_status' => 'unpaid', 'total' => 1000]);
        $paidAccepted = $this->makeQuoteFor($serviceJobB, ['status' => 'accepted', 'payment_status' => 'paid', 'total' => 2000]);
        $sentQuote = $this->makeQuoteFor($serviceJobC, ['status' => 'sent', 'payment_status' => 'unpaid', 'total' => 3000]);
        $pendingQuote = $this->makeQuoteFor($serviceJobD, ['status' => 'accepted', 'payment_status' => 'unpaid', 'total' => 4000]);
        Payment::factory()->create([
            'quote_id' => $pendingQuote->id,
            'service_job_id' => $pendingQuote->service_job_id,
            'customer_id' => $customer->id,
            'amount' => 4000,
            'status' => Payment::STATUS_PENDING,
        ]);

        $response = $this->actingAs($customer)->get(route('customer.dashboard'));

        $response->assertOk();
        $response->assertViewIs('customer.dashboard');
        $response->assertViewHas('toPayCount', 1);
        $response->assertViewHas('toPayTotal', 1000.0);
    }

    public function test_owner_can_still_assign_employee_to_unpaid_job(): void
    {
        [$customer, $owner, $quote] = $this->makeAcceptedQuoteScenario();
        $serviceJob = ServiceJob::find($quote->service_job_id);
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $response = $this->actingAs($owner)->postJson(
            route('owner.jobs.assign', $serviceJob),
            ['employee_id' => $employee->id]
        );

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertEquals($employee->id, $serviceJob->fresh()->employee_id);
    }

    public function test_customer_pay_page_uploads_proof_screenshot(): void
    {
        Storage::fake('public');
        [$customer, , $quote] = $this->makeAcceptedQuoteScenario();

        $proof = UploadedFile::fake()->create('proof.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($customer)->post(
            route('customer.quotes.payments.store', $quote),
            [
                'reference_no' => '9999999',
                'proof' => $proof,
            ]
        );

        $response->assertRedirect(route('customer.quotes.show', $quote));

        $payment = Payment::where('quote_id', $quote->id)->first();
        $this->assertNotNull($payment->proof_path);
        Storage::disk('public')->assertExists($payment->proof_path);
    }

    public function test_views_render_for_accepted_quote_with_pending_payment(): void
    {
        [$customer, $owner, $quote] = $this->makeAcceptedQuoteScenario();
        $payment = Payment::factory()->create([
            'quote_id' => $quote->id,
            'service_job_id' => $quote->service_job_id,
            'customer_id' => $customer->id,
            'amount' => $quote->total,
            'status' => Payment::STATUS_PENDING,
        ]);

        $this->actingAs($customer)
            ->get(route('customer.quotes.show', $quote))
            ->assertOk()
            ->assertSeeText('Payment Submitted')
            ->assertSeeText('Awaiting owner verification.');

        $this->actingAs($customer)
            ->get(route('customer.payments.show', $payment))
            ->assertOk()
            ->assertSeeText('Awaiting Verification');

        $this->actingAs($owner)
            ->get(route('owner.payments'))
            ->assertOk()
            ->assertSeeText('Pending')
            ->assertSeeText($quote->quote_number);
    }

    public function test_views_render_for_verified_payment(): void
    {
        [$customer, $owner, $quote] = $this->makeAcceptedQuoteScenario();
        Payment::factory()->create([
            'quote_id' => $quote->id,
            'service_job_id' => $quote->service_job_id,
            'customer_id' => $customer->id,
            'amount' => $quote->total,
            'status' => Payment::STATUS_VERIFIED,
            'verified_at' => now(),
        ]);
        $quote->update(['payment_status' => 'paid']);

        $this->actingAs($customer)
            ->get(route('customer.quotes.show', $quote))
            ->assertOk()
            ->assertSeeText('Paid in Full');
    }

    public function test_reference_no_is_required_to_submit_payment(): void
    {
        [$customer, , $quote] = $this->makeAcceptedQuoteScenario();

        $response = $this->actingAs($customer)->post(
            route('customer.quotes.payments.store', $quote),
            []
        );

        $response->assertSessionHasErrors('reference_no');
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_owner_quotes_page_shows_payment_badges(): void
    {
        [$customer, $owner, $acceptedQuote] = $this->makeAcceptedQuoteScenario();
        $serviceJobPaid = $this->makeServiceJobFor($customer);
        $paidQuote = $this->makeQuoteFor($serviceJobPaid, [
            'customer_id' => $customer->id,
            'status' => 'accepted',
            'payment_status' => 'paid',
            'total' => 2000,
        ]);
        $serviceJobPending = $this->makeServiceJobFor($customer);
        $pendingQuote = $this->makeQuoteFor($serviceJobPending, [
            'customer_id' => $customer->id,
            'status' => 'accepted',
            'payment_status' => 'unpaid',
            'total' => 3000,
        ]);
        Payment::factory()->create([
            'quote_id' => $pendingQuote->id,
            'service_job_id' => $pendingQuote->service_job_id,
            'customer_id' => $customer->id,
            'amount' => 3000,
            'status' => Payment::STATUS_PENDING,
        ]);

        $response = $this->actingAs($owner)->get(route('owner.quotes'));

        $response->assertOk();
        $response->assertSeeText('Unpaid');
        $response->assertSeeText('Pending Verification');
        $response->assertSeeText('Paid');
    }

    /**
     * @return array{0: User, 1: User, 2: Quote}
     */
    private function makeAcceptedQuoteScenario(): array
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $serviceJob = $this->makeServiceJobFor($customer);

        $quote = $this->makeQuoteFor($serviceJob, [
            'customer_id' => $customer->id,
            'status' => 'accepted',
            'payment_status' => 'unpaid',
            'total' => 1500,
            'subtotal' => 1500,
            'approved_at' => now(),
        ]);

        return [$customer, $owner, $quote];
    }

    private function makeServiceJobFor(User $customer): ServiceJob
    {
        $printingService = PrintingService::factory()->create();

        return ServiceJob::create([
            'name' => $printingService->name,
            'description' => $printingService->description,
            'type' => 'printing',
            'customer_id' => $customer->id,
            'service_id' => $printingService->id,
            'service_type' => 'printing_service',
            'status' => 'pending',
        ]);
    }

    private function makeQuoteFor(ServiceJob $serviceJob, array $attributes): Quote
    {
        $quote = Quote::create(array_merge([
            'quote_number' => 'QT-'.now()->format('Ymd').'-'.str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT),
            'customer_id' => $serviceJob->customer_id,
            'service_job_id' => $serviceJob->id,
            'date' => now(),
            'status' => 'draft',
            'currency' => 'PHP',
            'subtotal' => 1000,
            'total' => 1000,
        ], $attributes));

        QuoteLineItem::create([
            'quote_id' => $quote->id,
            'item_name' => 'Sample Item',
            'quantity' => 1,
            'unit_price' => $quote->total,
            'line_total' => $quote->total,
        ]);

        return $quote;
    }
}
