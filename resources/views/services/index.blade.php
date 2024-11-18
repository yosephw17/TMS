@extends('layouts.admin')

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h2>Services</h2>
                    </div>
                    <div class="pull-right mb-3">
                        @can('service-create')
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#createServiceModal">
                                Create New Service
                            </button>
                        @endcan
                    </div>
                </div>
            </div>

            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Details</th>
                        <th width="280px">Action</th>
                    </tr>
                </thead>
                @foreach ($services as $service)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $service->name }}</td>
                        <td>{{ $service->details }}</td>
                        <td>
                            <div class="btn-group" role="group" aria-label="Service Actions">
                                @can('service-view')
                                    <button class="btn btn-info" data-bs-toggle="modal"
                                        data-bs-target="#serviceModal{{ $service->id }}"
                                        onclick="showServiceDetails({{ $service->id }})">Show</button>
                                @endcan
                                @can('service-update')
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#editServiceModal{{ $service->id }}">
                                        <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                    </button>
                                @endcan
                                {{-- <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
                                    data-bs-target="#addServiceDetailModal{{ $service->id }}">
                                    Add Specific Service
                                </button> --}}

                                {!! Form::open([
                                    'method' => 'DELETE',
                                    'route' => ['services.destroy', $service->id],
                                    'style' => 'display:inline',
                                ]) !!}
                                @can('service-delete')
                                    <button type="submit" class="btn btn-outline-danger" style="border:none;"
                                        onclick="return confirm('Are you sure you want to delete the service?');">
                                        <i class="fa-solid fa-trash-can fa-lg"></i>
                                    </button>
                                @endcan

                                {!! Form::close() !!}
                            </div>
                            @can('service-detail-view')
                                <button type="button"class="btn btn-primary"
                                    onclick="window.location='{{ route('services.show', $service->id) }}'">
                                    View
                                </button>
                            @endcan

                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>

    <!-- Create Service Modal -->
    <div class="modal fade" id="createServiceModal" tabindex="-1" aria-labelledby="createServiceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createServiceModalLabel">Create New Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['route' => 'services.store', 'method' => 'POST']) !!}
                    <div class="form-group">
                        <strong>Name:</strong>
                        {!! Form::text('name', null, ['placeholder' => 'Name', 'class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        <strong>Details:</strong>
                        {!! Form::textarea('details', null, ['placeholder' => 'Details', 'class' => 'form-control']) !!}
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    @foreach ($services as $service)
        <div class="modal fade" id="serviceModal{{ $service->id }}" tabindex="-1"
            aria-labelledby="serviceModalLabel{{ $service->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="serviceModalLabel{{ $service->id }}">Service Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Name:</strong> {{ $service->name }}</p>
                        <p><strong>Details:</strong> {{ $service->details }}</p>

                        <h6>Specific Service Details:</h6>
                        @if ($service->serviceDetails->isEmpty())
                            <p>No specific details available for this service.</p>
                        @else
                            <ul class="list-group">
                                @foreach ($service->serviceDetails as $detail)
                                    <li class="list-group-item">
                                        <strong>{{ $detail->detail_name }}</strong>: {{ $detail->description }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Edit Service Modal -->
        <div class="modal fade" id="editServiceModal{{ $service->id }}" tabindex="-1"
            aria-labelledby="editServiceModalLabel{{ $service->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editServiceModalLabel{{ $service->id }}">Edit Service</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('services.update', $service->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="form-group">
                                <label for="editServiceName{{ $service->id }}"><strong>Name:</strong></label>
                                <input type="text" name="name" value="{{ $service->name }}"
                                    id="editServiceName{{ $service->id }}" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="editServiceDetails{{ $service->id }}"><strong>Details:</strong></label>
                                <textarea name="details" id="editServiceDetails{{ $service->id }}" class="form-control" rows="3" required>{{ $service->details }}</textarea>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addServiceDetailModal{{ $service->id }}" tabindex="-1"
            aria-labelledby="addServiceDetailModalLabel{{ $service->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addServiceDetailModalLabel{{ $service->id }}">Add Specific Service
                            for {{ $service->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {!! Form::open(['route' => 'service-details.store', 'method' => 'POST']) !!}
                        <div class="form-group">
                            <label for="detail_name{{ $service->id }}"><strong>Detail Name:</strong></label>
                            <input type="text" name="detail_name" id="detail_name{{ $service->id }}"
                                class="form-control" required>
                            <input type="hidden" name="service_id" value="{{ $service->id }}">
                        </div>
                        <div class="form-group">
                            <label for="description{{ $service->id }}"><strong>Description:</strong></label>
                            <textarea name="description" id="description{{ $service->id }}" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Add Detail</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
