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

        $totalCount = Notification::where('user_id', $user->id)->count();
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
     * Get unread notifications count
     */
    public function unreadCount(): JsonResponse
    {
        $user = auth()->user();
        $count = $this->notificationService->getUnreadCount($user->id);

        return response()->json(['count' => $count]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = auth()->user();
        $count = $this->notificationService->markAllAsRead($user->id);

        return response()->json([
            'message' => 'All notifications marked as read',
            'count' => $count
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy($id): JsonResponse
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification deleted']);
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
            'has_more' => Notification::where('user_id', $user->id)->count() > 5
        ]);
    }
}
