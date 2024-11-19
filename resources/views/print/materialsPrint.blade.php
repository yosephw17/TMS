<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Print Materials - {{ $project->name }}</title>
    <link href="{{ asset('css/print.css') }}" rel="stylesheet" />
</head>

<body>
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
