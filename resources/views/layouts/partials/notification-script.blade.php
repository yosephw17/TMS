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

.header-notification-badge,
.sidebar-notification-badge,
.account-notification-badge {
    font-size: 10px;
    min-width: 18px;
    height: 18px;
}

.user-notification-indicator {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.2); opacity: 0.7; }
    100% { transform: scale(1); opacity: 1; }
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
class GlobalNotificationManager {
    constructor() {
        this.badges = {
            header: document.getElementById('headerNotificationBadge'),
            sidebar: document.getElementById('sidebarNotificationBadge'),
            account: document.getElementById('accountNotificationBadge'),
            userIndicator: document.getElementById('userNotificationIndicator')
        };
        
        this.dropdown = {
            list: document.getElementById('notificationsList'),
            loading: document.getElementById('notificationsLoading'),
            empty: document.getElementById('notificationsEmpty'),
            markAllBtn: document.getElementById('markAllReadBtn'),
            refreshBtn: document.getElementById('refreshNotificationsBtn')
        };
        
        this.currentUnreadCount = 0;
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadInitialNotifications();
        
        // Auto refresh every 30 seconds
        setInterval(() => {
            this.updateUnreadCount();
        }, 30000);
        
        // Refresh every 2 minutes for new notifications
        setInterval(() => {
            this.loadNotifications();
        }, 120000);
    }
    
    bindEvents() {
        // Dropdown events
        if (this.dropdown.markAllBtn) {
            this.dropdown.markAllBtn.addEventListener('click', () => {
                this.markAllAsRead();
            });
        }
        
        if (this.dropdown.refreshBtn) {
            this.dropdown.refreshBtn.addEventListener('click', () => {
                this.loadNotifications();
            });
        }
        
        // Load notifications when dropdown is opened
        const notificationDropdown = document.querySelector('[data-bs-toggle="dropdown"][href="#"]');
        if (notificationDropdown) {
            notificationDropdown.addEventListener('shown.bs.dropdown', () => {
                this.loadNotifications();
            });
        }
    }
    
    async loadInitialNotifications() {
        await this.updateUnreadCount();
        await this.loadNotifications();
    }
    
    async loadNotifications() {
        try {
            this.showLoading();
            
            const response = await fetch('/api/notifications/dropdown', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (!response.ok) throw new Error('Failed to load notifications');
            
            const data = await response.json();
            this.renderNotifications(data.notifications);
            this.updateAllBadges(data.unread_count);
            
        } catch (error) {
            console.error('Error loading notifications:', error);
            this.showError();
        }
    }
    
    renderNotifications(notifications) {
        this.hideLoading();
        
        if (!this.dropdown.list) return;
        
        if (notifications.length === 0) {
            this.showEmpty();
            return;
        }
        
        this.hideEmpty();
        const html = notifications.map(notification => this.createNotificationHTML(notification)).join('');
        this.dropdown.list.innerHTML = html;
        
        // Add click handlers
        this.dropdown.list.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', (e) => {
                const id = item.dataset.id;
                const url = item.dataset.url;
                this.markAsRead(id);
                if (url && url !== 'null' && url !== '') {
                    window.location.href = url;
                }
            });
        });
    }
    
    createNotificationHTML(notification) {
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
    
    async markAsRead(id) {
        try {
            const response = await fetch(`/api/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                // Update UI
                const item = document.querySelector(`[data-id="${id}"]`);
                if (item) {
                    item.classList.remove('unread');
                }
                this.updateUnreadCount();
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                // Update UI
                if (this.dropdown.list) {
                    this.dropdown.list.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                    });
                }
                this.updateAllBadges(0);
            }
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
        }
    }
    
    async updateUnreadCount() {
        try {
            const response = await fetch('/api/notifications/unread-count', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                this.updateAllBadges(data.count);
            }
        } catch (error) {
            console.error('Error getting unread count:', error);
        }
    }
    
    updateAllBadges(count) {
        this.currentUnreadCount = count;
        
        // Update all badges
        Object.values(this.badges).forEach(badge => {
            if (badge) {
                if (count > 0) {
                    if (badge.id === 'userNotificationIndicator') {
                        // Just show the indicator dot
                        badge.style.display = 'block';
                    } else {
                        badge.textContent = count > 99 ? '99+' : count;
                        badge.style.display = 'block';
                        badge.classList.add('pulse');
                        setTimeout(() => badge.classList.remove('pulse'), 1000);
                    }
                } else {
                    badge.style.display = 'none';
                }
            }
        });
    }
    
    showLoading() {
        if (this.dropdown.loading) {
            this.dropdown.loading.classList.remove('d-none');
        }
        if (this.dropdown.empty) {
            this.dropdown.empty.classList.add('d-none');
        }
        if (this.dropdown.list) {
            this.dropdown.list.innerHTML = '';
        }
    }
    
    hideLoading() {
        if (this.dropdown.loading) {
            this.dropdown.loading.classList.add('d-none');
        }
    }
    
    showEmpty() {
        if (this.dropdown.empty) {
            this.dropdown.empty.classList.remove('d-none');
        }
    }
    
    hideEmpty() {
        if (this.dropdown.empty) {
            this.dropdown.empty.classList.add('d-none');
        }
    }
    
    showError() {
        this.hideLoading();
        if (this.dropdown.list) {
            this.dropdown.list.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle text-warning fs-2 mb-3"></i>
                    <h6 class="text-muted">Error loading notifications</h6>
                    <p class="small text-muted mb-0">Please try again later</p>
                </div>
            `;
        }
    }
    
    // Public method to create a new notification (for real-time updates)
    addNewNotification(notification) {
        this.currentUnreadCount++;
        this.updateAllBadges(this.currentUnreadCount);
        
        // Show browser notification if permission granted
        if (Notification.permission === 'granted') {
            new Notification(notification.title, {
                body: notification.message,
                icon: '/favicon.ico'
            });
        }
    }
    
    // Request notification permission
    requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.globalNotificationManager = new GlobalNotificationManager();
    
    // Request notification permission
    window.globalNotificationManager.requestNotificationPermission();
});

// Global function for external use
function refreshNotifications() {
    if (window.globalNotificationManager) {
        window.globalNotificationManager.loadNotifications();
    }
}

function updateNotificationCount() {
    if (window.globalNotificationManager) {
        window.globalNotificationManager.updateUnreadCount();
    }
}
</script>
