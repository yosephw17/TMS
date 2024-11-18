 <!-- Images Tab -->
 <div class="tab-pane fade" id="images{{ $project->id }}" role="tabpanel" aria-labelledby="images-tab{{ $project->id }}">
     <div class="mt-3">
         <h5>Upload Project Images</h5>
         <form action="{{ route('projects.uploadFiles', $project->id) }}" method="POST" enctype="multipart/form-data">
             @csrf
             <div class="form-group">
                 <input type="file" name="files[]" class="form-control" accept="image/*" multiple>
                 <small class="form-text text-muted">You can upload multiple images.</small>
             </div>
             @can('project-upload-file')
                 <button type="submit" class="btn btn-primary">Upload</button>
             @endcan
         </form>

         <h5 class="mt-4">Uploaded Images</h5>
         @if ($project->files->count() > 0)
             <div class="row">
                 @foreach ($project->files as $file)
                     @if (str_contains($file->file_type, 'image/'))
                         <div class="col-md-4">
                             <div class="card mb-3">
                                 <img src="{{ Storage::url($file->file_path) }}" class="card-img-top"
                                     alt="{{ $file->file_name }}">
                                 <div class="card-body">
                                     <h5 class="card-title">{{ $file->file_name }}</h5>
                                     <a href="{{ Storage::url($file->file_path) }}" class="btn btn-info"
                                         target="_blank">View</a>
                                 </div>
                             </div>
                         </div>
                     @endif
                 @endforeach
             </div>
         @else
             <p>No images uploaded for this project yet.</p>
         @endif
     </div>
 </div>
