<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3 d-flex align-items-center" href="{{ route('dashboard') }}">
        Team Up
        <img src="{{ asset('assets/img/logo.png') }}" alt=""
            style="height: 60px; width: auto; margin-right: 20px; margin-top:30px">
    </a>

    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
            class="fas fa-bars"></i></button>
    <!-- Navbar Search-->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">

        <div class="input-group">
            <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..."
                aria-describedby="btnNavbarSearch" disabled />
            <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
        </div>
    </form>
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <!-- Notifications Dropdown -->
        <li class="nav-item dropdown me-2">
            <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger header-notification-badge" 
                      id="headerNotificationBadge" style="display: none; font-size: 0.6rem; min-width: 16px; height: 16px;">
                    0
                </span>
            </a>
            
            <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 380px; max-height: 500px;">
                <!-- Header -->
                <div class="dropdown-header d-flex justify-content-between align-items-center py-3 px-3 border-bottom">
                    <h6 class="mb-0 fw-bold">Notifications</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" id="markAllReadBtn" title="Mark all as read">
                            <i class="fas fa-check-double"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" id="refreshNotificationsBtn" title="Refresh">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Notifications List -->
                <div class="notification-list" id="notificationsList" style="max-height: 400px; overflow-y: auto;">
                    <!-- Loading State -->
                    <div class="text-center py-4" id="notificationsLoading">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="small text-muted mt-2 mb-0">Loading notifications...</p>
                    </div>
                    
                    <!-- Empty State -->
                    <div class="text-center py-4 d-none" id="notificationsEmpty">
                        <i class="fas fa-bell-slash text-muted fs-2 mb-3"></i>
                        <h6 class="text-muted">No notifications</h6>
                        <p class="small text-muted mb-0">You're all caught up!</p>
                    </div>
                    
                    <!-- Notifications will be loaded here -->
                </div>
                
                <!-- Footer -->
                <div class="dropdown-divider"></div>
                <div class="dropdown-item-text text-center">
                    <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i>View All Notifications
                    </a>
                </div>
            </div>
        </li>

        <!-- User Account Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle position-relative" id="navbarDropdown" href="#" role="button"
                data-bs-toggle="dropdown" aria-expanded="false"> 
                <span class="ms-3 me-3" style="font-size: 1.1rem; color: #ffc107;">{{ auth()->user()->name }}</span>
                <i class="fas fa-user fa-fw"></i>
                <!-- Unread notification indicator on user icon -->
                <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger user-notification-indicator" 
                      id="userNotificationIndicator" style="display: none; font-size: 0.5rem; width: 8px; height: 8px;">
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li>
                    <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('notifications.index') }}">
                        <span><i class="fas fa-bell me-2"></i>Notifications</span>
                        <span class="badge bg-primary rounded-pill account-notification-badge" id="accountNotificationBadge" style="display: none;">0</span>
                    </a>
                </li>
                <li><a class="dropdown-item" href="#!"><i class="fas fa-cog me-2"></i>Settings</a></li>
                <li><a class="dropdown-item" href="#!"><i class="fas fa-history me-2"></i>Activity Log</a></li>
                <li>
                    <hr class="dropdown-divider" />
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i>{{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</nav>
