@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Projects for {{ $customer->name }}</h2>
            </div>
            <div class="pull-right">
                <!-- Button to trigger modal -->
                @can('project-create')
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#serviceModal">
                        Add Project
                    </button>
                @endcan
            </div>
        </div>
    </div>



    <!-- Modal -->
    <div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
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

                        <!-- Select Teams -->
                        <div class="form-group">
                            <label for="teams">Select Teams</label>
                            <div id="teamSelect">
                                @foreach ($teams as $team)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="team_ids[]"
                                            value="{{ $team->id }}" id="team{{ $team->id }}"
                                            {{ isset($project) && $project->teams->contains($team->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="team{{ $team->id }}">
                                            {{ $team->name }}
                                        </label>
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


    @foreach ($projects as $project)
        <div class="card mb-4">
            <div
                class="card-header 
            {{ $project->status === 'completed' ? 'bg-success' : ($project->status === 'cancelled' ? 'bg-danger' : ($project->status === 'pending' ? 'bg-warning' : 'bg-secondary')) }} 
            text-white d-flex justify-content-between align-items-center">
                <h4>{{ $project->name }}</h4>
                <!-- Edit Button -->
                @can('project-edit')
                    <button class="btn btn-sm btn-light" data-bs-toggle="modal"
                        data-bs-target="#editProjectModal{{ $project->id }}">
                        Edit
                    </button>
                @endcan
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
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="name">Project Name</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ $project->name }}" required>
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
                                        <option value="completed"
                                            {{ $project->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled"
                                            {{ $project->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>

                                <!-- Services (Select Service Details) -->
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
                                                                name="service_detail_ids[]"
                                                                value="{{ $serviceDetail->id }}"
                                                                {{ isset($project) && $project->serviceDetails->contains($serviceDetail->id) ? 'checked' : '' }}>
                                                            <label
                                                                class="form-check-label">{{ $serviceDetail->detail_name }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <hr>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Team Selection -->
                                <div class="form-group mt-3">
                                    <label for="teams">Select Teams</label>
                                    <div class="teams">
                                        @foreach ($teams as $team)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="team_ids[]"
                                                    value="{{ $team->id }}"
                                                    {{ isset($project) && $project->teams->contains($team->id) ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ $team->name }}</label>
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
                            aria-controls="expenses{{ $project->id }}" aria-selected="false">Costs</button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="proformas-tab{{ $project->id }}" data-bs-toggle="tab"
                            data-bs-target="#proformas{{ $project->id }}" type="button" role="tab"
                            aria-controls="proformas{{ $project->id }}" aria-selected="false">Proformas</button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="images-tab{{ $project->id }}" data-bs-toggle="tab"
                            data-bs-target="#images{{ $project->id }}" type="button" role="tab"
                            aria-controls="images{{ $project->id }}" aria-selected="false">Images</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="task-tab{{ $project->id }}" data-bs-toggle="tab"
                            data-bs-target="#tasks{{ $project->id }}" type="button" role="tab"
                            aria-controls="tasks{{ $project->id }}" aria-selected="false">Daily Tasks</button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="projectTabContent{{ $project->id }}">
                    <!-- Project Tab -->
                    <div class="tab-pane fade show active" id="project{{ $project->id }}" role="tabpanel"
                        aria-labelledby="project-tab{{ $project->id }}">
                        <div class="mt-3">

                            <!-- Starting Date -->
                            <div class="mb-3">
                                <strong class="font-weight-bold mb-1">
                                    <i class="fas fa-calendar-alt"></i> Starting Date:
                                </strong>
                                <p class="lead">{{ \Carbon\Carbon::parse($project->starting_date)->format('F d, Y') }}
                                </p>
                            </div>

                            <!-- Ending Date (Ongoing if null) -->
                            <div class="mb-3">
                                <strong class="font-weight-bold mb-1">
                                    <i class="fas fa-calendar-check"></i> Ending Date:
                                </strong>
                                <p class="lead">
                                    {{ $project->ending_date ? \Carbon\Carbon::parse($project->ending_date)->format('F d, Y') : 'Ongoing' }}
                                </p>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <strong class="font-weight-bold mb-1">
                                    <i class="fas fa-align-left"></i> Description:
                                </strong>
                                <p class="lead">{{ $project->description }}</p>
                            </div>

                            <!-- Location -->
                            <div class="mb-3">
                                <strong class="font-weight-bold mb-1">
                                    <i class="fas fa-map-marker-alt"></i> Location:
                                </strong>
                                <p class="lead">{{ $project->location }}</p>
                            </div>

                            <!-- Services (if any) -->
                            <div class="mb-3">
                                <strong class="font-weight-bold mb-1">
                                    <i class="fas fa-concierge-bell"></i> Services Included:
                                </strong>
                                @if ($project->serviceDetails->count() > 0)
                                    <ul class="list-group">
                                        @foreach ($project->serviceDetails as $serviceDetail)
                                            <li class="list-group-item">
                                                {{ $serviceDetail->detail_name }} (under
                                                {{ $serviceDetail->service->name }})
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">None</p>
                                @endif
                            </div>

                            <!-- Team (if any) -->
                            <div class="mb-3">
                                <strong class="font-weight-bold mb-1">
                                    <i class="fas fa-users"></i> Team:
                                </strong>
                                @if ($project->teams->count() > 0)
                                    <ul class="list-group">
                                        @foreach ($project->teams as $team)
                                            <li class="list-group-item">
                                                {{ $team->name }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">None</p>
                                @endif
                            </div>

                            <!-- Status -->
                            <div class="mb-3">
                                <strong class="font-weight-bold mb-1">
                                    <i class="fas fa-tasks"></i> Status:
                                </strong>
                                <p class="lead">
                                    <span
                                        class="{{ $project->status == 'completed' ? 'text-success' : ($project->status == 'canceled' ? 'text-danger' : 'text-warning') }}">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>


                    @include('tab_components.quantityTab')

                    {{-- <div class="tab-pane fade" id="quantity{{ $project->id }}" role="tabpanel"
                        aria-labelledby="quantity-tab{{ $project->id }}">
                        <div class="mt-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#selectMaterialsModal{{ $project->id }}">
                                Add Materials
                            </button>
                        </div>

                        <div class="mt-3">
                            <h5>Materials</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Material Name</th>
                                        <th>Quantity</th>
                                        <th>Actions</th> <!-- Actions column -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($project->materials as $material)
                                        <tr>
                                            <td>{{ $material->name }}</td>
                                            <td>{{ $material->pivot->quantity }}</td>
                                            <td>
                                                <!-- Edit Button -->
                                                <!-- Edit Button -->
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#editMaterialModal{{ $material->id }}-{{ $project->id }}">
                                                    Edit
                                                </button>

                                                <!-- Delete Button (with a confirmation modal) -->
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteMaterialModal{{ $material->id }}-{{ $project->id }}">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> --}}

                    {{-- @foreach ($project->materials as $material)
                        <!-- Edit Material Modal -->
                        <div class="modal fade" id="editMaterialModal{{ $material->id }}-{{ $project->id }}"
                            tabindex="-1" aria-labelledby="editMaterialLabel{{ $material->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('projectMaterials.update', [$project->id, $material->id]) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editMaterialLabel{{ $material->id }}">Edit
                                                Material Quantity</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="quantity">Quantity</label>
                                                <input type="number" name="quantity" class="form-control"
                                                    value="{{ $material->pivot->quantity }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>


                        <!-- Delete Material Modal -->
                        <div class="modal fade" id="deleteMaterialModal{{ $material->id }}-{{ $project->id }}"
                            tabindex="-1" aria-labelledby="deleteMaterialLabel{{ $material->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('projects.materials.destroy', [$project->id, $material->id]) }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteMaterialLabel{{ $material->id }}">Confirm
                                                Delete</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to delete this material from the project?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach --}}


                    {{-- <!-- Select Materials Modal -->
                    <div class="modal fade" id="selectMaterialsModal{{ $project->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="selectMaterialsModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('projects.addMaterials', $project->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="selectMaterialsModalLabel">Select Materials for
                                            Project</h5>
                                        <button type="button" class="close" data-bs-dismiss="modal"
                                            aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="materials">Materials</label>
                                            <div class="row">
                                                @foreach ($materials as $material)
                                                    <div class="col-md-12">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="materials[]" value="{{ $material->id }}"
                                                                id="material{{ $material->id }}">
                                                            <label class="form-check-label"
                                                                for="material{{ $material->id }}">
                                                                {{ $material->name }}
                                                            </label>
                                                            <!-- Quantity input for each material -->
                                                            <input type="number" name="quantities[{{ $material->id }}]"
                                                                class="form-control mt-2" placeholder="Quantity"
                                                                min="1">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save Materials</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> --}}

                    {{-- <!-- Expenses Tab -->
                    <div class="tab-pane fade" id="expenses{{ $project->id }}" role="tabpanel"
                        aria-labelledby="expenses-tab{{ $project->id }}">
                        <div class="mt-3">
                            <!-- Replace this with the actual expenses information -->
                            <p>Expenses related to this project will be listed here.</p>
                        </div>
                    </div> --}}
                    @include('tab_components.expenseTab')
                    <!-- Images Tab -->
                    @include('tab_components.imageTab')

                    <!-- Proformas Tab -->

                    @php
                        $profileProformas = $project->proformas()->where('type', 'aluminium_profile')->get();
                        $accessoriesProformas = $project->proformas()->where('type', 'aluminium_accessories')->get();
                        $workProformas = $project->proformas()->where('type', 'work')->get();
                        $dailyActivities = $project->dailyActivities;
                    @endphp
                    <!-- Proformas Tab -->
                    <div class="tab-pane fade" id="proformas{{ $project->id }}" role="tabpanel"
                        aria-labelledby="proformas-tab{{ $project->id }}">
                        <div class="card mb-4">

                            <!-- Card Header -->
                            <div class="card-header text-white">
                                <h5>{{ $project->name }} Proformas</h5>
                            </div>

                            <!-- Tabs for Proforma Types -->
                            <div class="card-body">
                                <ul class="nav nav-tabs" id="proformaMainTabs{{ $project->id }}" role="tablist">
                                    <!-- Buyer Proforma Tab -->
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="buyer-tab{{ $project->id }}"
                                            data-bs-toggle="tab" href="#buyerProforma{{ $project->id }}" role="tab"
                                            aria-controls="buyerProforma{{ $project->id }}" aria-selected="true">
                                            <i class="bi bi-cart"></i> Aluminium Proforma
                                        </a>
                                    </li>

                                    <!-- Seller Proforma Tab -->
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="seller-tab{{ $project->id }}" data-bs-toggle="tab"
                                            href="#sellerProforma{{ $project->id }}" role="tab"
                                            aria-controls="sellerProforma{{ $project->id }}" aria-selected="false">
                                            <i class="bi bi-person-badge"></i> Sales Proforma
                                        </a>
                                    </li>
                                </ul>

                                <!-- Main Tab Content -->
                                <div class="tab-content mt-3" id="proformaMainTabContent{{ $project->id }}">
                                    <!-- Buyer Proforma Content -->
                                    <div class="tab-pane fade show active" id="buyerProforma{{ $project->id }}"
                                        role="tabpanel" aria-labelledby="buyer-tab{{ $project->id }}">
                                        <ul class="nav nav-tabs" id="proformaTabs{{ $project->id }}" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link active" id="profile-tab{{ $project->id }}"
                                                    data-bs-toggle="tab" href="#profileProforma{{ $project->id }}"
                                                    role="tab" aria-controls="profileProforma{{ $project->id }}"
                                                    aria-selected="true">
                                                    <i class="bi bi-box-seam"></i> Aluminium Profile Proforma
                                                </a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" id="accessories-tab{{ $project->id }}"
                                                    data-bs-toggle="tab" href="#accessoriesProforma{{ $project->id }}"
                                                    role="tab" aria-controls="accessoriesProforma{{ $project->id }}"
                                                    aria-selected="false">
                                                    <i class="bi bi-gear"></i> Accessories Proforma
                                                </a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" id="work-tab{{ $project->id }}"
                                                    data-bs-toggle="tab" href="#workProforma{{ $project->id }}"
                                                    role="tab" aria-controls="workProforma{{ $project->id }}"
                                                    aria-selected="false">
                                                    <i class="bi bi-briefcase"></i> Work Proforma
                                                </a>
                                            </li>
                                        </ul>

                                        <!-- Buyer Proforma Tab Content -->
                                        <div class="tab-content mt-3" id="proformaTabContent{{ $project->id }}">
                                            @include('tab_components.aluminiumProfileTab')
                                            @include('tab_components.aluminiumAccessoriesTab')
                                            @include('tab_components.workProformaTab')
                                        </div>
                                    </div>

                                    <!-- Seller Proforma Content -->
                                    @include('tab_components.sellerProformaTab')
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- Daily Tasks Tab -->
                    <div class="tab-pane fade" id="tasks{{ $project->id }}" role="tabpanel"
                        aria-labelledby="task-tab{{ $project->id }}">
                        <!-- Daily Tasks Content -->
                        <h5>Daily Tasks for Project {{ $project->name }}</h5>

                        <!-- Form to Add New Task -->
                        <form action="{{ route('daily_activities.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="description">Task Description</label>
                                <textarea name="description" class="form-control" required></textarea>
                            </div>
                            <input type="hidden" name="project_id" value="{{ $project->id }}">
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                            <button type="submit" class="btn btn-primary mt-3">Add Task</button>
                        </form>

                        <!-- Display Existing Tasks -->
                        <ul class="mt-4">
                            @foreach ($project->dailyActivities as $activity)
                                <li>
                                    <strong>{{ $activity->user->name }}</strong>: {{ $activity->description }}
                                    <em>{{ $activity->created_at->format('M d, Y') }}</em>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
