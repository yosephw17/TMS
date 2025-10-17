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
     * STEP 1: Get all stock entries for this material in LIFO order (newest first)
     * This is the reverse of FIFO deduction
     */
    $stockEntries = \DB::table('material_stock')
        ->where('material_id', $this->material_id)
        ->where('stock_id', $this->stock_id)
        ->where('status', 'active')
        ->orderBy('created_at', 'desc') // LIFO order - newest first
        ->orderBy('id', 'desc')
        ->get();

    $remainingToAdd = $this->quantity_restocked;
    $addedInfo = [];

    /**
     * STEP 2: Add to stock entries in LIFO order (newest first)
     */
    foreach ($stockEntries as $entry) {
        if ($remainingToAdd <= 0) break;

        // Calculate how much can be added to this entry (up to original quantity)
        $availableCapacity = $entry->original_quantity - $entry->remaining_quantity;
        $addToThisEntry = min($remainingToAdd, $availableCapacity);

        if ($addToThisEntry > 0) {
            $newRemainingQuantity = $entry->remaining_quantity + $addToThisEntry;
            $newCurrentTotalValue = $newRemainingQuantity * $entry->unit_price;

            // Update this stock entry
            \DB::table('material_stock')
                ->where('id', $entry->id)
                ->update([
                    'remaining_quantity' => $newRemainingQuantity,
                    'current_total_value' => $newCurrentTotalValue,
                    'total_used' => $entry->total_used - $addToThisEntry, // Reduce total used
                    'last_movement_at' => now(),
                    'movement_log' => json_encode(array_merge(
                        json_decode($entry->movement_log ?? '[]', true),
                        [[
                            'type' => 'restock_addition',
                            'quantity' => $addToThisEntry,
                            'unit_price' => $this->unit_price,
                            'total_value' => $addToThisEntry * $this->unit_price,
                            'restock_reference' => $this->restock_reference,
                            'project_id' => $this->project_id,
                            'timestamp' => now(),
                            'user' => $this->restockedBy->name,
                            'notes' => "Restocked to existing batch - Reference: {$entry->reference_number}",
                            'original_batch_reference' => $entry->reference_number
                        ]]
                    ))
                ]);

            $addedInfo[] = [
                'batch_reference' => $entry->reference_number,
                'quantity_added' => $addToThisEntry,
                'unit_price' => $entry->unit_price,
                'new_remaining_quantity' => $newRemainingQuantity
            ];

            $remainingToAdd -= $addToThisEntry;
        }
    }

    /**
     * STEP 3: If there's still remaining quantity after filling existing entries,
     * create a new stock entry with the restock reference
     */
    if ($remainingToAdd > 0) {
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
                'notes' => "New restock entry created"
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
    }

    // Log the restock operation for audit
    \Log::info("LIFO Restock Operation", [
        'restock_reference' => $this->restock_reference,
        'material_id' => $this->material_id,
        'stock_id' => $this->stock_id,
        'total_quantity_restocked' => $this->quantity_restocked,
        'unit_price' => $this->unit_price,
        'added_to_entries' => $addedInfo,
        'remaining_after_filling' => $remainingToAdd,
        'note' => 'Restocked in LIFO order (reverse of FIFO deduction)'
    ]);
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