<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Quote;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService) {}

    public function show(Quote $quote): View|RedirectResponse
    {
        $this->authorizeCustomerPaymentView($quote);

        $quote->load('lineItems', 'serviceJob');

        return view('customer.payment', [
            'quote' => $quote,
            'method' => config('payments.methods.gcash'),
        ]);
    }

    public function store(Request $request, Quote $quote): RedirectResponse
    {
        $this->authorizeCustomerPaymentView($quote);

        $validated = $request->validate([
            'reference_no' => 'required|string|max:100',
            'proof' => 'nullable|image|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $payment = $this->paymentService->submitPayment(
                quote: $quote,
                customer: $request->user(),
                referenceNo: $validated['reference_no'] ?? null,
                proof: $validated['proof'] ?? null,
                notes: $validated['notes'] ?? null,
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['payment' => $e->getMessage()]);
        }

        return redirect()
            ->route('customer.quotes.show', $quote)
            ->with('success', 'Payment submitted. Awaiting owner verification.');
    }

    public function showPayment(Payment $payment): View
    {
        abort_unless($payment->customer_id === auth()->id(), 403);

        $payment->load('quote.lineItems', 'verifier');

        return view('customer.payment-status', [
            'payment' => $payment,
        ]);
    }

    public function ownerIndex(Request $request): View
    {
        $status = $request->query('status', Payment::STATUS_PENDING);

        $payments = Payment::with(['customer', 'quote', 'serviceJob', 'verifier'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->get();

        $counts = [
            'pending' => Payment::where('status', Payment::STATUS_PENDING)->count(),
            'verified' => Payment::where('status', Payment::STATUS_VERIFIED)->count(),
            'rejected' => Payment::where('status', Payment::STATUS_REJECTED)->count(),
        ];

        return view('owner.payments', [
            'payments' => $payments,
            'currentStatus' => $status,
            'counts' => $counts,
        ]);
    }

    public function verify(Request $request, Payment $payment): RedirectResponse
    {
        $this->ensureOwner();

        try {
            $this->paymentService->verify($payment, $request->user());
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['payment' => $e->getMessage()]);
        }

        return redirect()
            ->route('owner.payments', ['status' => Payment::STATUS_PENDING])
            ->with('success', 'Payment verified.');
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $this->ensureOwner();

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        try {
            $this->paymentService->reject($payment, $request->user(), $validated['rejection_reason']);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['payment' => $e->getMessage()]);
        }

        return redirect()
            ->route('owner.payments', ['status' => Payment::STATUS_PENDING])
            ->with('success', 'Payment rejected. Customer has been notified.');
    }

    private function authorizeCustomerPaymentView(Quote $quote): void
    {
        abort_unless($quote->customer_id === auth()->id(), 403);
        abort_unless($quote->status === 'accepted', 404, 'Quote is not awaiting payment.');
        abort_if($quote->isFullyPaid(), 404, 'This quote is already fully paid.');
        abort_if($quote->hasPendingPayment(), 404, 'A payment is already awaiting verification.');
    }

    private function ensureOwner(): void
    {
        abort_unless(auth()->user()?->hasRole('owner'), 403);
    }
}
