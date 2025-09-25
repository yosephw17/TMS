@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-undo text-success"></i> Restock Details
        </h1>
        <div>
            <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            @if($restockEntry->status === 'pending')
                @can('restock-approve')
                    <div class="btn-group ml-2">
                        <form method="POST" action="{{ route('restock.approve', $restockEntry->id) }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Approve this restock request?')">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger btn-sm" onclick="showRejectModal()">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    </div>
                @endcan
            @endif
        </div>
    </div>

    <!-- Status Alert -->
    <div class="row mb-4">
        <div class="col-12">
            @if($restockEntry->status === 'pending')
                <div class="alert alert-warning">
                    <i class="fas fa-clock"></i> This restock request is pending approval.
                </div>
            @elseif($restockEntry->status === 'approved')
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> This restock request has been approved and processed.
                </div>
            @elseif($restockEntry->status === 'rejected')
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i> This restock request has been rejected.
                </div>
            @endif
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Restock Information Card -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Restock Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Reference:</strong></td>
                                    <td><span class="badge badge-info">{{ $restockEntry->restock_reference }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Project:</strong></td>
                                    <td>{{ $restockEntry->project->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Material:</strong></td>
                                    <td>{{ $restockEntry->material->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Stock Location:</strong></td>
                                    <td>{{ $restockEntry->stock->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Quantity Restocked:</strong></td>
                                    <td>{{ $restockEntry->quantity_restocked }} {{ $restockEntry->material->unit_of_measurement }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Unit Price:</strong></td>
                                    <td>${{ number_format($restockEntry->unit_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Cost Recovered:</strong></td>
                                    <td><span class="text-success font-weight-bold">${{ number_format($restockEntry->total_cost_deducted, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td><span class="badge badge-{{ $restockEntry->status_color }}">{{ ucfirst($restockEntry->status) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Requested By:</strong></td>
                                    <td>{{ $restockEntry->restockedBy->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Request Date:</strong></td>
                                    <td>{{ $restockEntry->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Reason and Notes -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6><strong>Reason for Restock:</strong></h6>
                            <p class="bg-light p-3 rounded">{{ $restockEntry->reason }}</p>
                        </div>
                    </div>

                    @if($restockEntry->notes)
                    <div class="row">
                        <div class="col-12">
                            <h6><strong>Additional Notes:</strong></h6>
                            <p class="bg-light p-3 rounded">{{ $restockEntry->notes }}</p>
                        </div>
                    </div>
                    @endif

                    @if($restockEntry->status === 'rejected' && $restockEntry->rejection_reason)
                    <div class="row">
                        <div class="col-12">
                            <h6><strong>Rejection Reason:</strong></h6>
                            <p class="bg-danger text-white p-3 rounded">{{ $restockEntry->rejection_reason }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Status Timeline Card -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history"></i> Status Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Request Created -->
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Request Created</h6>
                                <p class="mb-0 text-muted">{{ $restockEntry->created_at->format('M d, Y H:i') }}</p>
                                <small class="text-muted">by {{ $restockEntry->restockedBy->name }}</small>
                            </div>
                        </div>

                        @if($restockEntry->status === 'approved')
                        <!-- Approved -->
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Approved</h6>
                                <p class="mb-0 text-muted">{{ $restockEntry->approved_at->format('M d, Y H:i') }}</p>
                                <small class="text-muted">by {{ $restockEntry->approvedBy->name ?? 'System' }}</small>
                            </div>
                        </div>
                        @elseif($restockEntry->status === 'rejected')
                        <!-- Rejected -->
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Rejected</h6>
                                <p class="mb-0 text-muted">{{ $restockEntry->rejected_at->format('M d, Y H:i') }}</p>
                                <small class="text-muted">by {{ $restockEntry->approvedBy->name ?? 'System' }}</small>
                            </div>
                        </div>
                        @else
                        <!-- Pending -->
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Pending Approval</h6>
                                <p class="mb-0 text-muted">Waiting for approval...</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Original Purchase Info -->
            @if($restockEntry->original_purchase_data)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-receipt"></i> Original Purchase Info
                    </h6>
                </div>
                <div class="card-body">
                    @php $originalData = $restockEntry->original_purchase_data; @endphp
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>Purchase Request:</strong></td>
                            <td>PR-{{ $originalData['purchase_request_id'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Original Quantity:</strong></td>
                            <td>{{ $originalData['original_quantity'] ?? 'N/A' }} {{ $restockEntry->material->unit_of_measurement }}</td>
                        </tr>
                        <tr>
                            <td><strong>Original Cost:</strong></td>
                            <td>${{ number_format($originalData['original_cost'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Purchase Date:</strong></td>
                            <td>{{ isset($originalData['purchase_date']) ? \Carbon\Carbon::parse($originalData['purchase_date'])->format('M d, Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Requested By:</strong></td>
                            <td>{{ $originalData['requested_by'] ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Reject Restock Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('restock.reject', $restockEntry->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="4" required placeholder="Please provide a detailed reason for rejecting this restock request..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e3e6f0;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    padding-left: 10px;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-size: 14px;
}

.timeline-content p {
    margin-bottom: 2px;
    font-size: 13px;
}

.timeline-content small {
    font-size: 11px;
}
</style>

<script>
function showRejectModal() {
    $('#rejectModal').modal('show');
}
</script>
@endsection