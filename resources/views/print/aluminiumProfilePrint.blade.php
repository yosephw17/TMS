<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Proforma Invoice - {{ $companyInfo->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            font-size: clamp(10px, 1.5vw, 14px);
            line-height: 1.4;
            color: var(--text-color);
            background-color: var(--light-gray);
            width: 100%;
            min-height: 100vh;
        }

        /* Fluid container that adapts to available space */
        .container {
            width: 95%;
            max-width: 1200px;
            min-height: 95vh;
            margin: 2vh auto;
            padding: clamp(15px, 3vh, 30px);
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(to bottom right, white 0%, var(--light-blue) 100%);
            display: flex;
            flex-direction: column;
        }

        /* Fluid decorative corner elements */
        .decorative-corner {
            position: absolute;
            top: 0;
            right: 0;
            width: clamp(80px, 12vw, 150px);
            height: clamp(80px, 12vw, 150px);
            background: linear-gradient(135deg, var(--secondary-blue) 0%, var(--primary-blue) 100%);
            clip-path: polygon(0 0, 100% 0, 100% 100%, 0 50%);
            z-index: 0;
            opacity: 0.9;
        }

        .decorative-corner-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: clamp(60px, 10vw, 120px);
            height: clamp(60px, 10vw, 120px);
            background: linear-gradient(135deg, var(--accent-yellow) 0%, var(--dark-yellow) 100%);
            clip-path: polygon(0 100%, 100% 100%, 0 0);
            z-index: 0;
            opacity: 0.9;
        }

        /* Fluid header spacing */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: clamp(10px, 2vh, 20px);
            margin-bottom: clamp(15px, 3vh, 25px);
            border-bottom: 2px solid var(--secondary-blue);
            position: relative;
            z-index: 1;
            flex-shrink: 0;
        }

        .invoice-title h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(20px, 4vw, 36px);
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: clamp(4px, 1vh, 8px);
            letter-spacing: 0.5px;
            line-height: 1.1;
        }

        .invoice-title::after {
            content: '';
            display: block;
            width: clamp(40px, 6vw, 70px);
            height: clamp(2px, 0.4vh, 4px);
            background-color: var(--accent-yellow);
            margin-top: clamp(4px, 1vh, 8px);
            border-radius: 2px;
        }

        .invoice-meta {
            color: var(--dark-gray);
            font-size: clamp(9px, 1.2vw, 12px);
            background-color: var(--light-yellow);
            padding: clamp(4px, 1vh, 8px) clamp(8px, 1.5vw, 12px);
            border-radius: 4px;
            display: inline-block;
            margin-top: clamp(6px, 1vh, 10px);
        }

        .invoice-meta strong {
            color: var(--primary-blue);
        }

        .company-logo-container {
            background-color: white;
            padding: clamp(6px, 1vh, 10px);
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--medium-gray);
            flex-shrink: 0;
        }

        .company-logo {
            width: clamp(80px, 12vw, 140px);
            height: auto;
            max-height: clamp(50px, 8vh, 80px);
            object-fit: contain;
        }

        /* Fluid company/client section */
        .company-client-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: clamp(15px, 3vw, 30px);
            margin-bottom: clamp(15px, 3vh, 25px);
            position: relative;
            z-index: 1;
            flex-shrink: 0;
        }

        .company-details,
        .client-details {
            padding: clamp(12px, 2.5vh, 20px);
            background-color: white;
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-top: 3px solid var(--secondary-blue);
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
            height: 3px;
            background: linear-gradient(to right, var(--secondary-blue) 0%, var(--accent-yellow) 100%);
        }

        .section-title {
            font-size: clamp(11px, 1.8vw, 16px);
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: clamp(8px, 1.5vh, 12px);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
        }

        .section-title::before {
            content: '';
            display: inline-block;
            width: clamp(5px, 0.8vw, 8px);
            height: clamp(5px, 0.8vw, 8px);
            background-color: var(--accent-yellow);
            border-radius: 50%;
            margin-right: clamp(4px, 0.8vw, 8px);
        }

        .company-details p,
        .client-details p {
            margin-bottom: clamp(3px, 0.8vh, 6px);
            display: flex;
            font-size: clamp(9px, 1.2vw, 12px);
        }

        .company-details strong,
        .client-details strong {
            min-width: clamp(50px, 8vw, 80px);
            display: inline-block;
            color: var(--dark-gray);
        }

        /* Fluid table that adapts to available space */
        .table-container {
            flex: 1;
            overflow: hidden;
            position: relative;
            z-index: 1;
            margin-bottom: clamp(15px, 3vh, 25px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-radius: 6px;
            overflow: hidden;
            table-layout: fixed;
        }

        table thead {
            background: linear-gradient(to right, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
        }

        table th {
            padding: clamp(8px, 1.5vh, 14px) clamp(6px, 1vw, 10px);
            text-align: left;
            font-weight: 600;
            font-size: clamp(8px, 1.1vw, 11px);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        table th:first-child {
            border-top-left-radius: 6px;
            width: 6%;
        }

        table th:nth-child(2) {
            width: auto;
            min-width: 25%;
        }

        table th:nth-child(3) {
            width: 8%;
        }

        table th:nth-child(4) {
            width: 8%;
        }

        table th:nth-child(5) {
            width: 12%;
        }

        table th:nth-child(6) {
            width: 8%;
        }

        table th:nth-child(7) {
            width: 10%;
        }

        table th:last-child {
            border-top-right-radius: 6px;
            text-align: right;
            width: 12%;
        }

        table tbody tr {
            border-bottom: 1px solid var(--border-color);
        }

        table tbody tr:nth-child(even) {
            background-color: var(--light-gray);
        }

        table td {
            padding: clamp(6px, 1.2vh, 10px) clamp(6px, 1vw, 10px);
            font-size: clamp(8px, 1.1vw, 11px);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
        }

        table td:nth-child(2) {
            white-space: normal;
            word-wrap: break-word;
        }

        table td:last-child {
            text-align: right;
            font-weight: 500;
        }

        .drawing-img {
            max-width: 100%;
            max-height: clamp(35px, 6vh, 55px);
            object-fit: contain;
            border-radius: 3px;
            border: 1px solid var(--border-color);
            padding: 2px;
            background-color: white;
        }

        /* Fluid totals section */
        .totals {
            display: flex;
            justify-content: flex-end;
            margin-bottom: clamp(12px, 2.5vh, 20px);
            position: relative;
            z-index: 1;
            flex-shrink: 0;
        }

        .totals-inner {
            width: clamp(250px, 35vw, 400px);
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            background-color: white;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: clamp(8px, 1.5vh, 12px) clamp(12px, 2vw, 20px);
            border-bottom: 1px solid var(--border-color);
            font-size: clamp(9px, 1.3vw, 13px);
        }

        .total-row:last-child {
            border-bottom: none;
            background: linear-gradient(to right, var(--light-yellow) 0%, var(--light-blue) 100%);
            font-weight: 700;
            font-size: clamp(11px, 1.6vw, 15px);
            color: var(--primary-blue);
        }

        /* Fluid footer spacing */
        .footer-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: clamp(15px, 3vh, 25px);
            padding-top: clamp(12px, 2.5vh, 20px);
            border-top: 1px solid var(--border-color);
            position: relative;
            z-index: 1;
            flex-shrink: 0;
        }

        .footer-list {
            list-style-type: none;
            background-color: white;
            padding: clamp(10px, 2vh, 15px);
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .footer-list li {
            margin-bottom: clamp(4px, 1vh, 8px);
            display: flex;
            padding: clamp(2px, 0.5vh, 4px) 0;
            font-size: clamp(8px, 1.2vw, 12px);
        }

        .footer-list strong {
            min-width: clamp(90px, 15vw, 150px);
            display: inline-block;
            color: var(--primary-blue);
        }

        .signatures {
            display: flex;
            justify-content: flex-end;
            gap: clamp(10px, 2vw, 20px);
        }

        .signature-box {
            text-align: center;
            background-color: white;
            padding: clamp(10px, 2vh, 15px);
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            min-width: clamp(100px, 18vw, 180px);
        }

        .signature-line {
            width: clamp(80px, 15vw, 160px);
            height: 1px;
            background: linear-gradient(to right, var(--secondary-blue) 0%, var(--accent-yellow) 100%);
            margin: clamp(15px, 3vh, 25px) auto clamp(4px, 1vh, 8px);
        }

        .signature-label {
            font-size: clamp(7px, 1vw, 10px);
            color: var(--dark-gray);
            margin-top: clamp(3px, 0.5vh, 5px);
        }

        .qr-code-container {
            position: absolute;
            bottom: clamp(15px, 3vh, 25px);
            right: clamp(15px, 3vw, 25px);
            background-color: white;
            padding: clamp(4px, 1vh, 8px);
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            z-index: 1;
        }

        .qr-code {
            width: clamp(45px, 8vw, 80px);
            height: clamp(45px, 8vw, 80px);
        }

        /* Enhanced print styles for fluid adaptation */
        @media print {
            @page {
                margin: 0.5cm;
                size: A4;
            }

            body {
                margin: 0;
                padding: 0;
                background: white;
                width: 100%;
                height: 100%;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
                font-size: 11px;
            }

            .container {
                width: 100%;
                max-width: 100%;
                min-height: 100vh;
                margin: 0;
                padding: 1.5cm;
                box-shadow: none;
                border-radius: 0;
                background: linear-gradient(to bottom right, white 0%, var(--light-blue) 100%) !important;
                page-break-after: avoid;
                page-break-inside: avoid;
            }

            .decorative-corner,
            .decorative-corner-bottom {
                opacity: 0.9;
                display: block !important;
            }

            /* Force all elements to use available space */
            .invoice-header,
            .company-client-section,
            .table-container,
            .totals,
            .footer-section {
                width: 100%;
            }

            table {
                font-size: 10px;
            }

            table th,
            table td {
                padding: 6px 4px;
                font-size: 9px;
            }

            .drawing-img {
                max-height: 40px;
            }

            /* Ensure content fills the page */
            .container>* {
                flex-shrink: 0;
            }

            .table-container {
                flex: 1;
                min-height: 200px;
            }

            /* Hide print button when printing */
            .print-button {
                display: none;
            }
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
    </style>
</head>

<body>
    <!-- Print button for testing -->
    <button class="print-button" onclick="window.print()">🖨️ Print Invoice</button>

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
                @if ($companyInfo->logo && file_exists(public_path('storage/' . $companyInfo->logo)))
                <img src="{{ asset('storage/' . $companyInfo->logo) }}" alt="{{ $companyInfo->name }} Logo" class="company-logo" />
                @else
                <div style="width: clamp(80px, 12vw, 140px); height: clamp(50px, 8vh, 80px); background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; border-radius: 4px; font-size: clamp(9px, 1.2vw, 12px); letter-spacing: 1px;">
                    COMPANY LOGO
                </div>
                @endif
            </div>
        </header>

        <div class="company-client-section">
            <div class="company-details">
                <h3 class="section-title">From</h3>
                <p><strong>{{ $companyInfo->name }}</strong></p>
                <p><strong>Tel:</strong> {{ $companyInfo->phone }}</p>
                <p><strong>Fax:</strong> {{ $companyInfo->fax ?? 'N/A' }}</p>
                <p><strong>PO Box:</strong> {{ $companyInfo->po_box }}</p>
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
                            <img src="{{ asset('storage/' . $material->symbol) }}" alt="Symbol" class="drawing-img">
                            @else
                            <div style="width: 100%; height: clamp(35px, 6vh, 55px); background: linear-gradient(135deg, var(--light-gray) 0%, rgba(59, 130, 246, 0.1) 100%); border: 1px solid var(--border-color); border-radius: 3px; display: flex; align-items: center; justify-content: center; font-size: clamp(7px, 1vw, 9px); color: var(--dark-gray);">
                                -
                            </div>
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
            @if(file_exists(public_path('images/qr-code.png')))
            <img src="{{ asset('images/qr-code.png') }}" alt="QR Code" class="qr-code" />
            @else
            <div style="width: clamp(45px, 8vw, 80px); height: clamp(45px, 8vw, 80px); background: linear-gradient(135deg, var(--light-gray) 0%, rgba(245, 158, 11, 0.1) 100%); border: 2px solid var(--accent-yellow); border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: clamp(7px, 1vw, 9px); color: var(--dark-gray);">
                QR CODE
            </div>
            @endif
        </div>
    </div>

    <script>
        // Optional: Auto-adjust on window resize
        window.addEventListener('resize', function() {
            // Force reflow to ensure proper rendering
            document.body.style.zoom = '1';
        });
    </script>
</body>

</html>