@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>Company Information Settings</h1>

        <!-- Button to trigger Add Company Info Modal -->
        {{-- @can('setting-create')
            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addCompanyInfoModal">
                Add Company Info
            </button>
        @endcan --}}

        <!-- Display Company Info Boxes -->
        <div class="row">
            @foreach ($companyInfos as $companyInfo)
                <div class="col-md-9 mx-auto mb-4">
                    <div class="card shadow-sm p-3 mb-3 bg-body rounded">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light">
                            <h5 class="mb-0">{{ $companyInfo->name }}</h5>
                            <div>
                                <!-- Edit Button -->
                                @can('setting-update')
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editCompanyInfoModal{{ $companyInfo->id }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                @endcan

                                {{-- <!-- Delete Form -->
                                <form action="{{ route('settings.destroy', $companyInfo->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    @can('setting-delete')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this company info?');">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    @endcan
                                </form> --}}
                            </div>
                        </div>
                        <div class="card-body ">
                            <!-- Company Logo -->
                            @if ($companyInfo->logo)
                                <div class="text-center mb-3">
                                    <img src="{{ asset('storage/' . $companyInfo->logo) }}" alt="Company Logo"
                                        class="img-fluid" style="max-height: 150px;">
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card shadow-sm bg-light">
                                        <div class="card-body">
                                            <p><strong><i class="fas fa-phone-alt"></i> Phone:</strong>
                                                {{ $companyInfo->phone }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card shadow-sm bg-light">
                                        <div class="card-body">
                                            <p><strong><i class="fas fa-fax"></i> Fax:</strong> {{ $companyInfo->fax }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card shadow-sm bg-light">
                                        <div class="card-body">
                                            <p><strong><i class="fas fa-fax"></i> Po-box:</strong>
                                                {{ $companyInfo->po_box }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card shadow-sm bg-light">
                                        <div class="card-body">
                                            <p><strong><i class="fas fa-envelope"></i> Email:</strong>
                                                {{ $companyInfo->email }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card shadow-sm bg-light">
                                        <div class="card-body">
                                            <p><strong><i class="fas fa-quote-right"></i> Motto:</strong>
                                                {{ $companyInfo->motto }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Company Info Modal -->
                <div class="modal fade" id="editCompanyInfoModal{{ $companyInfo->id }}" tabindex="-1"
                    aria-labelledby="editCompanyInfoModalLabel{{ $companyInfo->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editCompanyInfoModalLabel{{ $companyInfo->id }}">Edit Company
                                    Info</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form action="{{ route('settings.update', $companyInfo->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <!-- Form fields for editing company info -->

                                    <!-- Name -->
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Company Name</label>
                                        <input type="text" name="name" class="form-control" id="name"
                                            value="{{ $companyInfo->name }}" required>
                                    </div>

                                    <!-- Phone -->
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="text" name="phone" class="form-control" id="phone"
                                            value="{{ $companyInfo->phone }}" required>
                                    </div>

                                    <!-- Fax -->
                                    <div class="mb-3">
                                        <label for="fax" class="form-label">Fax</label>
                                        <input type="text" name="fax" class="form-control" id="fax"
                                            value="{{ $companyInfo->fax }}">
                                    </div>

                                    <!-- po-box -->
                                    <div class="mb-3">
                                        <label for="po_box" class="form-label">Po-box</label>
                                        <input type="text" name="po_box" class="form-control" id="po_box"
                                            value="{{ $companyInfo->po_box }}">
                                    </div>

                                    <!-- Email -->
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" id="email"
                                            value="{{ $companyInfo->email }}" required>
                                    </div>

                                    <!-- Motto -->
                                    <div class="mb-3">
                                        <label for="motto" class="form-label">Motto</label>
                                        <input type="text" name="motto" class="form-control" id="motto"
                                            value="{{ $companyInfo->motto }}">
                                    </div>

                                    <!-- Logo Upload -->
                                    <div class="mb-3">
                                        <label for="logo" class="form-label">Company Logo</label>
                                        <input type="file" name="logo" class="form-control" id="logo">
                                        @if ($companyInfo->logo)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . $companyInfo->logo) }}"
                                                    alt="Current Logo" width="100">
                                            </div>
                                        @endif
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Add Company Info Modal -->
        <div class="modal fade" id="addCompanyInfoModal" tabindex="-1" aria-labelledby="addCompanyInfoModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCompanyInfoModalLabel">Add Company Info</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('settings.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="mb-3">
                                <label for="fax" class="form-label">Fax</label>
                                <input type="text" class="form-control" id="fax" name="fax">
                            </div>
                            <div class="mb-3">
                                <label for="po_box" class="form-label">Po-Box</label>
                                <input type="text" class="form-control" id="po_box" name="po_box">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="motto" class="form-label">Motto</label>
                                <input type="text" class="form-control" id="motto" name="motto">
                            </div>
                            <div class="mb-3">
                                <label for="logo" class="form-label">Logo</label>
                                <input type="file" class="form-control" id="logo" name="logo"
                                    accept="image/*">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add Company Info</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
