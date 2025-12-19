<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material QR - {{ $material->material_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white p-8 rounded-lg shadow-lg max-w-sm w-full text-center border-t-8 border-blue-600 print:shadow-none print:border-none">
        
        <div class="mb-4">
            <h1 class="text-xl font-bold text-gray-800">{{ $material->material_name }}</h1>
            <p class="text-sm text-gray-500">{{ $material->unique_id }}</p>
        </div>

        <div class="flex justify-center mb-6">
            <!-- Using a simple QR code API for now, in production use simple-qrcode package -->
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $material->unique_id }}" alt="QR Code" class="w-48 h-48">
        </div>

        <div class="text-left space-y-2 text-sm border-t pt-4">
            <div class="flex justify-between">
                <span class="text-gray-500">Lot No:</span>
                <span class="font-medium">{{ $material->lot_number ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Supplier:</span>
                <span class="font-medium">{{ $material->supplier ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Status:</span>
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-gray-200 text-gray-800">
                    {{ $material->status->getLabel() }}
                </span>
            </div>
        </div>

        <div class="mt-6 no-print">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full">
                Print Label
            </button>
        </div>
    </div>

</body>
</html>
