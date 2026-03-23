# Step8-AdditionalControllers.ps1
# Run this script to create all additional controllers

Write-Host "Step 8: Creating Additional Controllers..." -ForegroundColor Green

# Create CategoryController
@'
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view categories')->only(['index', 'show']);
        $this->middleware('permission:create categories')->only(['create', 'store']);
        $this->middleware('permission:edit categories')->only(['edit', 'update']);
        $this->middleware('permission:delete categories')->only(['destroy']);
    }

    public function index()
    {
        $categories = Category::with('parent')->paginate(20);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Category::create($request->all());
        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function edit(Category $category)
    {
        $categories = Category::where('is_active', true)
            ->where('id', '!=', $category->id)
            ->get();
        return view('categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $category->update($request->all());
        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete category with associated products.');
        }
        
        $category->delete();
        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully!');
    }
}
'@ | Out-File -FilePath "app\Http\Controllers\CategoryController.php" -Encoding UTF8 -NoNewline

# Create CustomerController
@'
<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view customers')->only(['index', 'show']);
        $this->middleware('permission:create customers')->only(['create', 'store']);
        $this->middleware('permission:edit customers')->only(['edit', 'update']);
        $this->middleware('permission:delete customers')->only(['destroy']);
    }

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
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Customer::create($request->all());
        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully!');
    }

    public function show(Customer $customer)
    {
        $customer->load('sales');
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
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
}
'@ | Out-File -FilePath "app\Http\Controllers\CustomerController.php" -Encoding UTF8 -NoNewline

# Create SupplierController
@'
<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view suppliers')->only(['index', 'show']);
        $this->middleware('permission:create suppliers')->only(['create', 'store']);
        $this->middleware('permission:edit suppliers')->only(['edit', 'update']);
        $this->middleware('permission:delete suppliers')->only(['destroy']);
    }

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
}
'@ | Out-File -FilePath "app\Http\Controllers\SupplierController.php" -Encoding UTF8 -NoNewline

# Create ReportController
@'
<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Services\SalesService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;

class ReportController extends Controller
{
    protected $reportService;
    protected $salesService;
    protected $inventoryService;

    public function __construct(
        ReportService $reportService,
        SalesService $salesService,
        InventoryService $inventoryService
    ) {
        $this->middleware('permission:view reports');
        $this->reportService = $reportService;
        $this->salesService = $salesService;
        $this->inventoryService = $inventoryService;
    }

    public function dailySales(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $sales = $this->salesService->getDailySales($date);
        $summary = $this->salesService->getSalesSummary($date, $date);
        
        return view('reports.daily-sales', compact('sales', 'date', 'summary'));
    }

    public function monthlySales(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $startDate = date('Y-m-01', strtotime($month));
        $endDate = date('Y-m-t', strtotime($month));
        
        $sales = $this->salesService->getSalesReport($startDate, $endDate);
        $summary = $this->salesService->getSalesSummary($startDate, $endDate);
        
        return view('reports.monthly-sales', compact('sales', 'month', 'summary'));
    }

    public function profitLoss(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        
        $profitLoss = $this->reportService->getProfitLoss($startDate, $endDate);
        
        return view('reports.profit-loss', compact('profitLoss', 'startDate', 'endDate'));
    }

    public function stockValuation()
    {
        $products = $this->inventoryService->getStockLevels();
        $totalValue = $this->inventoryService->getStockValue();
        
        return view('reports.stock-valuation', compact('products', 'totalValue'));
    }

    public function topProducts(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $limit = $request->get('limit', 10);
        
        $topProducts = $this->reportService->getTopProducts($startDate, $endDate, $limit);
        
        return view('reports.top-products', compact('topProducts', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        
        return Excel::download(new SalesExport($startDate, $endDate), 'sales-report.xlsx');
    }
}
'@ | Out-File -FilePath "app\Http\Controllers\ReportController.php" -Encoding UTF8 -NoNewline

# Create CCTVController
@'
<?php

namespace App\Http\Controllers;

use App\Models\CCTV;
use App\Models\CCTVLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CCTVController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view cctv')->only(['index', 'cameras', 'logs']);
        $this->middleware('permission:manage cctv')->only(['store', 'update', 'destroy']);
    }

    public function index()
    {
        $cameras = CCTV::all();
        $recentLogs = CCTVLog::with('cctv', 'user')
            ->orderBy('timestamp', 'desc')
            ->limit(20)
            ->get();
            
        return view('cctv.index', compact('cameras', 'recentLogs'));
    }

    public function cameras()
    {
        $cameras = CCTV::paginate(10);
        return view('cctv.cameras', compact('cameras'));
    }

