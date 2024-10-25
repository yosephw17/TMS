@extends('layouts.admin')

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Proforma Requests</h4>
            <!-- Button to trigger the modal -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProformaRequestModal">
                Create Purchase Request
            </button>
        </div>

        <div class="card-body">
            <!-- Table to show requests -->
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
                    @foreach ($purchaseRequests as $key => $purchaseRequest)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $purchaseRequest->project->name }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $purchaseRequest->type)) }}</td>
                            <td>{{ $purchaseRequest->user->name }}</td>
                            <td>
                                <span
                                    class="
                                    {{ $purchaseRequest->status == 'pending' ? 'text-warning' : '' }}
                                    {{ $purchaseRequest->status == 'approved' ? 'text-success' : '' }}
                                    {{ $purchaseRequest->status == 'rejected' ? 'text-danger' : '' }}
                                ">
                                    {{ ucfirst($purchaseRequest->status) }}
                                </span>
                            </td>
                            <td>
                                <!-- View Details Button -->
                                <button class="btn btn-info" data-bs-toggle="collapse"
                                    data-bs-target="#requestDetails{{ $purchaseRequest->id }}">
                                    View Details
                                </button>
                            </td>
                        </tr>

                        <!-- Accordion to show details directly -->
                        <tr id="requestDetails{{ $purchaseRequest->id }}" class="collapse">
                            <td colspan="6">
                                <div class="accordion-body">
                                    <!-- Project Details -->
                                    <div class="mb-3">
                                        <i class="fas fa-project-diagram"></i> <strong>Project:</strong>
                                        {{ $purchaseRequest->project->name }}
                                    </div>

                                    <!-- Request Type -->
                                    <div class="mb-3">
                                        <i class="fas fa-tags"></i> <strong>Type:</strong>
                                        {{ ucfirst($purchaseRequest->type) }}
                                    </div>

                                    <!-- Created By -->
                                    <div class="mb-3">
                                        <i class="fas fa-user"></i> <strong>Created By:</strong>
                                        {{ $purchaseRequest->user->name }}
                                    </div>

                                    <!-- Non-stock Material Details -->
                                    @if ($purchaseRequest->non_stock_name)
                                        <div class="mb-3">
                                            <i class="fas fa-cubes"></i> <strong>Non-stock Material:</strong>
                                            {{ $purchaseRequest->non_stock_name }}
                                        </div>


                                        @if ($purchaseRequest->non_stock_price)
                                            <div class="mb-3">
                                                <i class="fas fa-dollar-sign"></i> <strong>Non-stock Price:</strong>
                                                {{ $purchaseRequest->non_stock_price }}
                                            </div>
                                        @endif
                                        @if ($purchaseRequest->non_stock_quantity)
                                            <div class="mb-3">
                                                <i class="fas fa-shopping-cart"></i>
                                                <strong>Non-stock Quantity:</strong>
                                                {{ $purchaseRequest->non_stock_quantity }}
                                            </div>
                                        @endif
                                        <!-- Non-stock Material Image -->
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
                                    @else
                                        <div class="mb-3">
                                            <i class="fas fa-cubes"></i> <strong>Non-stock Material:</strong> N/A
                                        </div>
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
                                                        <th><i class="fas fa-balance-scale"></i> Unit of Measurement
                                                        </th>
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

                                    <!-- Approve and Decline buttons -->
                                    <div class="mt-3">
                                        <form action="{{ route('purchase_requests.approve', $purchaseRequest->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('purchase_requests.decline', $purchaseRequest->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-times"></i> Decline
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal for creating a new Proforma Request -->
    <div class="modal fade" id="createProformaRequestModal" tabindex="-1" aria-labelledby="createProformaRequestModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createProformaRequestModalLabel">Create Purchase Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('purchase_requests.store') }}" method="POST" enctype="multipart/form-data">
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

                        <!-- Type Selection Dropdown (Merged) -->
                        <div class="form-group">
                            <label for="type">Select Type</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="material_stock">Material (Stock)</option>
                                <option value="material_non_stock">Material (Non-stock)</option>
                                <option value="labour">Labour</option>
                                <option value="transport">Transport</option>
                            </select>
                        </div>

                        <!-- Material Selection (Stock Materials) -->
                        <div id="material-stock-section" class="form-group" style="display: none;">
                            <label for="stock_id">Select Stock</label>
                            <select name="stock_id" id="stock_id" class="form-control">
                                <option value="">-- Select Stock --</option>
                                @foreach ($stocks as $stock)
                                    <option value="{{ $stock->id }}">{{ $stock->name }}</option>
                                @endforeach
                            </select>

                            <div id="materials-list">
                                <!-- Material checkboxes will be loaded dynamically here -->
                            </div>
                        </div>
                        <input type="hidden" name="user_id" value="{{ Auth::id() }}">

                        <!-- Non-stock Material Input -->
                        <div id="material-non-stock-section" class="form-group" style="display: none;">
                            <label for="non_stock_name">Material Name</label>
                            <input type="text" name="non_stock_name" class="form-control">

                            <label for="non_stock_price">Material Price</label>
                            <input type="number" name="non_stock_price" class="form-control" step="0.01" required>


                            <label for="non_stock_quantity">Material Quantity</label>
                            <input type="number" name="non_stock_quantity" class="form-control">

                            <label for="non_stock_image">Material Image (Optional)</label>
                            <input type="file" name="non_stock_image" class="form-control" accept="image/*">
                        </div>

                        <!-- Labour/Transport Details -->
                        <div id="labour-transport-section" class="form-group" style="display: none;">
                            <label for="details">Enter Details</label>
                            <input type="text" name="details" class="form-control"
                                placeholder="Enter Labour/Transport details">
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Handle type selection for showing relevant sections
        document.getElementById('type').addEventListener('change', function() {
            let selectedType = this.value;

            // Show/hide sections based on selected type
            document.getElementById('material-stock-section').style.display = (selectedType === 'material_stock') ?
                'block' : 'none';
            document.getElementById('material-non-stock-section').style.display = (selectedType ===
                'material_non_stock') ? 'block' : 'none';
            document.getElementById('labour-transport-section').style.display = (selectedType === 'labour' ||
                selectedType === 'transport') ? 'block' : 'none';
        });

        // Load materials from the selected stock dynamically
        document.getElementById('stock_id').addEventListener('change', function() {
            let stockId = this.value;

            if (stockId) {
                fetch(`/api/stock/${stockId}/materials`)
                    .then(response => response.json())
                    .then(materials => {
                        let materialsList = document.getElementById('materials-list');
                        materialsList.innerHTML = ''; // Clear the previous list

                        // Create material checkboxes dynamically
                        materials.forEach(material => {
                            let materialCheck = `
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="materials[${material.id}][selected]" value="1">
                                    <label class="form-check-label">${material.name} (${material.unit_of_measurement}) -Available: ${material.pivot.quantity}  -Color: ${material.color}</label>
                                    <input type="number" name="materials[${material.id}][quantity]" class="form-control mt-2" placeholder="Quantity" min="1">
                                </div>`;
                            materialsList.innerHTML += materialCheck;
                        });
                    })
                    .catch(error => console.error('Error fetching materials:', error));
            }
        });

        // Trigger initial change event to show relevant sections based on pre-selected values
        document.getElementById('type').dispatchEvent(new Event('change'));
    </script>
@endsection
