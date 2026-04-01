<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->get('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('email', 'LIKE', "%{$request->search}%")
                  ->orWhere('phone', 'LIKE', "%{$request->search}%");
        }

        $customers = $query->paginate(20);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('name')
            ->get();
        
        return view('customers.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string',
            'opening_balance' => 'nullable|numeric|min:0',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'tax_number' => $request->tax_number,
                'current_balance' => $request->opening_balance ?? 0,
                'credit_limit' => $request->credit_limit,
                'notes' => $request->notes,
                'is_active' => $request->is_active ?? true,
            ]);

            $hasCreditItems = false;
            if ($request->has('credit_items') && is_array($request->credit_items)) {
                foreach ($request->credit_items as $item) {
                    if (empty($item['product_id']) || empty($item['quantity']) || $item['quantity'] <= 0) {
                        continue;
                    }
                    
                    $product = Product::find($item['product_id']);
                    if (!$product) {
                        continue;
                    }
                    
                    $unitPrice = !empty($item['unit_price']) ? $item['unit_price'] : $product->unit_price;
                    $total = $unitPrice * $item['quantity'];
                    
                    // Check stock
                    if ($product->stock_quantity < $item['quantity']) {
                        throw new \Exception("Insufficient stock for {$product->name}. Available: {$product->stock_quantity}");
                    }
                    
                    $sale = Sale::create([
                        'invoice_number' => 'CREDIT-' . date('Ymd') . '-' . $customer->id . '-' . rand(100, 999),
                        'customer_id' => $customer->id,
                        'sale_date' => now(),
                        'subtotal' => $total,
                        'tax' => 0,
                        'discount' => 0,
                        'total' => $total,
                        'payment_method' => 'credit',
                        'status' => 'completed',
                        'user_id' => auth()->id(),
                        'payment_status' => 'pending',
                        'paid_amount' => 0,
                        'change_amount' => 0,
                        'notes' => 'Initial credit purchase'
                    ]);
                    
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $unitPrice,
                        'discount' => 0,
                        'total' => $total,
                    ]);
                    
                    $product->stock_quantity -= $item['quantity'];
                    $product->save();
                    
                    $hasCreditItems = true;
                }
            }
            
            // Recalculate customer balance from all credit sales
            $totalCredit = Sale::where('customer_id', $customer->id)
                ->where('payment_method', 'credit')
                ->sum('total');
            
            $totalPaid = Payment::whereHas('sale', function($query) use ($customer) {
                $query->where('customer_id', $customer->id);
            })->sum('amount');
            
            $customer->current_balance = $totalCredit - $totalPaid;
            $customer->save();
            
            DB::commit();
            
            $message = 'Customer created successfully!';
            if ($hasCreditItems) {
                $message .= ' Credit items have been recorded. Balance: GHS ' . number_format($customer->current_balance, 2);
            }
            
            return redirect()->route('customers.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create customer: ' . $e->getMessage())
                ->withInput();
        }
    }

public function show(Customer $customer)
{
    // Refresh customer to get latest data
    $customer->refresh();
    
    // Get all sales and update payment status based on actual paid_amount
    $allSales = $customer->sales;
    foreach ($allSales as $sale) {
        $totalPaid = $sale->payments()->sum('amount');
        $sale->paid_amount = $totalPaid;
        
        if ($totalPaid >= $sale->total) {
            $sale->payment_status = 'paid';
        } elseif ($totalPaid > 0) {
            $sale->payment_status = 'partial';
        } else {
            $sale->payment_status = 'pending';
        }
        $sale->save();
    }
    
    // Get credit sales that are NOT fully paid (pending or partial)
    $creditSales = $customer->sales()
        ->where('payment_method', 'credit')
        ->whereIn('payment_status', ['pending', 'partial'])
        ->with(['items.product', 'payments'])
        ->orderBy('sale_date', 'desc')
        ->get();
    
    // Get all fully paid sales (including credit and cash)
    $paidSales = $customer->sales()
        ->where('payment_status', 'paid')
        ->with(['items.product', 'payments'])
        ->orderBy('sale_date', 'desc')
        ->get();
    
    // Get cash sales (non-credit)
    $cashSales = $customer->sales()
        ->where('payment_method', '!=', 'credit')
        ->where('payment_status', 'paid')
        ->with(['items.product'])
        ->orderBy('sale_date', 'desc')
        ->get();
    
    // Calculate totals for display
    $totalCredit = $customer->sales()
        ->where('payment_method', 'credit')
        ->sum('total');
    
    $totalPaidAll = Payment::whereHas('sale', function($query) use ($customer) {
        $query->where('customer_id', $customer->id);
    })->sum('amount');
    
    $balanceDue = $totalCredit - $totalPaidAll;
    
    // Update customer balance
    $customer->current_balance = $balanceDue;
    $customer->save();
    
    return view('customers.show', compact('customer', 'creditSales', 'paidSales', 'cashSales', 'totalCredit', 'totalPaidAll', 'balanceDue'));
}

    public function edit(Customer $customer)
    {
        $products = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('name')
            ->get();
        
        return view('customers.edit', compact('customer', 'products'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $customer->update($request->all());
        
        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully!');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->sales()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete customer with existing sales records.');
        }

        $customer->delete();
        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully!');
    }

