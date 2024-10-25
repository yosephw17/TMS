@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Stock: {{ $stock->name }} ({{ $stock->location }})</h2>
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                    Add Material
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
                        <th>Material Name</th>
                        <th>Color</th>
                        <th>Quantity</th>
                        <th width="200px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stock->materials as $key => $material)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $material->name }}</td>
                            <td>{{ $material->color }}</td>
                            <td>{{ $material->pivot->quantity }} </td>
                            <td <td>
                                <!-- Remove Button that opens the modal -->
                                <button class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#removeMaterialModal{{ $material->id }}">
                                    Remove
                                </button>
                            </td>
                        </tr>

                        <!-- Modal for Removing Material -->
                        </td>
                        </tr>
                        <div class="modal fade" id="removeMaterialModal{{ $material->id }}" tabindex="-1"
                            aria-labelledby="removeMaterialModalLabel{{ $material->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="removeMaterialModalLabel{{ $material->id }}">Remove
                                            Material Quantity</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('stocks.removeMaterial', [$stock->id, $material->id]) }}"
                                            method="POST">
                                            @csrf
                                            @method('POST')
                                            <div class="form-group">
                                                <label for="quantity">Quantity to Remove</label>
                                                <input type="number" name="quantity" class="form-control" min="1"
                                                    max="{{ $material->pivot->quantity }}" required>
                                            </div>
                                            <div class="text-center mt-3">
                                                <button type="submit" class="btn btn-danger">Remove</button>
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

    <!-- Add Material Modal -->
    <div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMaterialModalLabel">Add Material to Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('stocks.addMaterial', $stock->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <strong>Select Material:</strong>
                            <select name="material_id" class="form-control" required>
                                @foreach ($materials as $material)
                                    <option value="{{ $material->id }}">{{ $material->name }}@if ($material->color)
                                            ({{ $material->color }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <strong>Quantity:</strong>
                            <input type="number" name="quantity" class="form-control" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
