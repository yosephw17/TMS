<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">
            <div class="sb-sidenav-menu-heading">Core</div>
            <a class="nav-link" href="{{ route('dashboard') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                Dashboard
            </a>

            <div class="sb-sidenav-menu-heading">Management</div>
            <!-- Customers -->
            <a class="nav-link" href="{{ route('customers.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                Customers
            </a>

            <!-- Projects -->
            <a class="nav-link" href="{{ route('projects.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-tasks"></i></div>
                Projects
            </a>

            <!-- Materials -->
            <a class="nav-link" href="{{ route('materials.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-cubes"></i></div>
                Materials
            </a>
            <a class="nav-link" href="{{ route('teams.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                Teams
            </a>
            <a class="nav-link" href="{{ route('users.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                Users
            </a>
            <a class="nav-link" href="{{ route('stocks.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                Stock
            </a>
            <a class="nav-link" href="{{ route('settings.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                Settings
            </a>
            <!-- Services -->
            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseServices"
                aria-expanded="false" aria-controls="collapseServices">
                <div class="sb-nav-link-icon"><i class="fas fa-concierge-bell"></i></div>
                Services
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse" id="collapseServices" aria-labelledby="headingServices"
                data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <a class="nav-link" href="{{ route('services.index') }}">All Services</a>
                    <a class="nav-link" href="{{ route('service-details.index') }}">Service Details</a>
                </nav>
            </div>

            <!-- Additional Links -->
            <div class="sb-sidenav-menu-heading">Other</div>
            <a class="nav-link" href="">
                <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                Reports
            </a>



        </div>
    </div>
    <div class="sb-sidenav-footer">
        <div class="small">Logged in as:</div>
        {{ Auth::user()->name }}
    </div>
</nav>
