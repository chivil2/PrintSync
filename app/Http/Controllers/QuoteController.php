<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteLineItem;
use App\Models\User;
use App\Notifications\QuoteAcceptedNotification;
use App\Notifications\QuoteCancelledNotification;
use App\Notifications\QuoteNegotiationNotification;
use App\Notifications\QuoteRejectedNotification;
use App\Notifications\QuoteSentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuoteController extends Controller
{
    /**
     * Display a listing of quotes.
     */
    public function index(Request $request)
    {
        if (auth()->user()->can('manage_all_quotes')) {
            // Owner: Can see all quotes
            $quotes = Quote::with(['customer', 'employee', 'lineItems'])->latest()->paginate(20);
        } elseif (auth()->user()->can('view_assigned_quotes')) {
            // Employee: Can only see assigned quotes
            $quotes = Quote::where('employee_id', auth()->id())
                ->with(['customer', 'lineItems'])
                ->latest()
                ->paginate(20);
        } elseif (auth()->user()->can('view_own_quotes')) {
            // Customer: Can only see own quotes
            $quotes = Quote::where('customer_id', auth()->id())
                ->with(['employee', 'lineItems'])
                ->latest()
                ->paginate(20);
        } else {
            abort(403, 'Unauthorized access');
        }

        return response()->json($quotes);
    }

    /**
     * Store a newly created quote in storage.
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('manage_all_quotes') && ! auth()->user()->can('request_quotes')) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'quote_number' => 'required|string|unique:quotes',
            'customer_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|string|in:pending,sent,accepted,rejected',
            'currency' => 'required|string|max:3',
            'subtotal' => 'required|numeric',
            'total' => 'required|numeric',
            'notes' => 'nullable|string',
            'employee_id' => 'nullable|exists:users,id',
            'line_items' => 'required|array',
            'line_items.*.item_name' => 'required|string',
            'line_items.*.description' => 'nullable|string',
            'line_items.*.quantity' => 'required|numeric',
            'line_items.*.unit_price' => 'required|numeric',
            'line_items.*.line_total' => 'required|numeric',
        ]);

        $quote = Quote::create([
            'quote_number' => $validated['quote_number'],
            'customer_id' => $validated['customer_id'],
            'date' => $validated['date'],
            'status' => $validated['status'],
            'currency' => $validated['currency'],
            'subtotal' => $validated['subtotal'],
            'total' => $validated['total'],
            'notes' => $validated['notes'] ?? null,
            'employee_id' => $validated['employee_id'] ?? null,
        ]);

        foreach ($validated['line_items'] as $item) {
            QuoteLineItem::create([
                'quote_id' => $quote->id,
                'item_name' => $item['item_name'],
                'description' => $item['description'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'line_total' => $item['line_total'],
            ]);
        }

        return response()->json($quote->load('lineItems'), 201);
    }

    /**
     * Display the specified quote.
     */
    public function show(string $id)
    {
        $quote = Quote::with(['customer', 'employee', 'lineItems'])->findOrFail($id);

        if (auth()->user()->can('manage_all_quotes')) {
            // Owner: Can see any quote
            return response()->json($quote);
        } elseif (auth()->user()->can('view_assigned_quotes') && $quote->employee_id === auth()->id()) {
            // Employee: Can only see assigned quotes
            return response()->json($quote);
        } elseif (auth()->user()->can('view_own_quotes') && $quote->customer_id === auth()->id()) {
            // Customer: Can only see own quotes
            return response()->json($quote);
        } else {
            abort(403, 'Unauthorized access');
        }
    }

    /**
     * Update the specified quote in storage.
     */
    public function update(Request $request, string $id)
    {
        $quote = Quote::findOrFail($id);

        if (auth()->user()->can('manage_all_quotes')) {
            // Owner: Can update any quote
            $validated = $request->validate([
                'status' => 'required|string|in:pending,sent,accepted,rejected',
                'subtotal' => 'required|numeric',
                'total' => 'required|numeric',
                'notes' => 'nullable|string',
                'employee_id' => 'nullable|exists:users,id',
            ]);

            // Validate that employee can only be assigned if quote is approved
            if (isset($validated['employee_id']) && $validated['employee_id'] && $quote->status !== 'accepted') {
                return response()->json(['error' => 'Employee can only be assigned to approved quotes'], 422);
            }

            $quote->update($validated);

            return response()->json($quote->load('lineItems'));
        } elseif (auth()->user()->can('update_quote_status') && $quote->employee_id === auth()->id()) {
            // Employee: Can only update status of assigned quotes
            $validated = $request->validate([
                'status' => 'required|string|in:pending,sent,accepted,rejected',
            ]);

            $quote->update(['status' => $validated['status']]);

            return response()->json($quote->load('lineItems'));
        } else {
            abort(403, 'Unauthorized access');
        }
    }

    /**
     * Remove the specified quote from storage.
     */
    public function destroy(string $id)
    {
        if (! auth()->user()->can('manage_all_quotes')) {
            abort(403, 'Unauthorized access');
        }

        $quote = Quote::findOrFail($id);
        $quote->delete();

        return response()->json(null, 204);
    }

    /**
     * Display customer's quotes.
     */
    public function customerIndex()
    {
        $quotes = Quote::where('customer_id', auth()->id())
            ->whereIn('status', ['sent', 'accepted', 'rejected'])
            ->with(['serviceJob', 'lineItems'])
            ->latest()
            ->get();

        return view('customer.quotes', compact('quotes'));
    }

    /**
     * Display the specified quote for customer.
     */
    public function customerShow(Quote $quote)
    {
        if ($quote->customer_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $quote->load(['serviceJob', 'lineItems']);

        return view('customer.quote-detail', compact('quote'));
    }

    /**
     * Approve the specified quote.
     */
    public function approve(Request $request, Quote $quote)
    {
        if ($quote->customer_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        if ($quote->status !== 'sent') {
            return redirect()->back()->with('error', 'Quote cannot be approved in current status');
        }

        $quote->update([
            'status' => 'accepted',
            'approved_at' => now(),
        ]);

        User::role('owner')->first()?->notify(new QuoteAcceptedNotification($quote));

        return redirect()->route('customer.quotes.show', $quote)
            ->with('success', 'Quote approved successfully');
    }

    /**
     * Reject the specified quote.
     */
    public function reject(Request $request, Quote $quote)
    {
        if ($quote->customer_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        if ($quote->status !== 'sent') {
            return redirect()->back()->with('error', 'Quote cannot be rejected in current status');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $quote->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        User::role('owner')->first()?->notify(new QuoteRejectedNotification($quote));

        return redirect()->route('customer.quotes')
            ->with('success', 'Quote rejected. Owner will be notified.');
    }

    /**
     * Owner approve the specified quote.
     */
    public function ownerApprove(Request $request, Quote $quote)
    {
        if (! auth()->user()->can('manage_all_quotes')) {
            abort(403, 'Unauthorized access');
        }

        if (! in_array($quote->status, ['draft', 'pending'])) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Quote cannot be approved in current status']);
            }

            return redirect()->back()->with('error', 'Quote cannot be approved in current status');
        }

        $quote->update([
            'status' => 'accepted',
            'approved_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Quote approved successfully']);
        }

        return redirect()->route('owner.quotes')->with('success', 'Quote approved successfully');
    }

    /**
     * Owner reject the specified quote.
     */
    public function ownerReject(Request $request, Quote $quote)
    {
        if (! auth()->user()->can('manage_all_quotes')) {
            abort(403, 'Unauthorized access');
        }

        if ($quote->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Quote cannot be rejected in current status']);
        }

        $quote->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => 'Rejected by owner',
        ]);

        return response()->json(['success' => true, 'message' => 'Quote rejected successfully']);
    }

    /**
     * Display owner's quotes.
     */
    public function ownerIndex()
    {
        $quotes = Quote::with(['customer', 'serviceJob', 'lineItems'])
            ->latest()
            ->distinct('quotes.id')
            ->get();

        $topProducts = QuoteLineItem::select('item_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(line_total) as total_revenue'))
            ->whereHas('quote', function ($q) {
                $q->where('status', 'accepted');
            })
            ->groupBy('item_name')
            ->orderBy('total_qty', 'desc')
            ->take(10)
            ->get();

        return view('owner.quotes', compact('quotes', 'topProducts'));
    }

    /**
     * Show the form for editing the specified quote.
     */
    public function ownerView(Quote $quote)
    {
        $quote->load(['customer', 'serviceJob', 'lineItems']);

        return view('owner.quote-view', compact('quote'));
    }

    /**
     * Send the quote to customer.
     */
    public function send(Request $request, Quote $quote)
    {
        if (! in_array($quote->status, ['draft', 'sent'])) {
            return redirect()->back()->with('error', 'Quote cannot be sent in its current status');
        }

        $validated = $request->validate([
            'adjustment' => 'nullable|numeric',
            'notes' => 'nullable|string|max:2000',
        ]);

        $adjustment = (float) ($validated['adjustment'] ?? 0);
        $newTotal = (float) $quote->subtotal + $adjustment;

        $quote->update([
            'adjustment' => $adjustment,
            'total' => $newTotal,
            'notes' => $validated['notes'] ?? null,
            'status' => 'sent',
            'sent_at' => now(),
            'negotiation_adjustment' => null,
            'negotiation_notes' => null,
            'negotiation_status' => null,
        ]);

        $quote->customer->notify(new QuoteSentNotification($quote));

        return redirect()->route('owner.quotes')
            ->with('success', 'Quote sent to customer successfully');
    }

    /**
     * Customer submits a counter-offer (negotiate).
     */
    public function negotiate(Request $request, Quote $quote)
    {
        if ($quote->customer_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        if ($quote->status !== 'sent') {
            return redirect()->back()->with('error', 'Quote cannot be negotiated in its current status');
        }

        $validated = $request->validate([
            'negotiation_adjustment' => 'required|numeric',
            'negotiation_notes' => 'nullable|string|max:2000',
        ]);

        $quote->update([
            'negotiation_adjustment' => $validated['negotiation_adjustment'],
            'negotiation_notes' => $validated['negotiation_notes'] ?? null,
            'negotiation_status' => 'pending',
        ]);

        User::role('owner')->first()?->notify(new QuoteNegotiationNotification($quote));

        return redirect()->route('customer.quotes.show', $quote)
            ->with('success', 'Your counter-offer has been sent to the owner.');
    }

    /**
     * Customer cancels the order.
     */
    public function cancelOrder(Quote $quote)
    {
        if ($quote->customer_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        if ($quote->status !== 'sent') {
            return redirect()->back()->with('error', 'Quote cannot be cancelled in its current status');
        }

        $quote->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => 'Cancelled by customer',
            'negotiation_status' => 'cancelled',
        ]);

        User::role('owner')->first()?->notify(new QuoteCancelledNotification($quote));

        return redirect()->route('customer.quotes')
            ->with('success', 'Order cancelled successfully.');
    }
}
