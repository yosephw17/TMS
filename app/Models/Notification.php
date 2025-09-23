<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'data',
        'user_id',
        'created_by',
        'icon',
        'color',
        'action_url',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who should receive this notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who created this notification
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for recent notifications
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get formatted time ago
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get notification types with their configurations
     */
    public static function getNotificationTypes(): array
    {
        return [
            'project_created' => [
                'icon' => 'fas fa-project-diagram',
                'color' => 'success',
                'title' => 'New Project Created'
            ],
            'project_updated' => [
                'icon' => 'fas fa-edit',
                'color' => 'info',
                'title' => 'Project Updated'
            ],
            'project_completed' => [
                'icon' => 'fas fa-check-circle',
                'color' => 'success',
                'title' => 'Project Completed'
            ],
            'customer_added' => [
                'icon' => 'fas fa-user-plus',
                'color' => 'primary',
                'title' => 'New Customer Added'
            ],
            'customer_updated' => [
                'icon' => 'fas fa-user-edit',
                'color' => 'info',
                'title' => 'Customer Updated'
            ],
            'service_added' => [
                'icon' => 'fas fa-plus-circle',
                'color' => 'success',
                'title' => 'New Service Added'
            ],
            'team_member_added' => [
                'icon' => 'fas fa-user-tie',
                'color' => 'primary',
                'title' => 'New Team Member'
            ],
            'purchase_request_created' => [
                'icon' => 'fas fa-shopping-cart',
                'color' => 'warning',
                'title' => 'Purchase Request Created'
            ],
            'purchase_request_approved' => [
                'icon' => 'fas fa-check',
                'color' => 'success',
                'title' => 'Purchase Request Approved'
            ],
            'purchase_request_rejected' => [
                'icon' => 'fas fa-times',
                'color' => 'danger',
                'title' => 'Purchase Request Rejected'
            ],
            'system_maintenance' => [
                'icon' => 'fas fa-tools',
                'color' => 'warning',
                'title' => 'System Maintenance'
            ],
            'deadline_reminder' => [
                'icon' => 'fas fa-clock',
                'color' => 'warning',
                'title' => 'Deadline Reminder'
            ]
        ];
    }
}
