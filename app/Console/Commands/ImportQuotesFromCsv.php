<?php

namespace App\Console\Commands;

use App\Models\Quote;
use App\Models\QuoteLineItem;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('quotes:import {file : Path to CSV file} {--update : Update existing quotes instead of skipping}')]
#[Description('Import quotes from CSV file with deduplication')]
class ImportQuotesFromCsv extends Command
{
    protected $importedCount = 0;
    protected $skippedCount = 0;
    protected $updatedCount = 0;
    protected $errorCount = 0;

    public function handle()
    {
        $filePath = $this->argument('file');
        $updateExisting = $this->option('update');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Starting import from: {$filePath}");
        $this->info("Mode: " . ($updateExisting ? 'Update existing quotes' : 'Skip duplicates'));

        $csvFile = fopen($filePath, 'r');
        if ($csvFile === false) {
            $this->error("Could not open file: {$filePath}");
            return 1;
        }

        $header = fgetcsv($csvFile);
        if ($header === false) {
            $this->error("Could not read CSV header");
            fclose($csvFile);
            return 1;
        }

        $this->info("CSV columns found: " . implode(', ', array_filter($header)));

        DB::beginTransaction();

        try {
            $rowNumber = 1;
            $currentQuote = null;
            $quoteData = [];

            while (($row = fgetcsv($csvFile)) !== false) {
                $rowNumber++;

                try {
                    $rowData = array_combine($header, $row);

                    $quoteNumber = $this->getValue($rowData, 'quote_number') ?? $this->getValue($rowData, 'Quote Number') ?? $this->getValue($rowData, 2);

                    if (empty($quoteNumber)) {
                        $this->warn("Row {$rowNumber}: Missing quote number, skipping");
                        $this->errorCount++;
                        continue;
                    }

                    $existingQuote = Quote::where('quote_number', $quoteNumber)->first();

                    if ($existingQuote) {
                        if ($updateExisting) {
                            $this->updateQuote($existingQuote, $rowData);
                            $this->updatedCount++;
                            $this->line("Updated quote: {$quoteNumber}");
                        } else {
                            $this->line("Skipped duplicate quote: {$quoteNumber}");
                            $this->skippedCount++;
                        }
                        $currentQuote = $existingQuote;
                    } else {
                        $currentQuote = $this->createQuote($rowData);
                        $this->importedCount++;
                        $this->line("Imported quote: {$quoteNumber}");
                    }

                    $this->importLineItem($currentQuote, $rowData);

                } catch (\Exception $e) {
                    $this->error("Row {$rowNumber}: " . $e->getMessage());
                    $this->errorCount++;
                }
            }

            fclose($csvFile);
            DB::commit();

            $this->newLine();
            $this->info("Import completed:");
            $this->line("  Imported: {$this->importedCount}");
            $this->line("  Updated: {$this->updatedCount}");
            $this->line("  Skipped: {$this->skippedCount}");
            $this->line("  Errors: {$this->errorCount}");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($csvFile);
            $this->error("Import failed: " . $e->getMessage());
            return 1;
        }
    }

    private function getValue(array $data, $key)
    {
        return $data[$key] ?? null;
    }

    private function createQuote(array $rowData): Quote
    {
        $quoteNumber = $this->getValue($rowData, 'quote_number') ?? $this->getValue($rowData, 'Quote Number') ?? $this->getValue($rowData, 2);
        $customerId = $this->findOrCreateCustomer($rowData);
        $date = $this->parseDate($this->getValue($rowData, 'date') ?? $this->getValue($rowData, 0));
        $status = $this->getValue($rowData, 'status') ?? $this->getValue($rowData, 3) ?? 'pending';
        $currency = $this->getValue($rowData, 'currency') ?? $this->getValue($rowData, 6) ?? 'PHP';
        $subtotal = (float) ($this->getValue($rowData, 'subtotal') ?? $this->getValue($rowData, 9) ?? 0);
        $tax = (float) ($this->getValue($rowData, 'tax') ?? $this->getValue($rowData, 12) ?? 0);
        $discount = (float) ($this->getValue($rowData, 'discount') ?? $this->getValue($rowData, 13) ?? 0);
        $total = (float) ($this->getValue($rowData, 'total') ?? $this->getValue($rowData, 14) ?? 0);
        $terms = $this->getValue($rowData, 'terms') ?? $this->getValue($rowData, 17);
        $notes = $this->getValue($rowData, 'notes');

        return Quote::create([
            'quote_number' => $quoteNumber,
            'customer_id' => $customerId,
            'date' => $date,
            'status' => $status,
            'currency' => $currency,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
            'terms' => $terms,
            'notes' => $notes,
        ]);
    }

    private function updateQuote(Quote $quote, array $rowData): void
    {
        $quote->update([
            'status' => $this->getValue($rowData, 'status') ?? $this->getValue($rowData, 3) ?? $quote->status,
            'subtotal' => (float) ($this->getValue($rowData, 'subtotal') ?? $this->getValue($rowData, 9) ?? $quote->subtotal),
            'tax' => (float) ($this->getValue($rowData, 'tax') ?? $this->getValue($rowData, 12) ?? $quote->tax),
            'discount' => (float) ($this->getValue($rowData, 'discount') ?? $this->getValue($rowData, 13) ?? $quote->discount),
            'total' => (float) ($this->getValue($rowData, 'total') ?? $this->getValue($rowData, 14) ?? $quote->total),
        ]);

        $quote->lineItems()->delete();
    }

    private function importLineItem(Quote $quote, array $rowData): void
    {
        $itemName = $this->getValue($rowData, 'item_name') ?? $this->getValue($rowData, 19);
        $quantity = (float) ($this->getValue($rowData, 'quantity') ?? $this->getValue($rowData, 20) ?? 1);
        $unitPrice = (float) ($this->getValue($rowData, 'unit_price') ?? $this->getValue($rowData, 21) ?? 0);
        $lineTotal = (float) ($this->getValue($rowData, 'line_total') ?? $this->getValue($rowData, 22) ?? ($quantity * $unitPrice));

        if (empty($itemName)) {
            return;
        }

        QuoteLineItem::create([
            'quote_id' => $quote->id,
            'item_name' => $itemName,
            'description' => $this->getValue($rowData, 'description'),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'line_total' => $lineTotal,
        ]);
    }

    private function findOrCreateCustomer(array $rowData): int
    {
        $customerId = $this->getValue($rowData, 'customer_id') ?? $this->getValue($rowData, 2);

        if ($customerId && User::where('id', $customerId)->exists()) {
            return (int) $customerId;
        }

        $customerEmail = $this->getValue($rowData, 'customer_email');

        if ($customerEmail) {
            $customer = User::where('email', $customerEmail)->first();
            if ($customer) {
                return $customer->id;
            }
        }

        $customerName = $this->getValue($rowData, 'customer_name') ?? 'Walk-in Customer';

        $customer = User::firstOrCreate(
            ['email' => 'customer_' . uniqid() . '@temp.local'],
            [
                'first_name' => explode(' ', $customerName)[0] ?? 'Customer',
                'last_name' => explode(' ', $customerName)[1] ?? '',
                'password' => bcrypt('temp_password'),
            ]
        );

        return $customer->id;
    }

    private function parseDate($date): string
    {
        if (empty($date)) {
            return now()->toDateString();
        }

        try {
            return \Carbon\Carbon::parse($date)->toDateString();
        } catch (\Exception $e) {
            return now()->toDateString();
        }
    }
}
