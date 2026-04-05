<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%")
                  ->orWhere('barcode', 'LIKE', "%{$search}%");
            });
        }
        
        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Status filter
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        // Low stock filter
        if ($request->filled('low_stock') && $request->low_stock == 1) {
            $query->whereRaw('stock_quantity <= minimum_stock')
                  ->where('stock_quantity', '>', 0);
        }
        
        $products = $query->orderBy('name')->paginate(12);
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
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'unit' => 'required|string|max:50',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Image validation
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Generate SKU if not provided
            $data = $request->all();
            if (empty($data['sku'])) {
                $data['sku'] = strtoupper(substr($data['name'], 0, 3)) . '-' . strtoupper(Str::random(5));
            }
            
            // Stock quantity is always 0 when creating a product (comes from purchases)
            $data['stock_quantity'] = 0;
            $data['tax_rate'] = 0;
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $image->getClientOriginalName());
                $imagePath = $image->storeAs('products', $imageName, 'public');
                $data['image'] = $imagePath;
            }
            
            $product = $this->productService->createProduct($data);
            
            return redirect()->route('products.index')
                ->with('success', 'Product "' . $product->name . '" created successfully!');
                
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
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'unit' => 'required|string|max:50',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $updateData = $request->except('image');
            $updateData['tax_rate'] = 0;
            
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                
                $image = $request->file('image');
                $imageName = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $image->getClientOriginalName());
                $imagePath = $image->storeAs('products', $imageName, 'public');
                $updateData['image'] = $imagePath;
            }
            
            // Handle image removal
            if ($request->has('remove_image') && $request->remove_image == 1) {
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                $updateData['image'] = null;
            }
            
            $this->productService->updateProduct($product, $updateData);
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
            // Check if product has been sold
            if ($product->saleItems()->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete product that has been sold.');
            }
            
            // Delete product image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            
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

    public function search(Request $request)
    {
        $products = $this->productService->searchProducts($request->get('q', ''), 10);
        
        // Add image URL to response
        $products->transform(function($product) {
            $product->image_url = $product->image ? Storage::url($product->image) : null;
            return $product;
        });
        
        return response()->json($products);
    }
}