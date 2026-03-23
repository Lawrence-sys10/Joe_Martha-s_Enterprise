<nav x-data="{ open: false }" class="bg-gradient-to-r from-yellow-50 via-amber-50 to-orange-50 border-b border-amber-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-4">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-lg">JM</span>
                        </div>
                        <h1 class="text-lg font-bold bg-gradient-to-r from-amber-700 to-orange-700 bg-clip-text text-transparent hidden sm:block">
                            JM-EMS
                        </h1>
                    </a>
                </div>
                
                <!-- Desktop Navigation Links - Compact layout -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-2 py-2 text-sm font-medium text-amber-600 hover:text-amber-800 {{ request()->routeIs('dashboard') ? 'border-b-2 border-amber-500 text-amber-700' : '' }}">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                    
                    @can('view products')
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-2 py-2 text-sm font-medium text-amber-600 hover:text-amber-800 {{ request()->routeIs('products.*') ? 'border-b-2 border-amber-500 text-amber-700' : '' }}">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Products
                    </a>
                    @endcan
                    
                    @can('access pos')
                    <a href="{{ route('pos.index') }}" class="inline-flex items-center px-2 py-2 text-sm font-medium text-amber-600 hover:text-amber-800 {{ request()->routeIs('pos.*') ? 'border-b-2 border-amber-500 text-amber-700' : '' }}">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        POS
                    </a>
                    @endcan
                    
                    @can('view sales')
                    <a href="{{ route('sales.index') }}" class="inline-flex items-center px-2 py-2 text-sm font-medium text-amber-600 hover:text-amber-800 {{ request()->routeIs('sales.*') ? 'border-b-2 border-amber-500 text-amber-700' : '' }}">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Sales
                    </a>
                    @endcan
                    
                    @can('view customers')
                    <a href="{{ route('customers.index') }}" class="inline-flex items-center px-2 py-2 text-sm font-medium text-amber-600 hover:text-amber-800 {{ request()->routeIs('customers.*') ? 'border-b-2 border-amber-500 text-amber-700' : '' }}">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Customers
                    </a>
                    @endcan
                    
                    @can('view suppliers')
                    <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-2 py-2 text-sm font-medium text-amber-600 hover:text-amber-800 {{ request()->routeIs('suppliers.*') ? 'border-b-2 border-amber-500 text-amber-700' : '' }}">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Suppliers
                    </a>
                    @endcan
                    
                    <!-- Reports Dropdown - Compact -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="inline-flex items-center px-2 py-2 text-sm font-medium text-amber-600 hover:text-amber-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Reports
                            <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-52 bg-white rounded-lg shadow-lg py-1 z-50 border border-amber-100">
                            <a href="{{ route('reports.daily') }}" class="block px-4 py-2 text-sm text-amber-700 hover:bg-amber-50">📊 Daily Sales</a>
                            <a href="{{ route('reports.monthly') }}" class="block px-4 py-2 text-sm text-amber-700 hover:bg-amber-50">📈 Monthly Sales</a>
                            <a href="{{ route('reports.profit-loss') }}" class="block px-4 py-2 text-sm text-amber-700 hover:bg-amber-50">💰 Profit & Loss</a>
                            <a href="{{ route('reports.stock-valuation') }}" class="block px-4 py-2 text-sm text-amber-700 hover:bg-amber-50">📦 Stock Valuation</a>
                            <a href="{{ route('reports.top-products') }}" class="block px-4 py-2 text-sm text-amber-700 hover:bg-amber-50">🏆 Top Products</a>
                            <a href="{{ route('reports.customer-debt') }}" class="block px-4 py-2 text-sm text-amber-700 hover:bg-amber-50">👥 Customer Debt</a>
                            <a href="{{ route('reports.supplier-balance') }}" class="block px-4 py-2 text-sm text-amber-700 hover:bg-amber-50">🏭 Supplier Balance</a>
                            <a href="{{ route('reports.cash-flow') }}" class="block px-4 py-2 text-sm text-amber-700 hover:bg-amber-50">💵 Cash Flow</a>
                            <a href="{{ route('payments.index') }}" class="block px-4 py-2 text-sm text-amber-700 hover:bg-amber-50">💳 Payments</a>
                        </div>
                    </div>
                    
                    @can('view cctv')
                    <a href="{{ route('cctv.index') }}" class="inline-flex items-center px-2 py-2 text-sm font-medium text-amber-600 hover:text-amber-800 {{ request()->routeIs('cctv.*') ? 'border-b-2 border-amber-500 text-amber-700' : '' }}">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        CCTV
                        <span class="ml-1 px-1 py-0.5 text-xs bg-red-500 text-white rounded-full animate-pulse">Live</span>
                    </a>
                    @endcan
                </div>
            </div>
            
            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center text-sm font-medium text-amber-700 hover:text-amber-900 bg-amber-100 px-3 py-1.5 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <div class="w-7 h-7 bg-gradient-to-br from-amber-500 to-orange-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-xs font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                            <span class="hidden sm:inline-block text-sm">{{ Auth::user()->name }}</span>
                        </div>
                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 border border-amber-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-amber-700 hover:bg-amber-50">
                                <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Mobile menu button -->
            <div class="flex items-center md:hidden">
                <button @click="open = !open" class="p-2 rounded-md text-amber-600 hover:text-amber-900 hover:bg-amber-100">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile Navigation Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden md:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block pl-3 pr-4 py-2 text-base font-medium text-amber-600 hover:text-amber-800 hover:bg-amber-50">Dashboard</a>
            @can('view products')
            <a href="{{ route('products.index') }}" class="block pl-3 pr-4 py-2 text-base font-medium text-amber-600 hover:text-amber-800 hover:bg-amber-50">Products</a>
            @endcan
            @can('access pos')
            <a href="{{ route('pos.index') }}" class="block pl-3 pr-4 py-2 text-base font-medium text-amber-600 hover:text-amber-800 hover:bg-amber-50">POS</a>
            @endcan
            @can('view sales')
            <a href="{{ route('sales.index') }}" class="block pl-3 pr-4 py-2 text-base font-medium text-amber-600 hover:text-amber-800 hover:bg-amber-50">Sales</a>
            @endcan
            @can('view customers')
            <a href="{{ route('customers.index') }}" class="block pl-3 pr-4 py-2 text-base font-medium text-amber-600 hover:text-amber-800 hover:bg-amber-50">Customers</a>
            @endcan
            @can('view suppliers')
            <a href="{{ route('suppliers.index') }}" class="block pl-3 pr-4 py-2 text-base font-medium text-amber-600 hover:text-amber-800 hover:bg-amber-50">Suppliers</a>
            @endcan
            <a href="#" class="block pl-3 pr-4 py-2 text-base font-medium text-amber-600 hover:text-amber-800 hover:bg-amber-50">Reports</a>
            @can('view cctv')
            <a href="{{ route('cctv.index') }}" class="block pl-3 pr-4 py-2 text-base font-medium text-amber-600 hover:text-amber-800 hover:bg-amber-50">CCTV</a>
            @endcan
            <div class="border-t border-gray-200 my-2"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left pl-3 pr-4 py-2 text-base font-medium text-amber-600 hover:text-amber-800 hover:bg-amber-50">Log Out</button>
            </form>
        </div>
    </div>
</nav>