<?php

namespace Tests\Feature\Mail;

use App\Mail\InvoiceGenerated;
use App\Mail\QuoteSent;
use App\Models\PrintingService;
use App\Models\Quote;
use App\Models\QuoteLineItem;
use App\Models\ServiceJob;
use App\Models\User;
use App\Services\InvoiceService;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuoteAndInvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_quote_sent_mailable_content(): void
    {
        $customer = User::factory()->create(['first_name' => 'Jane', 'email' => 'jane@example.com']);
        $customer->assignRole('customer');

        $quote = Quote::factory()->create([
            'customer_id' => $customer->id,
            'quote_number' => 'QT-20260531-0001',
            'status' => 'draft',
        ]);

        QuoteLineItem::factory()->create([
            'quote_id' => $quote->id,
            'item_name' => 'Flyer Printing',
            'quantity' => 100,
            'unit_price' => 5,
            'line_total' => 500,
        ]);

        $quote->load('customer', 'lineItems');

        $mailable = new QuoteSent($quote);

        $mailable->assertSeeInHtml($quote->quote_number);
        $mailable->assertSeeInHtml('Jane');
        $mailable->assertSeeInHtml('Flyer Printing');
        $mailable->assertSeeInHtml('500.00');
        $mailable->assertSeeInHtml('PrintSync');
    }

    public function test_quote_sent_is_sent_when_owner_sends_quote(): void
    {
        Mail::fake();

        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $customer = User::factory()->create(['email' => 'jane@example.com']);
        $customer->assignRole('customer');

        $quote = Quote::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($owner)->post(route('owner.quotes.send', $quote));

        $response->assertRedirect(route('owner.quotes'));

        Mail::assertQueued(QuoteSent::class, function (QuoteSent $mail) use ($customer, $quote) {
            return $mail->hasTo($customer->email) &&
                $mail->quote->id === $quote->id;
        });
    }

    public function test_quote_sent_is_not_sent_from_non_draft_status(): void
    {
        Mail::fake();

        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $customer = User::factory()->create(['email' => 'jane@example.com']);
        $customer->assignRole('customer');

        $quote = Quote::factory()->sent()->create([
            'customer_id' => $customer->id,
        ]);

        $response = $this->actingAs($owner)->post(route('owner.quotes.send', $quote));

        $response->assertRedirect();
        Mail::assertNotSent(QuoteSent::class);
    }

    public function test_invoice_generated_mailable_content(): void
    {
        $customer = User::factory()->create(['first_name' => 'Jane', 'email' => 'jane@example.com']);
        $customer->assignRole('customer');

        $service = PrintingService::factory()->create();

        $job = ServiceJob::create([
            'customer_id' => $customer->id,
            'name' => 'Poster Printing',
            'status' => 'completed',
            'type' => 'printing',
            'service_id' => $service->id,
            'service_type' => 'printing_service',
        ]);

        Quote::create([
            'customer_id' => $customer->id,
            'service_job_id' => $job->id,
            'quote_number' => 'QT-20260531-0001',
            'date' => now(),
            'status' => 'accepted',
            'currency' => 'PHP',
            'subtotal' => 1500,
            'tax' => 180,
            'discount' => 0,
            'total' => 1680,
        ]);

        $mailable = new InvoiceGenerated($job);

        $mailable->assertSeeInHtml('Poster Printing');
        $mailable->assertSeeInHtml('1,500.00');
        $mailable->assertSeeInHtml('PrintSync');
    }

    public function test_invoice_generated_sent_via_service(): void
    {
        Mail::fake();

        $customer = User::factory()->create(['email' => 'jane@example.com']);
        $customer->assignRole('customer');

        $service = PrintingService::factory()->create();

        $job = ServiceJob::create([
            'customer_id' => $customer->id,
            'status' => 'completed',
            'type' => 'printing',
            'name' => 'Test Job',
            'service_id' => $service->id,
            'service_type' => 'printing_service',
        ]);

        Quote::create([
            'customer_id' => $customer->id,
            'service_job_id' => $job->id,
            'quote_number' => 'QT-INV-DIRECT',
            'date' => now(),
            'status' => 'accepted',
            'currency' => 'PHP',
            'subtotal' => 500,
            'tax' => 60,
            'discount' => 0,
            'total' => 560,
        ]);

        $invoiceService = new InvoiceService;
        $invoiceService->generateInvoice($job);

        Mail::assertQueued(InvoiceGenerated::class);
    }

    public function test_invoice_generated_after_job_completion(): void
    {
        Mail::fake();

        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $customer = User::factory()->create(['email' => 'jane@example.com']);
        $customer->assignRole('customer');

        $service = PrintingService::factory()->create();

        $job = ServiceJob::create([
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'status' => 'in_progress',
            'request_invoice' => true,
            'type' => 'printing',
            'name' => 'Test Job',
            'service_id' => $service->id,
            'service_type' => 'printing_service',
        ]);

        Quote::create([
            'customer_id' => $customer->id,
            'service_job_id' => $job->id,
            'quote_number' => 'QT-INV-0001',
            'date' => now(),
            'status' => 'accepted',
            'currency' => 'PHP',
            'subtotal' => 500,
            'tax' => 60,
            'discount' => 0,
            'total' => 560,
        ]);

        $this->actingAs($employee)->patch(route('employee.jobs.update', $job), [
            'status' => 'completed',
        ]);

        Mail::assertQueued(InvoiceGenerated::class);
    }

    public function test_invoice_generated_has_pdf_attachment(): void
    {
        $customer = User::factory()->create(['first_name' => 'Jane', 'email' => 'jane@example.com']);
        $customer->assignRole('customer');

        $service = PrintingService::factory()->create();

        $job = ServiceJob::create([
            'customer_id' => $customer->id,
            'name' => 'Banner Printing',
            'status' => 'completed',
            'type' => 'printing',
            'service_id' => $service->id,
            'service_type' => 'printing_service',
        ]);

        Quote::create([
            'customer_id' => $customer->id,
            'service_job_id' => $job->id,
            'quote_number' => 'QT-ATT-0001',
            'date' => now(),
            'status' => 'accepted',
            'currency' => 'PHP',
            'subtotal' => 750,
            'tax' => 90,
            'discount' => 0,
            'total' => 840,
        ]);

        $service = new InvoiceService;
        $service->generateInvoice($job);

        $this->assertNotNull($job->fresh()->invoice_path);

        $path = Storage::path($job->invoice_path);

        $mailable = new InvoiceGenerated($job);

        $mailable->assertHasAttachment(
            Attachment::fromPath($path),
        );
    }
}
