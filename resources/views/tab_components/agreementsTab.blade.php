<div class="tab-pane fade" id="agreements{{ $project->id }}" role="tabpanel" aria-labelledby="agreements-tab{{ $project->id }}">
    <div class="mt-3">
        <h5>Upload Project Agreements</h5>
        <form action="{{ route('projects.uploadFiles', $project->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <input type="file" name="files[]" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" multiple>
                <small class="form-text text-muted">You can upload multiple agreement documents (PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX).</small>
            </div>
            @can('project-upload-file')
                <button type="submit" class="btn btn-primary">Upload</button>
            @endcan
        </form>

        <h5 class="mt-4">Uploaded Agreements</h5>
        @if ($project->files->count() > 0)
            <div class="row">
                @foreach ($project->files as $file)
                    @if (!str_contains($file->file_type, 'image/'))
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if (str_contains($file->file_type, 'pdf'))
                                                <i class="fas fa-file-pdf text-danger" style="font-size: 2rem;"></i>
                                            @elseif (str_contains($file->file_type, 'word') || str_contains($file->file_name, '.doc'))
                                                <i class="fas fa-file-word text-primary" style="font-size: 2rem;"></i>
                                            @elseif (str_contains($file->file_type, 'excel') || str_contains($file->file_name, '.xls'))
                                                <i class="fas fa-file-excel text-success" style="font-size: 2rem;"></i>
                                            @elseif (str_contains($file->file_type, 'powerpoint') || str_contains($file->file_name, '.ppt'))
                                                <i class="fas fa-file-powerpoint text-warning" style="font-size: 2rem;"></i>
                                            @else
                                                <i class="fas fa-file text-muted" style="font-size: 2rem;"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="card-title mb-1">{{ $file->file_name }}</h6>
                                            <small class="text-muted">{{ $file->created_at->format('M d, Y') }}</small>
                                        </div>
                                        <div>
                                            <a href="{{ Storage::url($file->file_path) }}" class="btn btn-sm btn-info" target="_blank">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <p>No agreement documents uploaded for this project yet.</p>
        @endif
    </div>
</div>
