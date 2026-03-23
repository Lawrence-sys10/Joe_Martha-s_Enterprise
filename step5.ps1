# Step5-Controllers.ps1
# Run this script to create all controllers

Write-Host "Step 5: Creating Controllers..." -ForegroundColor Green

# Create Controllers directory
New-Item -ItemType Directory -Force -Path "app\Http\Controllers"

# Create DashboardController
@'
<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Services\SalesService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $productService;
    protected $salesService;
    protected $inventoryService;

    public function __construct(
        ProductService $productService,
        SalesService $salesService,
        InventoryService $inventoryService
    ) {
        $this->productService = $productService;
        $this->salesService = $salesService;
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        // Get today's sales
        $todaySales = $this->salesService->getDailySales(now()->toDateString());
        
        // Get low stock products
        $lowStockProducts = $this->productService->getLowStockProducts();
        
        // Get stock value
        $stockValue = $this->inventoryService->getStockValue();
        
        // Get total sales for today
        $todayTotal = $todaySales->sum('total');
        
        // Get number of sales today
        $todayCount = $todaySales->count();
        
        // Get top selling products this month
        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereMonth('sales.sale_date', now()->month)
            ->whereYear('sales.sale_date', now()->year)
            ->where('sales.status', 'completed')
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();
        
        return view('dashboard.index', compact(
            'todayTotal',
            'todayCount',
            'lowStockProducts',
            'stockValue',
            'topProducts'
        ));
    }
}
'@ | Out-File -FilePath "app\Http\Controllers\DashboardController.php" -Encoding UTF8 -NoNewline

# Create ProductController
@'
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        $this->middleware('permission:view products')->only(['index', 'show']);
        $this->middleware('permission:create products')->only(['create', 'store']);
        $this->middleware('permission:edit products')->only(['edit', 'update']);
        $this->middleware('permission:delete products')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'category_id' => $request->get('category_id'),
            'is_active' => $request->get('is_active'),
            'low_stock' => $request->get('low_stock'),
        ];
        
        $products = $this->productService->getAllProducts(20, $filters);
        $categories = Category::where('is_active', true)->get();
        
        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku',
            'barcode' => 'nullable|string|unique:products,barcode',
            'category_id' => 'nullable|exists:categories,id',
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $product = $this->productService->createProduct($request->all());
            return redirect()->route('products.index')
                ->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create product: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Product $product)
    {
        $product->load('category', 'stockMovements.user');
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $this->productService->updateProduct($product, $request->all());
            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update product: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Product $product)
    {
        try {
            $this->productService->deleteProduct($product);
            return redirect()->route('products.index')
                ->with('success', 'Product deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    public function lowStock()
    {
        $products = $this->productService->getLowStockProducts();
        return view('products.low-stock', compact('products'));
    }

    public function adjustStock(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:add,remove',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $this->productService->updateStock(
                $product,
                $request->quantity,
                $request->type,
                $request->notes
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Stock adjusted successfully!',
                'new_stock' => $product->fresh()->stock_quantity
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function search(Request $request)
    {
        $products = $this->productService->searchProducts($request->get('q', ''), 10);
        return response()->json($products);
    }
}
'@ | Out-File -FilePath "app\Http\Controllers\ProductController.php" -Encoding UTF8 -NoNewline

# Create SaleController
@'
<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Services\SalesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    protected $salesService;

    public function __construct(SalesService $salesService)
    {
        $this->salesService = $salesService;
        $this->middleware('permission:view sales')->only(['index', 'show']);
        $this->middleware('permission:create sales')->only(['create', 'store']);
        $this->middleware('permission:void sales')->only(['void']);
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
        
        return view('sales.index', compact('sales'));
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
            $this->salesService->voidSale($sale, $request->reason);
            return redirect()->route('sales.index')
                ->with('success', 'Sale voided successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to void sale: ' . $e->getMessage());
        }
    }

    public function dailySales()
    {
        $sales = $this->salesService->getDailySales();
        return view('sales.daily', compact('sales'));
    }
}
'@ | Out-File -FilePath "app\Http\Controllers\SaleController.php" -Encoding UTF8 -NoNewline

Write-Host "Step 5 Complete: Controllers created!" -ForegroundColor Green
Write-Host ""
Write-Host "Created controllers:" -ForegroundColor Yellow
Write-Host "  - DashboardController: Main dashboard with stats" -ForegroundColor Green
Write-Host "  - ProductController: Full CRUD + stock management" -ForegroundColor Green
Write-Host "  - SaleController: POS transactions and receipts" -ForegroundColor Green
Write-Host ""
Write-Host "Type 'next' to proceed to Step 6: Routes and Middleware" -ForegroundColor Yellow