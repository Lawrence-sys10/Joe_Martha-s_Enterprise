<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .receipt {
            max-width: 300px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #ddd;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #f59e0b;
        }
        .header p {
            margin: 5px 0;
            font-size: 10px;
            color: #666;
        }
        .info {
            margin-bottom: 15px;
            font-size: 10px;
        }
        .info p {
            margin: 3px 0;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items th, .items td {
            border-bottom: 1px solid #eee;
            padding: 5px 0;
            text-align: left;
        }
        .items th {
            font-weight: bold;
            font-size: 10px;
        }
        .items td {
            font-size: 10px;
        }
        .total {
            text-align: right;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #ddd;
        }
        .total p {
            margin: 3px 0;
            font-size: 10px;
        }
        .total .grand-total {
            font-size: 14px;
            font-weight: bold;
            color: #f59e0b;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #ddd;
            font-size: 9px;
            color: #666;
        }
        .thankyou {
            text-align: center;
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h1>JM-EMS</h1>
            <p>Joan-Mat Enterprise Management System</p>
            <p>Sunyani Technical University</p>
            <p>Tel: 0593001501</p>
        </div>
        
        <div class="info">
            <p><strong>Receipt #:</strong> {{ $sale->invoice_number }}</p>
            <p><strong>Date:</strong> {{ $sale->sale_date->format('Y-m-d H:i:s') }}</p>
            <p><strong>Cashier:</strong> {{ $sale->user->name ?? 'System' }}</p>
            <p><strong>Customer:</strong> {{ $sale->customer->name ?? 'Walk-in Customer' }}</p>
        </div>
        
        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Product' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>GHS {{ number_format($item->unit_price, 2) }}</td>
                    <td>GHS {{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="total">
            <p>Subtotal: GHS {{ number_format($sale->subtotal, 2) }}</p>
            <p>Tax (12.5%): GHS {{ number_format($sale->tax, 2) }}</p>
            @if($sale->discount > 0)
            <p>Discount: -GHS {{ number_format($sale->discount, 2) }}</p>
            @endif
            <p class="grand-total">Total: GHS {{ number_format($sale->total, 2) }}</p>
            <p>Amount Paid: GHS {{ number_format($sale->paid_amount, 2) }}</p>
            <p>Change: GHS {{ number_format($sale->change_amount, 2) }}</p>
        </div>
        
        <div class="footer">
            <p>Payment Method: {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</p>
            <p>Thank you for your business!</p>
            <!--<p>*** This is a computer-generated receipt ***</p>-->
        </div>
        
        <div class="thankyou">
            <p>Thank You!<br>Please come again</p>
        </div>
    </div>
</body>
</html>