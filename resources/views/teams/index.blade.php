@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Teams</h2>

        <!-- Button to trigger modal to add new team -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addTeamModal">Add Team</button>

        <!-- Team List Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($teams as $team)
                    <tr>
                        <td>{{ $team->name }}</td>
                        <td>{{ $team->description }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <!-- Show Button -->
                                <button class="btn btn-info" data-bs-toggle="modal"
                                    data-bs-target="#showTeamModal{{ $team->id }}">Show</button>

                                <!-- Edit Button -->
                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editTeamModal{{ $team->id }}"><i
                                        class="fa-regular fa-pen-to-square"></i></button>

                            </div>
                            <!-- Delete Form -->
                            {!! Form::open(['method' => 'DELETE', 'route' => ['teams.destroy', $team->id], 'style' => 'display:inline']) !!}
                            <button type="submit" class="btn btn-outline-danger"
                                onclick="return confirm('Are you sure you want to delete this team?')"> <i
                                    class="fa-solid fa-trash-can fa-lg"></i>
                            </button>
                            {!! Form::close() !!}
    </div>
    </td>
    </tr>

    <!-- Show Team Modal -->
    <div class="modal fade" id="showTeamModal{{ $team->id }}" tabindex="-1"
        aria-labelledby="showTeamModalLabel{{ $team->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Team Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Team Information -->
                    <p><strong>Name:</strong> {{ $team->name }}</p>
                    <p><strong>Description:</strong> {{ $team->description }}</p>

                    <!-- List of Users Assigned to this Team -->
                    <p><strong>Users Assigned:</strong></p>
                    @if ($team->users->count() > 0)
                        <ul>
                            @foreach ($team->users as $user)
                                <li>{{ $user->name }} </li>
                            @endforeach
                        </ul>
                    @else
                        <p>No users are assigned to this team.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Team Modal -->
    <div class="modal fade" id="editTeamModal{{ $team->id }}" tabindex="-1"
        aria-labelledby="editTeamModalLabel{{ $team->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Team</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('teams.update', $team->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $team->name }}" required>
                        </div>
                        <div class="form-group mt-2">
                            <label for="description">Description</label>
                            <textarea name="description" class="form-control">{{ $team->description }}</textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-warning">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    </tbody>
    </table>

    <!-- Add Team Modal -->
    <div class="modal fade" id="addTeamModal" tabindex="-1" aria-labelledby="addTeamModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Team</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('teams.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mt-2">
                            <label for="description">Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
