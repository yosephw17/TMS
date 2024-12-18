<div class="tab-pane fade show active" id="profileProforma{{ $project->id }}" role="tabpanel"
    aria-labelledby="profile-tab{{ $project->id }}">
    <h5>Aluminium Profile Proformas</h5>
    @can('project-create')
        <button class="btn btn-primary mb-3" data-bs-toggle="modal"
            data-bs-target="#addProfileProformaModal{{ $project->id }}">
            Add Proforma
        </button>
    @endcan

    @if ($profileProformas->isEmpty())
        <p>No Aluminium Profile Proformas available.</p>
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
                @foreach ($profileProformas as $proforma)
                    @if ($proforma->type === 'aluminium_profile')
                        <!-- Ensure the type matches -->

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
                                @can('proforma-edit')
                                    <button class="btn btn-outline-primary btn-sm edit-proforma-btn" data-bs-toggle="modal"
                                        data-bs-target="#editProfileProformaModal{{ $proforma->id }}">
                                        Edit
                                    </button>
                                @endcan
                                <form action="{{ route('proformas.destroy', $proforma->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    @can('proforma-delete')
                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this proforma?')">
                                            Delete
                                        </button>
                                    @endcan
                                </form>
                                @can('proforma-print')
                                    <a href="{{ route('print.aluminiumProfile', $proforma->id) }}"
                                        class="btn btn-secondary" target="_blank"
                                        onclick="event.preventDefault(); window.open(this.href, '_blank'); return false;">
                                        <i class="fas fa-print"></i>
                                        Print
                                    </a>
                                @endcan
                            </td>

                        </tr>
                        <tr class="collapse bg-light" id="collapseProforma{{ $proforma->id }}">
                            <td colspan="8">
                                <div class="p-3">
                                    <h6>Proforma Details</h6>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Material Name</th>
                                                <th>Unit Price</th>
                                                <th>Quantity</th>
                                                <th>Total Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($proforma->materials as $material)
                                                <tr>
                                                    <td>{{ $material->name }}</td>
                                                    <td>{{ number_format($material->unit_price, 2) }}
                                                    </td>
                                                    <td>{{ $material->pivot->quantity }}</td>
                                                    <td>{{ number_format($material->pivot->total_price, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <p><strong>Payment Validity:</strong>
                                                {{ $proforma->payment_validity }}</p>
                                            <p><strong>Delivery Terms:</strong>
                                                {{ $proforma->delivery_terms }}</p>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <table class="table">
                                                <tr>
                                                    <th>Subtotal (Before VAT):</th>
                                                    <td>{{ number_format($proforma->before_vat_total, 2) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>VAT
                                                        ({{ $proforma->vat_percentage }}%)
                                                        :
                                                    </th>
                                                    <td>{{ number_format($proforma->vat_amount, 2) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>After VAT Total:</th>
                                                    <td>{{ number_format($proforma->after_vat_total, 2) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Discount:</th>
                                                    <td>{{ number_format($proforma->discount ?? 0, 2) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><strong>Final Total:</strong></th>
                                                    <td><strong>{{ number_format($proforma->final_total, 2) }}</strong>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <div class="modal fade" id="editProfileProformaModal{{ $proforma->id }}" tabindex="-1"
                            aria-labelledby="editProfileProformaModalLabel{{ $proforma->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <form action="{{ route('proformas.update', $proforma->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"
                                                id="editProfileProformaModalLabel{{ $proforma->id }}">
                                                Edit
                                                Aluminium Profile Proforma</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Customer and Project Info -->
                                            <input type="hidden" name="project_id" value="{{ $project->id }}">
                                            <input type="hidden" name="type" value="aluminium_profile">
                                            <input type="hidden" name="customer_id" class="form-control"
                                                value="{{ $project->customer_id }}">

                                            <!-- Reference Number -->
                                            <div class="form-group mb-3">
                                                <label for="ref_no">Reference Number</label>
                                                <input type="text" name="ref_no" class="form-control"
                                                    value="{{ $proforma->ref_no }}"
                                                    placeholder="Enter reference number">
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="date">Date</label>
                                                <input type="date" name="date" class="form-control"
                                                    value="{{ $proforma->date->format('Y-m-d') }}">
                                            </div>

                                            <!-- Select Materials -->
                                            <div class="form-group mb-3">
                                                <label for="materials">Select Materials</label>
                                                <div class="row">
                                                    @foreach ($materials as $material)
                                                        @if ($material->type === 'aluminium_profile')
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="materials[{{ $material->id }}][selected]"
                                                                        id="material{{ $material->id }}"
                                                                        {{ $proforma->materials->contains($material->id) ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="material{{ $material->id }}">
                                                                        {{ $material->name }}
                                                                        ({{ $material->unit_price }}
                                                                        per
                                                                        {{ $material->unit_of_measurement }})
                                                                    </label>
                                                                </div>
                                                                <input type="number"
                                                                    name="materials[{{ $material->id }}][quantity]"
                                                                    class="form-control mt-1" placeholder="Quantity"
                                                                    min="0" step="1"
                                                                    value="{{ $proforma->materials->contains($material->id) ? $proforma->materials->find($material->id)->pivot->quantity : 0 }}">

                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- Discount and VAT -->
                                            <div class="form-group mb-3">
                                                <label for="discount">Discount</label>
                                                <input type="number" name="discount" class="form-control"
                                                    placeholder="Enter discount (if any)"
                                                    value="{{ $proforma->discount }}">
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="vat_percentage">VAT
                                                    Percentage</label>
                                                <input type="number" name="vat_percentage" class="form-control"
                                                    value="{{ $proforma->vat_percentage }}">
                                            </div>

                                            <!-- Payment Validity and Delivery Terms -->
                                            <div class="form-group mb-3">
                                                <label for="payment_validity">Payment
                                                    Validity</label>
                                                <input type="text" name="payment_validity" class="form-control"
                                                    placeholder="Enter payment validity"
                                                    value="{{ $proforma->payment_validity }}">
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="delivery_terms">Delivery
                                                    Terms</label>
                                                <input type="text" name="delivery_terms" class="form-control"
                                                    placeholder="Enter delivery terms"
                                                    value="{{ $proforma->delivery_terms }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Update
                                                Proforma</button>
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
</div>
<!-- Add Aluminium Profile Proforma Modal -->
<div class="modal fade" id="addProfileProformaModal{{ $project->id }}" tabindex="-1"
    aria-labelledby="addProfileProformaModalLabel{{ $project->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('proformas.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProfileProformaModalLabel{{ $project->id }}">Add
                        Aluminium Profile Proforma</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Customer and Project Info -->
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="type" value="aluminium_profile">

                    <!-- Reference Number -->
                    <div class="form-group mb-3">
                        <label for="ref_no">Reference Number</label>
                        <input type="text" name="ref_no" class="form-control"
                            placeholder="Enter reference number">
                    </div>
                    <div class="form-group mb-3">
                        <label for="date">Date</label>
                        <input type="date" name="date" class="form-control">
                    </div>
                    <input type="hidden" name="customer_id" class="form-control"
                        value="{{ $project->customer_id }}">

                    <!-- Select Materials -->
                    <div class="form-group mb-3">
                        <label for="materials">Select Materials</label>
                        <div class="row">
                            @foreach ($materials as $material)
                                @if ($material->type === 'aluminium_profile')
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
                                            class="form-control mt-1" placeholder="Quantity" min="0"
                                            step="1">
                                    </div>
                                @endif
                            @endforeach
                        </div>
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
