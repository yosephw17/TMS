<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Material Stock Report - {{ $stock->name }}</title>
    <link href="{{ asset('css/print.css') }}" rel="stylesheet" />
</head>

<body>
    <div class="container">
        <header>
            <div>
                <h1>Material Stock Report</h1>
                <p>Date: {{ now()->format('d/m/Y') }} | Reference: {{ $referenceNumber }}</p>
            </div>
            @php
                $companyInfo = \App\Models\CompanyInfo::first();
            @endphp
            @if ($companyInfo && $companyInfo->logo && file_exists(public_path('storage/' . $companyInfo->logo)))
                <img src="{{ asset('storage/' . $companyInfo->logo) }}" alt="{{ $companyInfo->name }} Logo"
                    class="company-logo" />
            @endif
        </header>

        <div class="company-client-section">
            <div class="company-details">
                @if($companyInfo)
                    <p><strong>{{ $companyInfo->name }}</strong></p>
                    <p>Tel: {{ $companyInfo->phone }}</p>
                    <p>Fax: {{ $companyInfo->fax ?? 'N/A' }}</p>
                    <p>PO Box: {{ $companyInfo->po_box }}</p>
                    <p>Email: {{ $companyInfo->email }}</p>
                @else
                    <p><strong>TeamUp Management System</strong></p>
                @endif
            </div>
            <div class="client-details">
                <p><strong>Stock Location: {{ $stock->name }}</strong></p>
                <p><strong>Address: </strong>{{ $stock->location }}</p>
                <p><strong>Reference Number: </strong>{{ $referenceNumber }}</p>
                <p><strong>Total Materials: </strong>{{ $materials->count() }}</p>
            </div>
        </div>

        <div class="invoice-info">
            <div></div>
            <div></div>
            <div></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Material Name</th>
                    <th>Drawing</th>
                    <th>Unit</th>
                    <th>Color</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($materials as $index => $material)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $material->name }}</td>
                        <td>
                            @if (!empty($material->symbol) && file_exists(public_path('storage/' . $material->symbol)))
                                <img src="{{ asset('storage/' . $material->symbol) }}" alt="Symbol" width="50">
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $material->unit_of_measurement }}</td>
                        <td>{{ $material->color ?? '-' }}</td>
                        <td>{{ $material->quantity }}</td>
                        <td>{{ number_format($material->unit_price, 2) }}</td>
                        <td>{{ number_format($material->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <p>Total Materials: {{ $materials->count() }}</p>
            <p>Total Quantity: {{ $materials->sum('quantity') }}</p>
            <p class="total">Total Value: {{ number_format($materials->sum('total_price'), 2) }}</p>
        </div>

        <footer>
            <ul class="footer-list">
                <li><strong>Print Date:</strong> {{ now()->format('F d, Y - H:i:s') }}</li>
                <li><strong>Reference:</strong> {{ $referenceNumber }}</li>
                <li><strong>Stock Location:</strong> {{ $stock->name }} ({{ $stock->location }})</li>
            </ul>
        </footer>

        @if($companyInfo && $companyInfo->qr_code)
            <img src="{{ asset('storage/' . $companyInfo->qr_code) }}" alt="QR Code" class="qr-code" />
        @else
            <img src="{{ asset('images/qr-code.png') }}" alt="QR Code" class="qr-code" />
        @endif
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>

</html>
