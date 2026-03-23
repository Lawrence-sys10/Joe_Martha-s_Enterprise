<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('supplier', 'user');
        
        if ($request->get('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        
        if ($request->get('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->get('start_date')) {
            $query->whereDate('purchase_date', '>=', $request->start_date);
        }
        
        if ($request->get('end_date')) {
            $query->whereDate('purchase_date', '<=', $request->end_date);
        }
        
        $purchases = $query->orderBy('purchase_date', 'desc')->paginate(20);
        $suppliers = Supplier::where('is_active', true)->get();
        
        return view('purchases.index', compact('purchases', 'suppliers'));
    }

        public function create()
    {
        return redirect()->route('suppliers.index')
            ->with('info', 'Please select a supplier first to create a purchase order.');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();
            
            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber();
            
            // Calculate totals
            $subtotal = 0;
            $tax = 0;
            
            foreach ($request->items as $item) {
                $itemTotal = $item['quantity'] * $item['unit_price'];
                $itemTax = $itemTotal * 0.125; // 12.5% tax
                $subtotal += $itemTotal;
                $tax += $itemTax;
            }
            
            $total = $subtotal + $tax;
            
            // Create purchase
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'invoice_number' => $invoiceNumber,
                'purchase_date' => $request->purchase_date,
                'due_date' => $request->due_date,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => 'pending',
                'payment_status' => 'pending',
                'notes' => $request->notes,
                'user_id' => auth()->id(),
            ]);
            
            // Create purchase items and update stock
            foreach ($request->items as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                    'expiry_date' => $item['expiry_date'] ?? null,
                ]);
                
                // Update product stock
                $product = Product::find($item['product_id']);
                $product->stock_quantity += $item['quantity'];
                
                // Update cost price (weighted average)
                if ($product->stock_quantity > 0) {
                    $newTotalCost = ($product->cost_price * ($product->stock_quantity - $item['quantity'])) + ($item['unit_price'] * $item['quantity']);
                    $product->cost_price = $newTotalCost / $product->stock_quantity;
                }
                $product->save();
            }
            
            // Update supplier balance
            $supplier = Supplier::find($request->supplier_id);
            $supplier->current_balance += $total;
            $supplier->save();
            
            DB::commit();
            
            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase order created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create purchase: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('supplier', 'user', 'items.product');
        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        if ($purchase->status != 'pending') {
            return redirect()->route('purchases.index')
                ->with('error', 'Cannot edit completed or cancelled purchases.');
        }
        
        $suppliers = Supplier::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        if ($purchase->status != 'pending') {
            return redirect()->route('purchases.index')
                ->with('error', 'Cannot update completed or cancelled purchases.');
        }
        
        // Similar to store but for update
        return redirect()->route('purchases.index')
            ->with('info', 'Update functionality coming soon.');
    }

    public function destroy(Purchase $purchase)
    {
        if ($purchase->status != 'pending') {
            return redirect()->back()
                ->with('error', 'Cannot delete completed or cancelled purchases.');
        }
        
        try {
            DB::beginTransaction();
            
            // Restore stock and supplier balance
            foreach ($purchase->items as $item) {
                $product = $item->product;
                $product->stock_quantity -= $item->quantity;
                $product->save();
            }
            
            $supplier = $purchase->supplier;
            $supplier->current_balance -= $purchase->total;
            $supplier->save();
            
            $purchase->delete();
            
            DB::commit();
            return redirect()->route('purchases.index')
                ->with('success', 'Purchase order deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete purchase: ' . $e->getMessage());
        }
    }

    public function complete(Purchase $purchase)
    {
        if ($purchase->status != 'pending') {
            return redirect()->back()
                ->with('error', 'Purchase already completed or cancelled.');
        }
        
        $purchase->status = 'completed';
        $purchase->save();
        
        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Purchase marked as completed!');
    }

    public function recordPayment(Request $request, Purchase $purchase)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,mobile_money,cheque',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            DB::beginTransaction();
            
            // Generate payment number
            $paymentNumber = 'PPMT-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Create payment
            $payment = PurchasePayment::create([
                'purchase_id' => $purchase->id,
                'payment_number' => $paymentNumber,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'user_id' => auth()->id(),
            ]);
            
            // Update supplier balance
            $supplier = $purchase->supplier;
            $supplier->current_balance -= $request->amount;
            $supplier->save();
            
            DB::commit();
            
            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Payment recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }

    public function printOrder(Purchase $purchase)
    {
        $purchase->load('supplier', 'items.product');
        return view('purchases.print', compact('purchase'));
    }

    private function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        $lastPurchase = Purchase::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastPurchase ? intval(substr($lastPurchase->invoice_number, -4)) + 1 : 1;
        
        return "PO-{$year}{$month}-" . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}