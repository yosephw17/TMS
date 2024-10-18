@extends('layouts.admin')

@section('content')
    <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">{{ $service->name }}</h4>
            <!-- Change this to the appropriate permission if necessary -->
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addSpecificServiceModal">
                <i class="bi bi-plus-circle me-2"></i>Add Specific Service
            </button>

            <!-- Add Specific Service Modal -->
            <div class="modal fade" id="addSpecificServiceModal" tabindex="-1" aria-labelledby="addSpecificServiceModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-dark" id="addSpecificServiceModalLabel">Add New Specific Service
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            {!! Form::open(['route' => 'service-details.store', 'method' => 'POST']) !!}
                            <div class="mb-3">
                                <label for="detail_name" class="form-label text-dark">Detail Name</label>
                                <input type="text" class="form-control" id="detail_name" name="detail_name" required>
                                <input type="hidden" name="service_id" value="{{ $service->id }}">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label text-dark">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Specific Service</button>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">

            <h6>Specific Service Details:</h6>
            @if ($service->serviceDetails->isEmpty())
                <p>No specific details available for this service.</p>
            @else
                <ul class="list-group">
                    @foreach ($service->serviceDetails as $detail)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>{{ $detail->detail_name }}</strong>: {{ $detail->description }}
                            <div>
                                <!-- Edit Button -->
                                <!-- Adjust this permission as needed -->
                                <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#editSpecificServiceModal{{ $detail->id }}">
                                    <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                </button>

                                <!-- Delete Button -->
                                <!-- Adjust this permission as needed -->
                                {!! Form::open([
                                    'route' => ['service-details.destroy', $detail->id],
                                    'method' => 'DELETE',
                                    'style' => 'display:inline',
                                ]) !!}
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this specific service?');"><i
                                        class="fa-solid fa-trash-can fa-lg"></i></button>
                                {!! Form::close() !!}
                            </div>
                        </li>

                        <!-- Edit Specific Service Modal -->
                        <div class="modal fade" id="editSpecificServiceModal{{ $detail->id }}" tabindex="-1"
                            aria-labelledby="editSpecificServiceModalLabel{{ $detail->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editSpecificServiceModalLabel{{ $detail->id }}">Edit
                                            Specific Service</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        {!! Form::model($detail, ['route' => ['service-details.update', $detail->id], 'method' => 'PATCH']) !!}
                                        <div class="mb-3">
                                            <label for="detail_name" class="form-label">Detail Name</label>
                                            {!! Form::text('detail_name', null, ['class' => 'form-control', 'required']) !!}
                                        </div>
                                        <input type="hidden" name="service_id" value="{{ $service->id }}">

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3]) !!}
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </ul>
            @endif

        </div>
    </div>
@endsection
