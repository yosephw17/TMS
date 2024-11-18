<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Proforma Work - {{ $companyInfo->name }}</title>
    <style>
        body {
            font-family: "Arial", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 8px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #ddd;
        }

        header h1 {
            font-size: 24px;
            color: #333;
            margin: 0;
        }

        header .company-logo {
            width: 120px;
        }

        .details-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .company-details,
        .client-details {
            width: 48%;
        }

        .company-details p,
        .client-details p {
            margin: 5px 0;
            font-size: 14px;
        }

        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .invoice-info div {
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
            font-size: 14px;
        }

        table th {
            background-color: #f7f7f7;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .totals {
            text-align: right;
            margin-top: 20px;
            font-size: 16px;
        }

        .totals .total {
            font-weight: bold;
            font-size: 18px;
        }

        .footer-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: inline-block;
            text-align: left;
        }

        .footer-list li {
            margin-bottom: 5px;
        }

        .footer-list li strong {
            color: #555;
        }

        .qr-code {
            position: relative;
            left: 20px;
            top: 0px;
            width: 80px;
            height: 80px;
        }
    </style>
</head>

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

        <div class="details-container">
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
