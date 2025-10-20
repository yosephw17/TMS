<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RestockEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'purchase_request_id',
        'material_id',
        'stock_id',
        'restocked_by',
        'quantity_restocked',
        'unit_price',
        'total_cost_deducted',
        'restock_reference',
        'reason',
        'notes',
        'status',
        'approved_by',
        'approved_at',
        'original_purchase_data',
        'stock_movement_log'
    ];

    protected $casts = [
        'original_purchase_data' => 'array',
        'stock_movement_log' => 'array',
        'approved_at' => 'datetime',
        'quantity_restocked' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'total_cost_deducted' => 'decimal:2'
    ];

    /**
     * Relationships
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function restockedBy()
    {
        return $this->belongsTo(User::class, 'restocked_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Generate unique restock reference
     */
    public static function generateRestockReference($project, $material)
    {
        $date = Carbon::now()->format('Ymd');
        $projectCode = strtoupper(substr($project->name, 0, 3));
        $materialCode = strtoupper(substr($material->name, 0, 3));
        
        // Get next sequence number for this combination
        $lastEntry = self::where('restock_reference', 'like', "REF-{$projectCode}-{$materialCode}-{$date}-%")
                        ->orderBy('restock_reference', 'desc')
                        ->first();
        
        $sequence = 1;
        if ($lastEntry) {
            $lastSequence = (int)substr($lastEntry->restock_reference, -3);
            $sequence = $lastSequence + 1;
        }
        
        return "REF-{$projectCode}-{$materialCode}-{$date}-" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Approve restock entry
     */
    public function approve($approvedBy)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now()
        ]);

        // Update project costs
        $this->project->calculateAndUpdateCosts();

        // Add stock back to inventory
        $this->addToStock();

        return $this;
    }

    /**
     * Reject restock entry
     */
    public function reject($rejectedBy, $reason = null)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $rejectedBy,
            'approved_at' => now(),
            'notes' => $this->notes . "\nRejected: " . ($reason ?? 'No reason provided')
        ]);

        return $this;
    }

    /**
     * Add restocked quantity back to stock
     */
