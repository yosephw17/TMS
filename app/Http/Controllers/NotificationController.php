<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display the notifications page
     */
    public function indexPage()
    {
        return view('notifications.index');
    }

    /**
     * Get notifications for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $filter = $request->get('filter', 'all');
        $type = $request->get('type', '');

        $query = Notification::where('user_id', $user->id)
            ->forUser($user)
            ->with('creator:id,name');

        // Apply filters
        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'read') {
            $query->read();
        }

        if ($type) {
            $query->where('type', $type);
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        $totalCount = $query->count();
        $unreadCount = $this->notificationService->getUnreadCount($user->id);
        $hasMore = ($page * $limit) < $totalCount;

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
            'total' => $totalCount,
            'has_more' => $hasMore,
            'current_page' => $page
        ]);
    }

    /**
     * Get notifications via API (alternative method)
     */
    public function getNotifications(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = $request->get('limit', 10);
        $unreadOnly = $request->boolean('unread_only', false);

        $notifications = $this->notificationService->getUserNotifications($user, $limit, $unreadOnly);
        $unreadCount = $this->notificationService->getUnreadCount($user);

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'icon' => $notification->icon,
                    'color_class' => $notification->color_class,
                    'action_url' => $notification->action_url,
                    'priority' => $notification->priority,
                    'is_read' => $notification->isRead(),
                    'created_at' => $notification->created_at->diffForHumans(),
                    'created_by' => $notification->createdBy ? [
                        'id' => $notification->createdBy->id,
                        'name' => $notification->createdBy->name,
                    ] : null,
                ];
            }),
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        try {
            $notification = Notification::where('id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $markAsRead = $request->input('mark_as_read', true);

            if ($markAsRead) {
                $notification->markAsRead();
            } else {
                $notification->markAsUnread();
            }

            $unreadCount = $this->notificationService->getUnreadCount(auth()->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Notification updated successfully',
                'unread_count' => $unreadCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking notification as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        try {
            $user = auth()->user();
            $count = $this->notificationService->markAllAsRead($user->id);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
                'marked_count' => $count,
                'unread_count' => 0
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking all notifications as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred'
            ], 500);
        }
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount(Request $request): JsonResponse
    {
        $count = $this->notificationService->getUnreadCount($request->user());

        return response()->json([
            'unread_count' => $count,
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy($id): JsonResponse
    {
        try {
            $notification = Notification::where('id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->delete();

            $unreadCount = $this->notificationService->getUnreadCount(auth()->user());

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully',
                'unread_count' => $unreadCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred'
            ], 500);
        }
    }

    /**
     * Get notification details
     */
    public function show($id): JsonResponse
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->with('creator:id,name')
            ->first();

        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        // Mark as read when viewed
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return response()->json(['notification' => $notification]);
    }

    /**
     * Get notifications for display in dropdown
     */
    public function dropdown(): JsonResponse
    {
        $user = auth()->user();

        $notifications = Notification::where('user_id', $user->id)
            ->forUser($user)
            ->with('creator:id,name')
            ->orderBy('is_read', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $unreadCount = $this->notificationService->getUnreadCount($user->id);

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'is_read' => $notification->is_read,
                    'time_ago' => $notification->time_ago,
                    'action_url' => $notification->action_url,
                    'creator_name' => $notification->creator ? $notification->creator->name : 'System'
                ];
            }),
            'unread_count' => $unreadCount,
            'has_more' => Notification::where('user_id', $user->id)->forUser($user)->count() > 5
        ]);
    }

    /**
     * Send test notification (for development/testing)
     */
    public function sendTest(Request $request): JsonResponse
    {
        $user = $request->user();

        $notification = $this->notificationService->create(
            $user,
            'test_notification',
            [
                'title' => 'Test Notification',
                'message' => 'This is a test notification sent at ' . now()->format('Y-m-d H:i:s'),
                'action_url' => route('notifications.index'),
            ],
            'normal'
        );

        return response()->json([
            'success' => true,
            'notification' => [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
            ],
        ]);
    }

    /**
     * Test method to create sample notifications for testing permissions
     */
    public function testPermissions()
    {
        $user = auth()->user();

        // Debug: Check which users have customer-notification permission
        $usersWithCustomerPermission = \App\Models\User::whereHas('roles.permissions', function ($query) {
            $query->where('name', 'customer-notification');
        })->get(['id', 'name', 'email']);

        // Create a single test notification
        $notifications = $this->notificationService->createForUsersWithNotificationPermission('customer_added', [
            'type' => 'customer_added',
            'message' => 'DEBUG: Test customer notification created at ' . now()->format('H:i:s'),
            'data' => ['test' => true, 'debug' => true],
            'action_url' => '#',
            'created_by' => auth()->id()
        ]);

        // Check all notifications in database for debugging
        $allNotifications = \App\Models\Notification::where('type', 'customer_added')
            ->where('created_at', '>=', now()->subMinutes(1))
            ->get(['id', 'user_id', 'message', 'created_at']);

        return response()->json([
            'message' => 'Debug test completed!',
            'current_user' => [
                'id' => $user->id,
                'name' => $user->name,
                'permissions' => $user->getAllPermissions()->pluck('name')->toArray()
            ],
            'users_with_customer_permission' => $usersWithCustomerPermission->toArray(),
            'notifications_created' => $notifications->count(),
            'all_customer_notifications' => $allNotifications->toArray(),
            'notification_details' => $notifications->map(function ($n) {
                return [
                    'id' => $n->id,
                    'user_id' => $n->user_id,
                    'message' => $n->message
                ];
            })->toArray()
        ]);
    }

    /**
     * Test cross-user notification visibility by creating a test customer
     */
    public function testCrossUser()
    {
        $user = auth()->user();

        // Create a test customer using the CustomerController logic
        $customer = \App\Models\Customer::create([
            'name' => 'Test Customer ' . now()->format('H:i:s'),
            'phone' => '1234567890',
            'address' => 'Test Address',
            'type' => 'project'
        ]);

        // Create notification using the same method as CustomerController
        $this->notificationService->createForUsersWithNotificationPermission('customer_added', [
            'type' => 'customer_added',
            'message' => "TEST: New customer '{$customer->name}' has been added to the system",
            'action_url' => route('customers.index'),
            'data' => [
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_phone' => $customer->phone
            ],
            'created_by' => auth()->id()
        ]);

        // Get all users and their notification counts
        $allUsers = \App\Models\User::with('roles')->get();
        $userNotificationCounts = [];

        foreach ($allUsers as $u) {
            $count = \App\Models\Notification::where('user_id', $u->id)
                ->where('message', 'LIKE', 'TEST: New customer%')
                ->count();
            $userNotificationCounts[] = [
                'user_id' => $u->id,
                'user_name' => $u->name,
                'roles' => $u->roles->pluck('name'),
                'has_customer_permission' => $u->can('customer-notification'),
                'notification_count' => $count
            ];
        }

        return response()->json([
            'message' => 'Cross-user test completed!',
            'test_customer' => [
                'id' => $customer->id,
                'name' => $customer->name
            ],
            'created_by_user' => [
                'id' => $user->id,
                'name' => $user->name
            ],
            'user_notification_analysis' => $userNotificationCounts,
            'expected_behavior' => 'All users with customer-notification permission should have notification_count = 1'
        ]);
    }

    /**
     * Get unread notifications count (alternative method)
     */
    public function unreadCount(): JsonResponse
    {
        $user = auth()->user();
        $count = $this->notificationService->getUnreadCount($user->id);

        return response()->json(['count' => $count]);
    }
}
