@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ $project->name }} - Material Order</h2>
                <p class="text-muted">Customer: {{ $customer->name }} | Date: {{ \Carbon\Carbon::parse($project->starting_date)->format('d/m/Y') }}</p>
            </div>
            <div class="pull-right">
                <a href="{{ route('projects.show', $customer->id) }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back to Orders
                </a>
            </div>
        </div>
    </div>

    <!-- Project Description -->
    @if($project->description)
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Order Description</h5>
                <p class="card-text">{{ $project->description }}</p>
            </div>
        </div>
    @endif

    <!-- Material Tabs -->
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs" id="materialTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" 
                            type="button" role="tab" aria-controls="profile" aria-selected="true">
                        <i class="fas fa-industry"></i> Aluminum Profiles
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="accessories-tab" data-bs-toggle="tab" data-bs-target="#accessories" 
                            type="button" role="tab" aria-controls="accessories" aria-selected="false">
                        <i class="fas fa-tools"></i> Aluminum Accessories
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="materialTabsContent">
                <!-- Aluminum Profile Tab -->
                <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="mt-3">
                        @include('tab_components.aluminiumProfileTab', [
                            'project' => $project,
                            'materials' => $materials,
                            'profileProformas' => $profileProformas
                        ])
                    </div>
                </div>

                <!-- Aluminum Accessories Tab -->
                <div class="tab-pane fade" id="accessories" role="tabpanel" aria-labelledby="accessories-tab">
                    <div class="mt-3">
                        <h5>Accessories Proformas</h5>
                        @can('proforma-create')
                            <button class="btn btn-primary mb-3" data-bs-toggle="modal"
                                data-bs-target="#addAccessoriesProformaModal{{ $project->id }}">
                                Add Proforma
                            </button>
                        @endcan

                        @if ($accessoriesProformas->isEmpty())
                            <p>No Accessories Proformas available.</p>
                        @else
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Ref No</th>
                                        <th>Date</th>
                                        <th>Before VAT Total</th>
                                        <th>VAT Percentage</th>
                                        <th>After VAT Total</th>
                                        <th>Discount</th>
                                        <th>Final Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($accessoriesProformas as $proforma)
                                        @if ($proforma->type === 'aluminium_accessories')
                                            <tr data-bs-toggle="collapse" data-bs-target="#collapseProforma{{ $proforma->id }}"
                                                aria-expanded="false" aria-controls="collapseProforma{{ $proforma->id }}">
                                                <td>{{ $proforma->ref_no }}</td>
                                                <td>{{ $proforma->date->format('F d, Y') }}</td>
                                                <td>{{ $proforma->before_vat_total }}</td>
                                                <td>{{ $proforma->vat_percentage }}%</td>
                                                <td>{{ $proforma->after_vat_total }}</td>
                                                <td>{{ $proforma->discount }}</td>
                                                <td>{{ $proforma->final_total }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        {{-- Status Badge --}}
                                                        @if ($proforma->status === 'pending')
                                                            <span class="badge bg-warning me-2">Pending</span>
                                                        @elseif($proforma->status === 'approved')
                                                            <span class="badge bg-success me-2">Approved</span>
                                                        @elseif($proforma->status === 'rejected')
                                                            <span class="badge bg-danger me-2">Rejected</span>
                                                        @endif

                                                        {{-- Actions Dropdown --}}
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                                                id="actionsDropdown{{ $proforma->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu" aria-labelledby="actionsDropdown{{ $proforma->id }}">
                                                                {{-- Approval Actions (only for pending proformas) --}}
                                                                @if ($proforma->status === 'pending')
                                                                    @can('proforma-edit')
                                                                        <li>
                                                                            <form action="{{ route('proformas.approve', $proforma->id) }}" method="POST" class="d-inline">
                                                                                @csrf
                                                                                @method('PATCH')
                                                                                <button type="submit" class="dropdown-item text-success" 
                                                                                    onclick="return confirm('Are you sure you want to approve this proforma?')">
                                                                                    <i class="fas fa-check me-2"></i>Approve
                                                                                </button>
                                                                            </form>
                                                                        </li>
                                                                        <li>
                                                                            <form action="{{ route('proformas.decline', $proforma->id) }}" method="POST" class="d-inline">
                                                                                @csrf
                                                                                @method('PATCH')
                                                                                <button type="submit" class="dropdown-item text-danger" 
                                                                                    onclick="return confirm('Are you sure you want to decline this proforma?')">
                                                                                    <i class="fas fa-times me-2"></i>Decline
                                                                                </button>
                                                                            </form>
                                                                        </li>
                                                                        <li><hr class="dropdown-divider"></li>
                                                                    @endcan
                                                                @endif

                                                                {{-- Edit Action --}}
                                                                @can('proforma-edit')
                                                                    <li>
                                                                        <button class="dropdown-item" data-bs-toggle="modal"
                                                                            data-bs-target="#editAccessoriesProformaModal{{ $proforma->id }}">
                                                                            <i class="fas fa-edit me-2"></i>Edit
                                                                        </button>
                                                                    </li>
                                                                @endcan

                                                                {{-- Print Action --}}
                                                                @can('proforma-print')
                                                                    <li>
                                                                        <a href="{{ route('print.accessories', $proforma->id) }}"
                                                                            class="dropdown-item" target="_blank"
                                                                            onclick="event.preventDefault(); window.open(this.href, '_blank'); return false;">
                                                                            <i class="fas fa-print me-2"></i>Print
                                                                        </a>
                                                                    </li>
                                                                @endcan

                                                                {{-- Delete Action --}}
                                                                @can('proforma-delete')
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form action="{{ route('proformas.destroy', $proforma->id) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="dropdown-item text-danger"
                                                                                onclick="return confirm('Are you sure you want to delete this proforma?')">
                                                                                <i class="fas fa-trash me-2"></i>Delete
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                @endcan
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="collapse" id="collapseProforma{{ $proforma->id }}">
                                                <td colspan="8">
                                                    <div class="p-3">
                                                        <h6>Proforma Details</h6>
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th>Material Name</th>
                                                                    <th>Code</th>
                                                                    <th>Unit Price</th>
                                                                    <th>Quantity</th>
                                                                    <th>Total Price</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($proforma->materials as $material)
                                                                    <tr>
                                                                        <td>{{ $material->name }}</td>
                                                                        <td>{{ $material->code }}</td>
                                                                        <td>{{ number_format($material->unit_price, 2) }}</td>
                                                                        <td>{{ $material->pivot->quantity }}</td>
                                                                        <td>{{ number_format($material->pivot->total_price, 2) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>

                                                        <div class="row mt-4">
                                                            <div class="col-md-6">
                                                                <p><strong>Payment Validity:</strong> {{ $proforma->payment_validity }}</p>
                                                                <p><strong>Delivery Terms:</strong> {{ $proforma->delivery_terms }}</p>
                                                            </div>
                                                            <div class="col-md-6 text-end">
                                                                <table class="table">
                                                                    <tr>
                                                                        <th>Subtotal (Before VAT):</th>
                                                                        <td>{{ number_format($proforma->before_vat_total, 2) }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>VAT ({{ $proforma->vat_percentage }}%):</th>
                                                                        <td>{{ number_format($proforma->vat_amount, 2) }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>After VAT Total:</th>
                                                                        <td>{{ number_format($proforma->after_vat_total, 2) }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Discount:</th>
                                                                        <td>{{ number_format($proforma->discount ?? 0, 2) }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th><strong>Final Total:</strong></th>
                                                                        <td><strong>{{ number_format($proforma->final_total, 2) }}</strong></td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Modal for Editing -->
                                            <div class="modal fade" id="editAccessoriesProformaModal{{ $proforma->id }}" tabindex="-1"
                                                aria-labelledby="editAccessoriesProformaModalLabel{{ $proforma->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <form action="{{ route('proformas.update', $proforma->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editAccessoriesProformaModalLabel{{ $proforma->id }}">
                                                                    Edit Accessories Proforma</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <!-- Customer and Project Info -->
                                                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                                                <input type="hidden" name="type" value="aluminium_accessories">
                                                                <input type="hidden" name="customer_id" class="form-control" value="{{ $project->customer_id }}">

                                                                <!-- Reference Number -->
                                                                <div class="form-group mb-3">
                                                                    <label for="ref_no">Reference Number</label>
                                                                    <input type="text" name="ref_no" class="form-control" value="{{ $proforma->ref_no }}" placeholder="Enter reference number">
                                                                </div>
                                                                <div class="form-group mb-3">
                                                                    <label for="date">Date</label>
                                                                    <input type="date" name="date" class="form-control" value="{{ $proforma->date->format('Y-m-d') }}">
                                                                </div>

                                                                <!-- Select Materials -->
                                                                <div class="form-group mb-3">
                                                                    <label for="materials">Select Materials</label>
                                                                    <div class="row">
                                                                        @foreach ($materials as $material)
                                                                            @if ($material->type === 'aluminium_accessory')
                                                                                <div class="col-md-6">
                                                                                    <div class="form-check">
                                                                                        <input class="form-check-input" type="checkbox"
                                                                                            name="materials[{{ $material->id }}][selected]"
                                                                                            id="material{{ $material->id }}"
                                                                                            {{ $proforma->materials->contains($material->id) ? 'checked' : '' }}>
                                                                                        <label class="form-check-label" for="material{{ $material->id }}">
                                                                                            {{ $material->name }} ({{ $material->unit_price }} per {{ $material->unit_of_measurement }})
                                                                                        </label>
                                                                                    </div>
                                                                                    <input type="number" name="materials[{{ $material->id }}][quantity]"
                                                                                        class="form-control mt-1" placeholder="Quantity" min="0" step="1"
                                                                                        value="{{ $proforma->materials->contains($material->id) ? $proforma->materials->find($material->id)->pivot->quantity : 0 }}">
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                </div>

                                                                <!-- Discount and VAT -->
                                                                <div class="form-group mb-3">
                                                                    <label for="discount">Discount</label>
                                                                    <input type="number" name="discount" class="form-control" value="{{ $proforma->discount }}" min="0" placeholder="Enter discount">
                                                                </div>
                                                                <div class="form-group mb-3">
                                                                    <label for="vat_percentage">VAT Percentage</label>
                                                                    <input type="number" name="vat_percentage" class="form-control" value="{{ $proforma->vat_percentage }}" placeholder="Enter VAT Percentage">
                                                                </div>

                                                                <div class="form-group mb-3">
                                                                    <label for="payment_validity">Payment Validity</label>
                                                                    <input type="text" name="payment_validity" class="form-control" value="{{ $proforma->payment_validity }}" placeholder="Enter payment validity">
                                                                </div>
                                                                <div class="form-group mb-3">
                                                                    <label for="delivery_terms">Delivery Terms</label>
                                                                    <input type="text" name="delivery_terms" class="form-control" value="{{ $proforma->delivery_terms }}" placeholder="Enter delivery terms">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                        <!-- Add Accessories Proforma Modal -->
                        <div class="modal fade" id="addAccessoriesProformaModal{{ $project->id }}" tabindex="-1"
                            aria-labelledby="addAccessoriesProformaModalLabel{{ $project->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <form action="{{ route('proformas.store') }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addAccessoriesProformaModalLabel{{ $project->id }}">Add Accessories Proforma</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Customer and Project Info -->
                                            <input type="hidden" name="project_id" value="{{ $project->id }}">
                                            <input type="hidden" name="type" value="aluminium_accessories">

                                            <!-- Reference Number -->
                                            <div class="form-group mb-3">
                                                <label for="ref_no">Reference Number</label>
                                                <input type="text" name="ref_no" class="form-control" placeholder="Enter reference number">
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="date">Date</label>
                                                <input type="date" name="date" class="form-control">
                                            </div>
                                            <input type="hidden" name="customer_id" class="form-control" value="{{ $project->customer_id }}">

                                            <!-- Select Materials -->
                                            <div class="form-group mb-3">
                                                <label for="materials">Select Materials</label>
                                                <div class="row">
                                                    @foreach ($materials as $material)
                                                        @if ($material->type === 'aluminium_accessory')
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="materials[{{ $material->id }}][selected]"
                                                                        id="material{{ $material->id }}">
                                                                    <label class="form-check-label" for="material{{ $material->id }}">
                                                                        {{ $material->name }} ({{ $material->unit_price }} per {{ $material->unit_of_measurement }})
                                                                    </label>
                                                                </div>
                                                                <input type="number" name="materials[{{ $material->id }}][quantity]"
                                                                    class="form-control mt-1" placeholder="Quantity" min="0" step="1">
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- Discount and VAT -->
                                            <div class="form-group mb-3">
                                                <label for="discount">Discount</label>
                                                <input type="number" name="discount" class="form-control" placeholder="Enter discount (if any)">
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="vat_percentage">VAT Percentage</label>
                                                <input type="number" name="vat_percentage" value="15" class="form-control">
                                            </div>

                                            <!-- Payment Validity and Delivery Terms -->
                                            <div class="form-group mb-3">
                                                <label for="payment_validity">Payment Validity</label>
                                                <input type="text" name="payment_validity" class="form-control" placeholder="Enter payment validity">
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="delivery_terms">Delivery Terms</label>
                                                <input type="text" name="delivery_terms" class="form-control" placeholder="Enter delivery terms">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save Proforma</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Profile Proformas</h5>
                    @if($profileProformas->count() > 0)
                        <div class="list-group">
                            @foreach($profileProformas as $proforma)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $proforma->ref_no }}</strong>
                                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($proforma->date)->format('d/m/Y') }}</small>
                                    </div>
                                    <div>
                                        <span class="badge 
                                            @if($proforma->status === 'approved') bg-success
                                            @elseif($proforma->status === 'rejected') bg-danger
                                            @else bg-warning @endif">
                                            {{ ucfirst($proforma->status) }}
                                        </span>
                                        <br><strong>${{ number_format($proforma->final_total, 2) }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No profile proformas yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Accessories Proformas</h5>
                    @if($accessoriesProformas->count() > 0)
                        <div class="list-group">
                            @foreach($accessoriesProformas as $proforma)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $proforma->ref_no }}</strong>
                                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($proforma->date)->format('d/m/Y') }}</small>
                                    </div>
                                    <div>
                                        <span class="badge 
                                            @if($proforma->status === 'approved') bg-success
                                            @elseif($proforma->status === 'rejected') bg-danger
                                            @else bg-warning @endif">
                                            {{ ucfirst($proforma->status) }}
                                        </span>
                                        <br><strong>${{ number_format($proforma->final_total, 2) }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No accessories proformas yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Total Summary -->
    @if($profileProformas->count() > 0 || $accessoriesProformas->count() > 0)
        <div class="card mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="card-title">Order Summary</h5>
                        <p class="text-muted">Total value of all proformas for this material order</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <h3 class="text-primary">
                            ${{ number_format($profileProformas->sum('final_total') + $accessoriesProformas->sum('final_total'), 2) }}
                        </h3>
                        <small class="text-muted">Total Order Value</small>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
<script>
    // Initialize Bootstrap tabs
    var triggerTabList = [].slice.call(document.querySelectorAll('#materialTabs button'))
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl)
        
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault()
            tabTrigger.show()
        })
    })
</script>
@endsection
