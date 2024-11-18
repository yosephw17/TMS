@extends('layouts.admin')

@section('content')
    <h4>Proforma Images for {{ $seller->name }}</h4>
    @can('proforma-image-create')
        <button class="btn btn-success mb-4" data-bs-toggle="modal" data-bs-target="#addProformaModal">
            Add Proforma Image
        </button>
    @endcan

    <div class="card-body">
        <div class="row">
            @foreach ($proformaImages as $proforma)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Proforma Type: {{ $proforma->proforma_type }}</h5>
                            <p>Seller: {{ $seller->name }}</p>
                            <p>Phone: {{ $seller->phone }}</p>
                        </div>
                        <div class="card-body">
                            <a href="{{ asset('storage/' . $proforma->image_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $proforma->image_path) }}" alt="Proforma Image"
                                    style="width: 100%; height: auto;" class="img-thumbnail">
                            </a>

                            @if ($proforma->project)
                                <h6>Related Project:</h6>
                                <p><strong>{{ $proforma->project->name }}</strong></p>
                            @endif
                            <p>Status: <strong>{{ ucfirst($proforma->status) }}</strong></p>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <form action="{{ route('proforma_images.approve', $proforma->id) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                @can('proforma-image-approve')
                                    <button type="submit" class="btn btn-success btn-sm">
                                        Approve
                                    </button>
                                @endcan
                            </form>

                            <form action="{{ route('proforma_images.decline', $proforma->id) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                @can('proforma-image-decline')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Decline
                                    </button>
                                @endcan
                            </form>

                            <form action="{{ route('proforma_images.destroy', $proforma->id) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                @method('DELETE')
                                @can('proforma-image-delete')
                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this image?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endcan
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>


    <div class="modal fade" id="addProformaModal" tabindex="-1" aria-labelledby="addProformaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProformaModalLabel">Add Proforma Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('proforma_images.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="seller_id" value="{{ $seller->id }}">

                        <div class="form-group">
                            <label for="project_id">Select Project</label>
                            <select name="project_id" class="form-control" required>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mt-2">
                            <label for="type">Proforma Type</label>
                            <select name="type" class="form-control" required>
                                <option value="aluminium">Aluminium</option>
                                <option value="finishing">Finishing</option>
                            </select>
                        </div>

                        <div class="form-group mt-2">
                            <label for="image">Proforma Image</label>
                            <input type="file" name="image" class="form-control" required>
                        </div>

                        <div class="mt-3 text-center">
                            <button type="submit" class="btn btn-success">Upload Proforma</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
