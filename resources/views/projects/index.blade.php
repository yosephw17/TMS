@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Projects</h2>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="projectTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="pending-tab" data-bs-toggle="tab" href="#pending" role="tab"
                    aria-controls="pending" aria-selected="true">Pending</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="completed-tab" data-bs-toggle="tab" href="#completed" role="tab"
                    aria-controls="completed" aria-selected="false">Completed</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="canceled-tab" data-bs-toggle="tab" href="#canceled" role="tab"
                    aria-controls="canceled" aria-selected="false">Canceled</a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content mt-4" id="projectTabContent">
            <!-- Pending Projects Tab -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <h4>Pending Projects</h4>
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Starting Date</th>
                                <th>Ending Date</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendingProjects as $project)
                                <tr>
                                    <td>{{ $project->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($project->starting_date)->format('F d, Y') }}</td>
                                    <td>{{ $project->ending_date ? \Carbon\Carbon::parse($project->ending_date)->format('F d, Y') : 'Ongoing' }}
                                    </td>
                                    <td>{{ $project->location }}</td>
                                    <td>
                                        <span class="badge bg-warning">{{ ucfirst($project->status) }}</span>
                                    </td>
                                    <td>
                                        @can('project-view')
                                            <a href="{{ route('projects.view', $project->id) }}"
                                                class="btn btn-sm btn-primary">View</a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">No pending projects available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Completed Projects Tab -->
            <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                <h4>Completed Projects</h4>
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Starting Date</th>
                                <th>Ending Date</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($completedProjects as $project)
                                <tr>
                                    <td>{{ $project->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($project->starting_date)->format('F d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($project->ending_date)->format('F d, Y') }}</td>
                                    <td>{{ $project->location }}</td>
                                    <td>
                                        <span class="badge bg-success">{{ ucfirst($project->status) }}</span>
                                    </td>

                                    <td>
                                        @can('project-view')
                                            <a href="{{ route('projects.view', $project->id) }}"
                                                class="btn btn-sm btn-primary">View</a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">No completed projects available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Canceled Projects Tab -->
            <div class="tab-pane fade" id="canceled" role="tabpanel" aria-labelledby="canceled-tab">
                <h4>Canceled Projects</h4>
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Starting Date</th>
                                <th>Ending Date</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($canceledProjects as $project)
                                <tr>
                                    <td>{{ $project->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($project->starting_date)->format('F d, Y') }}</td>
                                    <td>{{ $project->ending_date ? \Carbon\Carbon::parse($project->ending_date)->format('F d, Y') : 'Ongoing' }}
                                    </td>
                                    <td>{{ $project->location }}</td>
                                    <td>
                                        <span class="badge bg-danger">{{ ucfirst($project->status) }}</span>
                                    </td>
                                    <td>
                                        @can('project-view')
                                            <a href="{{ route('projects.view', $project->id) }}"
                                                class="btn btn-sm btn-primary">View</a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">No canceled projects available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
