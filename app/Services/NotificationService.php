<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Create a new notification
     */
    public function create(array $data): Notification
    {
        $notificationTypes = Notification::getNotificationTypes();
        $type = $data['type'];

        // Get default configuration for the notification type
        $config = $notificationTypes[$type] ?? [
            'icon' => 'fas fa-bell',
            'color' => 'primary',
            'title' => 'Notification'
        ];

        return Notification::create([
            'type' => $type,
            'title' => $data['title'] ?? $config['title'],
            'message' => $data['message'],
            'data' => $data['data'] ?? null,
            'user_id' => $data['user_id'],
            'created_by' => $data['created_by'] ?? auth()->id(),
            'icon' => $data['icon'] ?? $config['icon'],
            'color' => $data['color'] ?? $config['color'],
            'action_url' => $data['action_url'] ?? null,
        ]);
    }

    /**
     * Create notification for multiple users
     */
    public function createForUsers(array $userIds, array $data): Collection
    {
        $notifications = collect();

        foreach ($userIds as $userId) {
            $notificationData = array_merge($data, ['user_id' => $userId]);
            $notifications->push($this->create($notificationData));
        }

        return $notifications;
    }

    /**
     * Create notification for all users with specific permissions
     */
    public function createForUsersWithPermission(string $permission, array $data): Collection
    {
        $users = User::whereHas('roles.permissions', function ($query) use ($permission) {
            $query->where('name', $permission);
        })->pluck('id')->toArray();

        return $this->createForUsers($users, $data);
    }

    /**
     * Create notification for users with notification-specific permissions
     */
    public function createForUsersWithNotificationPermission(string $notificationType, array $data): Collection
    {
        $notificationTypes = Notification::getNotificationTypes();
        $requiredPermission = $notificationTypes[$notificationType]['permission'] ?? null;

        if (!$requiredPermission) {
            // If no specific permission required, send to all users
            $users = User::pluck('id')->toArray();
        } else {
            // Only send to users with the required permission
            $users = User::whereHas('roles.permissions', function ($query) use ($requiredPermission) {
                $query->where('name', $requiredPermission);
            })->pluck('id')->toArray();
        }

        // Debug logging
        \Log::info("Creating notification for type: {$notificationType}", [
            'required_permission' => $requiredPermission,
            'users_found' => count($users),
            'user_ids' => $users
        ]);

        return $this->createForUsers($users, $data);
    }

    /**
     * Create notification for all admin users
     */
    public function createForAdmins(array $data): Collection
    {
        $adminUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->pluck('id')->toArray();

        return $this->createForUsers($adminUsers, $data);
    }

    /**
     * Get notifications for a user
     */
    public function getUserNotifications(int $userId, int $limit = 10): Collection
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread notifications count for a user (filtered by permissions)
     */
    public function getUnreadCount(int $userId): int
    {
        $user = User::find($userId);
        if (!$user) {
            return 0;
        }

        return Notification::where('user_id', $userId)
            ->forUser($user)
            ->unread()
            ->count();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId): bool
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        return false;
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    /**
     * Delete old notifications (older than specified days)
     */
    public function deleteOldNotifications(int $days = 30): int
    {
        return Notification::where('created_at', '<', now()->subDays($days))->delete();
    }

    /**
     * Create project-related notifications
     */
    public function notifyProjectCreated($project, $createdBy = null): Collection
    {
        return $this->createForUsersWithNotificationPermission('project_created', [
            'type' => 'project_created',
            'message' => "New project '{$project->name}' has been created for customer {$project->customer->name}",
            'data' => [
                'project_id' => $project->id,
                'customer_id' => $project->customer_id,
                'project_name' => $project->name,
                'customer_name' => $project->customer->name
            ],
            'action_url' => route('projects.view', $project->id),
            'created_by' => $createdBy ?? auth()->id()
        ]);
    }
    /**
     * Create customer-related notifications
     */
    public function notifyCustomerAdded($customer, $createdBy = null): Collection
    {
        return $this->createForUsersWithNotificationPermission('customer_added', [
            'type' => 'customer_added',
            'message' => "New customer '{$customer->name}' has been added to the system",
            'data' => [
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_phone' => $customer->phone
            ],
            'action_url' => route('customers.index'),
            'created_by' => $createdBy ?? auth()->id()
        ]);
    }

    /**
     * Create service-related notifications
     */
    public function notifyServiceAdded($service, $createdBy = null): Collection
    {
        return $this->createForUsersWithNotificationPermission('service_added', [
            'type' => 'service_added',
            'message' => "New service '{$service->name}' has been added",
            'data' => [
                'service_id' => $service->id,
                'service_name' => $service->name
            ],
            'action_url' => route('services.index'),
            'created_by' => $createdBy ?? auth()->id()
        ]);
    }

    /**
     * Create purchase request notifications
     */
    public function notifyPurchaseRequestCreated($purchaseRequest, $createdBy = null): Collection
    {
        return $this->createForUsersWithNotificationPermission('purchase_request_created', [
            'type' => 'purchase_request_created',
            'message' => "New purchase request for '{$purchaseRequest->item_name}' requires approval",
            'data' => [
                'purchase_request_id' => $purchaseRequest->id,
                'item_name' => $purchaseRequest->item_name,
                'amount' => $purchaseRequest->amount
            ],
            'action_url' => route('purchase-requests.index'),
            'created_by' => $createdBy ?? auth()->id()
        ]);
    }

    /**
     * Create deadline reminder notifications
     */
    public function notifyDeadlineReminder($project): Collection
    {
        return $this->createForUsersWithNotificationPermission('deadline_reminder', [
            'type' => 'deadline_reminder',
            'message' => "Project '{$project->name}' deadline is approaching ({$project->ending_date})",
            'data' => [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'deadline' => $project->ending_date
            ],
            'action_url' => route('projects.view', $project->id)
        ]);
    }

    /**
     * Create material-related notifications
     */
    public function notifyMaterialAdded($material, $createdBy = null): Collection
    {
        return $this->createForUsersWithNotificationPermission('material_added', [
            'type' => 'material_added',
            'message' => "New material '{$material->name}' has been added to inventory",
            'data' => [
                'material_id' => $material->id,
                'material_name' => $material->name,
                'material_type' => $material->type
            ],
            'action_url' => route('materials.index'),
            'created_by' => $createdBy ?? auth()->id()
        ]);
    }

    /**
     * Create proforma-related notifications
     */
    public function notifyProformaCreated($proforma, $createdBy = null): Collection
    {
        return $this->createForUsersWithNotificationPermission('proforma_created', [
            'type' => 'proforma_created',
            'message' => "New proforma '{$proforma->ref_no}' has been created for project '{$proforma->project->name}'",
            'data' => [
                'proforma_id' => $proforma->id,
                'proforma_ref' => $proforma->ref_no,
                'project_id' => $proforma->project_id,
                'project_name' => $proforma->project->name
            ],
            'action_url' => route('projects.view', $proforma->project_id),
            'created_by' => $createdBy ?? auth()->id()
        ]);
    }

    /**
     * Create proforma approval notifications
     */
    public function notifyProformaApproved($proforma, $approvedBy = null): Collection
    {
        return $this->createForUsersWithNotificationPermission('proforma_approved', [
            'type' => 'proforma_approved',
            'message' => "Proforma '{$proforma->ref_no}' has been approved",
            'data' => [
                'proforma_id' => $proforma->id,
                'proforma_ref' => $proforma->ref_no,
                'project_id' => $proforma->project_id,
                'project_name' => $proforma->project->name
            ],
            'action_url' => route('projects.view', $proforma->project_id),
            'created_by' => $approvedBy ?? auth()->id()
        ]);
    }

    /**
     * Create team member notifications
     */
    public function notifyTeamMemberAdded($team, $user, $createdBy = null): Collection
    {
        return $this->createForUsersWithNotificationPermission('team_member_added', [
            'type' => 'team_member_added',
            'message' => "New team member '{$user->name}' has been added to team '{$team->name}'",
            'data' => [
                'team_id' => $team->id,
                'team_name' => $team->name,
                'user_id' => $user->id,
                'user_name' => $user->name
            ],
            'action_url' => route('teams.index'),
            'created_by' => $createdBy ?? auth()->id()
        ]);
    }
}
