@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Role Management</h2>
            </div>
            <div class="pull-right">
                <!-- Changed data-toggle and data-target to Bootstrap 5 format -->
                @can('role-create')
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                        Create New Role
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th width="200px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $key => $role)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $role->name }}</td>
                            <td>
                                <!-- Show Role Modal Button -->
                                @can('role-view')
                                    <button class="btn btn-info" data-bs-toggle="modal"
                                        data-bs-target="#showRoleModal{{ $role->id }}">Show</button>
                                @endcan

                                <!-- Edit Role Modal Button -->
                                @can('role-update')
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#editRoleModal{{ $role->id }}">Edit</button>
                                @endcan

                                <!-- Delete Form -->
                                {!! Form::open([
                                    'method' => 'DELETE',
                                    'route' => ['roles.destroy', $role->id],
                                    'style' => 'display:inline',
                                    'onsubmit' => 'return confirmDelete()',
                                ]) !!}
                                <button type="submit" class="btn btn-outline-danger" style="border:none;"
                                    onclick="return confirm('Are you sure you want to delete this role?')">
                                    <i class="fa-solid fa-trash-can fa-lg"></i>
                                </button>
                                {!! Form::close() !!}
                            </td>
                        </tr>

                        <!-- Show Role Modal -->
                        <div class="modal fade" id="showRoleModal{{ $role->id }}" tabindex="-1"
                            aria-labelledby="showRoleModalLabel{{ $role->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="showRoleModalLabel{{ $role->id }}">Show Role</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Name:</strong> {{ $role->name }}</p>
                                        <p><strong>Permissions:</strong></p>
                                        <ul>
                                            @foreach ($role->permissions as $permission)
                                                <li>{{ $permission->name }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Role Modal -->
                        <div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1"
                            aria-labelledby="editRoleModalLabel{{ $role->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editRoleModalLabel{{ $role->id }}">Edit Role</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        {!! Form::model($role, [
                                            'method' => 'PATCH',
                                            'route' => ['roles.update', $role->id],
                                            'id' => 'editRoleForm' . $role->id,
                                        ]) !!}
                                        <div class="form-group">
                                            <strong>Name:</strong>
                                            {!! Form::text('name', null, ['class' => 'form-control', 'id' => 'editRoleName' . $role->id]) !!}
                                        </div>
                                        <div class="form-group">
                                            <strong>Permission:</strong>
                                            <br />
                                            @foreach ($permissions as $permission)
                                                <label for="permission{{ $permission->id }}">
                                                    {{ Form::checkbox('permissions[]', $permission->id, in_array($permission->id, $role->permissions->pluck('id')->toArray()), ['class' => 'edit-permission', 'id' => 'permission' . $permission->id]) }}
                                                    {{ $permission->name }}
                                                </label>
                                                <br />
                                            @endforeach
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Role Modal -->
    <div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createRoleModalLabel">Create New Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" placeholder="Name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <strong>Permission:</strong>
                            <br />
                            @foreach ($permissions as $permission)
                                <label for="permission{{ $permission->id }}">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        class="create-permission" id="permission{{ $permission->id }}">
                                    {{ $permission->name }}
                                </label>
                                <br />
                            @endforeach
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
