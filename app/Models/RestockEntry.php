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
        // Find or create material_stock entry
        $materialStock = \DB::table('material_stock')
            ->where('material_id', $this->material_id)
            ->where('stock_id', $this->stock_id)
            ->first();

        if ($materialStock) {
            // Update existing entry
            \DB::table('material_stock')
                ->where('id', $materialStock->id)
                ->update([
                    'quantity' => $materialStock->quantity + $this->quantity_restocked,
                    'remaining_quantity' => $materialStock->remaining_quantity + $this->quantity_restocked,
                    'original_quantity' => $materialStock->original_quantity + $this->quantity_restocked,
                    'current_total_value' => ($materialStock->remaining_quantity + $this->quantity_restocked) * $this->unit_price,
                    'last_movement_at' => now(),
                    'movement_log' => json_encode(array_merge(
                        json_decode($materialStock->movement_log ?? '[]', true),
                        [[
                            'type' => 'restock_addition',
                            'quantity' => $this->quantity_restocked,
                            'unit_price' => $this->unit_price,
                            'total_value' => $this->quantity_restocked * $this->unit_price,
                            'restock_reference' => $this->restock_reference,
                            'project_id' => $this->project_id,
                            'timestamp' => now(),
                            'user' => $this->restockedBy->name,
                            'notes' => "Restocked from project: {$this->project->name}"
                        ]]
                    ))
                ]);
        } else {
            // Create new entry
            \DB::table('material_stock')->insert([
                'material_id' => $this->material_id,
                'stock_id' => $this->stock_id,
                'quantity' => $this->quantity_restocked, // FIXED: Added missing quantity field
                'original_quantity' => $this->quantity_restocked,
                'remaining_quantity' => $this->quantity_restocked,
                'total_used' => 0,
                'unit_price' => $this->unit_price,
                'total_price' => $this->quantity_restocked * $this->unit_price,
                'current_total_value' => $this->quantity_restocked * $this->unit_price,
                'status' => 'active',
                'reference_number' => $this->restock_reference,
                'notes' => "Restocked from project: {$this->project->name}",
                'last_movement_at' => now(),
                'movement_log' => json_encode([[
                    'type' => 'restock_addition',
                    'quantity' => $this->quantity_restocked,
                    'unit_price' => $this->unit_price,
                    'total_value' => $this->quantity_restocked * $this->unit_price,
                    'restock_reference' => $this->restock_reference,
                    'project_id' => $this->project_id,
                    'timestamp' => now(),
                    'user' => $this->restockedBy->name,
                    'notes' => "Initial restock from project"
                ]]),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
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