@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Material Management</h2>
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createMaterialModal">
                    Create New Material
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
                        <th>Symbol</th>
                        <th>Unit Price</th>
                        <th>Unit of Measurement</th>
                        <th width="200px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($materials as $key => $material)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $material->name }}</td>
                            <td>{{ $material->symbol }}</td>
                            <td>{{ $material->unit_price }}</td>
                            <td>{{ $material->unit_of_measurement }}</td>
                            <td>
                                <button class="btn btn-info" data-bs-toggle="modal"
                                    data-bs-target="#showMaterialModal{{ $material->id }}">
                                    Show
                                </button>
                                <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#editMaterialModal{{ $material->id }}">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-outline-danger"
                                    onclick="deleteMaterial({{ $material->id }})" style="border:none;">
                                    <i class="fa-solid fa-trash-can fa-lg"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Show Material Modal -->
                        <div class="modal fade" id="showMaterialModal{{ $material->id }}" tabindex="-1"
                            aria-labelledby="showMaterialModalLabel{{ $material->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="showMaterialModalLabel{{ $material->id }}">Show
                                            Material</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Name:</strong> {{ $material->name }}</p>
                                        <p><strong>Symbol:</strong> {{ $material->symbol }}</p>
                                        <p><strong>Unit Price:</strong> {{ $material->unit_price }}</p>
                                        <p><strong>Unit of Measurement:</strong> {{ $material->unit_of_measurement }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Material Modal -->
                        <div class="modal fade" id="editMaterialModal{{ $material->id }}" tabindex="-1"
                            aria-labelledby="editMaterialModalLabel{{ $material->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editMaterialModalLabel{{ $material->id }}">Edit
                                            Material</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('materials.update', $material->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="form-group">
                                                <strong>Name:</strong>
                                                <input type="text" name="name" value="{{ $material->name }}"
                                                    class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <strong>Symbol:</strong>
                                                <input type="text" name="symbol" value="{{ $material->symbol }}"
                                                    class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <strong>Unit Price:</strong>
                                                <input type="text" name="unit_price" value="{{ $material->unit_price }}"
                                                    class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <strong>Unit of Measurement:</strong>
                                                <input type="text" name="unit_of_measurement"
                                                    value="{{ $material->unit_of_measurement }}" class="form-control"
                                                    required>
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

    <!-- Create Material Modal -->
    <div class="modal fade" id="createMaterialModal" tabindex="-1" aria-labelledby="createMaterialModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createMaterialModalLabel">Create New Material</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('materials.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" placeholder="Name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <strong>Symbol:</strong>
                            <input type="text" name="symbol" placeholder="Symbol" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <strong>Unit Price:</strong>
                            <input type="text" name="unit_price" placeholder="Unit Price" class="form-control"
                                required>
                        </div>
                        <div class="form-group">
                            <strong>Unit of Measurement:</strong>
                            <input type="text" name="unit_of_measurement" placeholder="Unit of Measurement"
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
@endsection

<script>
    function deleteMaterial(materialId) {
        if (confirm("Are you sure to delete this material?")) {
            fetch(`materials/${materialId}`, {
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
                        alert("Failed to delete material.");
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    }
</script>
