<x-filament-widgets::widget>
    <div class="space-y-4">
        {{-- Tabs --}}
        <div class="flex items-center space-x-1 p-1 bg-gray-100 dark:bg-gray-800 rounded-lg w-fit">
            @foreach ($tabs as $key => $tab)
                    <button type="button" wire:key="tab-{{ $key }}" wire:click.prevent="setTab('{{ $key }}')" class="
                                                px-3 py-1.5 text-sm font-medium rounded-md flex items-center gap-2 transition-all
                                                {{ $activeTab === $key
                ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm ring-1 ring-gray-200 dark:ring-gray-600'
                : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' 
                                                }}
                                    ">
                        {{ $tab['label'] }}

                        @if($tab['count'] !== null)
                                <span class="
                                                            inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-xs font-medium
                                                            {{ $activeTab === $key
                            ? 'bg-' . ($tab['color'] === 'gray' ? 'gray' : $tab['color']) . '-100 text-' . ($tab['color'] === 'gray' ? 'gray' : $tab['color']) . '-700 dark:bg-gray-600 dark:text-gray-200'
                            : 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400'
                                                            }}
                                                        ">
                                    {{ $tab['count'] }}
                                </span>
                        @endif
                    </button>
            @endforeach
        </div>

        {{-- Table --}}
        <div wire:loading.class="opacity-50 pointer-events-none" wire:target="setTab">
            {{ $this->table }}
        </div>
    </div>
</x-filament-widgets::widget>