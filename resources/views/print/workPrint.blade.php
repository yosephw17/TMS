<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Proforma Work - {{ $companyInfo->name }}</title>
    <link href="{{ asset('css/print.css') }}" rel="stylesheet" />


<body>
    <div class="container">
        <header>
            <div>
                <h1>Proforma Work</h1>
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
                <p><strong>Subject: </strong>Work Proforma for Project</p>
            </div>
        </div>

        <div class="invoice-info">
            {{-- <div>
                <p><strong>Work Type:</strong> {{ $proforma->work_type }}</p>
            </div>
            <div>
                <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($proforma->due_date)->format('d/m/Y') }}</p>
            </div> --}}
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Unit</th>
                    <th>Amount</th>
                    <th>Qty</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($proforma->works as $index => $work)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $work->work_name }}</td>
                        <td>{{ $work->work_unit }}</td>
                        <td>{{ $work->work_amount }}</td>
                        <td>{{ $work->work_quantity }}</td>
                        <td>{{ number_format($work->work_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <p>Sub Total: {{ number_format($proforma->before_vat_total, 2) }}</p>
            <p>VAT: {{ number_format($proforma->vat_amount, 2) }}</p>
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
            printPage();
        };

        function printPage() {
            window.print();
        }
    </script>
</body>

</html>
