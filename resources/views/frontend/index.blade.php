@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Project Management</h2>
            </div>
            <div class="pull-right">
                @can('project-create')
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                        Create New Project
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <table id="datatablesSimple" class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Project Name</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Image</th>
                        <th width="200px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projects as $key => $project)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $project->project_name }}</td>
                            <td>{{ $project->description }}</td>
                            <td>{{ $project->date }}</td>
                            <td>{{ $project->client }}</td>
                            <td>
                                @if ($project->image)
                                    <img src="{{ asset('storage/' . $project->image) }}" alt="Image" width="50">
                                @endif
                            </td>
                            <td>
                                @can('project-view')
                                    <button class="btn btn-info" data-bs-toggle="modal"
                                        data-bs-target="#showProjectModal{{ $project->id }}">Show</button>
                                @endcan
                                @can('project-edit')
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#editProjectModal{{ $project->id }}">Edit</button>
                                @endcan
                                @can('project-delete')
                                    <button type="button" class="btn btn-outline-danger"
                                        onclick="deleteProject({{ $project->id }})" style="border:none;">
                                        <i class="fa-solid fa-trash-can fa-lg"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>

                        <!-- Show Project Modal -->
                        <div class="modal fade" id="showProjectModal{{ $project->id }}" tabindex="-1"
                            aria-labelledby="showProjectModalLabel{{ $project->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="showProjectModalLabel{{ $project->id }}">Show Project
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Project Name:</strong> {{ $project->project_name }}</p>
                                        <p><strong>Description:</strong> {{ $project->description }}</p>
                                        <p><strong>Date:</strong> {{ $project->date }}</p>
                                        <p><strong>Client:</strong> {{ $project->client }}</p>
                                        @if ($project->image)
                                            <p><strong>Image:</strong> <img src="{{ asset('storage/' . $project->image) }}"
                                                    alt="Image" width="100"></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Project Modal -->
                        <div class="modal fade" id="editProjectModal{{ $project->id }}" tabindex="-1"
                            aria-labelledby="editProjectModalLabel{{ $project->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editProjectModalLabel{{ $project->id }}">Edit Project
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('frontends.update', $project->id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PATCH')

                                            <div class="form-group">
                                                <strong>Project Name:</strong>
                                                <input type="text" name="project_name"
                                                    value="{{ $project->project_name }}" class="form-control" required>
                                            </div>

                                            <div class="form-group">
                                                <strong>Description:</strong>
                                                <textarea name="description" class="form-control" required>{{ $project->description }}</textarea>
                                            </div>

                                            <div class="form-group">
                                                <strong>Date:</strong>
                                                <input type="date" name="date" value="{{ $project->date }}"
                                                    class="form-control" required>
                                            </div>

                                            <div class="form-group">
                                                <strong>Client:</strong>
                                                <input type="text" name="client" value="{{ $project->client }}"
                                                    class="form-control" required>
                                            </div>

                                            <div class="form-group">
                                                <strong>Image:</strong>
                                                <input type="file" name="image" class="form-control">
                                                <small>Leave blank to keep current image.</small>
                                                @if ($project->image)
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/' . $project->image) }}"
                                                            alt="Current Image" width="50">
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="mt-3">
                                                <button type="submit" class="btn btn-primary">Update</button>
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

    <!-- Create Project Modal -->
    <div class="modal fade" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createProjectModalLabel">Create New Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('frontends.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <strong>Project Name:</strong>
                            <input type="text" name="project_name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <strong>Description:</strong>
                            <textarea name="description" class="form-control" required></textarea>
                        </div>

                        <div class="form-group">
                            <strong>Date:</strong>
                            <input type="date" name="date" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <strong>Client:</strong>
                            <input type="text" name="client" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <strong>Image:</strong>
                            <input type="file" name="image" class="form-control">
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteProject(id) {
            if (confirm('Are you sure to delete this project?')) {
                window.location.href = "/frontends/delete/" + id; // Adjust your route
            }
        }
    </script>
@endsection
