<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ScanIncoming extends Page implements \Filament\Forms\Contracts\HasForms
{
    use \Filament\Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Incoming Scanner';
    protected static string $view = 'filament.pages.scan-incoming';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('incoming') || auth()->user()->hasRole('admin');
    }

    public ?array $data = [];
    public ?\App\Models\Material $scannedMaterial = null;
    public array $recentScans = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\TextInput::make('scanned_code')
                    ->label('Scan QR Code')
                    ->autofocus()
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state) {
                        $this->handleScan($state);
                    }),
            ])
            ->statePath('data');
    }

    public function handleScan($code)
    {
        if (empty($code))
            return;

        $material = \App\Models\Material::where('unique_id', $code)->first();

        if (!$material) {
            \Filament\Notifications\Notification::make()->title('Material Not Found')->danger()->send();
            $this->data['scanned_code'] = '';
            return;
        }

        // Incoming: Only Scheduled -> Arrived
        if ($material->status === \App\Enums\MaterialStatus::Scheduled) {
            $material->update([
                'status' => \App\Enums\MaterialStatus::Arrived,
                'date_incoming' => now(),
                'time_incoming' => now(),
            ]);

            $this->addToHistory($material, 'Arrived');
            \Filament\Notifications\Notification::make()->title('Material Arrived')->success()->send();
        } elseif ($material->status === \App\Enums\MaterialStatus::Arrived) {
            \Filament\Notifications\Notification::make()->title('Already Arrived')->warning()->send();
            $this->scannedMaterial = $material; // Show it anyway
        } else {
            \Filament\Notifications\Notification::make()->title('Invalid Status')->body("Status is {$material->status->getLabel()}, expected Scheduled.")->danger()->send();
        }

        $this->scannedMaterial = $material;
        $this->data['scanned_code'] = '';
    }

    protected function addToHistory($material, $statusLabel)
    {
        array_unshift($this->recentScans, [
            'time' => now()->format('H:i:s'),
            'name' => $material->material_name,
            'unique_id' => $material->unique_id,
            'status' => $statusLabel,
        ]);
        $this->recentScans = array_slice($this->recentScans, 0, 5);
    }
}
