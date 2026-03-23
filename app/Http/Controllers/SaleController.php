<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\SalesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    protected $salesService;

    public function __construct(SalesService $salesService)
    {
        $this->salesService = $salesService;
    }

    public function index(Request $request)
    {
        $filters = [
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'customer_id' => $request->get('customer_id'),
            'status' => $request->get('status'),
            'payment_method' => $request->get('payment_method'),
        ];
        
        $sales = $this->salesService->getAllSales(20, $filters);
        
        // Calculate statistics
        $totalSales = Sale::where('status', 'completed')->sum('total');
        $todaySales = Sale::whereDate('created_at', today())->where('status', 'completed')->sum('total');
        $todayCount = Sale::whereDate('created_at', today())->where('status', 'completed')->count();
        $totalTransactions = Sale::count();
        $avgSaleValue = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;
        
        return view('sales.index', compact('sales', 'totalSales', 'todaySales', 'todayCount', 'totalTransactions', 'avgSaleValue'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->get();
        return view('sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method' => 'required|in:cash,mobile_money,credit,bank',
            'discount' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $sale = $this->salesService->createSale($request->all(), $request->items);
            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully!',
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show(Sale $sale)
    {
        $sale->load('customer', 'user', 'items.product', 'payments');
        return view('sales.show', compact('sale'));
    }

    public function printReceipt(Sale $sale)
    {
        return $this->salesService->generateReceipt($sale);
    }
    
    public function void(Request $request, Sale $sale)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            DB::beginTransaction();
            
            // Restore stock for voided sale
            foreach ($sale->items as $item) {
                $product = $item->product;
                $beforeQuantity = $product->stock_quantity;
                $afterQuantity = $beforeQuantity + $item->quantity;
                
                $product->stock_quantity = $afterQuantity;
                $product->save();
                
                // Record stock movement
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => StockMovement::TYPE_RETURN,
                    'quantity' => $item->quantity,
                    'before_quantity' => $beforeQuantity,
                    'after_quantity' => $afterQuantity,
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'notes' => "Void sale #{$sale->invoice_number}: {$request->reason}",
                    'user_id' => auth()->id(),
                ]);
            }
            
            $sale->status = Sale::STATUS_CANCELLED;
            $sale->notes = ($sale->notes ? $sale->notes . "\n" : '') . "Voided: {$request->reason}";
            $sale->save();
            
            DB::commit();
            
            return redirect()->route('sales.index')
                ->with('success', 'Sale voided successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to void sale: ' . $e->getMessage());
        }
    }
}