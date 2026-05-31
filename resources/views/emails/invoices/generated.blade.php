<x-mail::message>
# Hi {{ $customer->first_name }},

Great news! Your service has been completed. Please find your invoice attached to this email for your records.

<div style="margin: 24px 0; padding: 16px 20px; background-color: #F8FAFC; border-radius: 6px; border-left: 4px solid #0369A1;">
    <strong style="color: #0F172A; font-size: 15px;">{{ $serviceJob->name }}</strong><br>
    <span style="color: #64748B; font-size: 13px;">Invoice Reference: {{ $quote->quote_number }}</span>
</div>

<x-mail::table>
| Description | Amount |
|:----------- |:------:|
| {{ $serviceJob->name }} | ₱{{ number_format($quote->subtotal, 2) }} |
| Tax | ₱{{ number_format($quote->tax, 2) }} |
@if ((float) $quote->discount > 0)
| Discount | -₱{{ number_format($quote->discount, 2) }} |
@endif
</x-mail::table>

<table width="100%" cellpadding="4" cellspacing="0" style="margin-top: 4px;">
    <tr>
        <td align="right" style="border-top: 2px solid #0F172A; padding-top: 8px; padding-right: 16px; font-weight: 700; color: #0F172A; font-size: 16px;">Total Due:</td>
        <td align="right" width="120" style="border-top: 2px solid #0F172A; padding-top: 8px; font-weight: 700; color: #0F172A; font-size: 16px;">₱{{ number_format($quote->total, 2) }}</td>
    </tr>
</table>

<x-mail::button :url="route('customer.quotes.show', $quote)" color="primary">
    View Invoice Details
</x-mail::button>

<p style="color: #64748B; font-size: 14px; margin-top: 24px;">
    Thank you for your business! If you have any questions regarding this invoice, please don't hesitate to reach out.
</p>

Thanks,<br>
<strong style="color: #0F172A;">PrintSync Team</strong>
</x-mail::message>
