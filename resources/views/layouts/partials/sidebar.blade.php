<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">
            <div class="sb-sidenav-menu-heading">Core</div>


            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                Dashboard
            </a>


            <div class="sb-sidenav-menu-heading">Management</div>

            <!-- Customers -->
            @can('manage-customer')
                <a class="nav-link {{ request()->routeIs('customers.index', 'projects.show') ? 'active' : '' }}"
                    href="{{ route('customers.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-address-book"></i></div>
                    Customers
                </a>
            @endcan

            <!-- Projects -->
            @can('manage-project')
                <a class="nav-link {{ request()->routeIs('projects.index', 'projects.view') ? 'active' : '' }}"
                    href="{{ route('projects.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-project-diagram"></i></div>
                    Projects
                </a>
            @endcan
            <!-- Purchase Requests -->
            @can('manage-purchase-request')
                <a class="nav-link {{ request()->routeIs('purchase_requests.index') ? 'active' : '' }}"
                    href="{{ route('purchase_requests.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-file-invoice"></i></div>
                    Requests
                </a>
            @endcan
            <!-- Materials -->
            @can('manage-material')
                <a class="nav-link {{ request()->routeIs('materials.index') ? 'active' : '' }}"
                    href="{{ route('materials.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-boxes"></i></div>
                    Materials
                </a>
            @endcan




            <!-- Stock -->
            @can('manage-stock')
                <a class="nav-link {{ request()->routeIs('stocks.index', 'stocks.show') ? 'active' : '' }}"
                    href="{{ route('stocks.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-warehouse"></i></div>
                    Stock
                </a>
            @endcan
            <!-- Stock -->
            @can('manage-seller')
                <a class="nav-link {{ request()->routeIs('sellers.index', 'proforma_images.index') ? 'active' : '' }}"
                    href="{{ route('sellers.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                    Sellers
                </a>
            @endcan

            <!-- Settings -->
            @can('manage-setting')
                <a class="nav-link {{ request()->routeIs('settings.index') ? 'active' : '' }}"
                    href="{{ route('settings.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-sliders-h"></i></div>
                    Settings
                </a>
            @endcan
            @can('manage-service')
                <a class="nav-link {{ request()->routeIs('services.index', 'services.show') ? 'active' : '' }}"
                    href="{{ route('services.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-concierge-bell"></i></div>
                    Services
                </a>
            @endcan



            <!-- User Management -->
            @can('manage-user')
                <a class="nav-link collapsed {{ request()->routeIs(['users.index', 'roles.index', 'permissions.index', 'teams.index']) ? 'active' : '' }}"
                    href="#" data-bs-toggle="collapse" data-bs-target="#collapseUserManagement" aria-expanded="false"
                    aria-controls="collapseUserManagement">
                    <div class="sb-nav-link-icon"><i class="fas fa-users-cog"></i></div>
                    User Management
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
            @endcan
            <div class="collapse" id="collapseUserManagement" aria-labelledby="headingUserManagement"
                data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    @can('manage-user')
                        <a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}"
                            href="{{ route('users.index') }}"><i class="fas fa-user"></i> Users</a>
                    @endcan
                    @can('manage-role')
                        <a class="nav-link {{ request()->routeIs('roles.index') ? 'active' : '' }}"
                            href="{{ route('roles.index') }}"><i class="fas fa-user-tag"></i> Roles</a>
                    @endcan
                    @can('manage-permission')
                        <a class="nav-link {{ request()->routeIs('permissions.index') ? 'active' : '' }}"
                            href="{{ route('permissions.index') }}"><i class="fas fa-user-shield"></i>
                            Permissions</a>
                    @endcan
                    @can('manage-team')
                        <!-- Teams -->
                        <a class="nav-link" href="{{ route('teams.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-users-cog"></i></div>
                            Teams
                        </a>
                    @endcan
                </nav>

            </div>
        </div>
    </div>


</nav>
