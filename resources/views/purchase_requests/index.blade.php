@extends('layouts.admin')

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Purchase Requests</h4>
            @can('purchase-request-create')
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProformaRequestModal">
                    Create Purchase Request
                </button>
            @endcan
        </div>

        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="purchaseRequestTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">
                        <i class="fas fa-clock text-warning"></i> Pending 
                        <span class="badge bg-warning text-dark ms-1">{{ $purchaseRequests->where('status', 'pending')->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab" aria-controls="approved" aria-selected="false">
                        <i class="fas fa-check text-success"></i> Approved 
                        <span class="badge bg-success ms-1">{{ $purchaseRequests->where('status', 'approved')->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab" aria-controls="rejected" aria-selected="false">
                        <i class="fas fa-times text-danger"></i> Rejected 
                        <span class="badge bg-danger ms-1">{{ $purchaseRequests->where('status', 'rejected')->count() }}</span>
                    </button>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content" id="purchaseRequestTabContent">
                <!-- Pending Tab -->
                <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Project</th>
                                    <th>Type</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchaseRequests->where('status', 'pending') as $key => $purchaseRequest)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $purchaseRequest->project->name }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $purchaseRequest->type)) }}</td>
                                        <td>{{ $purchaseRequest->user->name }}</td>
                                        <td>
                                            <span class="text-warning">
                                                {{ ucfirst($purchaseRequest->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-info" data-bs-toggle="collapse"
                                                data-bs-target="#requestDetails{{ $purchaseRequest->id }}">
                                                View Details
                                            </button>
                                        </td>
                                    </tr>

                                    <tr id="requestDetails{{ $purchaseRequest->id }}" class="collapse">
                                        <td colspan="6">
                                            <div class="accordion-body">
                                                <div class="mb-3">
                                                    <i class="fas fa-project-diagram"></i> <strong>Project:</strong>
                                                    {{ $purchaseRequest->project->name }}
                                                </div>

                                                <div class="mb-3">
                                                    <i class="fas fa-tags"></i> <strong>Type:</strong>
                                                    {{ ucfirst($purchaseRequest->type) }}
                                                </div>

                                                <div class="mb-3">
                                                    <i class="fas fa-user"></i> <strong>Created By:</strong>
                                                    {{ $purchaseRequest->user->name }}
                                                </div>

                                                @if ($purchaseRequest->non_stock_name || $purchaseRequest->non_stock_price)
                                                    @if (!in_array($purchaseRequest->type, ['labour', 'transport']))
                                                        <div class="mb-3">
                                                            <i class="fas fa-cubes"></i> <strong>Non-stock Material:</strong>
                                                            {{ $purchaseRequest->non_stock_name }}
                                                        </div>
                                                    @else
                                                        <div class="mb-3">
                                                            <i class="fas fa-info-circle"></i>
                                                            <strong>Details:</strong> {{ $purchaseRequest->details }}
                                                        </div>
                                                    @endif

                                                    @if ($purchaseRequest->non_stock_price)
                                                        <div class="mb-3">
                                                            <i class="fas fa-dollar-sign"></i> <strong>Price:</strong>
                                                            {{ $purchaseRequest->non_stock_price }}
                                                        </div>
                                                    @endif

                                                    @if ($purchaseRequest->non_stock_quantity)
                                                        <div class="mb-3">
                                                            <i class="fas fa-shopping-cart"></i>
                                                            <strong>Quantity:</strong>
                                                            {{ $purchaseRequest->non_stock_quantity }}
                                                        </div>
                                                    @endif

                                                    @if ($purchaseRequest->non_stock_image)
                                                        <div class="mb-3">
                                                            <i class="fas fa-image"></i> <strong>Non-stock Material Image:</strong>
                                                            <a href="{{ asset('storage/' . $purchaseRequest->non_stock_image) }}"
                                                                target="_blank" class="btn btn-outline-secondary btn-sm">
                                                                <img src="{{ asset('storage/' . $purchaseRequest->non_stock_image) }}"
                                                                    alt="Non-stock Material Image" class="img-fluid mt-2"
                                                                    width="100">
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endif

                                                <!-- Materials from Stock -->
                                                @if ($purchaseRequest->materials->count() > 0)
                                                    <div class="mb-3">
                                                        <i class="fas fa-boxes"></i> <strong>Materials from Stock:</strong>
                                                        <table class="table table-bordered mt-2">
                                                            <thead>
                                                                <tr>
                                                                    <th><i class="fas fa-box"></i> Material Name</th>
                                                                    <th><i class="fas fa-ruler"></i> Quantity</th>
                                                                    <th><i class="fas fa-balance-scale"></i> Unit of Measurement</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($purchaseRequest->materials as $material)
                                                                    <tr>
                                                                        <td>{{ $material->name }}</td>
                                                                        <td>{{ $material->pivot->quantity }}</td>
                                                                        <td>{{ $material->unit_of_measurement }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif

                                                <div class="mt-3">
                                                    <form action="{{ route('purchase_requests.approve', $purchaseRequest->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('POST')
                                                        @can('purchase-request-approve')
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="fas fa-check"></i> Approve
                                                            </button>
                                                        @endcan
                                                    </form>
                                                    <form action="{{ route('purchase_requests.decline', $purchaseRequest->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('POST')
                                                        @can('purchase-request-decline')
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fas fa-times"></i> Decline
                                                            </button>
                                                        @endcan
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>No pending purchase requests found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Approved Tab -->
                <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Project</th>
                                    <th>Type</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchaseRequests->where('status', 'approved') as $key => $purchaseRequest)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $purchaseRequest->project->name }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $purchaseRequest->type)) }}</td>
                                        <td>{{ $purchaseRequest->user->name }}</td>
                                        <td>
                                            <span class="text-success">
                                                {{ ucfirst($purchaseRequest->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-info" data-bs-toggle="collapse"
                                                data-bs-target="#approvedDetails{{ $purchaseRequest->id }}">
                                                View Details
                                            </button>
                                        </td>
                                    </tr>

                                    <tr id="approvedDetails{{ $purchaseRequest->id }}" class="collapse">
                                        <td colspan="6">
                                            <div class="accordion-body">
                                                <div class="mb-3">
                                                    <i class="fas fa-project-diagram"></i> <strong>Project:</strong>
                                                    {{ $purchaseRequest->project->name }}
                                                </div>

                                                <div class="mb-3">
                                                    <i class="fas fa-tags"></i> <strong>Type:</strong>
                                                    {{ ucfirst($purchaseRequest->type) }}
                                                </div>

                                                <div class="mb-3">
                                                    <i class="fas fa-user"></i> <strong>Created By:</strong>
                                                    {{ $purchaseRequest->user->name }}
                                                </div>

                                                @if ($purchaseRequest->non_stock_name || $purchaseRequest->non_stock_price)
                                                    @if (!in_array($purchaseRequest->type, ['labour', 'transport']))
                                                        <div class="mb-3">
                                                            <i class="fas fa-cubes"></i> <strong>Non-stock Material:</strong>
                                                            {{ $purchaseRequest->non_stock_name }}
                                                        </div>
                                                    @else
                                                        <div class="mb-3">
                                                            <i class="fas fa-info-circle"></i>
                                                            <strong>Details:</strong> {{ $purchaseRequest->details }}
                                                        </div>
                                                    @endif

                                                    @if ($purchaseRequest->non_stock_price)
                                                        <div class="mb-3">
                                                            <i class="fas fa-dollar-sign"></i> <strong>Price:</strong>
                                                            {{ $purchaseRequest->non_stock_price }}
                                                        </div>
                                                    @endif

                                                    @if ($purchaseRequest->non_stock_quantity)
                                                        <div class="mb-3">
                                                            <i class="fas fa-shopping-cart"></i>
                                                            <strong>Quantity:</strong>
                                                            {{ $purchaseRequest->non_stock_quantity }}
                                                        </div>
                                                    @endif

                                                    @if ($purchaseRequest->non_stock_image)
                                                        <div class="mb-3">
                                                            <i class="fas fa-image"></i> <strong>Non-stock Material Image:</strong>
                                                            <a href="{{ asset('storage/' . $purchaseRequest->non_stock_image) }}"
                                                                target="_blank" class="btn btn-outline-secondary btn-sm">
                                                                <img src="{{ asset('storage/' . $purchaseRequest->non_stock_image) }}"
                                                                    alt="Non-stock Material Image" class="img-fluid mt-2"
                                                                    width="100">
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endif

                                                <!-- Materials from Stock -->
                                                @if ($purchaseRequest->materials->count() > 0)
                                                    <div class="mb-3">
                                                        <i class="fas fa-boxes"></i> <strong>Materials from Stock:</strong>
                                                        <table class="table table-bordered mt-2">
                                                            <thead>
                                                                <tr>
                                                                    <th><i class="fas fa-box"></i> Material Name</th>
                                                                    <th><i class="fas fa-ruler"></i> Quantity</th>
                                                                    <th><i class="fas fa-balance-scale"></i> Unit of Measurement</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($purchaseRequest->materials as $material)
                                                                    <tr>
                                                                        <td>{{ $material->name }}</td>
                                                                        <td>{{ $material->pivot->quantity }}</td>
                                                                        <td>{{ $material->unit_of_measurement }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif

                                                <div class="mt-3">
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check"></i> Already Approved
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                                            <p>No approved purchase requests found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Rejected Tab -->
                <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Project</th>
                                    <th>Type</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchaseRequests->where('status', 'rejected') as $key => $purchaseRequest)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $purchaseRequest->project->name }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $purchaseRequest->type)) }}</td>
                                        <td>{{ $purchaseRequest->user->name }}</td>
                                        <td>
                                            <span class="text-danger">
                                                Declined
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-info" data-bs-toggle="collapse"
                                                data-bs-target="#rejectedDetails{{ $purchaseRequest->id }}">
                                                View Details
                                            </button>
                                        </td>
                                    </tr>

                                    <tr id="rejectedDetails{{ $purchaseRequest->id }}" class="collapse">
                                        <td colspan="6">
                                            <div class="accordion-body">
                                                <div class="mb-3">
                                                    <i class="fas fa-project-diagram"></i> <strong>Project:</strong>
                                                    {{ $purchaseRequest->project->name }}
                                                </div>

                                                <div class="mb-3">
                                                    <i class="fas fa-tags"></i> <strong>Type:</strong>
                                                    {{ ucfirst($purchaseRequest->type) }}
                                                </div>

                                                <div class="mb-3">
                                                    <i class="fas fa-user"></i> <strong>Created By:</strong>
                                                    {{ $purchaseRequest->user->name }}
                                                </div>

                                                @if ($purchaseRequest->non_stock_name || $purchaseRequest->non_stock_price)
                                                    @if (!in_array($purchaseRequest->type, ['labour', 'transport']))
                                                        <div class="mb-3">
                                                            <i class="fas fa-cubes"></i> <strong>Non-stock Material:</strong>
                                                            {{ $purchaseRequest->non_stock_name }}
                                                        </div>
                                                    @else
                                                        <div class="mb-3">
                                                            <i class="fas fa-info-circle"></i>
                                                            <strong>Details:</strong> {{ $purchaseRequest->details }}
                                                        </div>
                                                    @endif

                                                    @if ($purchaseRequest->non_stock_price)
                                                        <div class="mb-3">
                                                            <i class="fas fa-dollar-sign"></i> <strong>Price:</strong>
                                                            {{ $purchaseRequest->non_stock_price }}
                                                        </div>
                                                    @endif

                                                    @if ($purchaseRequest->non_stock_quantity)
                                                        <div class="mb-3">
                                                            <i class="fas fa-shopping-cart"></i>
                                                            <strong>Quantity:</strong>
                                                            {{ $purchaseRequest->non_stock_quantity }}
                                                        </div>
                                                    @endif

                                                    @if ($purchaseRequest->non_stock_image)
                                                        <div class="mb-3">
                                                            <i class="fas fa-image"></i> <strong>Non-stock Material Image:</strong>
                                                            <a href="{{ asset('storage/' . $purchaseRequest->non_stock_image) }}"
                                                                target="_blank" class="btn btn-outline-secondary btn-sm">
                                                                <img src="{{ asset('storage/' . $purchaseRequest->non_stock_image) }}"
                                                                    alt="Non-stock Material Image" class="img-fluid mt-2"
                                                                    width="100">
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endif

                                                <!-- Materials from Stock -->
                                                @if ($purchaseRequest->materials->count() > 0)
                                                    <div class="mb-3">
                                                        <i class="fas fa-boxes"></i> <strong>Materials from Stock:</strong>
                                                        <table class="table table-bordered mt-2">
                                                            <thead>
                                                                <tr>
                                                                    <th><i class="fas fa-box"></i> Material Name</th>
                                                                    <th><i class="fas fa-ruler"></i> Quantity</th>
                                                                    <th><i class="fas fa-balance-scale"></i> Unit of Measurement</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($purchaseRequest->materials as $material)
                                                                    <tr>
                                                                        <td>{{ $material->name }}</td>
                                                                        <td>{{ $material->pivot->quantity }}</td>
                                                                        <td>{{ $material->unit_of_measurement }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif

                                                <div class="mt-3">
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times"></i> Request Declined
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            <i class="fas fa-times-circle fa-2x mb-2 text-danger"></i>
                                            <p>No rejected purchase requests found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for creating a new Purchase Request -->
    <div class="modal fade" id="createProformaRequestModal" tabindex="-1" aria-labelledby="createProformaRequestModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createProformaRequestModalLabel">Create Purchase Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('purchase_requests.store') }}" method="POST" enctype="multipart/form-data"
                        novalidate>
                        @csrf

                        <!-- Project Selection -->
                        <div class="form-group">
                            <label for="project_id">Select Project</label>
                            <select name="project_id" class="form-control" required>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="type">Select Type</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="material_stock">Material (Stock)</option>
                                <option value="material_non_stock">Material (Non-stock)</option>
                                <option value="labour">Labour</option>
                                <option value="transport">Transport</option>
                            </select>
                        </div>

                        <div id="material-stock-section" class="form-group" style="display: none;">
                            <label for="stock_id">Select Stock</label>
                            <select name="stock_id" id="stock_id" class="form-control">
                                <option value="">-- Select Stock --</option>
                                @foreach ($stocks as $stock)
                                    <option value="{{ $stock->id }}">{{ $stock->name }}</option>
                                @endforeach
                            </select>

                            <div id="materials-list">
                            </div>
                        </div>

                        <input type="hidden" name="user_id" value="{{ Auth::id() }}">

                        <!-- Non-stock Material Input -->
                        <div id="material-non-stock-section" class="form-group" style="display: none;">
                            <label for="non_stock_name">Material Name</label>
                            <input type="text" name="non_stock_name" class="form-control">

                            <label for="non_stock_price">Material Price</label>
                            <input type="number" name="non_stock_price" class="form-control" step="0.01">

                            <label for="non_stock_quantity">Material Quantity</label>
                            <input type="number" name="non_stock_quantity" class="form-control">

                            <label for="non_stock_image">Material Image (Optional)</label>
                            <input type="file" name="non_stock_image" id="non_stock_image" class="form-control"
                                accept="image/*">
                        </div>

                        <!-- Labour/Transport Details -->
                        <div id="labour-transport-section" class="form-group" style="display: none;">
                            <label for="details">Enter Details</label>
                            <input type="text" name="details" class="form-control"
                                placeholder="Enter Labour/Transport details">

                            <label for="labour_transport_price">Enter Price</label>
                            <input type="number" name="labour_transport_price" class="form-control"
                                placeholder="Enter Labour/Transport Price" step="0.001" min="0">
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeField = document.getElementById('type');
            const materialStockSection = document.getElementById('material-stock-section');
            const materialNonStockSection = document.getElementById('material-non-stock-section');
            const labourTransportSection = document.getElementById('labour-transport-section');
            const stockSelect = document.getElementById('stock_id');
            const materialsList = document.getElementById('materials-list');

            typeField.addEventListener('change', function() {
                const selectedType = typeField.value;

                materialStockSection.style.display = selectedType === 'material_stock' ? 'block' : 'none';
                materialNonStockSection.style.display = selectedType === 'material_non_stock' ? 'block' : 'none';
                labourTransportSection.style.display = (selectedType === 'labour' || selectedType === 'transport') ? 'block' : 'none';

                document.querySelectorAll('#material-stock-section input, #labour-transport-section input, #material-non-stock-section input')
                    .forEach(input => {
                        input.required = input.closest('div').style.display === 'block';
                    });
            });

            stockSelect.addEventListener('change', function() {
                const stockId = stockSelect.value;

                if (stockId) {
                    // Add cache-busting parameter to prevent cached responses
                    fetch(`/api/stock/${stockId}/materials?t=${Date.now()}`)
                        .then(response => {
                            console.log('Response status:', response.status);
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(materials => {
                            console.log('API Response:', materials);
                            console.log('Materials type:', typeof materials);
                            console.log('Is array:', Array.isArray(materials));

                            materialsList.innerHTML = '';

                            // Validate array
                            if (!Array.isArray(materials)) {
                                console.error('Materials is not an array:', materials);
                                materialsList.innerHTML = '<div class="alert alert-warning">No materials data received</div>';
                                return;
                            }

                            if (materials.length === 0) {
                                materialsList.innerHTML = '<div class="alert alert-info">No materials available in this stock</div>';
                                return;
                            }

                            // Render each material
                            materials.forEach((material, index) => {
                                console.log(`Processing material ${index}:`, material);

                                // Resolve a reliable id (prefer material_id from root or pivot)
                                const id = (material.material_id)
                                    || (material.pivot && material.pivot.material_id)
                                    || material.id
                                    || null;

                                console.log('Resolved material id:', id);
                                if (!id) {
                                    console.warn('Material missing id:', material);
                                    return; // skip if no id
                                }

                                const availableQuantity = Number(material.available_quantity || 0);
                                const materialName = material.name || 'Unknown Material';
                                const unitOfMeasurement = material.unit_of_measurement || '';
                                const color = material.color || '';

                                const materialItem = `
                                    <div class="form-check mb-2">
                                        <input type="hidden" name="materials[${id}][material_id]" value="${id}">
                                        <input class="form-check-input" id="material_chk_${id}" type="checkbox" name="materials[${id}][selected]" value="1">
                                        <label class="form-check-label" for="material_chk_${id}">
                                            ${materialName} (${unitOfMeasurement}) - Available: ${availableQuantity}${color ? ` - Color: ${color}` : ''}
                                        </label>
                                        <input type="number"
                                               name="materials[${id}][quantity]"
                                               class="form-control mt-2"
                                               placeholder="Quantity"
                                               min="1"
                                               max="${availableQuantity}"
                                               value="${availableQuantity > 0 ? 1 : 0}">
                                    </div>`;

                                materialsList.insertAdjacentHTML('beforeend', materialItem);
                            });

                            console.log('Materials list updated. Total materials processed:', materials.length);
                        })
                        .catch(error => {
                            console.error('Error fetching materials:', error);
                            materialsList.innerHTML = '<div class="alert alert-danger">Error loading materials. Please try again.</div>';
                        });
                } else {
                    materialsList.innerHTML = '';
                }
            });

            // Initialize sections on load
            typeField.dispatchEvent(new Event('change'));
        });
    </script>
@endsection