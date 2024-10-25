@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ $customer->name }}'s Project</h2>
            </div>
            <div class="pull-right">
                <!-- Button to trigger modal -->
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#serviceModal">
                    Add Project
                </button>
            </div>
        </div>
    </div>



    <!-- Modal -->
    <div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceModalLabel">Select Services for Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('projects.store') }}" method="POST">
                        @csrf
                        <!-- Project Name and other fields -->
                        <div class="form-group mb-3">
                            <label for="name">Project Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <!-- Starting Date -->
                        <div class="form-group mb-3">
                            <label for="starting_date">Starting Date</label>
                            <input type="date" name="starting_date" class="form-control" required>
                        </div>
                        <!-- Ending Date -->
                        <div class="form-group mb-3">
                            <label for="ending_date">Ending Date</label>
                            <input type="date" name="ending_date" class="form-control" required>
                        </div>
                        <!-- Hidden customer ID input -->
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">

                        <!-- Description -->
                        <div class="form-group mb-3">
                            <label for="description">Project Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>

                        <!-- Location -->
                        <div class="form-group mb-3">
                            <label for="location">Location</label>
                            <input type="text" name="location" class="form-control" required>
                        </div>

                        <!-- Select Services and Categorized Service Details -->
                        <div class="form-group">
                            <label for="services">Select Service Details</label>
                            <div class="services">
                                @foreach ($services as $service)
                                    <div class="service-category">
                                        <strong>{{ $service->name }}</strong> <!-- Service Name -->
                                        <div class="service-details">
                                            @foreach ($service->serviceDetails as $serviceDetail)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="service_detail_ids[]" value="{{ $serviceDetail->id }}"
                                                        {{ isset($project) && $project->serviceDetails->contains($serviceDetail->id) ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        {{ $serviceDetail->detail_name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <hr>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save Project</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <div class="card mb-4">
        <div
            class="card-header 
            {{ $project->status === 'completed' ? 'bg-success' : ($project->status === 'cancelled' ? 'bg-danger' : ($project->status === 'pending' ? 'bg-warning' : 'bg-secondary')) }} 
            text-white d-flex justify-content-between align-items-center">
            <h4>{{ $project->name }}</h4>
            <!-- Edit Button -->
            <button class="btn btn-sm btn-light" data-bs-toggle="modal"
                data-bs-target="#editProjectModal{{ $project->id }}">
                Edit
            </button>
        </div>

        <!-- Edit Project Modal -->
        <div class="modal fade" id="editProjectModal{{ $project->id }}" tabindex="-1"
            aria-labelledby="editProjectModalLabel{{ $project->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('projects.update', $project->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editProjectModalLabel{{ $project->id }}">Edit Project</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">Project Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $project->name }}"
                                    required>
                            </div>

                            <input type="hidden" name="customer_id" value="{{ $customer->id }}">


                            <div class="form-group mt-3">
                                <label for="starting_date">Starting Date</label>
                                <input type="date" name="starting_date" class="form-control"
                                    value="{{ $project->starting_date }}" required>
                            </div>

                            <div class="form-group mt-3">
                                <label for="ending_date">Ending Date</label>
                                <input type="date" name="ending_date" class="form-control"
                                    value="{{ $project->ending_date }}">
                            </div>

                            <div class="form-group mt-3">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control">{{ $project->description }}</textarea>
                            </div>

                            <div class="form-group mt-3">
                                <label for="location">Location</label>
                                <input type="text" name="location" class="form-control"
                                    value="{{ $project->location }}">
                            </div>

                            <div class="form-group mt-3">
                                <label for="total_price">Total Price</label>
                                <input type="number" name="total_price" class="form-control"
                                    value="{{ $project->total_price }}">
                            </div>

                            <div class="form-group mt-3">
                                <label for="status">Status</label>
                                <select name="status" class="form-control">
                                    <option value="pending" {{ $project->status === 'pending' ? 'selected' : '' }}>
                                        Pending</option>
                                    <option value="completed" {{ $project->status === 'completed' ? 'selected' : '' }}>
                                        Completed</option>
                                    <option value="cancelled" {{ $project->status === 'cancelled' ? 'selected' : '' }}>
                                        Cancelled</option>
                                </select>
                            </div>


                            <div class="form-group">
                                <label for="services">Select Service Details</label>
                                <div class="services">
                                    @foreach ($services as $service)
                                        <div class="service-category">
                                            <strong>{{ $service->name }}</strong> <!-- Service Name -->
                                            <div class="service-details">
                                                @foreach ($service->serviceDetails as $serviceDetail)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="service_detail_ids[]" value="{{ $serviceDetail->id }}"
                                                            {{-- Check if the project exists (for update) and if the service detail is already selected --}}
                                                            {{ isset($project) && $project->serviceDetails->contains($serviceDetail->id) ? 'checked' : '' }}>
                                                        <label class="form-check-label">
                                                            {{ $serviceDetail->detail_name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <hr>
                                        </div>
                                    @endforeach
                                </div>
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

        <div class="card-body">
            <!-- Tabs for Project Details -->
            <ul class="nav nav-tabs" id="projectTab{{ $project->id }}" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="project-tab{{ $project->id }}" data-bs-toggle="tab"
                        data-bs-target="#project{{ $project->id }}" type="button" role="tab"
                        aria-controls="project{{ $project->id }}" aria-selected="true">Project</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="quantity-tab{{ $project->id }}" data-bs-toggle="tab"
                        data-bs-target="#quantity{{ $project->id }}" type="button" role="tab"
                        aria-controls="quantity{{ $project->id }}" aria-selected="false">Quantity</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="expenses-tab{{ $project->id }}" data-bs-toggle="tab"
                        data-bs-target="#expenses{{ $project->id }}" type="button" role="tab"
                        aria-controls="expenses{{ $project->id }}" aria-selected="false">Expenses</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="images-tab{{ $project->id }}" data-bs-toggle="tab"
                        data-bs-target="#images{{ $project->id }}" type="button" role="tab"
                        aria-controls="images{{ $project->id }}" aria-selected="false">Images</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="proformas-tab{{ $project->id }}" data-bs-toggle="tab"
                        data-bs-target="#proformas{{ $project->id }}" type="button" role="tab"
                        aria-controls="proformas{{ $project->id }}" aria-selected="false">Proformas</button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="projectTabContent{{ $project->id }}">
                <!-- Project Tab -->

                @include('tab_components/projectTab')

                @include('tab_components.quantityTab')

                @include('tab_components.expenseTab')

                @include('tab_components.imageTab')

                @include('tab_components.proformaTab')

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Select all edit buttons within the accordion
                        document.querySelectorAll('.edit-proforma-btn').forEach(function(button) {
                            button.addEventListener('click', function(event) {
                                event.stopPropagation(); // Prevent the accordion from closing
                            });
                        });
                    });
                </script>
            @endsection
