<?php

namespace Tests\Feature;

use App\Http\Controllers\PrintbuddyController;
use App\Models\PrintingService;
use App\Models\Quote;
use App\Models\QuoteLineItem;
use App\Models\ServiceJob;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintbuddyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_get_quotes_returns_payment_status_for_each_quote(): void
    {
        $customer = $this->makeCustomer();
        $job = $this->makeServiceJobFor($customer);
        $this->makeQuoteFor($job, ['payment_status' => 'unpaid']);
        $this->makeQuoteFor($job, ['payment_status' => 'paid']);

        $payload = $this->controller()->getQuotes()->getData(true);

        $this->assertCount(2, $payload['quotes']);
        $statuses = collect($payload['quotes'])->pluck('payment_status')->all();
        $this->assertEqualsCanonicalizing(['unpaid', 'paid'], $statuses);
    }

    public function test_get_quotes_filters_by_payment_status(): void
    {
        $customer = $this->makeCustomer();
        $job = $this->makeServiceJobFor($customer);
        $unpaid = $this->makeQuoteFor($job, ['payment_status' => 'unpaid']);
        $this->makeQuoteFor($job, ['payment_status' => 'paid']);
        $this->makeQuoteFor($job, ['payment_status' => 'partially_paid']);

        $payload = $this->controller()->getQuotes(['payment_status' => 'unpaid'])->getData(true);

        $this->assertCount(1, $payload['quotes']);
        $this->assertSame($unpaid->id, $payload['quotes'][0]['id']);
        $this->assertSame('unpaid', $payload['quotes'][0]['payment_status']);
    }

    public function test_get_jobs_includes_payment_status_from_linked_quote(): void
    {
        $customer = $this->makeCustomer();
        $job = $this->makeServiceJobFor($customer);
        $this->makeQuoteFor($job, ['payment_status' => 'unpaid']);

        $payload = $this->controller()->getJobs()->getData(true);

        $this->assertCount(1, $payload['jobs']);
        $this->assertArrayHasKey('payment_status', $payload['jobs'][0]);
        $this->assertSame('unpaid', $payload['jobs'][0]['payment_status']);
    }

    public function test_get_jobs_filters_by_payment_status_of_linked_quote(): void
    {
        $customer = $this->makeCustomer();

        $unpaidJob = $this->makeServiceJobFor($customer);
        $this->makeQuoteFor($unpaidJob, ['payment_status' => 'unpaid']);

        $paidJob = $this->makeServiceJobFor($customer);
        $this->makeQuoteFor($paidJob, ['payment_status' => 'paid']);

        $this->makeServiceJobFor($customer);

        $payload = $this->controller()->getJobs(['payment_status' => 'unpaid'])->getData(true);

        $this->assertCount(1, $payload['jobs']);
        $this->assertSame($unpaidJob->id, $payload['jobs'][0]['id']);
        $this->assertSame('unpaid', $payload['jobs'][0]['payment_status']);
        $this->assertNotSame($paidJob->id, $payload['jobs'][0]['id']);
    }

    public function test_get_jobs_returns_null_payment_status_when_no_quote_linked(): void
    {
        $customer = $this->makeCustomer();
        $this->makeServiceJobFor($customer);

        $payload = $this->controller()->getJobs()->getData(true);

        $this->assertCount(1, $payload['jobs']);
        $this->assertNull($payload['jobs'][0]['payment_status']);
    }

    private function controller(): PrintbuddyController
    {
        return new PrintbuddyController;
    }

    private function makeCustomer(): User
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        return $customer;
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

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeQuoteFor(ServiceJob $serviceJob, array $attributes): Quote
    {
        $quote = Quote::create(array_merge([
            'quote_number' => 'QT-'.now()->format('Ymd').'-'.str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT),
            'customer_id' => $serviceJob->customer_id,
            'service_job_id' => $serviceJob->id,
            'date' => now(),
            'status' => 'accepted',
            'currency' => 'PHP',
            'subtotal' => 1000,
            'total' => 1000,
            'payment_status' => 'unpaid',
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
