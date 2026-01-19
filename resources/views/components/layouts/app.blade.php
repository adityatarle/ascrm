<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AgriChemTech ERP') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Vite -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    
    @livewireStyles
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <i class="fas fa-seedling me-2"></i>
                AgriChemTech
            </div>
            
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="fas fa-home me-2"></i> Dashboard
                </a>
                
                @can('viewAny', \App\Models\Product::class)
                <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                    <i class="fas fa-box me-2"></i> Products
                </a>
                @endcan
                
                @if(auth()->user()->hasRole('admin'))
                <div class="nav-section-title">Masters</div>
                <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                    <i class="fas fa-folder me-2"></i> Categories
                </a>
                <a class="nav-link {{ request()->routeIs('crops.*') ? 'active' : '' }}" href="{{ route('crops.index') }}">
                    <i class="fas fa-seedling me-2"></i> Crops
                </a>
                <a class="nav-link {{ request()->routeIs('banners.*') ? 'active' : '' }}" href="{{ route('banners.index') }}">
                    <i class="fas fa-image me-2"></i> Banners
                </a>
                <a class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}" href="{{ route('units.index') }}">
                    <i class="fas fa-ruler me-2"></i> Units
                </a>
                <a class="nav-link {{ request()->routeIs('discount-slabs.*') ? 'active' : '' }}" href="{{ route('discount-slabs.index') }}">
                    <i class="fas fa-percent me-2"></i> Discount Slabs
                </a>
                <a class="nav-link {{ request()->routeIs('state-wise-product-rates.*') ? 'active' : '' }}" href="{{ route('state-wise-product-rates.index') }}">
                    <i class="fas fa-map-marker-alt me-2"></i> State-wise Product Rates
                </a>
                <a class="nav-link {{ request()->routeIs('states.*') ? 'active' : '' }}" href="{{ route('states.index') }}">
                    <i class="fas fa-map me-2"></i> States
                </a>
                <a class="nav-link {{ request()->routeIs('districts.*') ? 'active' : '' }}" href="{{ route('districts.index') }}">
                    <i class="fas fa-map-marked-alt me-2"></i> Districts
                </a>
                <a class="nav-link {{ request()->routeIs('talukas.*') ? 'active' : '' }}" href="{{ route('talukas.index') }}">
                    <i class="fas fa-map-pin me-2"></i> Talukas
                </a>
                @endif
                
                @if(auth()->user()->hasRole('admin'))
                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <i class="fas fa-user-shield me-2"></i> Users
                </a>
                @endif
                
                @can('viewAny', \App\Models\Dealer::class)
                <a class="nav-link {{ request()->routeIs('dealers.*') ? 'active' : '' }}" href="{{ route('dealers.index') }}">
                    <i class="fas fa-users me-2"></i> Dealers
                </a>
                @endcan
                
                @can('viewAny', \App\Models\Order::class)
                <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                    <i class="fas fa-shopping-cart me-2"></i> Orders
                </a>
                @endcan
                
                @can('viewAny', \App\Models\Dispatch::class)
                <a class="nav-link {{ request()->routeIs('dispatches.*') ? 'active' : '' }}" href="{{ route('dispatches.index') }}">
                    <i class="fas fa-truck me-2"></i> Dispatches
                </a>
                @endcan
                
                @can('view-reports')
                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                    <i class="fas fa-chart-bar me-2"></i> Reports
                </a>
                @endcan
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4 rounded shadow-sm">
                <div class="container-fluid">
                    <button class="btn btn-link d-lg-none" type="button" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <span class="me-3">
                            <strong>{{ auth()->user()->organization->name ?? 'Organization' }}</strong>
                        </span>
                        <div class="dropdown">
                            <button class="btn btn-link dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>
                                {{ auth()->user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main>
                @if(session('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Mobile Sticky Bottom Bar -->
    <div class="sticky-bottom-bar d-lg-none">
        <div class="d-flex justify-content-around">
            <a href="{{ route('dashboard') }}" class="btn btn-link">
                <i class="fas fa-home"></i>
            </a>
            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Order
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-link">
                <i class="fas fa-box"></i>
            </a>
        </div>
    </div>

    @livewireScripts
    
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>
    
    @stack('scripts')
</body>
</html>
