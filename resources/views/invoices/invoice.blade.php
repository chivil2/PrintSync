<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoiceNumber }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #E8743B;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #E8743B;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-number {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .invoice-date {
            color: #666;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .address-box {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th {
            background: #E8743B;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        .table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        .table tr:last-child td {
            border-bottom: none;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
        }
        .total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }
        .total-label {
            margin-right: 20px;
            color: #666;
        }
        .total-value {
            font-weight: bold;
            color: #333;
        }
        .grand-total {
            font-size: 18px;
            color: #E8743B;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">PrintSync</div>
            <div class="invoice-info">
                <div class="invoice-number">Invoice #{{ $invoiceNumber }}</div>
                <div class="invoice-date">Date: {{ $date->format('F d, Y') }}</div>
            </div>
        </div>

        <!-- Bill To -->
        <div class="section">
            <div class="section-title">Bill To</div>
            <div class="address-box">
                <strong>{{ $customer->first_name }} {{ $customer->last_name }}</strong><br>
                @if($customer->email)
                    {{ $customer->email }}<br>
                @endif
                @if($customer->phone)
                    {{ $customer->phone }}
                @endif
            </div>
        </div>

        <!-- Service Details -->
        <div class="section">
            <div class="section-title">Service Details</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $serviceJob->name }}</td>
                        <td>1</td>
                        <td>₱{{ number_format($quote->subtotal, 2) }}</td>
                        <td>₱{{ number_format($quote->subtotal, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="total-section">
            <div class="total-row">
                <span class="total-label">Subtotal:</span>
                <span class="total-value">₱{{ number_format($quote->subtotal, 2) }}</span>
            </div>
            <div class="total-row">
                <span class="total-label grand-total">Total:</span>
                <span class="total-value grand-total">₱{{ number_format($quote->total, 2) }}</span>
            </div>
        </div>

        <!-- Notes -->
        @if($serviceJob->notes)
        <div class="section">
            <div class="section-title">Notes</div>
            <div class="address-box">
                {{ $serviceJob->notes }}
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>This invoice serves as official receipt for completed services.</p>
            <p>For questions, please contact us at support@printsync.com</p>
        </div>
    </div>
</body>
</html>
