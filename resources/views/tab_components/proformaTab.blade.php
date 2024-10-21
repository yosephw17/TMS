<!-- Proformas Tab -->
<div class="tab-pane fade" id="proformas{{ $project->id }}" role="tabpanel"
    aria-labelledby="proformas-tab{{ $project->id }}">
    <div class="card mb-4">
        <!-- Card Header -->
        <div class="card-header  text-white">
            <h5>{{ $project->name }} Proformas</h5>
        </div>

        <!-- Tabs for Proforma Types -->
        <div class="card-body">
            <ul class="nav nav-tabs" id="proformaTabs{{ $project->id }}" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="profile-tab{{ $project->id }}" data-bs-toggle="tab"
                        href="#profileProforma{{ $project->id }}" role="tab"
                        aria-controls="profileProforma{{ $project->id }}" aria-selected="true">
                        <i class="bi bi-box-seam"></i> Aluminium Profile Proforma
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="accessories-tab{{ $project->id }}" data-bs-toggle="tab"
                        href="#accessoriesProforma{{ $project->id }}" role="tab"
                        aria-controls="accessoriesProforma{{ $project->id }}" aria-selected="false">
                        <i class="bi bi-gear"></i> Accessories Proforma
                    </a>
                </li>


                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="work-tab{{ $project->id }}" data-bs-toggle="tab"
                        href="#workProforma{{ $project->id }}" role="tab"
                        aria-controls="workProforma{{ $project->id }}" aria-selected="false">
                        <i class="bi bi-briefcase"></i> Work Proforma
                    </a>
                </li>
            </ul>

            <!-- Tab Content for Proforma Types -->
            <div class="tab-content mt-3" id="proformaTabContent{{ $project->id }}">
                <!-- Aluminium Profile Proforma Content -->
                @include('tab_components.aluminiumProfileTab')



                <!-- Accessories Proforma Content -->
                @include('tab_components.aluminiumAccessoriesTab')

                <!-- Work Proforma Content -->
                <div class="tab-pane fade" id="workProforma{{ $project->id }}" role="tabpanel"
                    aria-labelledby="work-tab{{ $project->id }}">
                    <div class="card mt-3">

                        <div class="card-body">
                            <button class="btn btn-primary mb-3" data-bs-toggle="modal"
                                data-bs-target="#addWorkProformaModal{{ $project->id }}">
                                Add Proforma
                            </button>
                            <p>List of Work Proformas will appear here.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>






</div>




<!-- Add Work Proforma Modal -->
<div class="modal fade" id="addWorkProformaModal{{ $project->id }}" tabindex="-1"
    aria-labelledby="addWorkProformaModalLabel{{ $project->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('proformas.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addWorkProformaModalLabel{{ $project->id }}">Add Work
                        Proforma</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Customer and Project Info -->
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="type" value="work">

                    <!-- Reference Number -->
                    <div class="form-group mb-3">
                        <label for="ref_no">Reference Number</label>
                        <input type="text" name="ref_no" class="form-control" placeholder="Enter reference number">
                    </div>

                    <!-- Select Materials -->
                    <div class="form-group mb-3">
                        <label for="materials">Select Materials</label>
                        <div class="row">
                            @foreach ($materials as $material)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            name="materials[{{ $material->id }}][selected]"
                                            id="material{{ $material->id }}">
                                        <label class="form-check-label" for="material{{ $material->id }}">
                                            {{ $material->name }} ({{ $material->unit_price }} per
                                            {{ $material->unit_of_measurement }})
                                        </label>
                                    </div>
                                    <input type="number" name="materials[{{ $material->id }}][quantity]"
                                        class="form-control mt-1" placeholder="Quantity" min="0" step="1">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Add Labor and Other Costs -->
                    <div class="form-group mb-3">
                        <label for="labor_cost">Labor Cost</label>
                        <input type="number" name="labor_cost" class="form-control" placeholder="Enter labor cost">
                    </div>
                    <div class="form-group mb-3">
                        <label for="other_costs">Other Costs</label>
                        <input type="number" name="other_costs" class="form-control"
                            placeholder="Enter other costs">
                    </div>

                    <!-- Discount and VAT -->
                    <div class="form-group mb-3">
                        <label for="discount">Discount</label>
                        <input type="number" name="discount" class="form-control"
                            placeholder="Enter discount (if any)">
                    </div>
                    <div class="form-group mb-3">
                        <label for="vat_percentage">VAT Percentage</label>
                        <input type="number" name="vat_percentage" value="15" class="form-control">
                    </div>

                    <!-- Payment Validity and Delivery Terms -->
                    <div class="form-group mb-3">
                        <label for="payment_validity">Payment Validity</label>
                        <input type="text" name="payment_validity" class="form-control"
                            placeholder="Enter payment validity">
                    </div>
                    <div class="form-group mb-3">
                        <label for="delivery_terms">Delivery Terms</label>
                        <input type="text" name="delivery_terms" class="form-control"
                            placeholder="Enter delivery terms">
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
