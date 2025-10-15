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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mark all as read - Enhanced version with better debugging
        document.getElementById('markAllReadBtn').addEventListener('click', function() {
            console.log('🎯 Mark All Read button clicked');

            // Show immediate visual feedback
            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Marking...';
            btn.disabled = true;
            btn.style.border = '2px solid #ffc107'; // Visual feedback

            // Test if the endpoint exists first
            console.log('🔍 Testing mark-all-read endpoint...');

            fetch('/api/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    console.log('📡 Response status:', response.status);
                    console.log('📡 Response headers:', Object.fromEntries(response.headers.entries()));

                    if (!response.ok) {
                        // Try to get more detailed error info
                        return response.text().then(text => {
                            console.error('❌ Response text:', text);
                            throw new Error(`HTTP ${response.status}: ${response.statusText}. Response: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ Mark all read SUCCESS! Data:', data);

                    if (data.success) {
                        // Update all notifications in UI to read state
                        const unreadItems = document.querySelectorAll('.notification-item.unread');
                        console.log(`🔄 Updating ${unreadItems.length} notifications to read state`);

                        unreadItems.forEach(item => {
                            item.classList.remove('unread');
                            const markReadBtn = item.querySelector('.mark-read-btn');
                            if (markReadBtn) {
                                markReadBtn.remove();
                            }
                        });

                        // Update unread count if displayed
                        const unreadBadge = document.querySelector('.unread-count-badge');
                        if (unreadBadge) {
                            unreadBadge.textContent = '0';
                            unreadBadge.style.display = 'none';
                        }

                        // Show success message
                        showToast(`✅ Success! ${data.marked_count || 'All'} notifications marked as read!`, 'success');
                    } else {
                        throw new Error(data.message || 'Server returned success: false');
                    }
                })
                .catch(error => {
                    console.error('💥 Fetch error:', error);

                    // Try alternative endpoints as fallback
                    console.log('🔄 Trying alternative approach...');
                    markAllVisibleAsReadFallback();
                })
                .finally(() => {
                    // Reset button
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                    btn.style.border = '';
                });
        });

        // Fallback: Mark all visible notifications individually
        async function markAllVisibleAsReadFallback() {
            console.log('🔄 Starting fallback: marking visible notifications individually');

            const unreadItems = document.querySelectorAll('.notification-item.unread');
            console.log(`📋 Found ${unreadItems.length} unread notifications to mark`);

            if (unreadItems.length === 0) {
                showToast('ℹ️ All notifications are already read!', 'info');
                return;
            }

            let successCount = 0;
            let errorCount = 0;

            // Process each notification
            for (const item of unreadItems) {
                const id = item.dataset.id;
                if (!id) continue;

                try {
                    console.log(`🔵 Marking notification ${id} as read...`);

                    const response = await fetch(`/api/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            mark_as_read: true
                        })
                    });

                    if (response.ok) {
                        // Update UI immediately
                        item.classList.remove('unread');
                        const markReadBtn = item.querySelector('.mark-read-btn');
                        if (markReadBtn) {
                            markReadBtn.remove();
                        }
                        successCount++;
                        console.log(`✅ Notification ${id} marked as read`);
                    } else {
                        errorCount++;
                        console.error(`❌ Failed to mark notification ${id} as read`);
                    }
                } catch (error) {
                    errorCount++;
                    console.error(`❌ Error marking notification ${id}:`, error);
                }
            }

            console.log(`🎉 Fallback completed: ${successCount} successful, ${errorCount} failed`);

            if (successCount > 0) {
                showToast(`✅ Marked ${successCount} notifications as read!`, 'success');
            }
            if (errorCount > 0) {
                showToast(`⚠️ Failed to mark ${errorCount} notifications as read`, 'warning');
            }
        }

        // Refresh button
        document.getElementById('refreshBtn').addEventListener('click', function() {
            window.notificationPage.currentPage = 1;
            window.notificationPage.loadNotifications(true);
        });

        // Filter buttons
        document.querySelectorAll('input[name="filter"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                window.notificationPage.currentFilter = e.target.value;
                window.notificationPage.currentPage = 1;
                window.notificationPage.loadNotifications(true);
            });
        });

        // Type filter
        document.getElementById('typeFilter').addEventListener('change', (e) => {
            window.notificationPage.currentType = e.target.value;
            window.notificationPage.currentPage = 1;
            window.notificationPage.loadNotifications(true);
        });

        // Load more button
        document.getElementById('loadMoreBtn').addEventListener('click', function() {
            window.notificationPage.currentPage++;
            window.notificationPage.loadNotifications(false);
        });

        // Toast function
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'error' ? 'danger' : (type === 'success' ? 'success' : (type === 'warning' ? 'warning' : 'info'))} alert-dismissible fade show position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
            toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : (type === 'error' ? 'exclamation-triangle' : (type === 'warning' ? 'exclamation-triangle' : 'info-circle'))} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
            document.body.appendChild(toast);

            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 5000);
        }

        // NotificationPage class
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
                // Event delegation for notification actions
                document.getElementById('notificationsContainer').addEventListener('click', (e) => {
                    const markReadBtn = e.target.closest('.mark-read-btn');
                    const deleteBtn = e.target.closest('.delete-btn');
                    const notificationItem = e.target.closest('.notification-item');

                    if (markReadBtn) {
                        e.preventDefault();
                        e.stopPropagation();
                        const id = markReadBtn.dataset.id;
                        this.markAsRead(id);
                    } else if (deleteBtn) {
                        e.preventDefault();
                        e.stopPropagation();
                        const id = deleteBtn.dataset.id;
                        this.deleteNotification(id);
                    } else if (notificationItem && !e.target.closest('.notification-actions')) {
                        // Only handle notification item clicks that are NOT on action buttons
                        e.preventDefault();
                        e.stopPropagation();
                        const id = notificationItem.dataset.id;
                        this.handleNotificationCardClick(id, notificationItem);
                    }
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
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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

                notifications.forEach((notification) => {
                    const html = this.createNotificationHTML(notification);
                    container.insertAdjacentHTML('beforeend', html);
                });
            }

            createNotificationHTML(notification) {
                const unreadClass = notification.is_read ? '' : 'unread';
                const timeAgo = this.formatTimeAgo(notification.created_at);
                const {
                    icon,
                    color
                } = this.getNotificationStyle(notification.type, notification.title, notification.message);

                return `
                <div class="notification-item ${unreadClass}" data-id="${notification.id}" data-action-url="${notification.action_url || '#'}">
                    <div class="d-flex align-items-start">
                        <div class="notification-icon bg-${color} me-4">
                            <i class="${icon}"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">${notification.title}</div>
                            <div class="notification-message">${notification.message}</div>
                            <div class="notification-meta">
                                <span><i class="fas fa-clock me-1"></i>${timeAgo}</span>
                                ${notification.creator ? `<span><i class="fas fa-user me-1"></i>By ${notification.creator.name}</span>` : ''}
                                <span class="badge bg-${color} bg-opacity-10 text-${color}">${this.formatType(notification.type)}</span>
                            </div>
                        </div>
                        <div class="notification-actions">
                            ${!notification.is_read ? `
                                <button class="btn btn-sm btn-outline-primary mark-read-btn" data-id="${notification.id}">
                                    <i class="fas fa-check"></i>
                                </button>
                            ` : ''}
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${notification.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            }

            getNotificationStyle(type, title = '', message = '') {
                // Default styles - UPDATED: purchase_request_created is now yellow (warning)
                const defaultStyles = {
                    'project_created': {
                        icon: 'fas fa-project-diagram',
                        color: 'primary'
                    },
                    'customer_added': {
                        icon: 'fas fa-users',
                        color: 'success'
                    },
                    'service_added': {
                        icon: 'fas fa-concierge-bell',
                        color: 'info'
                    },
                    'purchase_request_created': {
                        icon: 'fas fa-shopping-cart',
                        color: 'warning' // CHANGED: Now yellow for purchase requests
                    }
                };

                // Check for purchase request approval/declination in title or message
                const lowerTitle = title.toLowerCase();
                const lowerMessage = message.toLowerCase();

                // Purchase Request Approved - Green bell
                if (lowerTitle.includes('approved') || lowerMessage.includes('approved')) {
                    return {
                        icon: 'fas fa-bell',
                        color: 'success'
                    };
                }

                // Purchase Request Declined/Rejected - Red bell
                if (lowerTitle.includes('declined') || lowerMessage.includes('declined') ||
                    lowerTitle.includes('rejected') || lowerMessage.includes('rejected') ||
                    lowerTitle.includes('denied') || lowerMessage.includes('denied')) {
                    return {
                        icon: 'fas fa-bell',
                        color: 'danger'
                    };
                }

                // Purchase Request Pending - Orange/yellow bell (same as created)
                if (lowerTitle.includes('pending') || lowerMessage.includes('pending') ||
                    lowerTitle.includes('waiting') || lowerMessage.includes('waiting') ||
                    lowerTitle.includes('created') || lowerMessage.includes('created')) {
                    return {
                        icon: 'fas fa-bell',
                        color: 'warning'
                    };
                }

                // Return default style based on type
                return defaultStyles[type] || {
                    icon: 'fas fa-bell',
                    color: 'secondary'
                };
            }

            async markAsRead(id) {
                try {
                    console.log('Marking notification as read:', id);

                    const response = await fetch(`/api/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            mark_as_read: true
                        })
                    });

                    if (response.ok) {
                        const item = document.querySelector(`.notification-item[data-id="${id}"]`);
                        if (item) {
                            item.classList.remove('unread');
                            const markReadBtn = item.querySelector('.mark-read-btn');
                            if (markReadBtn) {
                                markReadBtn.remove();
                            }
                        }
                        showToast('Notification marked as read!', 'success');
                    } else {
                        showToast('Failed to mark notification as read', 'error');
                    }
                } catch (error) {
                    console.error('Error marking notification as read:', error);
                    showToast('Error marking notification as read', 'error');
                }
            }

            async handleNotificationCardClick(id, itemElement) {
                const actionUrl = itemElement.dataset.actionUrl;
                const isUnread = itemElement.classList.contains('unread');

                if (isUnread) {
                    try {
                        await this.markAsRead(id);
                        setTimeout(() => {
                            if (actionUrl && actionUrl !== '#') {
                                window.location.href = actionUrl;
                            }
                        }, 300);
                    } catch (error) {
                        if (actionUrl && actionUrl !== '#') {
                            window.location.href = actionUrl;
                        }
                    }
                } else {
                    if (actionUrl && actionUrl !== '#') {
                        window.location.href = actionUrl;
                    }
                }
            }

            async deleteNotification(id) {
                if (!confirm('Are you sure you want to delete this notification?')) return;

                try {
                    const response = await fetch(`/api/notifications/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        const item = document.querySelector(`.notification-item[data-id="${id}"]`);
                        if (item) item.remove();

                        const container = document.getElementById('notificationsContainer');
                        if (container.children.length === 0) {
                            this.loadNotifications(true);
                        }
                        showToast('Notification deleted!', 'success');
                    }
                } catch (error) {
                    console.error('Error deleting notification:', error);
                    showToast('Error deleting notification', 'error');
                }
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
                const container = document.getElementById('notificationsContainer');
                container.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-exclamation-triangle text-warning fs-1 mb-3"></i>
                    <h5 class="text-muted">Error loading notifications</h5>
                    <p class="text-muted">Please try again later</p>
                    <button class="btn btn-primary" onclick="location.reload()">Retry</button>
                </div>
            `;
                container.classList.remove('d-none');
            }
        }

        // Initialize
        window.notificationPage = new NotificationPage();
    });
</script>
@endsection