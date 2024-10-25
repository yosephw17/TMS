@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Stock Management</h2>
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createStockModal">
                    Create New Stock
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
                        <th>Stock Name</th>
                        <th>Location</th>
                        <th width="200px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stocks as $key => $stock)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $stock->name }}</td>
                            <td>{{ $stock->location }}</td>
                            <td>
                                <a class="btn btn-sm btn-info" href="{{ route('stocks.show', $stock->id) }}">
                                    View
                                </a>
                                <!-- Edit Button -->
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#editStockModal-{{ $stock->id }}">
                                    <i class="fa-regular fa-pen-to-square"></i> </button>

                                <!-- Delete Button -->
                                <form id="delete-form-{{ $stock->id }}"
                                    action="{{ route('stocks.destroy', $stock->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')

                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="if(confirm('Are you sure you want to delete this stock?')) { document.getElementById('delete-form-{{ $stock->id }}').submit(); }">
                                        <i class="fa-solid fa-trash-can fa-lg"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                        <!-- Edit Stock Modal -->
                        <div class="modal fade" id="editStockModal-{{ $stock->id }}" tabindex="-1"
                            aria-labelledby="editStockModalLabel-{{ $stock->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editStockModalLabel-{{ $stock->id }}">Edit Stock
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('stocks.update', $stock->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-group">
                                                <strong>Stock Name:</strong>
                                                <input type="text" name="name" value="{{ $stock->name }}"
                                                    placeholder="Stock Name" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <strong>Location:</strong>
                                                <input type="text" name="location" value="{{ $stock->location }}"
                                                    placeholder="Location" class="form-control" required>
                                            </div>
                                            <div class="text-center">
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

    <!-- Create Stock Modal -->
    <div class="modal fade" id="createStockModal" tabindex="-1" aria-labelledby="createStockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createStockModalLabel">Create New Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('stocks.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <strong>Stock Name:</strong>
                            <input type="text" name="name" placeholder="Stock Name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <strong>Location:</strong>
                            <input type="text" name="location" placeholder="Location" class="form-control" required>
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
