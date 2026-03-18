<!-- Main Sidebar Container -->
<aside class="main-sidebar elevation-4" style="background: linear-gradient(180deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);">
    <!-- Brand Logo -->
    <a href="{{route('home')}}" class="brand-link" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
        <img src="{{ asset('images/new-albasha.png') }}" alt="Logo" class="brand-image img-circle elevation-3"
             style="opacity: .9">
        <span class="brand-text font-weight-light" style="color: #e8e8e8; font-size: 1.05em;">{{ config('app.name') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
            <div class="image">
                <img src="{{ auth()->user()->getAvatar() }}" class="img-circle elevation-2" alt="User Image" style="width: 34px; height: 34px;">
            </div>
            <div class="info">
                <a href="#" class="d-block" style="color: #ddd;">{{ auth()->user()->getFullname() }}</a>
                <small style="color: #aaa;">
                    <i class="fas fa-circle text-success" style="font-size: 8px;"></i>
                    {{ ucfirst(auth()->user()->role) }}
                </small>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">

                {{-- Dashboard — Admin & Cashier --}}
                @if(auth()->user()->hasRole('admin', 'cashier'))
                <li class="nav-item">
                    <a href="{{route('home')}}" class="nav-link {{ activeSegment('/') }}" style="border-radius: 8px; margin: 2px 8px; color: #ddd;">
                        <i class="nav-icon fas fa-tachometer-alt" style="color: #667eea;"></i>
                        <p>{{ __('dashboard.title') }}</p>
                    </a>
                </li>
                @endif

                {{-- Products — Admin only (full), others can view --}}
                @if(auth()->user()->isAdmin())
                <li class="nav-item">
                    <a href="{{ route('products.index') }}" class="nav-link {{ activeSegment('products') }}" style="border-radius: 8px; margin: 2px 8px; color: #ddd;">
                        <i class="nav-icon fas fa-th-large" style="color: #e17055;"></i>
                        <p>{{ __('product.title') }}</p>
                    </a>
                </li>
                @endif

                {{-- Cart / POS — Admin & Cashier --}}
                @if(auth()->user()->hasRole('admin', 'cashier'))
                <li class="nav-item">
                    <a href="{{ route('cart.index') }}" class="nav-link {{ activeSegment('cart') }}" style="border-radius: 8px; margin: 2px 8px; color: #ddd;">
                        <i class="nav-icon fas fa-cash-register" style="color: #00b894;"></i>
                        <p>{{ __('cart.title') }}</p>
                    </a>
                </li>
                @endif

                {{-- Orders — All roles --}}
                <li class="nav-item">
                    <a href="{{ route('orders.index') }}" class="nav-link {{ activeSegment('orders') }}" style="border-radius: 8px; margin: 2px 8px; color: #ddd;">
                        <i class="nav-icon fas fa-receipt" style="color: #fdcb6e;"></i>
                        <p>{{ __('order.title') }}</p>
                    </a>
                </li>

                {{-- Settings — Admin only --}}
                @if(auth()->user()->isAdmin())
                <li class="nav-item">
                    <a href="{{ route('settings.index') }}" class="nav-link {{ activeSegment('settings') }}" style="border-radius: 8px; margin: 2px 8px; color: #ddd;">
                        <i class="nav-icon fas fa-cogs" style="color: #a29bfe;"></i>
                        <p>{{ __('settings.title') }}</p>
                    </a>
                </li>

                {{-- User Management — Admin only --}}
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link {{ activeSegment('users') }}" style="border-radius: 8px; margin: 2px 8px; color: #ddd;">
                        <i class="nav-icon fas fa-users" style="color: #6c5ce7;"></i>
                        <p>User Management</p>
                    </a>
                </li>
                @endif

                {{-- Logout --}}
                <li class="nav-item mt-3" style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 10px;">
                    <a href="#" class="nav-link" onclick="document.getElementById('logout-form').submit()" style="border-radius: 8px; margin: 2px 8px; color: #ddd;">
                        <i class="nav-icon fas fa-sign-out-alt" style="color: #ff7675;"></i>
                        <p>{{ __('common.Logout') }}</p>
                        <form action="{{route('logout')}}" method="POST" id="logout-form">
                            @csrf
                        </form>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
