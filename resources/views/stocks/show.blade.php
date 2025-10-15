@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Stock: {{ $stock->name }} ({{ $stock->location }})</h2>
            </div>
            <div class="pull-right">
                @can('stock-add-material')
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                        Add Material
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Materials in Stock</h5>
                <div>
                    <button class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#printModal">
                        <i class="fas fa-print me-1"></i>Print Materials
                    </button>
                    <span class="badge bg-info">{{ $stock->materials->count() }} Total Entries</span>
                </div>
            </div>
            
            @php
                // Group materials by material ID to show all reference entries together
                $groupedMaterials = $stock->materials->groupBy('id');
            @endphp
            
            <div class="accordion" id="materialsAccordion">
                @foreach ($groupedMaterials as $materialId => $materialEntries)
                    @php
                        $material = $materialEntries->first();
                        $totalOriginal = $materialEntries->sum('pivot.original_quantity');
                        $totalRemaining = $materialEntries->sum('pivot.remaining_quantity');
                        $totalUsed = $materialEntries->sum('pivot.total_used');
                        $totalValue = $materialEntries->sum('pivot.current_total_value');
                        // Calculate simple average of unit prices (not weighted by quantity)
                        // Only include ACTIVE entries in the average calculation
                        $totalPrice = 0;
                        $entryCount = 0;
                        foreach ($materialEntries as $entry) {
                            if ($entry->pivot->unit_price && $entry->pivot->status === 'active') {
                                $totalPrice += $entry->pivot->unit_price;
                                $entryCount++;
                            }
                        }
                        $averagePrice = $entryCount > 0 ? $totalPrice / $entryCount : 0;
                        $activeEntries = $materialEntries->where('pivot.status', 'active')->count();
                        $depletedEntries = $materialEntries->where('pivot.status', 'depleted')->count();
                    @endphp
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $materialId }}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse{{ $materialId }}" aria-expanded="false" 
                                    aria-controls="collapse{{ $materialId }}">
                                <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-cube text-primary me-3"></i>
                                        <div>
                                            <strong>{{ $material->name }}</strong>
                                            @if($material->color)
                                                <span class="badge bg-secondary ms-2">{{ $material->color }}</span>
                                            @endif
                                            <div class="small text-muted">{{ $material->unit_of_measurement }}</div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success">{{ $totalRemaining }} Available</div>
                                        <div class="small text-muted">
                                            Original: {{ $totalOriginal }} | Used: {{ $totalUsed }}
                                        </div>
                                        <div class="small text-muted">
                                            <span class="badge bg-success">{{ $activeEntries }} Active</span>
                                            @if($depletedEntries > 0)
                                                <span class="badge bg-danger">{{ $depletedEntries }} Depleted</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse{{ $materialId }}" class="accordion-collapse collapse" 
                             aria-labelledby="heading{{ $materialId }}" data-bs-parent="#materialsAccordion">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Status</th>
                                                <th>Reference</th>
                                                <th>Batch</th>
                                                <th>Supplier</th>
                                                <th>Original Qty</th>
                                                <th>Remaining</th>
                                                <th>Used</th>
                                                <th>Unit Price</th>
                                                <th>Current Value</th>
                                                <th>Expiry</th>
                                                <th>Last Movement</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($materialEntries as $index => $entry)
                                                <tr class="{{ $entry->pivot->status === 'depleted' ? 'table-secondary' : '' }}">
                                                    <td>
                                                        @if($entry->pivot->status === 'active')
                                                            <span class="badge bg-success">Active</span>
                                                        @elseif($entry->pivot->status === 'depleted')
                                                            <span class="badge bg-danger">Depleted</span>
                                                        @else
                                                            <span class="badge bg-warning">Reserved</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($entry->pivot->reference_number)
                                                            <span class="badge bg-primary">{{ $entry->pivot->reference_number }}</span>
                                                            <button class="btn btn-sm btn-outline-secondary ms-1" 
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#referenceModal{{ $entry->id }}_{{ $index }}"
                                                                    title="View Reference">
                                                                <i class="fas fa-info-circle"></i>
                                                            </button>
                                                        @else
                                                            <span class="text-muted small">No Reference</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $entry->pivot->batch_number ?? '-' }}
                                                    </td>
                                                    <td>
                                                        {{ $entry->pivot->supplier ?? '-' }}
                                                    </td>
                                                    <td>
                                                        <strong>{{ $entry->pivot->original_quantity }}</strong>
                                                        <small class="text-muted">{{ $material->unit_of_measurement }}</small>
                                                    </td>
                                                    <td>
                                                        <strong class="text-success">{{ $entry->pivot->remaining_quantity ?? $entry->pivot->quantity }}</strong>
                                                        <small class="text-muted">{{ $material->unit_of_measurement }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="text-danger">{{ $entry->pivot->total_used ?? 0 }}</span>
                                                        <small class="text-muted">{{ $material->unit_of_measurement }}</small>
                                                    </td>
                                                    <td>
                                                        @if($entry->pivot->unit_price)
                                                            <span class="text-success fw-bold">${{ number_format($entry->pivot->unit_price, 2) }}</span>
                                                        @else
                                                            <span class="text-muted">No Price</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($entry->pivot->current_total_value)
                                                            <span class="text-primary fw-bold">${{ number_format($entry->pivot->current_total_value, 2) }}</span>
                                                        @else
                                                            <span class="text-muted">No Value</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($entry->pivot->expiry_date)
                                                            <small class="{{ \Carbon\Carbon::parse($entry->pivot->expiry_date)->isPast() ? 'text-danger' : 'text-muted' }}">
                                                                {{ \Carbon\Carbon::parse($entry->pivot->expiry_date)->format('M d, Y') }}
                                                            </small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            {{ $entry->pivot->last_movement_at ? \Carbon\Carbon::parse($entry->pivot->last_movement_at)->diffForHumans() : 'Never' }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            @if($entry->pivot->remaining_quantity > 0)
                                                                <button class="btn btn-sm btn-outline-warning" 
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#useMaterialModal{{ $entry->id }}_{{ $index }}"
                                                                        title="Use Material">
                                                                    <i class="fas fa-minus"></i>
                                                                </button>
                                                            @endif
                                                            <button class="btn btn-sm btn-outline-info" 
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#viewMovementModal{{ $entry->id }}_{{ $index }}"
                                                                    title="View Movement History">
                                                                <i class="fas fa-history"></i>
                                                            </button>
                                                            @can('stock-remove-material')
                                                                <button class="btn btn-sm btn-outline-danger" 
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#removeMaterialModal{{ $entry->id }}_{{ $index }}"
                                                                        title="Remove Entry">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <td colspan="4"><strong>Total for {{ $material->name }}:</strong></td>
                                                <td><strong>{{ $totalOriginal }} {{ $material->unit_of_measurement }}</strong></td>
                                                <td><strong class="text-success">{{ $totalRemaining }} {{ $material->unit_of_measurement }}</strong></td>
                                                <td><strong class="text-danger">{{ $totalUsed }} {{ $material->unit_of_measurement }}</strong></td>
                                                <td><strong>Avg: ${{ number_format($averagePrice, 2) }}</strong></td>
                                                <td><strong>${{ number_format($totalValue, 2) }}</strong></td>
                                                <td colspan="3"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modals for each entry -->
                    @foreach ($materialEntries as $index => $entry)
                        <!-- Use Material Modal -->
                        <div class="modal fade" id="useMaterialModal{{ $entry->id }}_{{ $index }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Use Material</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-info">
                                            <strong>Material:</strong> {{ $material->name }}<br>
                                            <strong>Reference:</strong> {{ $entry->pivot->reference_number ?? 'No Reference' }}<br>
                                            <strong>Available:</strong> {{ $entry->pivot->remaining_quantity ?? $entry->pivot->quantity }} {{ $material->unit_of_measurement }}
                                        </div>
                                        <form action="{{ route('stocks.useMaterial', [$stock->id, $entry->id, $entry->pivot->id ?? 0]) }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">Quantity to Use</label>
                                                    <input type="number" name="quantity_used" class="form-control" min="1"
                                                        max="{{ $entry->pivot->remaining_quantity ?? $entry->pivot->quantity }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Project (Optional)</label>
                                                    <select name="project_id" class="form-control">
                                                        <option value="">Select Project</option>
                                                        <!-- Add projects here -->
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <label class="form-label">Reason for Use</label>
                                                <input type="text" name="reason" class="form-control" 
                                                    placeholder="e.g., Project construction, maintenance, etc." required>
                                            </div>
                                            <div class="text-center mt-3">
                                                <button type="submit" class="btn btn-warning">Use Material</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Movement History Modal -->
                        <div class="modal fade" id="viewMovementModal{{ $entry->id }}_{{ $index }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Movement History</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        @php
                                            $movements = json_decode($entry->pivot->movement_log, true) ?? [];
                                        @endphp
                                        @if(!empty($movements))
                                            <div class="timeline">
                                                @foreach($movements as $movement)
                                                    <div class="timeline-item">
                                                        <div class="timeline-marker bg-{{ $movement['type'] === 'initial_stock' ? 'success' : 'warning' }}"></div>
                                                        <div class="timeline-content">
                                                            <h6>{{ ucfirst(str_replace('_', ' ', $movement['type'])) }}</h6>
                                                            <p class="mb-1">
                                                                <strong>Quantity:</strong> {{ $movement['quantity'] }} {{ $material->unit_of_measurement }}
                                                                @if(isset($movement['remaining_after']))
                                                                    | <strong>Remaining:</strong> {{ $movement['remaining_after'] }}
                                                                @endif
                                                            </p>
                                                            @if(isset($movement['reason']))
                                                                <p class="mb-1"><strong>Reason:</strong> {{ $movement['reason'] }}</p>
                                                            @endif
                                                            <small class="text-muted">
                                                                {{ \Carbon\Carbon::parse($movement['timestamp'])->format('M d, Y H:i') }} 
                                                                by {{ $movement['user'] }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-muted">No movement history available.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reference Modal -->
                        <div class="modal fade" id="referenceModal{{ $entry->id }}_{{ $index }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Reference Number: {{ $entry->pivot->reference_number }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-info">
                                            <strong>Material:</strong> {{ $material->name }}<br>
                                            <strong>Reference:</strong> {{ $entry->pivot->reference_number }}<br>
                                            <strong>Available:</strong> {{ $entry->pivot->remaining_quantity ?? $entry->pivot->quantity }} {{ $material->unit_of_measurement }}
                                        </div>
                                        <button class="btn btn-sm btn-outline-secondary" 
                                                onclick="window.printReference('{{ $entry->pivot->reference_number }}')"
                                                title="Print this reference">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Remove Material Modal -->
                        <div class="modal fade" id="removeMaterialModal{{ $entry->id }}_{{ $index }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Remove Material Entry</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Warning:</strong> This action cannot be undone. This will permanently remove this material entry from the stock.
                                        </div>
                                        <div class="alert alert-info">
                                            <strong>Material:</strong> {{ $material->name }}<br>
                                            <strong>Reference:</strong> {{ $entry->pivot->reference_number ?? 'No Reference' }}<br>
                                            <strong>Remaining Quantity:</strong> {{ $entry->pivot->remaining_quantity ?? $entry->pivot->quantity }} {{ $material->unit_of_measurement }}<br>
                                            <strong>Current Value:</strong> ${{ number_format($entry->pivot->current_total_value, 2) }}
                                        </div>
                                        <form action="{{ route('stocks.removeMaterial', [$stock->id, $entry->id, $entry->pivot->id ?? 0]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="mb-3">
                                                <label class="form-label">Reason for Removal</label>
                                                <textarea name="reason" class="form-control" rows="3" 
                                                          placeholder="Please provide a reason for removing this material entry..." required></textarea>
                                            </div>
                                            <div class="text-center">
                                                <button type="submit" class="btn btn-danger">Confirm Removal</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
            
            @if($groupedMaterials->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Materials in Stock</h5>
                    <p class="text-muted">Add some materials to get started.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Add Material Modal -->
    <div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMaterialModalLabel">Add Materials to Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('stocks.addMaterial', $stock->id) }}" method="POST" id="addMaterialForm">
                        @csrf
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Reference Numbers:</strong> Each material will automatically get a unique reference number when added to stock
                        </div>
                        
                        <!-- Search and Filter Section -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Search Materials</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            <input type="text" id="materialSearch" class="form-control" 
                                                   placeholder="Search by material name, color, or description...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Filter by Category</label>
                                        <select id="categoryFilter" class="form-select">
                                            <option value="">All Categories</option>
                                            @php
                                                $categories = $materials->pluck('category')->filter()->unique()->sort();
                                            @endphp
                                            @foreach($categories as $category)
                                                <option value="{{ $category }}">{{ $category }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Quick Actions</label>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-outline-primary btn-sm flex-fill" 
                                                    onclick="selectAllMaterials()">
                                                <i class="fas fa-check-square me-1"></i>Select All
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" 
                                                    onclick="deselectAllMaterials()">
                                                <i class="fas fa-times-circle me-1"></i>Deselect All
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Selected Counter -->
                                <div class="mt-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-primary" id="selectedCount">0 materials selected</span>
                                        <span class="badge bg-success ms-2" id="totalValue">Total: $0.00</span>
                                    </div>
                                    <div>
                                        <small class="text-muted">
                                            Showing <span id="visibleCount">{{ $materials->count() }}</span> of {{ $materials->count() }} materials
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Materials Grid -->
                        <div class="form-group">
                            <div class="row" id="materialsGrid">
                                @foreach ($materials as $material)
                                    <div class="col-lg-6 col-xl-4 mb-3 material-item" 
                                         data-name="{{ strtolower($material->name) }}"
                                         data-color="{{ strtolower($material->color ?? '') }}"
                                         data-category="{{ strtolower($material->category ?? '') }}"
                                         data-description="{{ strtolower($material->description ?? '') }}">
                                        <div class="card border h-100 material-card">
                                            <div class="card-body p-3">
                                                <!-- Material Selection Header -->
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input material-checkbox" type="checkbox" 
                                                               name="materials[]" value="{{ $material->id }}" 
                                                               id="material{{ $material->id }}"
                                                               onchange="toggleMaterialFields({{ $material->id }})">
                                                        <label class="form-check-label fw-bold" for="material{{ $material->id }}">
                                                            {{ $material->name }}
                                                        </label>
                                                    </div>
                                                    <div class="text-end">
                                                        @if($material->color)
                                                            <span class="badge bg-secondary">{{ $material->color }}</span>
                                                        @endif
                                                        @if($material->category)
                                                            <span class="badge bg-light text-dark small">{{ $material->category }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <!-- Material Details -->
                                                <div class="small text-muted mb-2">
                                                    <div><i class="fas fa-ruler me-1"></i>{{ $material->unit_of_measurement }}</div>
                                                    @if($material->description)
                                                        <div class="mt-1">{{ Str::limit($material->description, 60) }}</div>
                                                    @endif
                                                </div>
                                                
                                                <!-- Pricing Fields (Hidden by default) -->
                                                <div id="fields{{ $material->id }}" class="material-fields" style="display: none;">
                                                    <hr class="my-2">
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <label class="form-label small fw-bold">Quantity <span class="text-danger">*</span></label>
                                                            <input type="number" name="quantities[{{ $material->id }}]"
                                                                   class="form-control form-control-sm" placeholder="Qty" 
                                                                   min="1" step="1" onchange="calculateTotal({{ $material->id }})">
                                                            <small class="text-muted">{{ $material->unit_of_measurement }}</small>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label small fw-bold">Unit Price <span class="text-danger">*</span></label>
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text">$</span>
                                                                <input type="number" name="unit_prices[{{ $material->id }}]"
                                                                       class="form-control" placeholder="0.00" 
                                                                       step="0.01" min="0" onchange="calculateTotal({{ $material->id }})">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row g-2 mt-1">
                                                        <div class="col-6">
                                                            <label class="form-label small fw-bold">Total Price</label>
                                                            <input type="text" id="total{{ $material->id }}" 
                                                                   class="form-control form-control-sm bg-light" readonly 
                                                                   placeholder="$0.00">
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label small fw-bold">Batch No.</label>
                                                            <input type="text" name="batch_numbers[{{ $material->id }}]"
                                                                   class="form-control form-control-sm" 
                                                                   placeholder="Optional">
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <label class="form-label small fw-bold">Supplier & Notes</label>
                                                        <textarea name="notes[{{ $material->id }}]" 
                                                                class="form-control form-control-sm" rows="2" 
                                                                placeholder="Supplier, quality notes, etc."></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-transparent p-2">
                                                <small class="text-muted">
                                                    ID: {{ $material->id }} • Ref: Auto-generated
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- No Results Message -->
                            <div id="noResults" class="text-center py-5" style="display: none;">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Materials Found</h5>
                                <p class="text-muted">Try adjusting your search terms or filters</p>
                                <button type="button" class="btn btn-outline-primary" onclick="clearSearch()">
                                    Clear Search
                                </button>
                            </div>
                        </div>
                        
                        <!-- Summary and Submit -->
                        <div class="card mt-4 bg-light">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h6 class="mb-1">Selection Summary</h6>
                                        <div class="small text-muted" id="selectionSummary">
                                            No materials selected
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-plus-circle me-2"></i>Add Selected Materials
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Modal -->
    <div class="modal fade" id="printModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-print me-2"></i>Print Materials</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Print Options</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary" onclick="window.printAllStock()">
                                    <i class="fas fa-print me-2"></i>Print All Materials
                                </button>
                                <button class="btn btn-outline-success" onclick="window.printActiveOnly()">
                                    <i class="fas fa-check-circle me-2"></i>Print Active Materials Only
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Print by Reference Number</h6>
                            <div class="reference-list" style="max-height: 300px; overflow-y: auto;">
                                @php
                                    $uniqueReferences = $stock->materials
                                        ->pluck('pivot.reference_number')
                                        ->filter()
                                        ->unique()
                                        ->sort();
                                @endphp
                                
                                @if($uniqueReferences->isNotEmpty())
                                    @foreach($uniqueReferences as $reference)
                                        @php
                                            $refMaterials = $stock->materials->where('pivot.reference_number', $reference);
                                            $totalMaterials = $refMaterials->count();
                                            $totalValue = $refMaterials->sum('pivot.current_total_value');
                                            $firstMaterial = $refMaterials->first();
                                        @endphp
                                        <div class="card mb-2">
                                            <div class="card-body p-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="badge bg-primary">{{ $reference }}</span>
                                                        <small class="text-muted d-block">
                                                            {{ $totalMaterials }} material{{ $totalMaterials > 1 ? 's' : '' }} 
                                                            | ${{ number_format($totalValue, 2) }}
                                                        </small>
                                                        <small class="text-muted">
                                                            Added: {{ $firstMaterial->pivot->created_at ? \Carbon\Carbon::parse($firstMaterial->pivot->created_at)->format('M d, Y') : 'N/A' }}
                                                        </small>
                                                    </div>
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            onclick="window.printReference('{{ $reference }}')"
                                                            title="Print Reference {{ $reference }}">
                                                        <i class="fas fa-print"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-3">
                                        <i class="fas fa-info-circle text-muted mb-2"></i>
                                        <p class="text-muted">No reference numbers found</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize search functionality
    initMaterialSearch();
    
    // Update selection counters
    updateSelectionCounters();
    
    // Define functions globally so they can be called from inline event handlers
    window.toggleMaterialFields = function(materialId) {
        const checkbox = document.getElementById('material' + materialId);
        const fields = document.getElementById('fields' + materialId);
        const card = fields.closest('.material-card');
        const quantityInput = document.querySelector(`input[name="quantities[${materialId}]"]`);
        const priceInput = document.querySelector(`input[name="unit_prices[${materialId}]"]`);
        
        if (checkbox.checked) {
            // Show fields and highlight card
            fields.style.display = 'block';
            card.classList.add('border-primary', 'bg-light');
            
            quantityInput.required = true;
            priceInput.required = true;
            
            // Add animation
            card.style.transition = 'all 0.3s ease';
            setTimeout(() => quantityInput.focus(), 100);
        } else {
            // Hide fields and remove highlight
            fields.style.display = 'none';
            card.classList.remove('border-primary', 'bg-light');
            
            quantityInput.required = false;
            priceInput.required = false;
            quantityInput.value = '';
            priceInput.value = '';
            document.getElementById('total' + materialId).value = '';
        }
        
        updateSelectionCounters();
    };

    window.calculateTotal = function(materialId) {
        const quantity = document.querySelector(`input[name="quantities[${materialId}]"]`).value;
        const unitPrice = document.querySelector(`input[name="unit_prices[${materialId}]"]`).value;
        const totalField = document.getElementById('total' + materialId);
        
        if (quantity && unitPrice && quantity > 0 && unitPrice >= 0) {
            const total = parseFloat(quantity) * parseFloat(unitPrice);
            totalField.value = '$' + total.toFixed(2);
            totalField.classList.add('text-success', 'fw-bold');
        } else {
            totalField.value = '$0.00';
            totalField.classList.remove('text-success', 'fw-bold');
        }
        
        updateSelectionCounters();
    };

    // Print functionality - Define globally
    window.printReference = function(referenceNumber) {
        const stockId = {{ $stock->id }};
        const url = `/stocks/${stockId}/print-reference?reference=${referenceNumber}`;
        window.open(url, '_blank');
    };

    window.printAllStock = function() {
        const stockId = {{ $stock->id }};
        const url = `/stocks/${stockId}/print-all`;
        window.open(url, '_blank');
    };

    window.printActiveOnly = function() {
        const stockId = {{ $stock->id }};
        const url = `/stocks/${stockId}/print-active`;
        window.open(url, '_blank');
    };

    // Form validation before submit
    const form = document.getElementById('addMaterialForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.material-checkbox:checked');
            
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                showAlert('Please select at least one material to add to stock.', 'warning');
                return false;
            }
            
            let isValid = true;
            let errorMessage = '';
            
            checkedBoxes.forEach(function(checkbox) {
                const materialId = checkbox.value;
                const materialName = document.querySelector(`label[for="material${materialId}"]`).textContent.trim();
                const quantity = document.querySelector(`input[name="quantities[${materialId}]"]`).value;
                const unitPrice = document.querySelector(`input[name="unit_prices[${materialId}]"]`).value;
                
                if (!quantity || quantity <= 0) {
                    isValid = false;
                    errorMessage += `• ${materialName}: Please enter a valid quantity\n`;
                }
                if (!unitPrice || unitPrice < 0) {
                    isValid = false;
                    errorMessage += `• ${materialName}: Please enter a valid unit price\n`;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showAlert('Please fix the following issues:\n\n' + errorMessage, 'warning');
                return false;
            }
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding Materials...';
            submitBtn.disabled = true;
            
            // Re-enable if form submission fails (shouldn't happen with proper validation)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });
    }
});

function initMaterialSearch() {
    const searchInput = document.getElementById('materialSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const materialItems = document.querySelectorAll('.material-item');
    
    function filterMaterials() {
        const searchTerm = searchInput.value.toLowerCase();
        const categoryValue = categoryFilter.value.toLowerCase();
        let visibleCount = 0;
        
        materialItems.forEach(item => {
            const name = item.dataset.name;
            const color = item.dataset.color;
            const category = item.dataset.category;
            const description = item.dataset.description;
            
            const matchesSearch = !searchTerm || 
                name.includes(searchTerm) || 
                color.includes(searchTerm) || 
                description.includes(searchTerm);
            
            const matchesCategory = !categoryValue || category.includes(categoryValue);
            
            if (matchesSearch && matchesCategory) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Update visible count
        document.getElementById('visibleCount').textContent = visibleCount;
        
        // Show/hide no results message
        const noResults = document.getElementById('noResults');
        if (visibleCount === 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }
    
    searchInput.addEventListener('input', filterMaterials);
    categoryFilter.addEventListener('change', filterMaterials);
}

function selectAllMaterials() {
    const visibleItems = document.querySelectorAll('.material-item[style="display: block"]');
    visibleItems.forEach(item => {
        const checkbox = item.querySelector('.material-checkbox');
        if (checkbox && !checkbox.checked) {
            checkbox.checked = true;
            toggleMaterialFields(checkbox.value);
        }
    });
    updateSelectionCounters();
}

function deselectAllMaterials() {
    const checkboxes = document.querySelectorAll('.material-checkbox:checked');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
        toggleMaterialFields(checkbox.value);
    });
    updateSelectionCounters();
}

function clearSearch() {
    document.getElementById('materialSearch').value = '';
    document.getElementById('categoryFilter').value = '';
    initMaterialSearch(); // Re-run filter to show all
}

function updateSelectionCounters() {
    const selectedCheckboxes = document.querySelectorAll('.material-checkbox:checked');
    const selectedCount = selectedCheckboxes.length;
    
    // Update selected count
    document.getElementById('selectedCount').textContent = 
        `${selectedCount} material${selectedCount !== 1 ? 's' : ''} selected`;
    
    // Calculate total value
    let totalValue = 0;
    selectedCheckboxes.forEach(checkbox => {
        const materialId = checkbox.value;
        const totalField = document.getElementById(`total${materialId}`);
        if (totalField && totalField.value) {
            const value = parseFloat(totalField.value.replace('$', '')) || 0;
            totalValue += value;
        }
    });
    
    // Update total value
    document.getElementById('totalValue').textContent = 
        `Total: $${totalValue.toFixed(2)}`;
    
    // Update selection summary
    const summaryElement = document.getElementById('selectionSummary');
    if (selectedCount === 0) {
        summaryElement.innerHTML = '<span class="text-muted">No materials selected</span>';
    } else {
        const materialNames = Array.from(selectedCheckboxes).map(checkbox => {
            const label = document.querySelector(`label[for="material${checkbox.value}"]`);
            return label ? label.textContent.trim() : '';
        }).filter(name => name);
        
        if (materialNames.length <= 3) {
            summaryElement.textContent = `Selected: ${materialNames.join(', ')}`;
        } else {
            summaryElement.textContent = `Selected: ${materialNames.slice(0, 3).join(', ')} and ${selectedCount - 3} more`;
        }
    }
}

function showAlert(message, type = 'info') {
    // Simple alert for now - you can replace with a toast notification system
    alert(message);
}
</script>

<style>
/* Timeline styles for movement history */
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.timeline-item:last-child {
    border-bottom: none;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #ddd;
}

.timeline-content h6 {
    margin-bottom: 5px;
    color: #333;
}

.timeline-content p {
    margin-bottom: 5px;
    color: #666;
}

/* Material card styles */
.material-card {
    transition: all 0.3s ease;
    height: 100%;
}

.material-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.material-card.border-primary {
    border-width: 2px !important;
}

.material-fields {
    transition: all 0.3s ease-in-out;
}

#materialsGrid {
    transition: all 0.3s ease;
}

/* Smooth animations */
.fade-in {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Custom scrollbar for modal */
.modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

.modal-body::-webkit-scrollbar {
    width: 6px;
}

.modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.modal-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Print styles */
@media print {
    .btn, .modal, .accordion-button {
        display: none !important;
    }
    
    .accordion-collapse {
        display: block !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
    
    .table {
        font-size: 12px;
    }
    
    .badge {
        border: 1px solid #000;
        color: #000 !important;
        background: #fff !important;
    }
}

/* Fix modal backdrop issues */
.modal-backdrop {
    z-index: 1040;
}

.modal {
    z-index: 1050;
}
</style>