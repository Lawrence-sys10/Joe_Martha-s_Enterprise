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
use App\Http\Controllers\PaymentController;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes (simple version)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    $credentials = request()->only('email', 'password');
    
    if (auth()->attempt($credentials)) {
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

// Authenticated routes
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
    Route::get('reports/customer-debt/export', [ReportController::class, 'exportCustomerDebt'])->name('reports.customer-debt.export');
    Route::get('reports/supplier-balance/export', [ReportController::class, 'exportSupplierBalance'])->name('reports.supplier-balance.export');
    
    // Payment Routes
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'printReceipt'])->name('payments.receipt');
    
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
    
    // Purchase Routes
    Route::resource('purchases', PurchaseController::class);
    Route::post('purchases/{purchase}/payment', [PurchaseController::class, 'recordPayment'])->name('purchases.payment');
    
    // Supplier Purchase Routes
    Route::get('suppliers/{supplier}/purchase', [SupplierController::class, 'createPurchase'])->name('suppliers.purchase.create');
    Route::post('suppliers/{supplier}/purchase', [SupplierController::class, 'storePurchase'])->name('suppliers.purchase.store');
    
    // Supplier Payment Routes
    Route::prefix('supplier-payments')->group(function () {
        Route::get('/', [SupplierPaymentController::class, 'index'])->name('supplier-payments.index');
        Route::get('/create/{supplier}', [SupplierPaymentController::class, 'create'])->name('supplier-payments.create');
        Route::post('/', [SupplierPaymentController::class, 'store'])->name('supplier-payments.store');
        Route::get('/{supplierPayment}', [SupplierPaymentController::class, 'show'])->name('supplier-payments.show');
        Route::get('/{supplierPayment}/receipt', [SupplierPaymentController::class, 'printReceipt'])->name('supplier-payments.receipt');
    });
});