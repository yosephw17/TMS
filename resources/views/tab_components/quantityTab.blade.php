<div class="tab-pane fade" id="quantity{{ $project->id }}" role="tabpanel"
    aria-labelledby="quantity-tab{{ $project->id }}">
    <div class="mt-3">
        @can('material-create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#selectMaterialsModal{{ $project->id }}">
                Add Materials
            @endcan
        </button>
        @can('project-material-print')
            <a href="{{ route('projects.materials.print', $project->id) }}" class="btn btn-secondary" target="_blank">
                <i class="fas fa-print"></i>
                Print
            </a>
        @endcan

    </div>

    <div class="mt-3">
        <h5>Materials</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Material Name</th>
                    <th>Color</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($project->materials as $material)
                    <tr>
                        <td>{{ $material->name }}</td>
                        <td>{{ $material->color }}</td>
                        <td>{{ $material->pivot->quantity }}</td>
                        <td>

                            @can('project-material-edit')
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#editMaterialModal{{ $material->id }}-{{ $project->id }}">
                                    <i class="fa-regular fa-pen-to-square"></i>

                                </button>
                            @endcan

                            @can('project-material-delete')
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#deleteMaterialModal{{ $material->id }}-{{ $project->id }}">
                                    <i class="fa-solid fa-trash-can fa-lg"></i>

                                </button>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@foreach ($project->materials as $material)
    <!-- Edit Material Modal -->
    <div class="modal fade" id="editMaterialModal{{ $material->id }}-{{ $project->id }}" tabindex="-1"
        aria-labelledby="editMaterialLabel{{ $material->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('projectMaterials.update', [$project->id, $material->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editMaterialLabel{{ $material->id }}">Edit
                            Material Quantity</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" name="quantity" class="form-control"
                                value="{{ $material->pivot->quantity }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Delete Material Modal -->
    <div class="modal fade" id="deleteMaterialModal{{ $material->id }}-{{ $project->id }}" tabindex="-1"
        aria-labelledby="deleteMaterialLabel{{ $material->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('projects.materials.destroy', [$project->id, $material->id]) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteMaterialLabel{{ $material->id }}">Confirm
                            Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this material from the project?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endforeach


<!-- Select Materials Modal -->
<div class="modal fade" id="selectMaterialsModal{{ $project->id }}" tabindex="-1" role="dialog"
    aria-labelledby="selectMaterialsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('projects.addMaterials', $project->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="selectMaterialsModalLabel">Select Materials for
                        Project</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="materials">Materials</label>
                        <div class="row">
                            @foreach ($materials as $material)
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="materials[]"
                                            value="{{ $material->id }}" id="material{{ $material->id }}">
                                        <label class="form-check-label" for="material{{ $material->id }}">
                                            {{ $material->name }} @if ($material->color)
                                                ({{ $material->color }})
                                            @endif
                                        </label>
                                        <input type="number" name="quantities[{{ $material->id }}]"
                                            class="form-control mt-2" placeholder="Quantity" min="1">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Materials</button>
                </div>
            </form>
        </div>
    </div>
</div>
