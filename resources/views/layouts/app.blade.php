<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'JM-EMS') - Joan-Mat Enterprise Management System</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Figtree', sans-serif; -webkit-tap-highlight-color: transparent; }
        
        /* Mobile optimizations */
        @media (max-width: 640px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            button, a {
                min-height: 44px;
                min-width: 44px;
            }
        }
        
        /* Touch-friendly buttons */
        .touch-friendly {
            min-height: 48px;
            padding: 12px 16px;
        }
        
        /* Better scrolling on mobile */
        .overflow-auto {
            -webkit-overflow-scrolling: touch;
        }
        
        /* ========== FIX: Prevent emoji interference with input fields ========== */
        
        /* Ensure inputs have their own independent styling layer */
        input, select, textarea {
            font-family: 'Figtree', sans-serif;
            font-size: 16px !important;
            position: relative;
            z-index: 1;
            background: white !important;
            color: #1f2937 !important;
            letter-spacing: normal !important;
            word-spacing: normal !important;
        }
        
        /* Remove any pseudo-elements that might appear on inputs */
        input::before, input::after,
        select::before, select::after,
        textarea::before, textarea::after {
            display: none !important;
            content: none !important;
        }
        
        /* Fix for search input specific issues */
        input[type="search"] {
            -webkit-appearance: textfield !important;
            appearance: textfield !important;
        }
        
        /* Remove default search cancel button that might conflict */
        input[type="search"]::-webkit-search-cancel-button,
        input[type="search"]::-webkit-search-decoration {
            -webkit-appearance: none !important;
            appearance: none !important;
            display: none !important;
        }
        
        /* Ensure emoji elements don't affect input layout */
        .space-y-2 a, .space-y-2 button,
        .mobile-menu-item, .block.p-3 {
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
        }
        
        /* Make emojis behave like normal text without special positioning */
        .space-y-2 a, .space-y-2 button {
            font-family: 'Figtree', 'Apple Color Emoji', 'Segoe UI Emoji', 'Noto Color Emoji', sans-serif !important;
        }
        
        /* Prevent emoji from creating extra space or line-height issues */
        .space-y-2 a, .space-y-2 button {
            line-height: 1.5 !important;
        }
        
        /* Fix any potential overflow issues */
        input, select, textarea {
            overflow: visible !important;
        }
    </style>
    
    @stack('styles')
        <style>
        /* Simple Dropdown and Input Borders */
        select, input[type="date"], input[type="text"], input[type="number"], input[type="email"], input[type="tel"], input[type="search"] {
            border: 2px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            padding: 0.5rem 1rem !important;
            background-color: white !important;
            transition: all 0.2s ease !important;
        }
        
        select:focus, input:focus {
            border-color: #f59e0b !important;
            outline: none !important;
            box-shadow: 0 0 0 2px #fde68a !important;
        }
        
        select:hover, input:hover {
            border-color: #f59e0b !important;
        }
        
        /* Keep your existing filter section styles but remove the specific class requirements */
        .filter-section {
            background: white;
            border-radius: 1rem;
            border: 1px solid #fef3c7;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .filter-section h3 {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
        }
        
        .filter-section .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .filter-section .reset-btn {
            font-size: 0.875rem;
            color: #d97706;
            transition: color 0.2s;
        }
        
        .filter-section .reset-btn:hover {
            color: #b45309;
        }
        
        .filter-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .filter-actions {
            display: flex;
            gap: 0.5rem;
            align-items: flex-end;
        }
        
        .filter-btn {
            background: linear-gradient(to right, #f59e0b, #ea580c);
            color: white;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }
        
        .filter-btn:hover {
            background: linear-gradient(to right, #d97706, #c2410c);
            transform: scale(1.02);
        }
        
        .clear-btn {
            background-color: #6b7280;
            color: white;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }
        
        .clear-btn:hover {
            background-color: #4b5563;
        }
        
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1rem;
        }
        
        @media (min-width: 768px) {
            .filter-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
        
        /* Additional fix for mobile menu items with emojis */
        .space-y-2 a, .space-y-2 button {
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
        }
        
        /* Ensure emojis display properly without affecting layout */
        .space-y-2 a, .space-y-2 button {
            font-size: 0.875rem !important;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen pb-16 md:pb-0">
        @include('layouts.navigation')
        
        <!-- Page Heading -->
        @hasSection('header')
            <header class="bg-white shadow-sm sticky top-0 z-40">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    @yield('header')
                </div>
            </header>
        @endif
        
        <!-- Page Content -->
        <main class="pb-20 md:pb-8">
            @if(session('success'))
                <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md" role="alert">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    </div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md" role="alert">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    </div>
                </div>
            @endif
            
            @yield('content')
        </main>
        
        <!-- Mobile Bottom Navigation -->
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 md:hidden z-50">
            <div class="flex justify-around items-center py-2">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center p-2 {{ request()->routeIs('dashboard') ? 'text-amber-600' : 'text-gray-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="text-xs mt-1">Home</span>
                </a>
                @can('access pos')
                <a href="{{ route('pos.index') }}" class="flex flex-col items-center p-2 {{ request()->routeIs('pos.*') ? 'text-amber-600' : 'text-gray-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span class="text-xs mt-1">POS</span>
                </a>
                @endcan
                @can('view products')
                <a href="{{ route('products.index') }}" class="flex flex-col items-center p-2 {{ request()->routeIs('products.*') ? 'text-amber-600' : 'text-gray-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span class="text-xs mt-1">Products</span>
                </a>
                @endcan
                <a href="#" onclick="toggleMobileMenu()" class="flex flex-col items-center p-2 text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <span class="text-xs mt-1">Menu</span>
                </a>
            </div>
        </div>
        
        <!-- Mobile Menu Overlay - Emojis preserved but styled to not interfere -->
        <div id="mobileMenu" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden md:hidden" onclick="toggleMobileMenu()">
            <div class="absolute bottom-16 left-0 right-0 bg-white rounded-t-2xl max-h-96 overflow-y-auto" onclick="event.stopPropagation()">
                <div class="p-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Menu</h3>
                        <button onclick="toggleMobileMenu()" class="p-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-2">
                        @can('view sales')
                        <a href="{{ route('sales.index') }}" class="block p-3 rounded-lg hover:bg-amber-50 transition-colors">📊 Sales</a>
                        @endcan
                        @can('view customers')
                        <a href="{{ route('customers.index') }}" class="block p-3 rounded-lg hover:bg-amber-50 transition-colors">👥 Customers</a>
                        @endcan
                        @can('view cctv')
                        <a href="{{ route('cctv.index') }}" class="block p-3 rounded-lg hover:bg-amber-50 transition-colors">📹 CCTV</a>
                        @endcan
                        @can('view reports')
                        <a href="{{ route('reports.daily') }}" class="block p-3 rounded-lg hover:bg-amber-50 transition-colors">📈 Reports</a>
                        @endcan
                        <div class="border-t my-2"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left p-3 rounded-lg hover:bg-red-50 text-red-600 transition-colors">🚪 Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                menu.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }
        
        // Additional fix: Ensure inputs are never affected by emoji styling
        document.addEventListener('DOMContentLoaded', function() {
            // Force inputs to have proper font-family
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.style.fontFamily = "'Figtree', sans-serif";
                input.style.fontWeight = 'normal';
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>