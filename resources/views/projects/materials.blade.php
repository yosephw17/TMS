@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Materials for {{ $customer->name }}</h2>
                <p class="text-muted">Material Customer - Simplified Material Management</p>
            </div>
            <div class="pull-right">
                @can('project-create')
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#materialOrderModal">
                        Create Material Order
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- Material Orders List -->
    <div class="card mb-4">
        <div class="card-body">
            <table id="datatablesSimple" class="table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th width="200px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projects as $key => $project)
                        <tr>
                            <td>{{ $project->id }}</td>
                            <td>{{ $project->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($project->starting_date)->format('d/m/Y') }}</td>
                            <td>{{ Str::limit($project->description, 50) }}</td>
                            <td>
                                <span class="badge 
                                    @if($project->status === 'completed') bg-success
                                    @elseif($project->status === 'pending') bg-warning
                                    @elseif($project->status === 'cancelled') bg-danger
                                    @else bg-primary @endif">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </td>
                            <td>
                                @can('project-view')
                                    <a href="{{ route('projects.view', $project->id) }}" class="btn btn-sm btn-info">
                                        View Materials
                                    </a>
                                @endcan
                                @can('project-edit')
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#editOrderModal{{ $project->id }}">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>

                        <!-- Edit Order Modal -->
                        <div class="modal fade" id="editOrderModal{{ $project->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Material Order</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('projects.update', $project->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                            
                                            <div class="form-group mb-3">
                                                <label for="name">Order Name</label>
                                                <input type="text" name="name" value="{{ $project->name }}" class="form-control" required>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="starting_date">Date</label>
                                                <input type="date" name="starting_date" value="{{ $project->starting_date }}" class="form-control" required>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="description">Description</label>
                                                <textarea name="description" class="form-control" rows="3" required>{{ $project->description }}</textarea>
                                            </div>

                                            <div class="text-center">
                                                <button type="submit" class="btn btn-primary">Update Order</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Material Order Modal -->
    <div class="modal fade" id="materialOrderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Material Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('projects.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <input type="hidden" name="ending_date" value="{{ date('Y-m-d') }}">
                        <input type="hidden" name="location" value="Material Order">
                        
                        <div class="form-group mb-3">
                            <label for="name">Order Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g., Aluminum Profiles Order #1" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="starting_date">Date</label>
                            <input type="date" name="starting_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Describe the materials needed..." required></textarea>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Create Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
