@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Permission Management</h2>
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createPermissionModal">
                    Create New Permission
                </button>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <table id="datatablesSimple" class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th width="200px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($permissions as $key => $permission)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $permission->name }}</td>
                            <td>
                                <button class="btn btn-info" data-bs-toggle="modal"
                                    data-bs-target="#showPermissionModal{{ $permission->id }}">
                                    Show
                                </button>
                                <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#editPermissionModal{{ $permission->id }}">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-outline-danger"
                                    onclick="deletePermission({{ $permission->id }})" style="border:none;">
                                    <i class="fa-solid fa-trash-can fa-lg"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Show Permission Modal -->
                        <div class="modal fade" id="showPermissionModal{{ $permission->id }}" tabindex="-1"
                            aria-labelledby="showPermissionModalLabel{{ $permission->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="showPermissionModalLabel{{ $permission->id }}">Show
                                            Permission</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Name:</strong> {{ $permission->name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Permission Modal -->
                        <div class="modal fade" id="editPermissionModal{{ $permission->id }}" tabindex="-1"
                            aria-labelledby="editPermissionModalLabel{{ $permission->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editPermissionModalLabel{{ $permission->id }}">Edit
                                            Permission</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="form-group">
                                                <strong>Name:</strong>
                                                <input type="text" name="name" value="{{ $permission->name }}"
                                                    class="form-control" required>
                                            </div>
                                            <div class="text-center">
                                                <button type="submit" class="btn btn-primary">Submit</button>
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

    <!-- Create Permission Modal -->
    <div class="modal fade" id="createPermissionModal" tabindex="-1" aria-labelledby="createPermissionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createPermissionModalLabel">Create New Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('permissions.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" placeholder="Name" class="form-control" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    function deletePermission(permissionId) {
        if (confirm("Are you sure to delete this permission?")) {
            fetch(`permissions/${permissionId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert("Failed to delete permission.");
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    }
</script>
