<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt - {{ $payment->payment_number }}</title>
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
        .payment-details {
            margin-bottom: 15px;
            padding: 10px;
            background: #f9fafb;
            border-radius: 5px;
        }
        .payment-details p {
            margin: 5px 0;
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
        .amount {
            font-size: 18px;
            font-weight: bold;
            color: #059669;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h1>JM-EMS</h1>
            <p>Payment Receipt</p>
            <p>Joan-Mat Enterprise Management System</p>
            <p>Sunyani Technical University</p>
            <p>Tel: 0593001501</p>
        </div>
        
        <div class="info">
            <p><strong>Receipt #:</strong> {{ $payment->payment_number }}</p>
            <p><strong>Date:</strong> {{ $payment->payment_date->format('Y-m-d H:i:s') }}</p>
            <p><strong>Processed by:</strong> {{ $payment->user->name ?? 'System' }}</p>
        </div>
        
        <div class="payment-details">
            <p><strong>Purchase Order:</strong> {{ $payment->purchase->invoice_number }}</p>
            <p><strong>Supplier:</strong> {{ $payment->purchase->supplier->name }}</p>
            <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
            @if($payment->reference_number)
            <p><strong>Reference:</strong> {{ $payment->reference_number }}</p>
            @endif
            @if($payment->notes)
            <p><strong>Notes:</strong> {{ $payment->notes }}</p>
            @endif
        </div>
        
        <div class="payment-details" style="background: #fef3c7;">
            <p><strong>Amount Paid:</strong></p>
            <p class="amount">GHS {{ number_format($payment->amount, 2) }}</p>
        </div>
        
        <div class="footer">
            <p>Payment Status: COMPLETED</p>
            <p>Thank you for your payment!</p>
            <p>*** This is a computer-generated receipt ***</p>
        </div>
        
        <div class="thankyou">
            <p>Thank You!<br>Please keep this receipt for your records</p>
        </div>
    </div>
</body>
</html>