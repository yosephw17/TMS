<!-- Notifications Dropdown -->
<div class="dropdown me-3">
    <button class="btn btn-outline-secondary position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge" id="notificationBadge" style="display: none;">
            0
        </span>
    </button>
    
    <div class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown" style="width: 380px; max-height: 500px;">
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
</div>

<!-- Notification Styles -->
<style>
.notification-dropdown {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    border: none;
    border-radius: 12px;
}

.notification-item {
    padding: 12px 16px;
    border-bottom: 1px solid #f1f3f4;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #e3f2fd;
    border-left: 4px solid #2196f3;
}

.notification-item.unread::before {
    content: '';
    position: absolute;
    top: 50%;
    right: 12px;
    transform: translateY(-50%);
    width: 8px;
    height: 8px;
    background-color: #2196f3;
    border-radius: 50%;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: white;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 4px;
    color: #333;
}

.notification-message {
    font-size: 13px;
    color: #666;
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.notification-time {
    font-size: 11px;
    color: #999;
}

.notification-badge {
    font-size: 10px;
    min-width: 18px;
    height: 18px;
}

@keyframes notificationPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.notification-badge.pulse {
    animation: notificationPulse 1s ease-in-out;
}
</style>

<!-- Notification JavaScript -->
<script>
class NotificationManager {
    constructor() {
        this.dropdown = document.getElementById('notificationDropdown');
        this.badge = document.getElementById('notificationBadge');
        this.list = document.getElementById('notificationsList');
        this.loading = document.getElementById('notificationsLoading');
        this.empty = document.getElementById('notificationsEmpty');
        this.markAllReadBtn = document.getElementById('markAllReadBtn');
        this.refreshBtn = document.getElementById('refreshNotificationsBtn');
        
        this.init();
    }
    
    init() {
        // Load notifications when dropdown is opened
        this.dropdown.addEventListener('shown.bs.dropdown', () => {
            this.loadNotifications();
        });
        
        // Mark all as read
        this.markAllReadBtn.addEventListener('click', () => {
            this.markAllAsRead();
        });
        
        // Refresh notifications
        this.refreshBtn.addEventListener('click', () => {
            this.loadNotifications();
        });
        
        // Initial load
        this.loadNotifications();
        
        // Auto refresh every 30 seconds
        setInterval(() => {
            this.updateBadge();
        }, 30000);
    }
    
    async loadNotifications() {
        try {
            this.showLoading();
            
            const response = await fetch('/api/notifications/dropdown', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) throw new Error('Failed to load notifications');
            
            const data = await response.json();
            this.renderNotifications(data.notifications);
            this.updateBadge(data.unread_count);
            
        } catch (error) {
            console.error('Error loading notifications:', error);
            this.showError();
        }
    }
    
    renderNotifications(notifications) {
        this.hideLoading();
        
        if (notifications.length === 0) {
            this.showEmpty();
            return;
        }
        
        const html = notifications.map(notification => this.createNotificationHTML(notification)).join('');
        this.list.innerHTML = html;
        
        // Add click handlers
        this.list.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', (e) => {
                const id = item.dataset.id;
                const url = item.dataset.url;
                this.markAsRead(id);
                if (url) {
                    window.location.href = url;
                }
            });
        });
    }
    
    createNotificationHTML(notification) {
        const iconColor = this.getIconColor(notification.color);
        const unreadClass = notification.is_read ? '' : 'unread';
        
        return `
            <div class="notification-item ${unreadClass}" data-id="${notification.id}" data-url="${notification.action_url || ''}">
                <div class="d-flex align-items-start">
                    <div class="notification-icon bg-${notification.color} me-3">
                        <i class="${notification.icon}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${notification.title}</div>
                        <div class="notification-message">${notification.message}</div>
                        <div class="notification-time">${notification.time_ago}</div>
                    </div>
                </div>
            </div>
        `;
    }
    
    getIconColor(color) {
        const colors = {
            'primary': '#0d6efd',
            'success': '#198754',
            'warning': '#ffc107',
            'danger': '#dc3545',
            'info': '#0dcaf0'
        };
        return colors[color] || colors.primary;
    }
    
    async markAsRead(id) {
        try {
            const response = await fetch(`/api/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (response.ok) {
                // Update UI
                const item = document.querySelector(`[data-id="${id}"]`);
                if (item) {
                    item.classList.remove('unread');
                }
                this.updateBadge();
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }
    
    async markAllAsRead() {
        try {
            const response = await fetch('/api/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (response.ok) {
                // Update UI
                this.list.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                });
                this.updateBadge(0);
            }
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
        }
    }
    
    async updateBadge(count = null) {
        if (count === null) {
            try {
                const response = await fetch('/api/notifications/unread-count', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    const data = await response.json();
                    count = data.count;
                }
            } catch (error) {
                console.error('Error getting unread count:', error);
                return;
            }
        }
        
        if (count > 0) {
            this.badge.textContent = count > 99 ? '99+' : count;
            this.badge.style.display = 'block';
            this.badge.classList.add('pulse');
            setTimeout(() => this.badge.classList.remove('pulse'), 1000);
        } else {
            this.badge.style.display = 'none';
        }
    }
    
    showLoading() {
        this.loading.classList.remove('d-none');
        this.empty.classList.add('d-none');
        this.list.innerHTML = '';
    }
    
    hideLoading() {
        this.loading.classList.add('d-none');
    }
    
    showEmpty() {
        this.empty.classList.remove('d-none');
    }
    
    showError() {
        this.hideLoading();
        this.list.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-exclamation-triangle text-warning fs-2 mb-3"></i>
                <h6 class="text-muted">Error loading notifications</h6>
                <p class="small text-muted mb-0">Please try again later</p>
            </div>
        `;
    }
    
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new NotificationManager();
});
</script>
