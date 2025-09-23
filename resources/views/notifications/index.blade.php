@extends('layouts.admin')

@section('content')
<main>
    <div class="container-fluid px-4">
        <!-- Enhanced Header -->
        <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
            <div>
                <h1 class="mb-1 fw-bold">Notifications</h1>
                <p class="text-muted mb-0">Stay updated with all your important activities and updates.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" id="markAllReadBtn">
                    <i class="fas fa-check-double me-1"></i>Mark All Read
                </button>
                <button class="btn btn-outline-secondary" id="refreshBtn">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
            </div>
        </div>

        <!-- Notification Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <span class="text-muted small fw-medium">Filter by:</span>
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" name="filter" id="all" value="all" checked>
                                    <label class="btn btn-outline-primary btn-sm" for="all">All</label>
                                    
                                    <input type="radio" class="btn-check" name="filter" id="unread" value="unread">
                                    <label class="btn btn-outline-primary btn-sm" for="unread">Unread</label>
                                    
                                    <input type="radio" class="btn-check" name="filter" id="read" value="read">
                                    <label class="btn btn-outline-primary btn-sm" for="read">Read</label>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="text-muted small fw-medium">Type:</span>
                                <select class="form-select form-select-sm" id="typeFilter" style="width: auto;">
                                    <option value="">All Types</option>
                                    <option value="project_created">Project Created</option>
                                    <option value="customer_added">Customer Added</option>
                                    <option value="service_added">Service Added</option>
                                    <option value="purchase_request_created">Purchase Requests</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <!-- Loading State -->
                        <div class="text-center py-5" id="loadingState">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-3 mb-0">Loading notifications...</p>
                        </div>

                        <!-- Notifications Container -->
                        <div id="notificationsContainer" class="d-none">
                            <!-- Notifications will be loaded here -->
                        </div>

                        <!-- Empty State -->
                        <div class="text-center py-5 d-none" id="emptyState">
                            <div class="mb-4">
                                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" 
                                     style="width: 120px; height: 120px;">
                                    <i class="fas fa-bell-slash text-muted" style="font-size: 3rem;"></i>
                                </div>
                            </div>
                            <h4 class="text-muted mb-3 fw-bold">No Notifications</h4>
                            <p class="text-muted mb-0 mx-auto" style="max-width: 400px;">
                                You're all caught up! No notifications to display at the moment.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Load More Button -->
        <div class="text-center mt-4 d-none" id="loadMoreContainer">
            <button class="btn btn-outline-primary" id="loadMoreBtn">
                <i class="fas fa-chevron-down me-1"></i>Load More Notifications
            </button>
        </div>
    </div>
</main>

<!-- Custom Styles -->
<style>
.notification-item {
    padding: 20px;
    border-bottom: 1px solid #f1f3f4;
    transition: all 0.3s ease;
    position: relative;
    cursor: pointer;
}

.notification-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.notification-item.unread {
    background: linear-gradient(90deg, rgba(33, 150, 243, 0.05) 0%, rgba(255, 255, 255, 1) 100%);
    border-left: 4px solid #2196f3;
}

.notification-item.unread::before {
    content: '';
    position: absolute;
    top: 20px;
    right: 20px;
    width: 10px;
    height: 10px;
    background-color: #2196f3;
    border-radius: 50%;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.2);
}

.notification-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    position: relative;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
}

.notification-message {
    font-size: 14px;
    color: #666;
    margin-bottom: 8px;
    line-height: 1.5;
}

.notification-meta {
    display: flex;
    align-items: center;
    gap: 15px;
    font-size: 12px;
    color: #999;
}