public function makePayment(Request $request, Customer $customer)
{
    // Clear any existing success messages to prevent duplicates
    session()->forget('success');
    
    $request->validate([
        'sale_id' => 'required|exists:sales,id',
        'amount' => 'required|numeric|min:0.01',
        'payment_method' => 'required|in:cash,mobile_money,bank',
        'notes' => 'nullable|string',
    ]);

    $sale = Sale::with('payments')->find($request->sale_id);
    
    // Verify this sale belongs to the customer
    if ($sale->customer_id != $customer->id) {
        return redirect()->back()->with('error', 'This sale does not belong to this customer.');
    }
    
    // Calculate total paid so far
    $totalPaid = $sale->payments()->sum('amount');
    $remaining = $sale->total - $totalPaid;
    
    if ($request->amount > $remaining) {
        return redirect()->back()->with('error', 'Payment amount cannot exceed remaining balance of GHS ' . number_format($remaining, 2));
    }
    
    // Check for duplicate payment in the last 10 seconds
    $recentPayment = Payment::where('sale_id', $sale->id)
        ->where('amount', $request->amount)
        ->where('payment_method', $request->payment_method)
        ->where('created_at', '>=', now()->subSeconds(10))
        ->exists();
    
    if ($recentPayment) {
        return redirect()->back()->with('error', 'Duplicate payment detected. Please wait a moment before trying again.');
    }
    
    DB::beginTransaction();
    
    try {
        $payment = Payment::create([
            'sale_id' => $sale->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_date' => now(),
            'notes' => $request->notes ?? 'Payment for invoice: ' . $sale->invoice_number,
            'user_id' => auth()->id(),
        ]);
        
        $newTotalPaid = $totalPaid + $request->amount;
        $sale->paid_amount = $newTotalPaid;
        
        if ($newTotalPaid >= $sale->total) {
            $sale->payment_status = 'paid';
            $sale->status = 'completed';
        } else {
            $sale->payment_status = 'partial';
            $sale->status = 'completed';
        }
        $sale->save();
        
        $customer->current_balance -= $request->amount;
        $customer->save();
        
        DB::commit();
        
        $status = $sale->payment_status == 'paid' ? 'fully paid' : 'partially paid';
        $newBalance = $customer->current_balance;
        
        // Use flash session that will be cleared after display
        return redirect()->back()->with('success', 
            'Payment of GHS ' . number_format($request->amount, 2) . ' recorded successfully! ' .
            'Sale #' . $sale->invoice_number . ' is now ' . $status . '. ' .
            'Remaining balance: GHS ' . number_format($sale->total - $newTotalPaid, 2) . '. ' .
            'Customer balance: GHS ' . number_format($newBalance, 2)
        );
        
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Failed to process payment: ' . $e->getMessage());
    }
}

public function addCredit(Request $request, Customer $customer)
{
    // Clear any existing success messages
    session()->forget('success');
    
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|numeric|min:1',
        'unit_price' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {
        $product = Product::find($request->product_id);
        
        if (!$product) {
            throw new \Exception('Product not found');
        }
        
        if ($product->stock_quantity < $request->quantity) {
            throw new \Exception('Insufficient stock. Available: ' . $product->stock_quantity);
        }
        
        $total = $request->unit_price * $request->quantity;
        $invoiceNumber = 'CREDIT-' . date('Ymd') . '-' . $customer->id . '-' . rand(100, 999);
        
        $sale = Sale::create([
            'invoice_number' => $invoiceNumber,
            'customer_id' => $customer->id,
            'sale_date' => now(),
            'subtotal' => $total,
            'tax' => 0,
            'discount' => 0,
            'total' => $total,
            'payment_method' => 'credit',
            'status' => 'completed',
            'user_id' => auth()->id(),
            'payment_status' => 'pending',
            'paid_amount' => 0,
            'change_amount' => 0,
            'notes' => 'Additional credit purchase'
        ]);
        
        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'discount' => 0,
            'total' => $total,
        ]);
        
        $product->stock_quantity -= $request->quantity;
        $product->save();
        
        $customer->current_balance += $total;
        $customer->save();
        
        DB::commit();
        
        return redirect()->route('customers.show', $customer)
            ->with('success', 
                'Credit item added successfully! ' .
                'Invoice: ' . $invoiceNumber . ' ' .
                'Amount: GHS ' . number_format($total, 2) . ' ' .
                'Customer new balance: GHS ' . number_format($customer->current_balance, 2)
            );
                
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->with('error', 'Failed to add credit item: ' . $e->getMessage());
    }
}
    
    public function getPaymentHistory(Customer $customer)
    {
        // Get payments through sales relationship
        $payments = Payment::whereHas('sale', function($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })->with('sale')
          ->orderBy('payment_date', 'desc')
          ->get();
            
        return response()->json([
            'success' => true,
            'data' => $payments,
            'total_payments' => $payments->sum('amount')
        ]);
    }
}