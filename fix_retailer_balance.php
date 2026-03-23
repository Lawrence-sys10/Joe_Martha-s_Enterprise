<?php
// fix_retailer_balance.php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Supplier;
use App\Models\SupplierPayment;

echo "========================================\n";
echo "Retail Shop - Supplier Balance Fix\n";
echo "========================================\n\n";

// Get the supplier
$supplier = Supplier::find(1);
if (!$supplier) {
    echo "❌ Supplier not found!\n";
    exit;
}

echo "Current Situation:\n";
echo "  Supplier: {$supplier->name}\n";
echo "  Current Balance: GHS " . number_format($supplier->current_balance, 2) . "\n\n";

if ($supplier->current_balance < 0) {
    echo "⚠️  NEGATIVE BALANCE DETECTED!\n";
    echo "This means you recorded a payment without a purchase order first.\n\n";
}

// Show existing payments
$payments = SupplierPayment::where('supplier_id', 1)->get();
if ($payments->count() > 0) {
    echo "Recorded Payments:\n";
    foreach ($payments as $payment) {
        echo "  - {$payment->payment_number}: GHS " . number_format($payment->amount, 2) . "\n";
    }
    echo "\n";
}

echo "========================================\n";
echo "CORRECT WORKFLOW\n";
echo "========================================\n\n";
echo "1. CREATE PURCHASE ORDER when you buy products\n";
echo "   → This INCREASES supplier balance (you owe money)\n\n";
echo "2. RECORD PAYMENT when you pay the supplier\n";
echo "   → This DECREASES supplier balance (paying off debt)\n\n";
echo "3. Balance becomes 0 when fully paid\n\n";

echo "Example:\n";
echo "  • You buy GHS 10,000 goods → Balance: +10,000\n";
echo "  • You pay GHS 5,000 → Balance: +5,000\n";
echo "  • You pay GHS 5,000 → Balance: 0\n\n";

echo "========================================\n";
echo "To fix your balance, type 'reset' to delete the payment and reset to 0: ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);

if (trim($line) == 'reset') {
    SupplierPayment::where('supplier_id', 1)->delete();
    $supplier->current_balance = 0;
    $supplier->save();
    echo "\n✅ Balance reset to GHS 0\n";
    echo "✅ Payment record deleted\n\n";
    echo "Now follow the correct workflow:\n";
    echo "  1. Create a PURCHASE ORDER first\n";
    echo "  2. Then record PAYMENT against that purchase\n";
} else {
    echo "\n❌ No changes made. You can manually edit the supplier balance.\n";
}