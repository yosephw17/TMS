<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable=[
'name', 'customer_id', 'starting_date', 'ending_date', 'description','location','total_price','status',
'actual_cost', 'material_cost', 'labour_cost', 'transport_cost', 'other_cost', 'cost_last_updated', 
'cost_breakdown', 'budget_variance', 'cost_percentage'
    ];

    protected $casts = [
        'cost_breakdown' => 'array',
        'cost_last_updated' => 'datetime',
        'starting_date' => 'date',
        'ending_date' => 'date',
    ];

    public function serviceDetails()
    {
        return $this->belongsToMany(ServiceDetail::class, 'project_service_detail');
    }   
    public function materials()
    {
        return $this->belongsToMany(Material::class)->withPivot('quantity');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function files()
    {
        return $this->hasMany(ProjectFile::class);
    }   

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'project_team')->withTimestamps();
    }
    public function dailyActivities(){
        return $this->hasMany(DailyActivity::class);
    }
    public function proformas(){
        return $this->hasMany(Proforma::class);
    }
    public function purchaseRequests(){
        return $this->hasMany(PurchaseRequest::class);
    }
    public function proformaImages()
    {
        return $this->hasMany(ProformaImage::class);
    }
    public function restockEntries()
    {
        return $this->hasMany(RestockEntry::class);
    }

    /**
     * Calculate and update project costs from approved purchase requests
     */
    public function calculateAndUpdateCosts()
    {
        $materialCost = 0;
        $labourCost = 0;
        $transportCost = 0;
        $otherCost = 0;
        $costBreakdown = [];

        foreach ($this->purchaseRequests()->where('status', 'approved')->get() as $purchaseRequest) {
            $requestCost = 0;
            
            if ($purchaseRequest->type == 'material_non_stock') {
                $requestCost = $purchaseRequest->non_stock_price * $purchaseRequest->non_stock_quantity;
                $materialCost += $requestCost;
                
            } elseif ($purchaseRequest->type == 'material_stock' && $purchaseRequest->materials->count() > 0) {
                foreach ($purchaseRequest->materials as $material) {
                    $weightedPrice = $material->getWeightedAveragePrice($purchaseRequest->stock_id);
                    $materialItemCost = $material->pivot->quantity * $weightedPrice;
                    $requestCost += $materialItemCost;
                }
                $materialCost += $requestCost;
                
            } elseif ($purchaseRequest->type == 'labour') {
                $requestCost = $purchaseRequest->non_stock_price;
                $labourCost += $requestCost;
                
            } elseif ($purchaseRequest->type == 'transport') {
                $requestCost = $purchaseRequest->non_stock_price;
                $transportCost += $requestCost;
                
            } else {
                $requestCost = $purchaseRequest->non_stock_price ?? 0;
                $otherCost += $requestCost;
            }
            
            // Store detailed breakdown
            $costBreakdown[] = [
                'id' => $purchaseRequest->id,
                'type' => $purchaseRequest->type,
                'cost' => $requestCost,
                'date' => $purchaseRequest->updated_at->toDateString(),
            ];
        }

        // Deduct approved restock costs
        $totalRestockDeduction = 0;
        foreach ($this->restockEntries()->where('status', 'approved')->get() as $restockEntry) {
            $totalRestockDeduction += $restockEntry->total_cost_deducted;
            
            // Add restock entry to cost breakdown
            $costBreakdown[] = [
                'id' => 'restock_' . $restockEntry->id,
                'type' => 'restock_deduction',
                'cost' => -$restockEntry->total_cost_deducted, // Negative for deduction
                'date' => $restockEntry->approved_at->toDateString(),
                'reference' => $restockEntry->restock_reference,
                'material' => $restockEntry->material->name
            ];
        }

        $actualCost = $materialCost + $labourCost + $transportCost + $otherCost - $totalRestockDeduction;
        $budgetVariance = $this->total_price ? ($actualCost - $this->total_price) : 0;
        $costPercentage = $this->total_price ? ($actualCost / $this->total_price) * 100 : 0;

        // Update the project with calculated costs
        $this->update([
            'actual_cost' => $actualCost,
            'material_cost' => $materialCost,
            'labour_cost' => $labourCost,
            'transport_cost' => $transportCost,
            'other_cost' => $otherCost,
            'cost_last_updated' => now(),
            'cost_breakdown' => $costBreakdown,
            'budget_variance' => $budgetVariance,
            'cost_percentage' => $costPercentage,
        ]);

        return $this;
    }

    /**
     * Get cost status color class based on percentage
     */
    public function getCostStatusColorAttribute()
    {
        if ($this->cost_percentage <= 60) {
            return 'text-success';
        } elseif ($this->cost_percentage > 60 && $this->cost_percentage <= 75) {
            return 'text-warning';
        }
        return 'text-danger';
    }

    /**
     * Get total restock deductions for this project
     */
    public function getTotalRestockDeductionsAttribute()
    {
        return $this->restockEntries()->where('status', 'approved')->sum('total_cost_deducted');
    }

    /**
     * Check if costs need recalculation (including restock changes)
     */
    public function costsNeedUpdate()
    {
        if (!$this->cost_last_updated) {
            return true;
        }
        
        // Check if any purchase request was updated after last cost calculation
        $latestPurchaseUpdate = $this->purchaseRequests()
            ->where('status', 'approved')
            ->max('updated_at');
            
        // Check if any restock entry was updated after last cost calculation
        $latestRestockUpdate = $this->restockEntries()
            ->where('status', 'approved')
            ->max('approved_at');
        
        return ($latestPurchaseUpdate && $latestPurchaseUpdate > $this->cost_last_updated) ||
               ($latestRestockUpdate && $latestRestockUpdate > $this->cost_last_updated);
    }

    /**
     * Get project cost summary with restock information
     */
    public function getCostSummary()
    {
        return [
            'budget' => $this->total_price,
            'actual_cost' => $this->actual_cost,
            'material_cost' => $this->material_cost,
            'labour_cost' => $this->labour_cost,
            'transport_cost' => $this->transport_cost,
            'other_cost' => $this->other_cost,
            'total_restock_deductions' => $this->total_restock_deductions,
            'budget_variance' => $this->budget_variance,
            'cost_percentage' => $this->cost_percentage,
            'cost_status' => $this->cost_status_color,
            'last_updated' => $this->cost_last_updated,
            'pending_restocks' => $this->restockEntries()->where('status', 'pending')->count(),
            'approved_restocks' => $this->restockEntries()->where('status', 'approved')->count(),
        ];
    }

    /**
     * Check if project is over budget
     */
    public function isOverBudget()
    {
        return $this->budget_variance > 0;
    }

    /**
     * Get cost efficiency rating
     */
    public function getCostEfficiencyRating()
    {
        if ($this->cost_percentage <= 60) {
            return 'Excellent';
        } elseif ($this->cost_percentage <= 75) {
            return 'Good';
        } elseif ($this->cost_percentage <= 90) {
            return 'Fair';
        } elseif ($this->cost_percentage <= 100) {
            return 'Poor';
        } else {
            return 'Over Budget';
        }
    }
}