private function addToStock()
{
    /**
     * STEP 1: Get ALL stock entries regardless of status in LIFO order (newest first)
     */
    $stockEntries = \DB::table('material_stock')
        ->where('material_id', $this->material_id)
        ->where('stock_id', $this->stock_id)
        ->orderBy('created_at', 'desc') // LIFO order - newest first
        ->orderBy('id', 'desc')
        ->get();

    \Log::info("LIFO Restock - All Entries", [
        'material_id' => $this->material_id,
        'stock_id' => $this->stock_id,
        'restock_reference' => $this->restock_reference,
        'quantity_to_restock' => $this->quantity_restocked,
        'entries_count' => $stockEntries->count(),
        'entries_details' => $stockEntries->map(function($entry) {
            $availableCapacity = $entry->original_quantity - $entry->remaining_quantity;
            
            return [
                'id' => $entry->id,
                'reference_number' => $entry->reference_number,
                'original_quantity' => $entry->original_quantity,
                'remaining_quantity' => $entry->remaining_quantity,
                'available_capacity' => $availableCapacity,
                'unit_price' => $entry->unit_price,
                'created_at' => $entry->created_at,
                'can_accept_restock' => $availableCapacity > 0 ? 'Yes' : 'No'
            ];
        })->toArray()
    ]);

    $remainingToAdd = $this->quantity_restocked;
    $addedInfo = [];

    /**
     * STEP 2: Add to stock entries in LIFO order (newest first)
     */
    foreach ($stockEntries as $entry) {
        if ($remainingToAdd <= 0) break;

        // Calculate available capacity (how much was used and can be restored)
        $availableCapacity = $entry->original_quantity - $entry->remaining_quantity;
        
        // Only proceed if there's available capacity to restore
        if ($availableCapacity > 0) {
            $addToThisEntry = min($remainingToAdd, $availableCapacity);

            $newRemainingQuantity = $entry->remaining_quantity + $addToThisEntry;
            $newCurrentTotalValue = $newRemainingQuantity * $entry->unit_price;

            // Update this stock entry
            \DB::table('material_stock')
                ->where('id', $entry->id)
                ->update([
                    'remaining_quantity' => $newRemainingQuantity,
                    'current_total_value' => $newCurrentTotalValue,
                    'total_used' => max(0, $entry->total_used - $addToThisEntry),
                    'last_movement_at' => now(),
                    'status'=> 'active',
                    'movement_log' => json_encode(array_merge(
                        json_decode($entry->movement_log ?? '[]', true),
                        [[
                            'type' => 'restock_addition',
                            'quantity' => $addToThisEntry,
                            'unit_price' => $entry->unit_price,
                            'total_value' => $addToThisEntry * $entry->unit_price,
                            'restock_reference' => $this->restock_reference,
                            'project_id' => $this->project_id,
                            'timestamp' => now(),
                            'user' => $this->restockedBy->name,
                            'notes' => "Restocked to existing batch",
                            'original_batch_reference' => $entry->reference_number,
                            'previous_remaining' => $entry->remaining_quantity,
                            'new_remaining' => $newRemainingQuantity
                        ]]
                    ))
                ]);

            $addedInfo[] = [
                'batch_reference' => $entry->reference_number,
                'quantity_added' => $addToThisEntry,
                'unit_price' => $entry->unit_price,
                'available_capacity' => $availableCapacity,
                'old_remaining' => $entry->remaining_quantity,
                'new_remaining_quantity' => $newRemainingQuantity,
                'restored_to_full' => $newRemainingQuantity == $entry->original_quantity
            ];

            $remainingToAdd -= $addToThisEntry;
            
            \Log::info("Added to existing entry", [
                'entry_id' => $entry->id,
                'reference' => $entry->reference_number,
                'quantity_added' => $addToThisEntry,
                'remaining_to_add' => $remainingToAdd,
                'old_remaining' => $entry->remaining_quantity,
                'new_remaining' => $newRemainingQuantity
            ]);
        }
    }

    /**
     * STEP 3: Only create new entry if ALL existing entries are completely full
     * and we still have quantity to add
     */
    if ($remainingToAdd > 0) {
        \Log::info("Creating new entry for remaining quantity", [
            'remaining_quantity' => $remainingToAdd,
            'reason' => $stockEntries->count() > 0 ? 
                'All existing entries are at full capacity' : 
                'No existing entries found for this material in this stock'
        ]);
        
        $newEntryId = \DB::table('material_stock')->insertGetId([
            'material_id' => $this->material_id,
            'stock_id' => $this->stock_id,
            'quantity' => $remainingToAdd,
            'original_quantity' => $remainingToAdd,
            'remaining_quantity' => $remainingToAdd,
            'total_used' => 0,
            'unit_price' => $this->unit_price,
            'total_price' => $remainingToAdd * $this->unit_price,
            'current_total_value' => $remainingToAdd * $this->unit_price,
            'status' => 'active',
            'reference_number' => $this->restock_reference,
            'batch_number' => $this->batch_number,
            'supplier' => $this->supplier,
            'expiry_date' => $this->expiry_date,
            'notes' => $this->notes ?? "Restocked from project: {$this->project->name}",
            'last_movement_at' => now(),
            'movement_log' => json_encode([[
                'type' => 'restock_addition',
                'quantity' => $remainingToAdd,
                'unit_price' => $this->unit_price,
                'total_value' => $remainingToAdd * $this->unit_price,
                'restock_reference' => $this->restock_reference,
                'project_id' => $this->project_id,
                'timestamp' => now(),
                'user' => $this->restockedBy->name,
                'notes' => $stockEntries->count() > 0 ? 
                    "New restock entry created - all existing entries at full capacity" :
                    "New restock entry created - no existing entries found"
            ]]),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $addedInfo[] = [
            'batch_reference' => $this->restock_reference,
            'quantity_added' => $remainingToAdd,
            'unit_price' => $this->unit_price,
            'new_remaining_quantity' => $remainingToAdd,
            'note' => 'New entry created'
        ];

        \Log::info("New stock entry created", [
            'new_entry_id' => $newEntryId,
            'reference' => $this->restock_reference,
            'quantity' => $remainingToAdd,
            'unit_price' => $this->unit_price
        ]);
    }

    /**
     * STEP 4: Final audit logging
     */
    \Log::info("LIFO Restock Operation Complete", [
        'restock_reference' => $this->restock_reference,
        'material_id' => $this->material_id,
        'stock_id' => $this->stock_id,
        'total_quantity_restocked' => $this->quantity_restocked,
        'unit_price' => $this->unit_price,
        'added_to_entries' => $addedInfo,
        'remaining_after_filling' => $remainingToAdd,
        'efficiency' => $remainingToAdd > 0 ? 'Partial' : 'Full',
        'existing_entries_used' => count($addedInfo) - ($remainingToAdd > 0 ? 1 : 0),
        'new_entries_created' => $remainingToAdd > 0 ? 1 : 0,
        'note' => 'Restocked in LIFO order - filled existing entries before creating new ones'
    ]);

    return [
        'total_restocked' => $this->quantity_restocked,
        'added_to_existing' => $this->quantity_restocked - $remainingToAdd,
        'added_to_new' => $remainingToAdd,
        'entries_updated' => $addedInfo
    ];
}
    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger'
        ][$this->status] ?? 'secondary';
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }
}