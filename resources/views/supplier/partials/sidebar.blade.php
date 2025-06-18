<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('supplier.dashboard') }}">
        <div class="sidebar-brand-text mx-3">Supplier Panel</div>
    </a>

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('supplier.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('supplier.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('products.index') }}">
            <i class="fas fa-fw fa-box"></i>
            <span>Product</span>
        </a>
    </li>
    

    <!-- Tambahan menu lainnya di sini -->
</ul>
