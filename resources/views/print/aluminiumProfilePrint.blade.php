<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Proforma Invoice - {{ $companyInfo->name }}</title>
    <link href="{{ asset('css/print.css') }}" rel="stylesheet" />


</head>

<body>
    <div class="container">
        <header>
            <div>
                <h1>Proforma Invoice</h1>
                <p>Date: {{ \Carbon\Carbon::parse($proforma->date)->format('d/m/Y') }} | Ref No: {{ $proforma->ref_no }}
                </p>
            </div>
            @if ($companyInfo->logo && file_exists(public_path('storage/' . $companyInfo->logo)))
                <img src="{{ asset('storage/' . $companyInfo->logo) }}" alt="{{ $companyInfo->name }} Logo"
                    class="company-logo" />
            @endif
        </header>

        <div class="company-client-section">

            <div class="company-details">
                <p><strong>{{ $companyInfo->name }}</strong></p>
                <p>Tel: {{ $companyInfo->phone }}</p>
                <p>Fax: {{ $companyInfo->fax ?? 'N/A' }}</p>
                <p>PO Box: {{ $companyInfo->po_box }}</p>
                <p>Email: {{ $companyInfo->email }}</p>
            </div>
            <div class="client-details">
                <p><strong>To: {{ $proforma->customer->name }}</strong></p>
                <p><strong>Phone: </strong>{{ $proforma->customer->phone }}</p>
                <p><strong>Subject:
                    </strong>{{ $proforma->type === 'aluminium_profile' ? 'Aluminum Profiles' : 'Aluminum Accessories' }}
                </p>
            </div>
        </div>

        <div class="invoice-info">
            <div>
            </div>
            <div>

            </div>
            <div>

            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Item Description</th>
                    <th>Drawing</th>
                    <th>Unit</th>
                    <th>Color</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($proforma->materials as $index => $material)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $material->name }}</td>
                        <td>
                            @if (!empty($material->symbol) && file_exists(public_path('storage/' . $material->symbol)))
                                <img src="{{ asset('storage/' . $material->symbol) }}" alt="Symbol" width="50">
                            @endif
                        </td>
                        <td>{{ $material->unit_of_measurement }}</td>
                        <td>{{ $material->color }}</td>
                        <td>{{ $material->pivot->quantity }}</td>
                        <td>{{ number_format($material->unit_price, 2) }}</td>
                        <td>{{ number_format($material->pivot->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <p>Sub Total: {{ number_format($proforma->before_vat_total, 2) }}</p>
            <p>VAT: {{ number_format($proforma->vat_amount, 2) }}</p>
            @if (isset($proforma->discount))
                <p>Discount: {{ number_format($proforma->discount, 2) }}</p>
            @endif
            <p class="total">Grand Total: {{ number_format($proforma->final_total, 2) }}</p>
        </div>
        <footer>
            <ul class="footer-list">
                <li><strong>Price Validity:</strong> Two Days</li>
                <li><strong>Payment Terms:</strong> 100%</li>
                <li><strong>Delivery:</strong> From Store</li>
            </ul>
        </footer>
        <img src="{{ asset('images\qr-code.png') }}" alt="QR Code" class="qr-code" />


    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
