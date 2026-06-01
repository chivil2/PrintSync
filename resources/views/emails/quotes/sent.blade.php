<x-mail::message>
# Hi {{ $customer->first_name }},

Thank you for choosing **PrintSync**! We have prepared a detailed quote for your requested service. Please review the breakdown below.

<div style="margin: 24px 0; padding: 16px 20px; background-color: #F8FAFC; border-radius: 6px; border-left: 4px solid #0369A1;">
    <strong style="color: #0F172A; font-size: 15px;">Quote #{{ $quote->quote_number }}</strong><br>
    <span style="color: #64748B; font-size: 13px;">{{ $quote->date->format('F d, Y') }}</span>
</div>

<x-mail::table>
| Item | Qty | Unit Price | Total |
|:---- |:---:|:----------:|:-----:|
@foreach ($lineItems as $item)
| {{ $item->item_name }} | {{ $item->quantity }} | ₱{{ number_format($item->unit_price, 2) }} | ₱{{ number_format($item->line_total, 2) }} |
@endforeach
</x-mail::table>

<table width="100%" cellpadding="4" cellspacing="0" style="margin-top: 8px;">
    <tr>
        <td align="right" style="color: #64748B; font-size: 14px; padding-right: 16px;">Subtotal:</td>
        <td align="right" width="120" style="color: #334155; font-size: 14px;">₱{{ number_format($quote->subtotal, 2) }}</td>
    </tr>
    <tr>
        <td align="right" style="border-top: 2px solid #0F172A; padding-top: 8px; padding-right: 16px; font-weight: 700; color: #0F172A; font-size: 16px;">Total:</td>
        <td align="right" style="border-top: 2px solid #0F172A; padding-top: 8px; font-weight: 700; color: #0F172A; font-size: 16px;">₱{{ number_format($quote->total, 2) }}</td>
    </tr>
</table>

<x-mail::button :url="route('customer.quotes.show', $quote)" color="primary">
    View & Approve Quote
</x-mail::button>

<p style="color: #64748B; font-size: 14px; margin-top: 24px;">
    If you have any questions about this quote, simply reply to this email or contact our support team. We're happy to help.
</p>

Thanks,<br>
<strong style="color: #0F172A;">PrintSync Team</strong>
</x-mail::message>
