@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Material Management</h2>
            </div>
            <div class="pull-right">
                @can('material-create')
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createMaterialModal">
                        Create New Material
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
                        <th>Name</th>
                        <th>Code</th>
                        <th>Symbol</th>
                        <th>Unit of Measurement</th>
                        <th>Color</th>
                        <th width="200px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($materials as $key => $material)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $material->name }}</td>
                            <td>{{ $material->code }}</td>
                            <!-- Display image for symbol -->
                            <td>
                                @if ($material->symbol)
                                    <img src="{{ asset('storage/' . $material->symbol) }}" alt="Symbol" width="50">
                                @endif
                            </td>

                            <td>{{ $material->unit_of_measurement }}</td>
                            <td>{{ $material->color }}</td>
                            <td>
                                @can('material-view')
                                    <button class="btn btn-info" data-bs-toggle="modal"
                                        data-bs-target="#showMaterialModal{{ $material->id }}">
                                        Show
                                    </button>
                                @endcan
                                @can('material-edit')
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#editMaterialModal{{ $material->id }}">
                                        Edit
                                    </button>
                                @endcan
                                @can('material-delete')
                                    <button type="button" class="btn btn-outline-danger"
                                        onclick="deleteMaterial({{ $material->id }})" style="border:none;">
                                        <i class="fa-solid fa-trash-can fa-lg"></i>
                                    </button>
                                @endcan
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
                                        <p><strong>Code:</strong> {{ $material->code }}</p>
                                        <!-- Display symbol image -->
                                        <p><strong>Symbol:</strong> <img src="{{ asset('storage/' . $material->symbol) }}"
                                                alt="Symbol" width="50">
                                        </p>
                                        <p><strong>Unit of Measurement:</strong> {{ $material->unit_of_measurement }}</p>
                                        <p><strong>Type:</strong> {{ $material->type }}</p>
                                        <p><strong>Color:</strong> <span
                                                style="background-color: {{ $material->color }}; padding: 0.2em 0.6em;">{{ $material->color }}</span>
                                        </p>

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
                                        <form action="{{ route('materials.update', $material->id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PATCH')

                                            <div class="form-group">
                                                <strong>Name:</strong>
                                                <input type="text" name="name" value="{{ $material->name }}"
                                                    class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <strong>Code:</strong>
                                                <input type="text" name="code" value="{{ $material->code }}"
                                                    class="form-control">
                                            </div>

                                            <div class="form-group">
                                                <strong>Symbol (Image):</strong>
                                                <input type="file" name="symbol" class="form-control">
                                                <small>Leave blank to keep the current symbol.</small>
                                                @if ($material->symbol)
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/' . $material->symbol) }}"
                                                            alt="Current Symbol" width="50">
                                                    </div>
                                                @endif
                                            </div>

         

                                            <div class="form-group">
                                                <strong>Unit of Measurement:</strong>
                                                <select name="unit_of_measurement" class="form-control" required>
                                                    <option value="kg"
                                                        {{ $material->unit_of_measurement == 'kg' ? 'selected' : '' }}>
                                                        Kilogram (kg)</option>
                                                    <option value="g"
                                                        {{ $material->unit_of_measurement == 'g' ? 'selected' : '' }}>Gram
                                                        (g)
                                                    </option>
                                                    <option value="m"
                                                        {{ $material->unit_of_measurement == 'm' ? 'selected' : '' }}>Meter
                                                        (m)</option>
                                                    <option value="cm"
                                                        {{ $material->unit_of_measurement == 'cm' ? 'selected' : '' }}>
                                                        Centimeter (cm)</option>
                                                    <option value="liter"
                                                        {{ $material->unit_of_measurement == 'liter' ? 'selected' : '' }}>
                                                        Liter (L)</option>
                                                    <option value="pcs"
                                                        {{ $material->unit_of_measurement == 'pcs' ? 'selected' : '' }}>
                                                        Pieces (pcs)</option>
                                                    <option value="bar"
                                                        {{ $material->unit_of_measurement == 'bar' ? 'selected' : '' }}>Bar
                                                        (bar)</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <strong>Color:</strong>
                                                <select name="color" class="form-control">
                                                    <option value="white"
                                                        {{ $material->color == 'white' ? 'selected' : '' }}>White</option>
                                                    <option value="black"
                                                        {{ $material->color == 'black' ? 'selected' : '' }}>Black</option>
                                                    <option value="red"
                                                        {{ $material->color == 'red' ? 'selected' : '' }}>Red</option>
                                                    <option value="blue"
                                                        {{ $material->color == 'blue' ? 'selected' : '' }}>Blue</option>
                                                    <option value="green"
                                                        {{ $material->color == 'green' ? 'selected' : '' }}>Green</option>
                                                    <option value="yellow"
                                                        {{ $material->color == 'yellow' ? 'selected' : '' }}>Yellow
                                                    </option>
                                                </select>
                                            </div>

                                            <!-- Type (Dropdown for Material Type) -->
                                            <div class="form-group mt-3">
                                                <strong>Type:</strong>
                                                <select name="type" class="form-control" required>
                                                    <option value="" disabled>Select Material Type</option>
                                                    <option value="aluminium_profile"
                                                        {{ $material->type == 'aluminium_profile' ? 'selected' : '' }}>
                                                        Aluminium Profile</option>
                                                    <option value="aluminium_accessory"
                                                        {{ $material->type == 'aluminium_accessory' ? 'selected' : '' }}>
                                                        Aluminium Accessory</option>
                                                    <option value="finishing"
                                                        {{ $material->type == 'finishing' ? 'selected' : '' }}>Finishing
                                                    </option>
                                                    <option value="other"
                                                        {{ $material->type == 'other' ? 'selected' : '' }}>Other</option>
                                                </select>
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
                    <form action="{{ route('materials.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- Material Name -->
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" placeholder="Name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <strong>Code:</strong>
                            <input type="text" name="code" placeholder="Code" class="form-control">
                        </div>

                        <!-- Unit of Measurement -->
                        <div class="form-group mt-3">
                            <strong>Unit of Measurement:</strong>
                            <select name="unit_of_measurement" class="form-control" required>
                                <option value="" disabled selected>Select Unit of Measurement</option>
                                <option value="kg">Kilogram (kg)</option>
                                <option value="g">Gram (g)</option>
                                <option value="m">Meter (m)</option>
                                <option value="cm">Centimeter (cm)</option>
                                <option value="liter">Liter (L)</option>
                                <option value="pcs">Pieces (pcs)</option>
                                <option value="bar">Bar (bar)</option>
                                <!-- Add more units as needed -->
                            </select>
                        </div>

                        <!-- Color (Dropdown with White and Black on Top) -->
                        <div class="form-group mt-3">
                            <strong>Color:</strong>
                            <select name="color" class="form-control">
                                <option value="" disabled selected>Select Color</option>
                                <option value="white">White</option>
                                <option value="black">Black</option>
                                <option value="red">Red</option>
                                <option value="blue">Blue</option>
                                <option value="green">Green</option>
                                <option value="yellow">Yellow</option>
                                <!-- Add more colors as needed -->
                            </select>
                        </div>

                        <!-- Type (Dropdown for Material Type) -->
                        <div class="form-group mt-3">
                            <strong>Type:</strong>
                            <select name="type" class="form-control" required>
                                <option value="" disabled selected>Select Material Type</option>
                                <option value="aluminium_profile">Aluminium Profile</option>
                                <option value="aluminium_accessory">Aluminium Accessory</option>
                                <option value="finishing">Finishing</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Symbol (Image Upload) -->
                        <div class="form-group mt-3">
                            <strong>Symbol (Upload Image):</strong>
                            <input type="file" name="symbol" class="form-control" accept="image/*">
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center mt-3">
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
