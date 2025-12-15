<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left Column: Scanner & Active Item --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Scanner Input Section --}}
            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">Scan Incoming QR Code
                    </h2>
                    <span
                        class="px-2 py-1 text-xs font-medium bg-green-50 text-green-700 rounded-md ring-1 ring-inset ring-green-700/10">Incoming
                        Mode</span>
                </div>
                {{ $this->form }}
            </div>

            {{-- Scanned Material Details Card --}}
            @if($scannedMaterial)
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                    {{-- Status Banner --}}
                    @php
                        $bannerColor = $scannedMaterial->status === 'arrived' ? 'bg-green-500' : 'bg-gray-500';
                    @endphp
                    <div class="{{ $bannerColor }} px-6 py-3">
                        <h3 class="text-white font-bold text-lg flex items-center">
                            @if($scannedMaterial->status === 'arrived')
                                <x-heroicon-m-check-circle class="w-6 h-6 mr-2" />
                                Arrived at Incoming
                            @else
                                {{ strtoupper(str_replace('_', ' ', $scannedMaterial->status)) }}
                            @endif
                        </h3>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Material Name</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $scannedMaterial->material_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Unique ID</p>
                                <p class="font-mono text-gray-700 dark:text-gray-200">{{ $scannedMaterial->unique_id }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Supplier</p>
                                <p class="text-gray-900 dark:text-gray-200">{{ $scannedMaterial->supplier }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">PO Number</p>
                                <p class="text-gray-900 dark:text-gray-200">{{ $scannedMaterial->po_number }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Qty</p>
                                <p class="text-gray-900 dark:text-gray-200">{{ $scannedMaterial->quantity }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Column: History --}}
        <div class="lg:col-span-1">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sticky top-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Recent Arrivals</h3>

                @if(count($recentScans) > 0)
                    <div class="space-y-3">
                        @foreach($recentScans as $scan)
                            <div
                                class="flex items-center space-x-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-100 dark:border-gray-600">
                                <div class="flex-shrink-0">
                                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $scan['name'] }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $scan['status'] }} • {{ $scan['time'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 text-center py-4">No recent scans.</p>
                @endif
            </div>
        </div>

    </div>
</x-filament-panels::page>