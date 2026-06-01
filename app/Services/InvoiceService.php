<?php

namespace App\Services;

use App\Models\ServiceJob;
use App\Notifications\InvoiceGeneratedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    public function generateInvoice(ServiceJob $serviceJob): string
    {
        $quote = $serviceJob->quote;
        $customer = $serviceJob->customer;

        $invoiceNumber = 'INV-'.date('Ymd').'-'.str_pad($serviceJob->id, 4, '0', STR_PAD_LEFT);

        $pdf = Pdf::loadView('invoices.invoice', [
            'invoiceNumber' => $invoiceNumber,
            'date' => now(),
            'customer' => $customer,
            'serviceJob' => $serviceJob,
            'quote' => $quote,
        ]);

        $filename = "invoices/{$invoiceNumber}.pdf";
        $pdf->save(storage_path('app/'.$filename));

        $serviceJob->update([
            'invoice_path' => $filename,
        ]);

        $serviceJob->customer->notify(new InvoiceGeneratedNotification($serviceJob));

        return $filename;
    }

    public function downloadInvoice(ServiceJob $serviceJob)
    {
        if (! $serviceJob->invoice_path) {
            abort(404, 'Invoice not found');
        }

        return Storage::download($serviceJob->invoice_path);
    }
}
