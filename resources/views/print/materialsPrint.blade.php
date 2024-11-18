<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Print Materials - {{ $project->name }}</title>
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

        /* Flexbox for company and project details */
        .details-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .company-details,
        .project-details {
            width: 48%;
            /* Adjusts the width of each section */
        }

        .company-details p,
        .project-details p {
            margin: 5px 0;
            font-size: 14px;
        }

        .project-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .project-info div {
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

<body onload="window.print()">
    <div class="container">
        <header>
            <div>
                <h1>Materials List for Project: {{ $project->name }}</h1>
                <p>Date: {{ \Carbon\Carbon::parse($project->created_at)->format('d/m/Y') }}

            </div>
            @if ($companyInfo->logo && file_exists(public_path('storage/' . $companyInfo->logo)))
                <img src="{{ asset('storage/' . $companyInfo->logo) }}" alt="{{ $companyInfo->name }} Logo"
                    class="company-logo" />
            @endif
        </header>

        <!-- Flexbox container for company and project details -->
        <div class="details-container">
            <div class="company-details">
                <p><strong>{{ $companyInfo->name }}</strong></p>
                <p>Tel: {{ $companyInfo->phone }}</p>
                <p>Fax: {{ $companyInfo->fax ?? 'N/A' }}</p>
                <p>PO Box: {{ $companyInfo->po_box }}</p>
                <p>Email: {{ $companyInfo->email }}</p>
            </div>
            <div class="project-details">
                <p><strong>Project: {{ $project->name }}</strong></p>
                <p><strong>Location: </strong>{{ $project->location }}</p>
            </div>
        </div>



        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Material Name</th>
                    <th>Color</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($project->materials as $index => $material)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $material->name }}</td>
                        <td>{{ $material->color }}</td>
                        <td>{{ $material->pivot->quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>


        <img src="{{ asset('images/qr-code.png') }}" alt="QR Code" class="qr-code" />
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