    public function logs(Request $request)
    {
        $query = CCTVLog::with('cctv', 'user');
        
        if ($request->get('cctv_id')) {
            $query->where('cctv_id', $request->cctv_id);
        }
        
        if ($request->get('event_type')) {
            $query->where('event_type', $request->event_type);
        }
        
        if ($request->get('start_date')) {
            $query->whereDate('timestamp', '>=', $request->start_date);
        }
        
        if ($request->get('end_date')) {
            $query->whereDate('timestamp', '<=', $request->end_date);
        }
        
        $logs = $query->orderBy('timestamp', 'desc')->paginate(50);
        $cameras = CCTV::all();
        
        return view('cctv.logs', compact('logs', 'cameras'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'camera_name' => 'required|string|max:255',
            'camera_ip' => 'nullable|ip',
            'camera_location' => 'required|string',
            'stream_url' => 'nullable|url',
            'is_active' => 'boolean',
            'recording_enabled' => 'boolean',
            'motion_detection' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        CCTV::create($request->all());
        return redirect()->route('cctv.cameras')
            ->with('success', 'Camera added successfully!');
    }

    public function update(Request $request, CCTV $cctv)
    {
        $validator = Validator::make($request->all(), [
            'camera_name' => 'required|string|max:255',
            'camera_ip' => 'nullable|ip',
            'camera_location' => 'required|string',
            'stream_url' => 'nullable|url',
            'is_active' => 'boolean',
            'recording_enabled' => 'boolean',
            'motion_detection' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $cctv->update($request->all());
        return redirect()->route('cctv.cameras')
            ->with('success', 'Camera updated successfully!');
    }

    public function destroy(CCTV $cctv)
    {
        $cctv->delete();
        return redirect()->route('cctv.cameras')
            ->with('success', 'Camera deleted successfully!');
    }

    public function stream(CCTV $cctv)
    {
        // This would integrate with actual camera streaming
        // For now, return the stream URL
        return response()->json([
            'stream_url' => $cctv->stream_url,
            'camera_name' => $cctv->camera_name
        ]);
    }

    public function logEvent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cctv_id' => 'required|exists:cctvs,id',
            'event_type' => 'required|string',
            'event_data' => 'nullable|array',
            'screenshot_path' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $log = CCTVLog::create([
            'cctv_id' => $request->cctv_id,
            'event_type' => $request->event_type,
            'event_data' => $request->event_data,
            'timestamp' => now(),
            'user_id' => auth()->id(),
            'screenshot_path' => $request->screenshot_path,
        ]);

        return response()->json(['success' => true, 'log' => $log]);
    }
}
'@ | Out-File -FilePath "app\Http\Controllers\CCTVController.php" -Encoding UTF8 -NoNewline

# Create UserController
@'
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view users')->only(['index', 'show']);
        $this->middleware('permission:create users')->only(['create', 'store']);
        $this->middleware('permission:edit users')->only(['edit', 'update']);
        $this->middleware('permission:delete users')->only(['destroy']);
        $this->middleware('permission:manage roles')->only(['assignRole']);
    }

    public function index(Request $request)
    {
        $query = User::query();
        
        if ($request->get('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('email', 'LIKE', "%{$request->search}%");
        }
        
        $users = $query->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
            'roles' => 'required|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->is_active ?? true,
        ]);

        $user->syncRoles($request->roles);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully!');
    }

    public function show(User $user)
    {
        $user->load('roles');
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
            'roles' => 'required|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->is_active ?? true,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles($request->roles);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account.');
        }
        
        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully!');
    }

    public function assignRole(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|exists:roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->assignRole($request->role);
        return response()->json(['success' => true]);
    }
}
'@ | Out-File -FilePath "app\Http\Controllers\UserController.php" -Encoding UTF8 -NoNewline

Write-Host "Step 8 Complete: Additional Controllers created!" -ForegroundColor Green
Write-Host ""
Write-Host "Created controllers:" -ForegroundColor Yellow
Write-Host "  - CategoryController: Product category management" -ForegroundColor Green
Write-Host "  - CustomerController: Customer CRUD with credit tracking" -ForegroundColor Green
Write-Host "  - SupplierController: Supplier management" -ForegroundColor Green
Write-Host "  - ReportController: All reporting functionality" -ForegroundColor Green
Write-Host "  - CCTVController: Camera monitoring and logs" -ForegroundColor Green
Write-Host "  - UserController: User and role management" -ForegroundColor Green
Write-Host ""
Write-Host "Type 'next' to proceed to Step 9: Blade Views for All Modules" -ForegroundColor Yellow