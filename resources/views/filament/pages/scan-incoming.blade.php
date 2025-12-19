<x-filament-panels::page>
    <div 
        x-data="{
            isCameraOpen: false,
            isScanning: false,
            lastResult: null, // 'success', 'error'
            buffer: '',
            lastKeyTime: 0,
            scanner: null,

            init() {
                // GLOBAL KEYBOARD LISTENER (Always Active)
                window.addEventListener('keydown', (e) => {
                    // Ignore typing in normal inputs
                    if (e.target.tagName === 'INPUT' && e.target.type !== 'file') return;
                    if (e.target.tagName === 'TEXTAREA') return;

                    const char = e.key;
                    const currentTime = Date.now();

                    if (char.length === 1) {
                        if (currentTime - this.lastKeyTime > 100) this.buffer = '';
                        this.buffer += char;
                        this.lastKeyTime = currentTime;
                    } 
                    else if (char === 'Enter') {
                        e.preventDefault();
                        if (this.buffer.length > 3) this.handleScan(this.buffer);
                        this.buffer = '';
                    }
                });
            },

            async toggleCamera() {
                if (this.isCameraOpen) {
                    this.stopCamera();
                } else {
                    await this.startCamera();
                }
            },

            async startCamera() {
                this.isCameraOpen = true;
                await this.$nextTick();
                
                if (typeof Html5Qrcode === 'undefined') {
                    alert('Error: Scanner library not loaded.');
                    return;
                }

                if (!this.scanner) {
                    this.scanner = new Html5Qrcode('incoming-reader'); // Unique ID
                }

                const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                
                this.scanner.start({ facingMode: 'environment' }, config, (decodedText) => {
                    this.handleScan(decodedText);
                    this.stopCamera();
                }, (errorMessage) => {
                    // ignore
                }).catch(err => {
                    console.error(err);
                    alert('Camera Access Error.');
                    this.stopCamera();
                });
            },

            stopCamera() {
                if (this.scanner) {
                    this.scanner.stop().then(() => this.scanner.clear()).catch(() => {});
                }
                this.isCameraOpen = false;
            },

            async scanFile(event) {
                if (event.target.files.length === 0) return;
                this.isScanning = true;
                const file = event.target.files[0];
                
                if (!this.scanner) this.scanner = new Html5Qrcode('incoming-reader-hidden');

                try {
                    const decodedText = await this.scanner.scanFile(file, true);
                    this.handleScan(decodedText);
                } catch (err) {
                    console.error(err);
                    this.playErrorSound();
                    new FilamentNotification().title('Scan Failed').danger().send();
                } finally {
                    this.isScanning = false;
                    event.target.value = '';
                }
            },

            handleScan(text) {
                console.log('Scanned:', text);
                this.playBeepSound();
                this.flashScreen();
                @this.call('handleScan', text);
            },

            playBeepSound() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.type = 'sine';
                    osc.frequency.value = 1200;
                    gain.gain.value = 0.1;
                    osc.start();
                    setTimeout(() => osc.stop(), 150);
                } catch (e) {}
            },

            playErrorSound() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.type = 'sawtooth';
                    osc.frequency.value = 200;
                    gain.gain.value = 0.2;
                    osc.start();
                    setTimeout(() => osc.stop(), 300);
                } catch (e) {}
            },

            flashScreen() {
                this.lastResult = 'success';
                setTimeout(() => this.lastResult = null, 800);
            }
        }"
        class="space-y-6"
    >
        <script src="{{ asset('js/html5-qrcode.min.js') }}" type="text/javascript"></script>
        <div id="incoming-reader-hidden" class="hidden"></div>
        
        {{-- Flash Overlay --}}
        <div x-show="lastResult === 'success'" 
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-500"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-primary-500/30 z-50 pointer-events-none flex items-center justify-center backdrop-blur-sm">
             <x-heroicon-o-check-circle class="w-32 h-32 text-white drop-shadow-xl" />
        </div>

        {{-- MAIN STATUS HEADER --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
            <div class="fi-section-header flex flex-col gap-3 px-6 py-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-white/10">
                <div class="flex items-center gap-4">
                    <div class="fi-icon-btn relative flex items-center justify-center rounded-lg bg-primary-50 dark:bg-primary-500/10 p-3">
                        <x-heroicon-m-truck class="h-8 w-8 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                            Incoming Scanner
                        </h3>
                        <p class="fi-section-header-description text-sm text-gray-500 dark:text-gray-400">
                            <span x-show="!isScanning && !isCameraOpen" class="flex items-center gap-2">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-success-500"></span>
                                </span>
                                Ready to Scan (Handheld or Camera)
                            </span>
                            <span x-show="isCameraOpen" class="text-primary-600 dark:text-primary-400">Camera Active</span>
                            <span x-show="isScanning" class="text-warning-600 dark:text-warning-400">Processing Image...</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    {{-- Camera Toggle --}}
                    <x-filament::button 
                        x-on:click="toggleCamera()" 
                        :color="'gray'"
                        x-bind:class="isCameraOpen ? '!bg-danger-50 !text-danger-600 dark:!bg-danger-400/10 dark:!text-danger-400' : ''"
                        icon="heroicon-o-video-camera"
                    >
                        <span x-text="isCameraOpen ? 'Stop Camera' : 'Camera'"></span>
                    </x-filament::button>

                    {{-- Photo Upload --}}
                    <div class="relative">
                        <x-filament::button 
                            x-on:click="$refs.fileInput.click()" 
                            color="primary"
                            icon="heroicon-o-photo"
                        >
                            Upload Photo
                        </x-filament::button>
                        <input x-ref="fileInput" type="file" accept="image/*" capture="environment" class="hidden" @change="scanFile($event)">
                    </div>
                </div>
            </div>

            {{-- Camera View (Expandable) --}}
            <div x-show="isCameraOpen" x-collapse>
                <div class="bg-gray-950 p-4">
                    <div id="incoming-reader" class="w-full max-w-md mx-auto rounded-lg overflow-hidden ring-2 ring-white/20"></div>
                    <p class="text-center text-gray-400 text-xs mt-2">Point camera at QR Code</p>
                </div>
            </div>
        </div>

        {{-- CONTENT GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Left: Active Scanned Item --}}
            <div class="lg:col-span-2">
                 @if($scannedMaterial)
                    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
                        @php
                            $statusColors = [
                                'arrived' => 'bg-success-600',
                                'scheduled' => 'bg-gray-500',
                                'rejected' => 'bg-danger-600',
                            ];
                            $statusKey = $scannedMaterial->status instanceof \UnitEnum ? $scannedMaterial->status->value : $scannedMaterial->status;
                            $bannerColor = $statusColors[$statusKey] ?? 'bg-gray-500';
                        @endphp
                        
                        <div class="{{ $bannerColor }} px-6 py-4 text-white">
                             <div class="flex items-center justify-between">
                                <h3 class="font-bold text-xl flex items-center">
                                    <x-heroicon-s-check-circle class="w-8 h-8 mr-3" />
                                    @if($statusKey === 'arrived') CHECKED IN & VERIFIED
                                    @else {{ strtoupper(str_replace('_', ' ', $statusKey)) }}
                                    @endif
                                </h3>
                                <p class="font-mono opacity-80">{{ $scannedMaterial->unique_id }}</p>
                             </div>
                        </div>

                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-950 dark:text-white mb-2">{{ $scannedMaterial->material_name }}</h2>
                            
                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Supplier</p>
                                    <p class="text-base text-gray-950 dark:text-white">{{ $scannedMaterial->supplier }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">PO Number</p>
                                    <p class="text-base text-gray-950 dark:text-white">{{ $scannedMaterial->po_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Quantity</p>
                                    <p class="text-base text-gray-950 dark:text-white">{{ $scannedMaterial->qty }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Arrival Date</p>
                                    <p class="text-base text-gray-950 dark:text-white">{{ now()->toFormattedDateString() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 h-64 flex flex-col items-center justify-center text-gray-400">
                        <x-heroicon-o-qr-code class="w-16 h-16 mb-4 opacity-50" />
                        <span class="font-medium">Scan incoming material to check-in</span>
                    </div>
                @endif
            </div>

            {{-- Right: History --}}
            <div class="lg:col-span-1">
                 <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
                    <h3 class="fi-section-header-heading text-sm font-semibold text-gray-950 dark:text-white mb-4">Recent Arrivals</h3>
                    @if(count($recentScans) > 0)
                        <div class="space-y-3">
                            @foreach($recentScans as $scan)
                                <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                     <div class="mt-1.5">
                                         <div class="w-2 h-2 rounded-full bg-primary-500"></div>
                                     </div>
                                     <div class="flex-1 min-w-0">
                                         <p class="text-sm font-medium text-gray-950 dark:text-white truncate">{{ $scan['name'] }}</p>
                                         <p class="text-xs text-gray-500 dark:text-gray-400">{{ $scan['status'] }} • {{ $scan['time'] }}</p>
                                     </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-gray-400 text-sm py-4">No activity yet</p>
                    @endif
                 </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>