.notification-actions {
    display: flex;
    gap: 8px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.notification-item:hover .notification-actions {
    opacity: 1;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.notification-item {
    animation: slideIn 0.4s ease-out;
}
</style>

<!-- JavaScript -->
<script>
class NotificationPage {
    constructor() {
        this.currentPage = 1;
        this.currentFilter = 'all';
        this.currentType = '';
        this.loading = false;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadNotifications();
    }
    
    bindEvents() {
        // Filter buttons
        document.querySelectorAll('input[name="filter"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                this.currentFilter = e.target.value;
                this.currentPage = 1;
                this.loadNotifications(true);
            });
        });
        
        // Type filter
        document.getElementById('typeFilter').addEventListener('change', (e) => {
            this.currentType = e.target.value;
            this.currentPage = 1;
            this.loadNotifications(true);
        });
        
        // Mark all read
        document.getElementById('markAllReadBtn').addEventListener('click', () => {
            this.markAllAsRead();
        });
        
        // Refresh
        document.getElementById('refreshBtn').addEventListener('click', () => {
            this.currentPage = 1;
            this.loadNotifications(true);
        });
        
        // Load more
        document.getElementById('loadMoreBtn').addEventListener('click', () => {
            this.currentPage++;
            this.loadNotifications(false);
        });
    }
    
    async loadNotifications(reset = false) {
        if (this.loading) return;
        
        this.loading = true;
        this.showLoading();
        
        try {
            const params = new URLSearchParams({
                page: this.currentPage,
                filter: this.currentFilter,
                type: this.currentType,
                limit: 10
            });
            
            const response = await fetch(`/api/notifications?${params}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (!response.ok) throw new Error('Failed to load notifications');
            
            const data = await response.json();
            this.renderNotifications(data.notifications, reset);
            this.updateLoadMoreButton(data.has_more);
            
        } catch (error) {
            console.error('Error loading notifications:', error);
            this.showError();
        } finally {
            this.loading = false;
            this.hideLoading();
        }
    }
    
    renderNotifications(notifications, reset = false) {
        const container = document.getElementById('notificationsContainer');
        
        if (reset) {
            container.innerHTML = '';
        }
        
        if (notifications.length === 0 && reset) {
            this.showEmpty();
            return;
        }
        
        this.hideEmpty();
        container.classList.remove('d-none');
        
        notifications.forEach((notification, index) => {
            const html = this.createNotificationHTML(notification);
            container.insertAdjacentHTML('beforeend', html);
            
            // Add click handler
            const item = container.lastElementChild;
            item.addEventListener('click', () => {
                this.handleNotificationClick(notification);
            });
        });
    }
    
    createNotificationHTML(notification) {
        const unreadClass = notification.is_read ? '' : 'unread';
        const timeAgo = this.formatTimeAgo(notification.created_at);
        
        return `
            <div class="notification-item ${unreadClass}" data-id="${notification.id}">
                <div class="d-flex align-items-start">
                    <div class="notification-icon bg-${notification.color} me-4">
                        <i class="${notification.icon}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${notification.title}</div>
                        <div class="notification-message">${notification.message}</div>
                        <div class="notification-meta">
                            <span><i class="fas fa-clock me-1"></i>${timeAgo}</span>
                            ${notification.creator ? `<span><i class="fas fa-user me-1"></i>By ${notification.creator.name}</span>` : ''}
                            <span class="badge bg-${notification.color} bg-opacity-10 text-${notification.color}">${this.formatType(notification.type)}</span>
                        </div>
                    </div>
                    <div class="notification-actions">
                        ${!notification.is_read ? `<button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); markAsRead(${notification.id})">
                            <i class="fas fa-check"></i>
                        </button>` : ''}
                        <button class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation(); deleteNotification(${notification.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
    
    formatType(type) {
        return type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }
    
    formatTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        if (diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
        return `${Math.floor(diffInSeconds / 86400)}d ago`;
    }
    
    handleNotificationClick(notification) {
        if (!notification.is_read) {
            this.markAsRead(notification.id);
        }
        
        if (notification.action_url) {
            window.location.href = notification.action_url;
        }
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
                const item = document.querySelector(`[data-id="${id}"]`);
                if (item) {
                    item.classList.remove('unread');
                    const actions = item.querySelector('.notification-actions');
                    if (actions) {
                        actions.innerHTML = actions.innerHTML.replace(/btn-outline-primary.*?<\/button>/, '');
                    }
                }
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
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                });
            }
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
        }
    }
    
    showLoading() {
        document.getElementById('loadingState').classList.remove('d-none');
    }
    
    hideLoading() {
        document.getElementById('loadingState').classList.add('d-none');
    }
    
    showEmpty() {
        document.getElementById('emptyState').classList.remove('d-none');
        document.getElementById('notificationsContainer').classList.add('d-none');
    }
    
    hideEmpty() {
        document.getElementById('emptyState').classList.add('d-none');
    }
    
    updateLoadMoreButton(hasMore) {
        const container = document.getElementById('loadMoreContainer');
        if (hasMore) {
            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
        }
    }
    
    showError() {
        document.getElementById('notificationsContainer').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-exclamation-triangle text-warning fs-1 mb-3"></i>
                <h5 class="text-muted">Error loading notifications</h5>
                <p class="text-muted">Please try again later</p>
                <button class="btn btn-primary" onclick="location.reload()">Retry</button>
            </div>
        `;
        document.getElementById('notificationsContainer').classList.remove('d-none');
    }
}

// Global functions for inline handlers
function markAsRead(id) {
    window.notificationPage.markAsRead(id);
}

function deleteNotification(id) {
    if (confirm('Are you sure you want to delete this notification?')) {
        // Implementation for delete
        console.log('Delete notification:', id);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.notificationPage = new NotificationPage();
});
</script>
@endsection
