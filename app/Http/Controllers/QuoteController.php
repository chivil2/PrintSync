<?php

namespace App\Http\Controllers;

use App\Mail\QuoteSent;
use App\Models\Quote;
use App\Models\QuoteLineItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
            'tax' => 'required|numeric',
            'discount' => 'required|numeric',
            'total' => 'required|numeric',
            'terms' => 'nullable|string',
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
            'tax' => $validated['tax'],
            'discount' => $validated['discount'],
            'total' => $validated['total'],
            'terms' => $validated['terms'] ?? null,
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
                'tax' => 'required|numeric',
                'discount' => 'required|numeric',
                'total' => 'required|numeric',
                'terms' => 'nullable|string',
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
     * Update the specified quote.
     */
    public function ownerUpdate(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'subtotal' => 'required|numeric',
            'tax' => 'required|numeric',
            'discount' => 'required|numeric',
            'total' => 'required|numeric',
            'terms' => 'nullable|string',
            'notes' => 'nullable|string',
            'employee_id' => 'nullable|exists:users,id',
            'line_items' => 'required|array',
            'line_items.*.id' => 'nullable|exists:quote_line_items,id',
            'line_items.*.item_name' => 'required|string',
            'line_items.*.description' => 'nullable|string',
            'line_items.*.quantity' => 'required|numeric',
            'line_items.*.unit_price' => 'required|numeric',
            'line_items.*.line_total' => 'required|numeric',
        ]);

        // Validate that employee can only be assigned if quote is approved
        if (isset($validated['employee_id']) && $validated['employee_id'] && $quote->status !== 'accepted') {
            return redirect()->back()->with('error', 'Employee can only be assigned to approved quotes');
        }

        $quote->update([
            'subtotal' => $validated['subtotal'],
            'tax' => $validated['tax'],
            'discount' => $validated['discount'],
            'total' => $validated['total'],
            'terms' => $validated['terms'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'employee_id' => $validated['employee_id'] ?? null,
        ]);

        foreach ($validated['line_items'] as $item) {
            if (isset($item['id'])) {
                QuoteLineItem::where('id', $item['id'])->update([
                    'item_name' => $item['item_name'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total'],
                ]);
            } else {
                QuoteLineItem::create([
                    'quote_id' => $quote->id,
                    'item_name' => $item['item_name'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total'],
                ]);
            }
        }

        return redirect()->route('owner.quotes.view', $quote)
            ->with('success', 'Quote updated successfully');
    }

    /**
     * Send the quote to customer.
     */
    public function send(Quote $quote)
    {
        if ($quote->status !== 'draft') {
            return redirect()->back()->with('error', 'Quote can only be sent from draft status');
        }

        $quote->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        Mail::to($quote->customer->email)->queue(new QuoteSent($quote));

        return redirect()->route('owner.quotes')
            ->with('success', 'Quote sent to customer successfully');
    }
}
