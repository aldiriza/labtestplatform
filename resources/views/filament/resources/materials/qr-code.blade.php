<div class="flex flex-col items-center justify-center p-4 space-y-4">
    <div class="bg-white p-4 rounded-lg shadow-sm">
        {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->generate($record->unique_id) !!}
    </div>

    <div class="text-center">
        <div class="text-sm text-gray-500">Unique ID</div>
        <div class="font-mono text-lg font-bold">{{ $record->unique_id }}</div>
    </div>

    <div class="text-center text-sm text-gray-600">
        {{ $record->material_name }} - {{ $record->color }}
    </div>
</div>