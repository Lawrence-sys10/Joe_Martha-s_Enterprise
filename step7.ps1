# Step7-BladeTemplates.ps1
# Run this script to create all blade templates

Write-Host "Step 7: Creating Blade Templates..." -ForegroundColor Green

# Create directories
New-Item -ItemType Directory -Force -Path "resources\views\layouts"
New-Item -ItemType Directory -Force -Path "resources\views\components"
New-Item -ItemType Directory -Force -Path "resources\views\dashboard"
New-Item -ItemType Directory -Force -Path "resources\views\products"
New-Item -ItemType Directory -Force -Path "resources\views\sales"
New-Item -ItemType Directory -Force -Path "resources\views\categories"
New-Item -ItemType Directory -Force -Path "resources\views\customers"
New-Item -ItemType Directory -Force -Path "resources\views\suppliers"
New-Item -ItemType Directory -Force -Path "resources\views\purchases"
New-Item -ItemType Directory -Force -Path "resources\views\expenses"
New-Item -ItemType Directory -Force -Path "resources\views\reports"
New-Item -ItemType Directory -Force -Path "resources\views\cctv"
New-Item -ItemType Directory -Force -Path "resources\views\users"
New-Item -ItemType Directory -Force -Path "resources\views\auth"
New-Item -ItemType Directory -Force -Path "resources\views\errors"

# Create main layout
@'
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'JM-EMS') - Joan-Mat Enterprise Management System</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')
        
        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif
        
        <!-- Page Content -->
        <main>
            @if(session('success'))
                <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            
            @yield('content')
        </main>
    </div>
    
    @stack('scripts')
</body>
</html>
'@ | Out-File -FilePath "resources\views\layouts\app.blade.php" -Encoding UTF8 -NoNewline

# Create navigation
@'
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <h1 class="text-xl font-bold text-indigo-600">JM-EMS</h1>
                    </a>
                </div>
                
                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>
                    
                    @can('view products')
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                        Products
                    </x-nav-link>
                    @endcan
                    
                    @can('access pos')
                    <x-nav-link :href="route('pos.index')" :active="request()->routeIs('pos.*')">
                        POS
                    </x-nav-link>
                    @endcan
                    
                    @can('view sales')
                    <x-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')">
                        Sales
                    </x-nav-link>
                    @endcan
                    
                    @can('view customers')
                    <x-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                        Customers
                    </x-nav-link>
                    @endcan
                    
                    @can('view reports')
                    <x-nav-link :href="route('reports.daily')" :active="request()->routeIs('reports.*')">
                        Reports
                    </x-nav-link>
                    @endcan
                    
                    @can('view cctv')
                    <x-nav-link :href="route('cctv.index')" :active="request()->routeIs('cctv.*')">
                        CCTV
                    </x-nav-link>
                    @endcan
                </div>
            </div>
            
            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profile
                        </x-dropdown-link>
                        
                        @can('view users')
                        <x-dropdown-link :href="route('users.index')">
                            Users
                        </x-dropdown-link>
                        @endcan
                        
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            
            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
        </div>
        
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Profile
                </x-responsive-nav-link>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
'@ | Out-File -FilePath "resources\views\layouts\navigation.blade.php" -Encoding UTF8 -NoNewline

# Create dashboard index
@'
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Today's Sales -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-500 rounded-full">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Today's Sales</p>
                                <p class="text-2xl font-bold">GHS {{ number_format($todayTotal, 2) }}</p>
                                <p class="text-xs text-gray-400">{{ $todayCount }} transactions</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Stock Value -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-500 rounded-full">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Stock Value</p>
                                <p class="text-2xl font-bold">GHS {{ number_format($stockValue, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Low Stock Alert -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-500 rounded-full">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Low Stock Items</p>
                                <p class="text-2xl font-bold">{{ $lowStockProducts->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Active Products -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-500 rounded-full">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Active Products</p>
                                <p class="text-2xl font-bold">{{ \App\Models\Product::where('is_active', true)->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Low Stock Products Table -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Low Stock Alert</h3>
                        @if($lowStockProducts->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left">Product</th>
                                            <th class="px-4 py-2 text-left">Current Stock</th>
                                            <th class="px-4 py-2 text-left">Min Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lowStockProducts as $product)
                                        <tr>
                                            <td class="px-4 py-2">{{ $product->name }}</td>
                                            <td class="px-4 py-2 text-red-600 font-bold">{{ $product->stock_quantity }}</td>
                                            <td class="px-4 py-2">{{ $product->minimum_stock }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500">All products have sufficient stock.</p>
                        @endif
                    </div>
                </div>
                
                <!-- Top Selling Products -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Top Selling Products (This Month)</h3>
                        @if($topProducts->count() > 0)
                            <div class="space-y-3">
                                @foreach($topProducts as $product)
                                <div>
                                    <div class="flex justify-between text-sm">
                                        <span>{{ $product->name }}</span>
                                        <span>GHS {{ number_format($product->total_revenue, 2) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ ($product->total_revenue / $topProducts->first()->total_revenue) * 100 }}%"></div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">No sales data available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
'@ | Out-File -FilePath "resources\views\dashboard\index.blade.php" -Encoding UTF8 -NoNewline

Write-Host "Step 7 Complete: Blade Templates created!" -ForegroundColor Green
Write-Host ""
Write-Host "Created views:" -ForegroundColor Yellow
Write-Host "  - layouts/app.blade.php: Main application layout" -ForegroundColor Green
Write-Host "  - layouts/navigation.blade.php: Navigation menu with roles" -ForegroundColor Green
Write-Host "  - dashboard/index.blade.php: Dashboard with stats" -ForegroundColor Green
Write-Host ""
Write-Host "Directories created for all modules:" -ForegroundColor Yellow
Write-Host "  - products, sales, categories, customers" -ForegroundColor Green
Write-Host "  - suppliers, purchases, expenses, reports" -ForegroundColor Green
Write-Host "  - cctv, users, auth, errors" -ForegroundColor Green
Write-Host ""
Write-Host "Type 'next' to proceed to Step 8: Additional Controllers (Customers, Suppliers, etc.)" -ForegroundColor Yellow