@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Customer Management</h2>
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createCustomerModal">
                    Create New Customer
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
                        <th>Phone</th>
                        <th>Address</th>
                        <th width="200px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $key => $customer)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ $customer->address }}</td>
                            <td>
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                    data-bs-target="#showCustomerModal{{ $customer->id }}">
                                    Show
                                </button>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#editCustomerModal{{ $customer->id }}">
                                    <i class="fa-regular fa-pen-to-square"></i>

                                </button>
                                <button type="button" class="btn btn-outline-danger"
                                    onclick="deleteCustomer({{ $customer->id }})" style="border:none;">
                                    <i class="fa-solid fa-trash-can fa-lg"></i>
                                </button>
                                <a href="{{ route('projects.show', $customer->id) }}" class="btn btn-sm btn-primary">
                                    View Projects
                                </a>
                            </td>
                        </tr>

                        <!-- Show Customer Modal -->
                        <div class="modal fade" id="showCustomerModal{{ $customer->id }}" tabindex="-1"
                            aria-labelledby="showCustomerModalLabel{{ $customer->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="showCustomerModalLabel{{ $customer->id }}">Show
                                            Customer
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Name:</strong> {{ $customer->name }}</p>
                                        <p><strong>Phone:</strong> {{ $customer->phone }}</p>
                                        <p><strong>Address:</strong> {{ $customer->address }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Customer Modal -->
                        <div class="modal fade" id="editCustomerModal{{ $customer->id }}" tabindex="-1"
                            aria-labelledby="editCustomerModalLabel{{ $customer->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editCustomerModalLabel{{ $customer->id }}">Edit
                                            Customer</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="form-group">
                                                <strong>Name:</strong>
                                                <input type="text" name="name" value="{{ $customer->name }}"
                                                    class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <strong>Phone:</strong>
                                                <input type="text" name="phone" value="{{ $customer->phone }}"
                                                    class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <strong>Address:</strong>
                                                <input type="text" name="address" value="{{ $customer->address }}"
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

    <!-- Create Customer Modal -->
    <div class="modal fade" id="createCustomerModal" tabindex="-1" aria-labelledby="createCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCustomerModalLabel">Create New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('customers.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" placeholder="Name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <strong>Phone:</strong>
                            <input type="text" name="phone" placeholder="Phone" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <strong>Address:</strong>
                            <input type="text" name="address" placeholder="Address" class="form-control" required>
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
    function deleteCustomer(customerId) {
        if (confirm("Are you sure to delete this customer?")) {
            fetch(`customers/${customerId}`, {
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
                        alert("Failed to delete customer.");
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    }
</script>
