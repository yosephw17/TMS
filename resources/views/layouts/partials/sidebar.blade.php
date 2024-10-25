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
            <a class="nav-link {{ request()->routeIs('customers.index') ? 'active' : '' }}"
                href="{{ route('customers.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-address-book"></i></div>
                Customers
            </a>

            <!-- Projects -->
            <a class="nav-link {{ request()->routeIs('projects.index') ? 'active' : '' }}"
                href="{{ route('projects.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-project-diagram"></i></div>
                Projects
            </a>
            <!-- Purchase Requests -->
            <a class="nav-link {{ request()->routeIs('purchase_requests.index') ? 'active' : '' }}"
                href="{{ route('purchase_requests.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-file-invoice"></i></div>
                Requests
            </a>
            <!-- Materials -->
            <a class="nav-link {{ request()->routeIs('materials.index') ? 'active' : '' }}"
                href="{{ route('materials.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-boxes"></i></div>
                Materials
            </a>




            <!-- Stock -->
            <a class="nav-link {{ request()->routeIs('stocks.index') ? 'active' : '' }}"
                href="{{ route('stocks.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-warehouse"></i></div>
                Stock
            </a>

            <!-- Settings -->
            <a class="nav-link {{ request()->routeIs('settings.index') ? 'active' : '' }}"
                href="{{ route('settings.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-sliders-h"></i></div>
                Settings
            </a>
            <a class="nav-link {{ request()->routeIs('services.index') ? 'active' : '' }}"
                href="{{ route('services.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-concierge-bell"></i></div>
                Services
            </a>



            <!-- User Management -->
            <a class="nav-link collapsed {{ request()->routeIs(['users.index', 'roles.index', 'permissions.index']) ? 'active' : '' }}"
                href="#" data-bs-toggle="collapse" data-bs-target="#collapseUserManagement" aria-expanded="false"
                aria-controls="collapseUserManagement">
                <div class="sb-nav-link-icon"><i class="fas fa-users-cog"></i></div>
                User Management
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse" id="collapseUserManagement" aria-labelledby="headingUserManagement"
                data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}"
                        href="{{ route('users.index') }}"><i class="fas fa-user"></i> Users</a>
                    <a class="nav-link {{ request()->routeIs('roles.index') ? 'active' : '' }}"
                        href="{{ route('roles.index') }}"><i class="fas fa-user-tag"></i> Roles</a>
                    <a class="nav-link {{ request()->routeIs('permissions.index') ? 'active' : '' }}"
                        href="{{ route('permissions.index') }}"><i class="fas fa-user-shield"></i>
                        Permissions</a>
                    <!-- Teams -->
                    <a class="nav-link" href="{{ route('teams.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-users-cog"></i></div>
                        Teams
                    </a>
                </nav>

            </div>
        </div>
    </div>


</nav>
