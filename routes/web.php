<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CCTVController;
use App\Http\Controllers\SupplierPaymentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchasePaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\PasswordController;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes with active user check
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    $credentials = request()->only('email', 'password');
    
    if (auth()->attempt($credentials)) {
        $user = auth()->user();
        
        // Check if user is active
        if (!$user->is_active) {
            auth()->logout();
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact the administrator.',
            ]);
        }
        
        // Check if user has any roles
        if ($user->roles->count() === 0) {
            auth()->logout();
            return back()->withErrors([
                'email' => 'Your account has no assigned roles. Please contact the administrator.',
            ]);
        }
        
        request()->session()->regenerate();
        return redirect()->intended('/dashboard');
    }
    
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
})->name('login.post');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Authenticated routes (without user.active middleware - login check is sufficient)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('sales', SaleController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::get('pos', [SaleController::class, 'create'])->name('pos.index');
    Route::post('pos', [SaleController::class, 'store'])->name('pos.store');
    Route::get('sales/receipt/{sale}', [SaleController::class, 'printReceipt'])->name('sales.receipt');
    Route::post('sales/{sale}/void', [SaleController::class, 'void'])->name('sales.void');
    
    // Report Routes
    Route::get('reports/daily-sales', [ReportController::class, 'dailySales'])->name('reports.daily');
    Route::get('reports/monthly-sales', [ReportController::class, 'monthlySales'])->name('reports.monthly');
    Route::get('reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
    Route::get('reports/stock-valuation', [ReportController::class, 'stockValuation'])->name('reports.stock-valuation');
    Route::get('reports/top-products', [ReportController::class, 'topProducts'])->name('reports.top-products');
    Route::get('reports/customer-debt', [ReportController::class, 'customerDebt'])->name('reports.customer-debt');
    Route::get('reports/supplier-balance', [ReportController::class, 'supplierBalance'])->name('reports.supplier-balance');
    Route::get('reports/cash-flow', [ReportController::class, 'cashFlow'])->name('reports.cash-flow');
    Route::get('reports/expense', [ReportController::class, 'expenseReport'])->name('reports.expense');
    Route::get('reports/customer-debt/export', [ReportController::class, 'exportCustomerDebt'])->name('reports.customer-debt.export');
    Route::get('reports/supplier-balance/export', [ReportController::class, 'exportSupplierBalance'])->name('reports.supplier-balance.export');
    
    // Supplier Payments Report Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/supplier-payments', [SupplierPaymentController::class, 'index'])->name('supplier-payments');
        Route::get('/supplier-payments/create/{supplierId?}/{purchaseId?}', [SupplierPaymentController::class, 'create'])->name('supplier-payments.create');
        Route::post('/supplier-payments', [SupplierPaymentController::class, 'store'])->name('supplier-payments.store');
        Route::get('/supplier-payments/{payment}', [SupplierPaymentController::class, 'show'])->name('supplier-payments.show');
    });
    
    // Purchase Routes
    Route::resource('purchases', PurchaseController::class);
    Route::post('/purchases/{purchase}/payment', [PurchasePaymentController::class, 'storeFromPurchase'])->name('purchases.payment.store');
    
    // Supplier Purchase Routes
    Route::get('suppliers/{supplier}/purchase', [SupplierController::class, 'createPurchase'])->name('suppliers.purchase.create');
    Route::post('suppliers/{supplier}/purchase', [SupplierController::class, 'storePurchase'])->name('suppliers.purchase.store');
    
    // CCTV Routes
    Route::prefix('cctv')->group(function () {
        Route::get('/', [CCTVController::class, 'index'])->name('cctv.index');
        Route::get('/cameras', [CCTVController::class, 'cameras'])->name('cctv.cameras');
        Route::get('/logs', [CCTVController::class, 'logs'])->name('cctv.logs');
        Route::get('/stream/{cctv}', [CCTVController::class, 'stream'])->name('cctv.stream');
        Route::post('/log-event', [CCTVController::class, 'logEvent'])->name('cctv.log-event');
        Route::get('/{cctv}/edit', [CCTVController::class, 'edit'])->name('cctv.edit');
        Route::put('/{cctv}', [CCTVController::class, 'update'])->name('cctv.update');
        Route::delete('/{cctv}', [CCTVController::class, 'destroy'])->name('cctv.destroy');
    });
    
    // Expense Management Routes
    Route::resource('expenses', App\Http\Controllers\ExpenseController::class);
    
    // Customer Routes
    Route::post('/customers/{customer}/add-credit', [App\Http\Controllers\CustomerController::class, 'addCredit'])->name('customers.add-credit');
    Route::post('/customers/{customer}/pay', [App\Http\Controllers\CustomerController::class, 'makePayment'])->name('customers.pay');
    
    // ==========================================
    // USER MANAGEMENT & PASSWORD ROUTES
    // ==========================================
    
    // User Management Routes (Admin only)
    Route::resource('users', UserController::class)->middleware('can:manage users');
    
    // User Activation/Deactivation Routes
    Route::put('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
    Route::put('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
    
    // Password Change Routes (for logged in user)
    Route::get('/password/change', [PasswordController::class, 'showChangeForm'])->name('password.change');
    Route::post('/password/change', [PasswordController::class, 'update'])->name('password.update');
    
    // Password Reset Routes for Admin (to reset other users' passwords)
    Route::get('/password/reset-users', [PasswordController::class, 'showResetUsers'])
        ->name('password.reset-users')
        ->middleware('can:reset passwords');
    Route::post('/password/reset-user/{user}', [PasswordController::class, 'resetUserPassword'])
        ->name('password.reset-user')
        ->middleware('can:reset passwords');
});

// API Routes for AJAX
Route::get('/api/supplier/{supplierId}/unpaid-purchases', [SupplierPaymentController::class, 'getUnpaidPurchases'])->name('api.supplier.unpaid-purchases');