<div class="tab-pane fade" id="purchase-proforma{{ $project->id }}" role="tabpanel"
    aria-labelledby="purchase-proforma-tab{{ $project->id }}">

    <div class="card">
        <div class="card-header">
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="aluminium-tab{{ $project->id }}" data-bs-toggle="tab"
                        data-bs-target="#aluminium{{ $project->id }}" type="button" role="tab"
                        aria-controls="aluminium{{ $project->id }}" aria-selected="true">Aluminium</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="finishing-tab{{ $project->id }}" data-bs-toggle="tab"
                        data-bs-target="#finishing{{ $project->id }}" type="button" role="tab"
                        aria-controls="finishing{{ $project->id }}" aria-selected="false">Finishing</button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Aluminium Proformas Tab -->
                <div class="tab-pane fade show active" id="aluminium{{ $project->id }}" role="tabpanel"
                    aria-labelledby="aluminium-tab{{ $project->id }}">

                    <div class="row">
                        @php
                            $aluminiumProformas = $project->proformaImages->where('proforma_type', 'aluminium');
                        @endphp

                        @if ($aluminiumProformas->isEmpty())
                            <p>No Aluminium Proformas available.</p>
                        @else
                            @foreach ($aluminiumProformas as $proforma)
                                <div class="col-md-4 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <p>Seller: {{ $proforma->seller->name }}</p>
                                            <p>Phone: {{ $proforma->seller->phone }}</p>
                                        </div>
                                        <div class="card-body">
                                            <a href="{{ asset('storage/' . $proforma->image_path) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $proforma->image_path) }}"
                                                    alt="Proforma Image" style="width: 100%; height: auto;"
                                                    class="img-thumbnail">
                                            </a>
                                            <h6>Related Project:</h6>
                                            <p><strong>{{ $project->name }}</strong></p>
                                            <p>Status: <strong>{{ ucfirst($proforma->status) }}</strong></p>
                                        </div>
                                        <div class="card-footer d-flex justify-content-between">
                                            <form action="{{ route('proforma_images.approve', $proforma->id) }}"
                                                method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                            </form>
                                            <form action="{{ route('proforma_images.decline', $proforma->id) }}"
                                                method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">Decline</button>
                                            </form>
                                            <form action="{{ route('proforma_images.destroy', $proforma->id) }}"
                                                method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this image?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Finishing Proformas Tab -->
                <div class="tab-pane fade" id="finishing{{ $project->id }}" role="tabpanel"
                    aria-labelledby="finishing-tab{{ $project->id }}">
                    <div class="row">
                        @php
                            $finishingProformas = $project->proformaImages->where('proforma_type', 'finishing');
                        @endphp

                        @if ($finishingProformas->isEmpty())
                            <p>No Finishing Proformas available.</p>
                        @else
                            @foreach ($finishingProformas as $proforma)
                                <div class="col-md-4 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <p>Seller: {{ $proforma->seller->name }}</p>
                                            <p>Phone: {{ $proforma->seller->phone }}</p>
                                        </div>
                                        <div class="card-body">
                                            <a href="{{ asset('storage/' . $proforma->image_path) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $proforma->image_path) }}"
                                                    alt="Proforma Image" style="width: 100%; height: auto;"
                                                    class="img-thumbnail">
                                            </a>
                                            <h6>Related Project:</h6>
                                            <p><strong>{{ $project->name }}</strong></p>
                                            <p>Status: <strong>{{ ucfirst($proforma->status) }}</strong></p>
                                        </div>
                                        <div class="card-footer d-flex justify-content-between">
                                            <form action="{{ route('proforma_images.approve', $proforma->id) }}"
                                                method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                            </form>
                                            <form action="{{ route('proforma_images.decline', $proforma->id) }}"
                                                method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">Decline</button>
                                            </form>
                                            <form action="{{ route('proforma_images.destroy', $proforma->id) }}"
                                                method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this image?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
