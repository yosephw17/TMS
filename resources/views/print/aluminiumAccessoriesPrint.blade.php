<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Proforma Invoice - Teamup Aluminium</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1e3a8a;
            --secondary-blue: #3b82f6;
            --light-blue: #dbeafe;
            --accent-yellow: #f59e0b;
            --light-yellow: #fef3c7;
            --dark-yellow: #d97706;
            --light-gray: #f8fafc;
            --medium-gray: #e2e8f0;
            --dark-gray: #64748b;
            --text-color: #1e293b;
            --border-color: #cbd5e1;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            font-size: clamp(12px, 1.5vw, 14px);
            line-height: 1.5;
            color: var(--text-color);
            background-color: #fff;
            width: 100%;
            min-height: 100vh;
        }

        .container {
            width: 95%;
            max-width: 1200px;
            min-height: 95vh;
            margin: 2.5vh auto;
            padding: clamp(20px, 4vh, 40px);
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(to bottom right, white 0%, var(--light-blue) 100%);
            display: flex;
            flex-direction: column;
        }

        .decorative-corner {
            position: absolute;
            top: 0;
            right: 0;
            width: clamp(100px, 15vw, 180px);
            height: clamp(100px, 15vw, 180px);
            background: linear-gradient(135deg, var(--secondary-blue) 0%, var(--primary-blue) 100%);
            clip-path: polygon(0 0, 100% 0, 100% 100%, 0 50%);
            z-index: 0;
        }

        .decorative-corner-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: clamp(80px, 12vw, 150px);
            height: clamp(80px, 12vw, 150px);
            background: linear-gradient(135deg, var(--accent-yellow) 0%, var(--dark-yellow) 100%);
            clip-path: polygon(0 100%, 100% 100%, 0 0);
            z-index: 0;
        }

        /* Header Styles */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: clamp(15px, 3vh, 25px);
            margin-bottom: clamp(20px, 4vh, 30px);
            border-bottom: 2px solid var(--secondary-blue);
            position: relative;
            z-index: 1;
            flex-shrink: 0;
        }

        .invoice-title h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(24px, 4.5vw, 40px);
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: clamp(6px, 1.5vh, 12px);
            letter-spacing: 0.5px;
            line-height: 1.1;
        }

        .invoice-title::after {
            content: '';
            display: block;
            width: clamp(50px, 8vw, 80px);
            height: clamp(3px, 0.6vh, 5px);
            background-color: var(--accent-yellow);
            margin-top: clamp(6px, 1.5vh, 12px);
            border-radius: 2px;
        }

        .invoice-meta {
            color: var(--dark-gray);
            font-size: clamp(11px, 1.4vw, 14px);
            background-color: var(--light-yellow);
            padding: clamp(6px, 1.2vh, 10px) clamp(10px, 1.8vw, 16px);
            border-radius: 6px;
            display: inline-block;
            margin-top: clamp(8px, 1.8vh, 14px);
        }

        .invoice-meta strong {
            color: var(--primary-blue);
        }

        .company-logo-container {
            background-color: white;
            padding: clamp(8px, 1.5vh, 12px);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--medium-gray);
            flex-shrink: 0;
        }

        .company-logo {
            width: clamp(100px, 16vw, 160px);
            height: auto;
            max-height: clamp(60px, 10vh, 100px);
            object-fit: contain;
        }

        /* Company & Client Section */
        .company-client-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: clamp(20px, 4vw, 40px);
            margin-bottom: clamp(20px, 4vh, 30px);
            position: relative;
            z-index: 1;
            flex-shrink: 0;
        }

        .company-details,
        .client-details {
            padding: clamp(15px, 3vh, 25px);
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-top: 4px solid var(--secondary-blue);
            position: relative;
            overflow: hidden;
        }

        .company-details::before,
        .client-details::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, var(--secondary-blue) 0%, var(--accent-yellow) 100%);
        }

        .section-title {
            font-size: clamp(14px, 2.2vw, 18px);
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: clamp(10px, 2vh, 16px);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
        }

        .section-title::before {
            content: '';
            display: inline-block;
            width: clamp(6px, 1vw, 10px);
            height: clamp(6px, 1vw, 10px);
            background-color: var(--accent-yellow);
            border-radius: 50%;
            margin-right: clamp(6px, 1vw, 10px);
        }

        .company-details p,
        .client-details p {
            margin-bottom: clamp(4px, 1vh, 8px);
            display: flex;
            font-size: clamp(11px, 1.4vw, 14px);
        }

        .company-details strong,
        .client-details strong {
            min-width: clamp(60px, 10vw, 100px);
            display: inline-block;
            color: var(--dark-gray);
        }

        /* Table Container for flexible sizing */
        .table-container {
            flex: 1;
            overflow: hidden;
            position: relative;
            z-index: 1;
            margin-bottom: clamp(20px, 4vh, 30px);
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            z-index: 1;
            table-layout: fixed;
        }

        table thead {
            background: linear-gradient(to right, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
        }

        table th {
            padding: clamp(10px, 2vh, 16px) clamp(8px, 1.5vw, 14px);
            text-align: left;
            font-weight: 600;
            font-size: clamp(10px, 1.3vw, 13px);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        table th:first-child {
            border-top-left-radius: 8px;
            width: 8%;
        }

        table th:nth-child(2) {
            width: 35%;
        }

        table th:nth-child(3) {
            width: 15%;
        }

        table th:nth-child(4) {
            width: 10%;
        }

        table th:nth-child(5) {
            width: 10%;
        }

        table th:nth-child(6) {
            width: 12%;
        }

        table th:last-child {
            border-top-right-radius: 8px;
            width: 15%;
        }

        table tbody tr {
            border-bottom: 1px solid var(--border-color);
        }

        table tbody tr:nth-child(even) {
            background-color: var(--light-gray);
        }

        table td {
            padding: clamp(8px, 1.8vh, 14px) clamp(8px, 1.5vw, 14px);
            font-size: clamp(10px, 1.3vw, 13px);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
        }

        table td:nth-child(2) {
            white-space: normal;
            word-wrap: break-word;
        }

        /* Totals Section */
        .totals {
            display: flex;
            justify-content: flex-end;
            margin-bottom: clamp(15px, 3vh, 25px);
            position: relative;
            z-index: 1;
            flex-shrink: 0;
        }

        .totals-inner {
            width: clamp(280px, 40vw, 450px);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            background-color: white;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: clamp(10px, 2vh, 16px) clamp(15px, 2.5vw, 24px);
            border-bottom: 1px solid var(--border-color);
            font-size: clamp(12px, 1.6vw, 15px);
        }

        .total-row:last-child {
            border-bottom: none;
            background: linear-gradient(to right, var(--light-yellow) 0%, var(--light-blue) 100%);
            font-weight: 700;
            font-size: clamp(14px, 2vw, 18px);
            color: var(--primary-blue);
        }

        /* Footer Section */
        .footer-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: clamp(20px, 4vh, 30px);
            padding-top: clamp(15px, 3vh, 25px);
            border-top: 1px solid var(--border-color);
            position: relative;
            z-index: 1;
            flex-shrink: 0;
        }

        .footer-list {
            list-style-type: none;
            background-color: white;
            padding: clamp(12px, 2.5vh, 20px);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .footer-list li {
            margin-bottom: clamp(6px, 1.5vh, 12px);
            display: flex;
            padding: clamp(3px, 0.8vh, 6px) 0;
            font-size: clamp(10px, 1.4vw, 14px);
        }

        .footer-list strong {
            min-width: clamp(110px, 18vw, 180px);
            display: inline-block;
            color: var(--primary-blue);
        }

        .signatures {
            display: flex;
            justify-content: flex-end;
            gap: clamp(20px, 4vw, 40px);
        }

        .signature-box {
            text-align: center;
            background-color: white;
            padding: clamp(12px, 2.5vh, 20px);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            min-width: clamp(140px, 22vw, 220px);
        }

        .signature-line {
            width: clamp(120px, 20vw, 200px);
            height: 1px;
            background: linear-gradient(to right, var(--secondary-blue) 0%, var(--accent-yellow) 100%);
            margin: clamp(20px, 4vh, 32px) auto clamp(6px, 1.5vh, 12px);
        }

        .signature-label {
            font-size: clamp(10px, 1.3vw, 13px);
            color: var(--dark-gray);
            margin-top: clamp(4px, 1vh, 8px);
        }

        .qr-code-container {
            position: absolute;
            bottom: clamp(20px, 4vh, 30px);
            right: clamp(20px, 4vw, 30px);
            background-color: white;
            padding: clamp(6px, 1.2vh, 10px);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            z-index: 1;
        }

        .qr-code {
            width: clamp(60px, 10vw, 100px);
            height: clamp(60px, 10vw, 100px);
        }

        /* Print Styles - Fluid and consistent with screen */
        @media print {
            @page {
                margin: 0.4in;
                size: A4;
            }

            body {
                margin: 0;
                padding: 0;
                background: white;
                width: 100%;
                height: 100%;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
                font-size: 11px;
            }

            .container {
                width: 100%;
                max-width: 100%;
                min-height: 100vh;
                margin: 0;
                padding: 0.3in;
                box-shadow: none;
                border-radius: 0;
                background: linear-gradient(to bottom right, white 0%, var(--light-blue) 100%) !important;
                page-break-after: avoid;
                page-break-inside: avoid;
            }

            .decorative-corner,
            .decorative-corner-bottom {
                display: block !important;
                opacity: 0.9;
            }

            .invoice-header {
                padding-bottom: 12px;
                margin-bottom: 18px;
            }

            .invoice-title h1 {
                font-size: 28px;
            }

            .company-logo {
                width: 110px;
                max-height: 70px;
            }

            .company-client-section {
                margin-bottom: 18px;
                gap: 20px;
            }

            .company-details,
            .client-details {
                padding: 15px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
            }

            .table-container {
                flex: 1;
                min-height: 200px;
            }

            table th,
            table td {
                padding: 6px 8px;
                font-size: 10px;
            }

            .totals-inner {
                width: 300px;
            }

            .total-row {
                padding: 8px 16px;
                font-size: 11px;
            }

            .total-row:last-child {
                font-size: 13px;
            }

            .footer-section {
                margin-top: 20px;
                padding-top: 15px;
            }

            .qr-code {
                width: 70px;
                height: 70px;
            }

            .signature-line {
                margin: 18px auto 6px;
                width: 140px;
            }

            /* Ensure all content uses available space */
            .container>* {
                flex-shrink: 0;
            }

            .table-container {
                flex: 1;
            }

            /* Force gradients and colors to print */
            .company-details::before,
            .client-details::before,
            .signature-line,
            table thead,
            .total-row:last-child,
            .invoice-title::after,
            .decorative-corner,
            .decorative-corner-bottom {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        /* Screen-only styles */
        @media screen {
            table tbody tr {
                transition: background-color 0.2s;
            }

            table tbody tr:hover {
                background-color: var(--light-blue);
            }

            /* Print button for testing */
            .print-button {
                position: fixed;
                top: 20px;
                right: 20px;
                background: var(--primary-blue);
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 6px;
                cursor: pointer;
                z-index: 1000;
                font-size: 14px;
                font-weight: 500;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                transition: all 0.3s ease;
            }

            .print-button:hover {
                background: var(--secondary-blue);
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            }
        }
    </style>
</head>

<body>
    
    <div class="container">
        <div class="decorative-corner"></div>
        <div class="decorative-corner-bottom"></div>

        <header class="invoice-header">
            <div class="invoice-title">
                <h1>PROFORMA INVOICE</h1>
                <div class="invoice-meta">
                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($proforma->date)->format('d/m/Y') }} | <strong>Ref No:</strong> {{ $proforma->ref_no }}</p>
                </div>
            </div>
            <div class="company-logo-container">
                <img src="{{ asset('storage/' . $companyInfo->logo) }}" alt="Company Logo" class="company-logo" />
            </div>
        </header>

        <div class="company-client-section">
            <div class="company-details">
                <h3 class="section-title">From</h3>
                <p><strong>{{ $companyInfo->name }}</strong></p>
                <p><strong>Tel:</strong> {{ $companyInfo->phone }}</p>
                <p><strong>Fax:</strong> {{ $companyInfo->fax }}</p>
                <p><strong>PO Box:</strong> {{ $companyInfo->po_box }}, Addis Ababa, Ethiopia</p>
                <p><strong>Email:</strong> {{ $companyInfo->email }}</p>
            </div>
            <div class="client-details">
                <h3 class="section-title">Bill To</h3>
                <p><strong>{{ $proforma->customer->name }}</strong></p>
                <p><strong>Phone:</strong> {{ $proforma->customer->phone }}</p>
                <p><strong>Subject:</strong>
                    {{ $proforma->type === 'aluminium_profile' ? 'Aluminum Profiles' : 'Aluminum Accessories' }}
                </p>
            </div>
        </div>

        <div class="table-container">
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
        </div>

        <div class="totals">
            <div class="totals-inner">
                <div class="total-row">
                    <span>Sub Total:</span>
                    <span>{{ number_format($proforma->before_vat_total, 2) }}</span>
                </div>
                <div class="total-row">
                    <span>VAT:</span>
                    <span>{{ number_format($proforma->vat_amount, 2) }}</span>
                </div>
                @if (isset($proforma->discount))
                <div class="total-row">
                    <span>Discount:</span>
                    <span>{{ number_format($proforma->discount, 2) }}</span>
                </div>
                @endif
                <div class="total-row">
                    <span>Grand Total:</span>
                    <span>{{ number_format($proforma->final_total, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="footer-section">
            <div>
            <ul class="footer-list">
                    <li><strong>Price Validity:</strong> {{$proforma->payment_validity}}</li>
                    <li><strong>Payment Terms:</strong> 100%</li>
                    <li><strong>Delivery:</strong> {{$proforma->delivery_terms}}</li>
                </ul>
            </div>
            <div class="signatures">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <p class="signature-label">
                        Prepared by:
                        @if($proforma->createdBy)
                        {{ $proforma->createdBy->name }}
                        @else
                        ___________
                        @endif
                    </p>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <p class="signature-label">
                        Approved by:
                        @if($proforma->status === 'approved' && $proforma->approvedBy)
                        {{ $proforma->approvedBy->name }}
                        @elseif($proforma->status === 'rejected' && $proforma->approvedBy)
                        {{ $proforma->approvedBy->name }} (Rejected)
                        @else
                        Pending
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="qr-code-container">
            <img src="{{ asset('images/qr-code.png') }}" alt="QR Code" class="qr-code" />
        </div>
    </div>

    <script>
        window.onload = function() {
            // Optional: Add a small delay before printing to ensure all elements are rendered
            setTimeout(function() {
                // Auto-print is disabled for better user experience
                // Uncomment the line below if you want auto-print
                // printPage();
            }, 500);
        };

        function printPage() {
            window.print();
        }

        // Optional: Adjust layout on window resize
        window.addEventListener('resize', function() {
            // Force reflow for better rendering
            document.body.style.zoom = '1';
        });
    </script>
</body>

</html>