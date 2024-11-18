    @extends('layouts.admin')

    @section('content')
        <h4>Sellers Management</h4>
        @can('seller-create')
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createSellerModal">
                Add Seller
            </button>
        @endcan

        <div class="card-body">
            <table id="datatablesSimple" class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sellers as $key => $seller)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $seller->name }}</td>
                            <td>{{ $seller->phone }}</td>
                            <td>
                                @can('seller-view')
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#viewSellerModal{{ $seller->id }}">View</button>
                                @endcan
                                @can('seller-edit')
                                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editSellerModal{{ $seller->id }}">
                                        <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                    </button>
                                @endcan

                                <form action="{{ route('sellers.destroy', $seller->id) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    @can('seller-delete')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this seller?')">
                                            <i class="fa-solid fa-trash-can fa-lg"></i>
                                        </button>
                                    @endcan
                                </form>

                                @can('proforma-image-view')
                                    <a href="{{ route('proforma_images.index', ['seller_id' => $seller->id]) }}"
                                        class="btn btn-primary">View Proformas</a>
                                @endcan

                            </td>
                        </tr>

                        <!-- View Seller Modal -->
                        <div class="modal fade" id="viewSellerModal{{ $seller->id }}" tabindex="-1"
                            aria-labelledby="viewSellerModalLabel{{ $seller->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="viewSellerModalLabel{{ $seller->id }}">View Seller
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Name:</strong> {{ $seller->name }}</p>
                                        <p><strong>Phone:</strong> {{ $seller->phone }}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Seller Modal -->
                        <div class="modal fade" id="editSellerModal{{ $seller->id }}" tabindex="-1"
                            aria-labelledby="editSellerModalLabel{{ $seller->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editSellerModalLabel{{ $seller->id }}">Edit Seller
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('sellers.update', $seller->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ $seller->name }}" required>
                                            </div>
                                            <div class="form-group mt-2">
                                                <label for="phone">Phone</label>
                                                <input type="text" name="phone" class="form-control"
                                                    value="{{ $seller->phone }}" required>
                                            </div>
                                            <div class="mt-3 text-center">
                                                <button type="submit" class="btn btn-success">Update Seller</button>
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

        <!-- Create Seller Modal -->
        <div class="modal fade" id="createSellerModal" tabindex="-1" aria-labelledby="createSellerModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createSellerModalLabel">Create New Seller</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('sellers.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter seller name"
                                    required>
                            </div>
                            <div class="form-group mt-2">
                                <label for="phone">Phone</label>
                                <input type="text" name="phone" class="form-control" placeholder="Enter phone number"
                                    required>
                            </div>
                            <div class="mt-3 text-center">
                                <button type="submit" class="btn btn-primary">Create Seller</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection
