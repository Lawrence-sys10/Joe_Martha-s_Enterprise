# Step6-Routes.ps1
# Run this script to create all web routes and middleware

Write-Host "Step 6: Creating Routes and Middleware..." -ForegroundColor Green

# Create Middleware directory
New-Item -ItemType Directory -Force -Path "app\Http\Middleware"

# Create RoleMiddleware
@'
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }
        
        abort(403, 'Unauthorized access.');
    }
}
'@ | Out-File -FilePath "app\Http\Middleware\RoleMiddleware.php" -Encoding UTF8 -NoNewline

# Create PermissionMiddleware
@'
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->can($permission)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
'@ | Out-File -FilePath "app\Http\Middleware\PermissionMiddleware.php" -Encoding UTF8 -NoNewline

# Update Kernel.php to register middleware
@'
<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'permission' => \App\Http\Middleware\PermissionMiddleware::class,
    ];
}
'@ | Out-File -FilePath "app\Http\Kernel.php" -Encoding UTF8 -NoNewline

# Create web.php routes
@'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CCTVController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    return view('auth.login');
});

// Authentication routes (provided by Laravel Breeze)
require __DIR__.'/auth.php';

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Products routes - accessible by Admin and Attendant
    Route::middleware(['permission:view products'])->group(function () {
        Route::resource('products', ProductController::class);
        Route::get('low-stock', [ProductController::class, 'lowStock'])->name('products.lowStock');
        Route::post('products/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('products.adjustStock');
        Route::get('products/search', [ProductController::class, 'search'])->name('products.search');
    });
    
    // Categories routes
    Route::middleware(['permission:view categories'])->group(function () {
        Route::resource('categories', CategoryController::class);
    });
    
    // Sales routes - accessible by Admin and Attendant
    Route::middleware(['permission:view sales'])->group(function () {
        Route::resource('sales', SaleController::class);
        Route::get('sales/receipt/{sale}', [SaleController::class, 'printReceipt'])->name('sales.receipt');
        Route::post('sales/{sale}/void', [SaleController::class, 'void'])->name('sales.void');
        Route::get('daily-sales', [SaleController::class, 'dailySales'])->name('sales.daily');
    });
    
    // POS routes
    Route::middleware(['permission:access pos'])->group(function () {
        Route::get('pos', [SaleController::class, 'create'])->name('pos.index');
        Route::post('pos', [SaleController::class, 'store'])->name('pos.store');
    });
    
    // Customers routes
    Route::middleware(['permission:view customers'])->group(function () {
        Route::resource('customers', CustomerController::class);
    });
    
    // Suppliers routes - Admin only
    Route::middleware(['permission:view suppliers'])->group(function () {
        Route::resource('suppliers', SupplierController::class);
    });
    
    // Purchases routes - Admin only
    Route::middleware(['permission:view purchases'])->group(function () {
        Route::resource('purchases', PurchaseController::class);
    });
    
    // Expenses routes
    Route::middleware(['permission:view expenses'])->group(function () {
        Route::resource('expenses', ExpenseController::class);
        Route::post('expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
    });
    
    // Reports routes
    Route::middleware(['permission:view reports'])->prefix('reports')->group(function () {
        Route::get('daily-sales', [ReportController::class, 'dailySales'])->name('reports.daily');
        Route::get('monthly-sales', [ReportController::class, 'monthlySales'])->name('reports.monthly');
        Route::get('profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
        Route::get('stock-valuation', [ReportController::class, 'stockValuation'])->name('reports.stock-valuation');
        Route::get('top-products', [ReportController::class, 'topProducts'])->name('reports.top-products');
        Route::get('export', [ReportController::class, 'export'])->name('reports.export');
    });
    
    // Users routes - Admin only
    Route::middleware(['permission:view users'])->prefix('admin')->group(function () {
        Route::resource('users', UserController::class);
    });
    
    // CCTV routes
    Route::middleware(['permission:view cctv'])->prefix('cctv')->group(function () {
        Route::get('/', [CCTVController::class, 'index'])->name('cctv.index');
        Route::get('/cameras', [CCTVController::class, 'cameras'])->name('cctv.cameras');
        Route::get('/logs', [CCTVController::class, 'logs'])->name('cctv.logs');
        Route::post('/cameras/{cctv}/stream', [CCTVController::class, 'stream'])->name('cctv.stream');
    });
});

// API routes for AJAX calls
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('products/search', [ProductController::class, 'search'])->name('api.products.search');
    Route::get('sales/today', [SaleController::class, 'todaySales'])->name('api.sales.today');
});
'@ | Out-File -FilePath "routes/web.php" -Encoding UTF8 -NoNewline

# Create auth.php routes (if not exists)
@'
<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
'@ | Out-File -FilePath "routes/auth.php" -Encoding UTF8 -NoNewline

Write-Host "Step 6 Complete: Routes and Middleware created!" -ForegroundColor Green
Write-Host ""
Write-Host "Created:" -ForegroundColor Yellow
Write-Host "  - RoleMiddleware: Role-based access control" -ForegroundColor Green
Write-Host "  - PermissionMiddleware: Permission-based access control" -ForegroundColor Green
Write-Host "  - Updated Kernel.php with middleware registration" -ForegroundColor Green
Write-Host "  - web.php with all application routes" -ForegroundColor Green
Write-Host "  - auth.php with authentication routes" -ForegroundColor Green
Write-Host ""
Write-Host "Route groups created:" -ForegroundColor Yellow
Write-Host "  - Dashboard: /dashboard" -ForegroundColor Green
Write-Host "  - Products: /products (CRUD + stock management)" -ForegroundColor Green
Write-Host "  - Sales/POS: /sales and /pos" -ForegroundColor Green
Write-Host "  - Reports: /reports/* (daily, monthly, profit-loss)" -ForegroundColor Green
Write-Host "  - CCTV: /cctv/* (cameras and logs)" -ForegroundColor Green
Write-Host "  - Admin: /admin/users (user management)" -ForegroundColor Green
Write-Host ""
Write-Host "Type 'next' to proceed to Step 7: Blade Templates and Views" -ForegroundColor Yellow