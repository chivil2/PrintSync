<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteLineItem;
use Illuminate\Http\Request;

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
}
