<!-- Expenses Tab -->
<div class="tab-pane fade" id="expenses{{ $project->id }}" role="tabpanel"
    aria-labelledby="expenses-tab{{ $project->id }}">
    <div class="mt-3">
        @php
            // Ensure costs are up to date
            if ($project->costsNeedUpdate()) {
                $project->calculateAndUpdateCosts();
            }
        @endphp

        @if ($project->purchaseRequests->count() > 0)
            <!-- Cost Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h6>Material Costs</h6>
                            <h4>${{ number_format($project->material_cost ?? 0, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h6>Labour Costs</h6>
                            <h4>${{ number_format($project->labour_cost ?? 0, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h6>Transport Costs</h6>
                            <h4>${{ number_format($project->transport_cost ?? 0, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-secondary text-white">
                        <div class="card-body text-center">
                            <h6>Other Costs</h6>
                            <h4>${{ number_format($project->other_cost ?? 0, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Manual Refresh Button -->
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div>
                    @if($project->cost_last_updated)
                        <small class="text-muted">Last updated: {{ $project->cost_last_updated->diffForHumans() }}</small>
                    @else
                        <small class="text-warning">Costs not calculated yet</small>
                    @endif
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshCosts({{ $project->id }})">
                    <i class="fas fa-sync-alt"></i> Refresh Costs
                </button>
            </div>

            <!-- Detailed Purchase Requests Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Request By</th>
                            <th>Details</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($project->purchaseRequests->where('status', 'approved') as $key => $purchaseRequest)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $purchaseRequest->type)) }}</td>
                                <td>{{ $purchaseRequest->user->name }}</td>

                                @if ($purchaseRequest->type == 'material_non_stock')
                                    <td>{{ $purchaseRequest->non_stock_name }} -
                                        ${{ $purchaseRequest->non_stock_price }}</td>
                                    <td>{{ $purchaseRequest->non_stock_quantity }}</td>
                                    @php
                                        $totalPrice = $purchaseRequest->non_stock_price * $purchaseRequest->non_stock_quantity;
                                    @endphp
                                    <td>${{ number_format($totalPrice, 2) }}</td>
                                @elseif ($purchaseRequest->type == 'material_stock' && $purchaseRequest->materials->count() > 0)
                                    <td>
                                        @foreach ($purchaseRequest->materials as $material)
                                            <p>{{ $material->name }} ({{ $material->unit_of_measurement }})</p>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($purchaseRequest->materials as $material)
                                            <p>{{ $material->pivot->quantity }}</p>
                                        @endforeach
                                    </td>
                                    <td>
                                        @php
                                            $stockTotal = 0;
                                        @endphp
                                        @foreach ($purchaseRequest->materials as $material)
                                            @php
                                                // Use weighted average price for professional cost calculation
                                                $weightedPrice = $material->getWeightedAveragePrice($purchaseRequest->stock_id);
                                                $materialCost = $material->pivot->quantity * $weightedPrice;
                                                $stockTotal += $materialCost;
                                            @endphp
                                            <p>${{ number_format($materialCost, 2) }} 
                                               <small class="text-muted">(@ ${{ number_format($weightedPrice, 2) }}/{{ $material->unit_of_measurement }})</small>
                                            </p>
                                        @endforeach
                                        <p><strong>${{ number_format($stockTotal, 2) }}</strong></p>
                                    </td>
                                @elseif ($purchaseRequest->type == 'labour' || $purchaseRequest->type == 'transport')
                                    <td>{{ $purchaseRequest->details }}</td>
                                    <td>{{ $purchaseRequest->non_stock_price }}</td>
                                    <td>${{ number_format($purchaseRequest->non_stock_price, 2) }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-info">
                            <td colspan="5" class="text-right"><strong>Overall Total:</strong></td>
                            <td><strong>${{ number_format($project->actual_cost ?? 0, 2) }}</strong></td>
                        </tr>
                        <tr class="table-light">
                            <td colspan="5" class="text-right"><strong>Budget Variance:</strong></td>
                            <td>
                                @if(($project->budget_variance ?? 0) > 0)
                                    <strong class="text-danger">+${{ number_format($project->budget_variance, 2) }} (Over Budget)</strong>
                                @elseif(($project->budget_variance ?? 0) < 0)
                                    <strong class="text-success">${{ number_format($project->budget_variance, 2) }} (Under Budget)</strong>
                                @else
                                    <strong class="text-info">$0.00 (On Budget)</strong>
                                @endif
                            </td>
                        </tr>
                        <tr class="table-light">
                            <td colspan="5" class="text-right"><strong>Used Cost Percentage:</strong></td>
                            <td>
                                <strong class="{{ $project->cost_status_color ?? 'text-muted' }}">{{ number_format($project->cost_percentage ?? 0, 2) }}%</strong>
                            </td>
                        </tr>
                        @if($project->cost_last_updated)
                        <tr class="table-light">
                            <td colspan="5" class="text-right"><strong>Last Updated:</strong></td>
                            <td>
                                <small class="text-muted">{{ $project->cost_last_updated->format('M d, Y H:i') }}</small>
                            </td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>

            <!-- Restock Management Section -->
            <div class="mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6><i class="fas fa-undo text-success"></i> Restock Management</h6>
                    <button type="button" class="btn btn-success btn-sm" 
                            data-bs-toggle="modal" data-bs-target="#restockModal{{ $project->id }}"
                            data-toggle="modal" data-target="#restockModal{{ $project->id }}"
                            onclick="openRestockModal({{ $project->id }})">
                        <i class="fas fa-plus"></i> Request Restock
                    </button>
                </div>

                @if($project->restockEntries && $project->restockEntries->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>Reference</th>
                                    <th>Material</th>
                                    <th>Quantity</th>
                                    <th>Cost Recovered</th>
                                    <th>Status</th>
                                    <th>Restocked By</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($project->restockEntries->sortByDesc('created_at') as $restock)
                                    <tr>
                                        <td><small class="font-weight-bold">{{ $restock->restock_reference }}</small></td>
                                        <td>{{ $restock->material->name }}</td>
                                        <td>{{ $restock->quantity_restocked }} {{ $restock->material->unit_of_measurement }}</td>
                                        <td>${{ number_format($restock->total_cost_deducted, 2) }}</td>
                                        <td><span class="badge badge-{{ $restock->status_color }}">{{ ucfirst($restock->status) }}</span></td>
                                        <td>{{ $restock->restockedBy->name }}</td>
                                        <td>{{ $restock->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if($restock->status === 'pending')
                                                
                                                    <div class="btn-group btn-group-sm">
                                                        <form method="POST" action="{{ route('restock.approve', $restock->id) }}" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-xs" onclick="return confirm('Approve this restock request?')">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <button type="button" class="btn btn-danger btn-xs" onclick="showRejectModal({{ $restock->id }})">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                               
                                            @endif
                                            <button type="button" class="btn btn-info btn-xs" onclick="showRestockDetails({{ $restock->id }}, '{{ addslashes($restock->reason) }}', '{{ addslashes($restock->notes ?? '') }}')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Restock Summary Cards -->
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6>Total Cost Recovered</h6>
                                    <h4>${{ number_format($project->total_restock_deductions ?? 0, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h6>Pending Approvals</h6>
                                    <h4>{{ $project->restockEntries ? $project->restockEntries->where('status', 'pending')->count() : 0 }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h6>Approved Restocks</h6>
                                    <h4>{{ $project->restockEntries ? $project->restockEntries->where('status', 'approved')->count() : 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-undo fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No restock requests yet.</p>
                    </div>
                @endif
            </div>

        @else
            <div class="text-center py-5">
                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                <p class="text-muted">No costs for this project yet.</p>
            </div>
        @endif
    </div>
</div>

<!-- Restock Modal -->
<div class="modal fade" id="restockModal{{ $project->id }}" tabindex="-1" role="dialog" aria-labelledby="restockModalLabel{{ $project->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="restockModalLabel{{ $project->id }}">Request Material Restock - {{ $project->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('restock.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Purchase Request</label>
                                <select name="purchase_request_id" class="form-control" required onchange="loadMaterials(this.value, {{ $project->id }})">
                                    <option value="">Select Purchase Request</option>
                                    @foreach($project->purchaseRequests->where('status', 'approved')->where('type', 'material_stock') as $pr)
                                        <option value="{{ $pr->id }}">
                                            PR-{{ $pr->id }} - {{ $pr->created_at->format('M d, Y') }}
                                            ({{ $pr->materials->count() }} materials)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Material</label>
                                <select name="material_id" class="form-control" required onchange="loadMaterialDetails(this.value, {{ $project->id }})">
                                    <option value="">Select Material</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Stock Location</label>
                                <select name="stock_id" class="form-control" required>
                                    <option value="">Select Stock</option>
                                    @foreach(\App\Models\Stock::all() as $stock)
                                        <option value="{{ $stock->id }}">{{ $stock->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Quantity to Restock</label>
                                <input type="number" name="quantity_restocked" class="form-control" step="0.001" required>
                                <small class="form-text text-muted">Available: <span id="availableQuantity{{ $project->id }}">-</span></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Unit Price</label>
                                <input type="number" name="unit_price" class="form-control" step="0.01" required>
                                <small class="form-text text-muted">Avg Price: $<span id="avgPrice{{ $project->id }}">-</span></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Reason for Restock <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Explain why this material needs to be restocked..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Additional Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Any additional information..."></textarea>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> This request will need approval before the cost is deducted from the project and the material is added back to stock.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Submit Restock Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Restock Details Modal -->
<div class="modal fade" id="restockDetailsModal" tabindex="-1" role="dialog" aria-labelledby="restockDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="restockDetailsModalLabel">Restock Details</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><strong>Reason:</strong></label>
                    <p id="restockReason"></p>
                </div>
                <div class="form-group">
                    <label><strong>Notes:</strong></label>
                    <p id="restockNotes"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function refreshCosts(projectId) {
    location.reload();
}

function openRestockModal(projectId) {
    const modalId = `restockModal${projectId}`;
    const modal = document.getElementById(modalId);
    
    if (!modal) {
        console.error('Modal not found:', modalId);
        return;
    }
    
    // Try Bootstrap 5 first
    if (typeof bootstrap !== 'undefined') {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    } 
    // Fallback to Bootstrap 4
    else if (typeof $ !== 'undefined' && $.fn.modal) {
        $(`#${modalId}`).modal('show');
    }
    // Manual fallback
    else {
        modal.style.display = 'block';
        modal.classList.add('show');
        document.body.classList.add('modal-open');
        
        // Create backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'modal-backdrop-' + projectId;
        document.body.appendChild(backdrop);
        
        // Close on backdrop click
        backdrop.onclick = function() {
            closeRestockModal(projectId);
        };
    }
}

function closeRestockModal(projectId) {
    const modalId = `restockModal${projectId}`;
    const modal = document.getElementById(modalId);
    
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
        
        // Remove backdrop
        const backdrop = document.getElementById('modal-backdrop-' + projectId);
        if (backdrop) {
            backdrop.remove();
        }
    }
}

function loadMaterials(purchaseRequestId, projectId) {
    if (!purchaseRequestId) {
        document.querySelector(`#restockModal${projectId} select[name="material_id"]`).innerHTML = '<option value="">Select Material</option>';
        return;
    }

    fetch(`/restock/materials/${purchaseRequestId}`)
        .then(response => response.json())
        .then(materials => {
            const materialSelect = document.querySelector(`#restockModal${projectId} select[name="material_id"]`);
            materialSelect.innerHTML = '<option value="">Select Material</option>';
            
            materials.forEach(material => {
                const option = document.createElement('option');
                option.value = material.id;
                option.textContent = `${material.name} (Used: ${material.quantity_used} ${material.unit_of_measurement})`;
                option.dataset.quantity = material.quantity_used;
                option.dataset.price = material.weighted_avg_price;
                option.dataset.stockId = material.stock_id;
                materialSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading materials:', error);
            alert('Error loading materials. Please try again.');
        });
}

function loadMaterialDetails(materialId, projectId) {
    const materialSelect = document.querySelector(`#restockModal${projectId} select[name="material_id"]`);
    const selectedOption = materialSelect.options[materialSelect.selectedIndex];
    
    if (selectedOption && selectedOption.dataset.quantity) {
        document.getElementById(`availableQuantity${projectId}`).textContent = 
            selectedOption.dataset.quantity + ' ' + selectedOption.textContent.match(/\(([^)]+)\)/)[1].split(' ').pop();
        document.getElementById(`avgPrice${projectId}`).textContent = 
            parseFloat(selectedOption.dataset.price).toFixed(2);
        document.querySelector(`#restockModal${projectId} input[name="unit_price"]`).value = 
            selectedOption.dataset.price;
        document.querySelector(`#restockModal${projectId} select[name="stock_id"]`).value = 
            selectedOption.dataset.stockId;
    }
}

function showRestockDetails(restockId, reason, notes) {
    document.getElementById('restockReason').textContent = reason || 'No reason provided';
    document.getElementById('restockNotes').textContent = notes || 'No additional notes';
    
    // Try to show modal
    if (typeof bootstrap !== 'undefined') {
        const modal = new bootstrap.Modal(document.getElementById('restockDetailsModal'));
        modal.show();
    } else if (typeof $ !== 'undefined' && $.fn.modal) {
        $('#restockDetailsModal').modal('show');
    } else {
        const modal = document.getElementById('restockDetailsModal');
        modal.style.display = 'block';
        modal.classList.add('show');
    }
}

function showRejectModal(restockId) {
    const reason = prompt('Please provide a reason for rejection:');
    if (reason && reason.trim()) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/restock/${restockId}/reject`;
        form.innerHTML = `
            @csrf
            <input type="hidden" name="rejection_reason" value="${reason.replace(/"/g, '&quot;')}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modal when clicking close buttons
document.addEventListener('click', function(e) {
    if (e.target.matches('[data-dismiss="modal"], [data-bs-dismiss="modal"]')) {
        const modal = e.target.closest('.modal');
        if (modal) {
            const projectId = modal.id.replace('restockModal', '');
            if (projectId) {
                closeRestockModal(projectId);
            } else {
                modal.style.display = 'none';
                modal.classList.remove('show');
                document.body.classList.remove('modal-open');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            }
        }
    }
});
</script>