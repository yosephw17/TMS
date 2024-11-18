<!-- Expenses Tab -->
<div class="tab-pane fade" id="expenses{{ $project->id }}" role="tabpanel"
    aria-labelledby="expenses-tab{{ $project->id }}">
    <div class="mt-3">
        @if ($project->purchaseRequests->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Request By</th>
                            <th>Details</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $overallTotal = 0; // Initialize total variable
                        @endphp

                        @foreach ($project->purchaseRequests->where('status', 'approved') as $key => $purchaseRequest)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $purchaseRequest->type)) }}</td>
                                <td>{{ $purchaseRequest->user->name }}</td>

                                @if ($purchaseRequest->type == 'material_non_stock')
                                    <td>{{ $purchaseRequest->non_stock_name }} -
                                        ${{ $purchaseRequest->non_stock_price }}</td>
                                    <td>{{ $purchaseRequest->non_stock_quantity }}</td>
                                    @php
                                        $totalPrice =
                                            $purchaseRequest->non_stock_price * $purchaseRequest->non_stock_quantity;
                                        $overallTotal += $totalPrice; // Accumulate total
                                    @endphp
                                    <td>${{ number_format($totalPrice, 2) }}</td>
                                @elseif ($purchaseRequest->type == 'material_stock' && $purchaseRequest->materials->count() > 0)
                                    <td>
                                        @foreach ($purchaseRequest->materials as $material)
                                            <p>{{ $material->name }} ({{ $material->unit_of_measurement }})</p>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($purchaseRequest->materials as $material)
                                            <p>{{ $material->pivot->quantity }}</p>
                                        @endforeach
                                    </td>
                                    <td>
                                        @php
                                            $stockTotal = 0; // Initialize stock total for this row
                                        @endphp
                                        @foreach ($purchaseRequest->materials as $material)
                                            @php
                                                $stockTotal += $material->pivot->quantity * $material->unit_price;
                                            @endphp
                                            <p>${{ number_format($material->pivot->quantity * $material->unit_price, 2) }}
                                            </p>
                                        @endforeach
                                        @php
                                            $overallTotal += $stockTotal; // Accumulate overall total
                                        @endphp
                                        <p>${{ number_format($stockTotal, 2) }}</p>
                                    </td>
                                @elseif ($purchaseRequest->type == 'labour' || $purchaseRequest->type == 'transport')
                                    <td>{{ $purchaseRequest->details }}</td>
                                    <td>{{ $purchaseRequest->non_stock_price }}</td>
                                    @php
                                        $overallTotal += $purchaseRequest->non_stock_price; // Accumulate overall total
                                    @endphp
                                    <td>${{ number_format($purchaseRequest->non_stock_price, 2) }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Overall Total:</strong></td>
                            <td><strong>${{ number_format($overallTotal, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Used Cost Percentage:</strong></td>
                            <td>
                                @php
                                    $usedCostPercentage = $project->total_price
                                        ? ($overallTotal / $project->total_price) * 100
                                        : 0;
                                    // Determine color based on percentage
                                    $colorClass = 'text-danger'; // Default red
                                    if ($usedCostPercentage <= 60) {
                                        $colorClass = 'text-success'; // Green
                                    } elseif ($usedCostPercentage > 60 && $usedCostPercentage <= 75) {
                                        $colorClass = 'text-warning'; // Yellow
                                    }
                                @endphp
                                <strong
                                    class="{{ $colorClass }}">{{ number_format($usedCostPercentage, 2) }}%</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <p>No costs for this project yet.</p>
        @endif
    </div>
</div>
