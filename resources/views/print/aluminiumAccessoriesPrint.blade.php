<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Proforma Invoice - Teamup Aluminium</title>
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
            position: relative;
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
            width: 150px;
            height: 120px;
        }

        /* Company and Client Details Side by Side */
        .company-details,
        .client-details {
            display: inline-block;
            width: 48%;
            /* Adjust the width as needed */
            vertical-align: top;
            margin-bottom: 20px;
        }

        .company-details {
            padding-right: 20px;
        }

        .client-details {
            padding-left: 20px;
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

        /* QR Code styling */
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
                <h1>Proforma Invoice</h1>
                <p>Date: {{ \Carbon\Carbon::parse($proforma->date)->format('d/m/Y') }} | Ref No: {{ $proforma->ref_no }}
                </p>
            </div>
            <img src="{{ asset('storage/' . $companyInfo->logo) }}" alt="Company Logo" class="company-logo" />
        </header>

        <div class="company-details">
            <p><strong>{{ $companyInfo->name }}</strong></p>
            <p>Tel: {{ $companyInfo->phone }}</p>
            <p>Fax: {{ $companyInfo->fax }}</p>
            <p>PO Box: {{ $companyInfo->po_box }}, Addis Ababa, Ethiopia</p>
            <p>Email: {{ $companyInfo->email }}</p>
        </div>

        <div class="client-details">
            <p><strong>To: {{ $proforma->customer->name }}</strong></p>
            <p><strong>Phone:</strong> {{ $proforma->customer->phone }}</p>
            <p><strong>Subject:</strong>
                {{ $proforma->type === 'aluminium_profile' ? 'Aluminum Profiles' : 'Aluminum Accessories' }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Item Description</th>
                    <th>Code</th>
                    <th>Unit</th>
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
                        <td>{{ $material->code }}</td>
                        <td>{{ $material->unit_of_measurement }}</td>
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
            <p class="total">Grand Total: {{ number_format($proforma->final_total, 2) }}</p>
        </div>

        <footer>
            <ul class="footer-list">
                <li><strong>Price Validity:</strong> Two Days</li>
                <li><strong>Payment Terms:</strong> 100%</li>
                <li><strong>Delivery:</strong> From Store</li>
            </ul>
        </footer>

        <!-- QR Code positioned at the bottom right corner -->
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
