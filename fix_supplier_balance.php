<?php
// fix_supplier_balance.php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Supplier;
use App\Models\SupplierPayment;

echo "========================================\n";
echo "Fixing Supplier Balance\n";
echo "========================================\n\n";

// Get the supplier
$supplier = Supplier::find(1);
if (!$supplier) {
    echo "❌ Supplier not found!\n";
    exit;
}

echo "Supplier: {$supplier->name}\n";
echo "Current Balance: GHS " . number_format($supplier->current_balance, 2) . "\n\n";

// Show existing payments
$payments = SupplierPayment::where('supplier_id', 1)->get();
echo "Existing Payment Records:\n";
if ($payments->count() > 0) {
    foreach ($payments as $payment) {
        echo "  - {$payment->payment_number}: GHS " . number_format($payment->amount, 2) . " ({$payment->payment_method}) - {$payment->created_at}\n";
    }
} else {
    echo "  No payment records found.\n";
}

echo "\n========================================\n";
echo "Do you want to reset the supplier balance to 0?\n";
echo "This will delete all payment records for this supplier.\n";
echo "Type 'yes' to confirm: ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
if (trim($line) == 'yes') {
    // Delete all payments
    SupplierPayment::where('supplier_id', 1)->delete();
    
    // Reset balance
    $supplier->current_balance = 0;
    $supplier->save();
    
    echo "\n✅ Supplier balance reset to GHS 0\n";
    echo "✅ All payment records deleted\n";
} else {
    echo "\n❌ Operation cancelled\n";
}

echo "\n========================================\n";
echo "CORRECT WORKFLOW FOR SUPPLIER PAYMENTS\n";
echo "========================================\n\n";
echo "You are the BUYER/DISTRIBUTOR. You buy products FROM suppliers.\n\n";
echo "Correct Process:\n";
echo "1. CREATE PURCHASE ORDER when you buy products from supplier\n";
echo "   → This INCREASES supplier balance (you owe money)\n\n";
echo "2. RECORD PAYMENT when you pay the supplier\n";
echo "   → This DECREASES supplier balance (paying off debt)\n\n";
echo "3. Balance becomes 0 when fully paid\n\n";
echo "Balance Meaning:\n";
echo "  + Positive = You OWE supplier money (you haven't paid fully)\n";
echo "  - Zero = All paid up\n";
echo "  - Negative = Supplier OWES you money (overpayment/refund)\n\n";
echo "Your negative balance means you overpaid without having a purchase order first.\n";