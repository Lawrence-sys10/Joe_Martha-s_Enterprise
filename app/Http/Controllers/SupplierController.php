<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();
        
        if ($request->get('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('email', 'LIKE', "%{$request->search}%")
                  ->orWhere('phone', 'LIKE', "%{$request->search}%");
        }
        
        $suppliers = $query->paginate(20);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Supplier::create($request->all());
        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully!');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('purchases');
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email,' . $supplier->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $supplier->update($request->all());
        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully!');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchases()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete supplier with existing purchase records.');
        }
        
        $supplier->delete();
        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully!');
    }
    public function createPurchase(Supplier $supplier)
    {
        $products = \App\Models\Product::where('is_active', true)->get();
        return view('suppliers.purchase', compact('supplier', 'products'));
    }

    public function storePurchase(Request $request, Supplier $supplier)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
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
            \Illuminate\Support\Facades\DB::beginTransaction();
            
            $invoiceNumber = $this->generatePurchaseInvoiceNumber();
            
            $subtotal = 0;
            $tax = 0;
            
            foreach ($request->items as $item) {
                $itemTotal = $item['quantity'] * $item['unit_price'];
                $itemTax = $itemTotal * 0.125;
                $subtotal += $itemTotal;
                $tax += $itemTax;
            }
            
            $total = $subtotal + $tax;
            
            $purchase = \App\Models\Purchase::create([
                'supplier_id' => $supplier->id,
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
            
            foreach ($request->items as $item) {
                \App\Models\PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
                
                $product = \App\Models\Product::find($item['product_id']);
                $product->stock_quantity += $item['quantity'];
                
                if ($product->stock_quantity > 0) {
                    $newTotalCost = ($product->cost_price * ($product->stock_quantity - $item['quantity'])) + ($item['unit_price'] * $item['quantity']);
                    $product->cost_price = $newTotalCost / $product->stock_quantity;
                }
                $product->save();
            }
            
            $supplier->current_balance += $total;
            $supplier->save();
            
            \Illuminate\Support\Facades\DB::commit();
            
            return redirect()->route('suppliers.show', $supplier)
                ->with('success', 'Purchase order created successfully!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create purchase: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function generatePurchaseInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        $lastPurchase = \App\Models\Purchase::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastPurchase ? intval(substr($lastPurchase->invoice_number, -4)) + 1 : 1;
        
        return "PO-{$year}{$month}-" . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}