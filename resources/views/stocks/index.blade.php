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
                                <a class="btn btn-info" href="{{ route('stocks.show', $stock->id) }}">
                                    View
                                </a>
                                <button type="button" class="btn btn-danger" onclick="deleteStock({{ $stock->id }})">
                                    Delete
                                </button>
                            </td>
                        </tr>
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
