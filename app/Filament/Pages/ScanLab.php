<?php

namespace App\Filament\Pages;

use App\Models\Material;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class ScanLab extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationLabel = 'Lab Scanner';
    protected static string $view = 'filament.pages.scan-lab';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('lab') || auth()->user()->hasRole('admin');
    }

    public ?array $data = [];
    public ?Material $scannedMaterial = null;
    public ?array $resultData = [];
    public array $recentScans = [];

    protected function getForms(): array
    {
        return [
            'form',
            'resultForm',
        ];
    }

    protected function addToHistory(Material $material, string $statusLabel)
    {
        array_unshift($this->recentScans, [
            'time' => now()->format('H:i:s'),
            'name' => $material->material_name,
            'unique_id' => $material->unique_id,
            'status' => $statusLabel,
        ]);

        // Keep only last 5
        $this->recentScans = array_slice($this->recentScans, 0, 5);
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('scanned_code')
                    ->label('Scan QR Code')
                    ->id('scannerInput')
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

        $material = Material::where('unique_id', $code)->first();

        if (!$material) {
            Notification::make()->title('Material Not Found')->danger()->send();
            $this->data['scanned_code'] = '';
            return;
        }

        // Lab: Only Arrived -> Lab In Progress OR Lab In Progress -> Result
        // Ignore Scheduled items (Incoming's job)
        if ($material->status === \App\Enums\MaterialStatus::Scheduled) {
            Notification::make()->title('Material Not Arrived')->body('This material has not been received by Incoming yet.')->warning()->send();
            $this->data['scanned_code'] = '';
            return;
        }

        $this->scannedMaterial = $material;
        $this->processStatusTransition($material);

        // Reset scan input for next scan
        $this->data['scanned_code'] = '';
    }

    protected function processStatusTransition(Material $material)
    {
        // 1. Arrived -> Lab In Progress
        // 1. Arrived -> Lab Received
        if ($material->status === \App\Enums\MaterialStatus::Arrived) {
            $material->update([
                'status' => \App\Enums\MaterialStatus::LabReceived,
                'lab_received_at' => now(),
                // 'sla_due_at' => now()->addDays(2), // SLA might start at InProgress, but let's keep it simple
            ]);

            $this->addToHistory($material, 'Lab Received');

            Notification::make()
                ->title('Material Received')
                ->body("{$material->material_name} marked as Received in Lab.")
                ->success()
                ->send();
        }
        // 2. Lab Received -> In Progress (Start Test)
        elseif ($material->status === \App\Enums\MaterialStatus::LabReceived) {
            $material->update([
                'status' => \App\Enums\MaterialStatus::InProgress,
                'testing_started_at' => now(),
            ]);

            $this->addToHistory($material, 'Test Started');

            Notification::make()
                ->title('Testing Started')
                ->body("Testing started for {$material->material_name}.")
                ->success()
                ->send();
        }
        // 3. Lab In Progress -> Prompt for Result
        elseif ($material->status === \App\Enums\MaterialStatus::InProgress) {
            Notification::make()
                ->title('Ready for Result')
                ->body("Please enter result for {$material->material_name}")
                ->info()
                ->send();
        } elseif ($material->status === \App\Enums\MaterialStatus::Completed) {
            Notification::make()->title('Already Completed')->warning()->send();
        }
    }

    public function submitResult()
    {
        if (!$this->scannedMaterial)
            return;

        $this->scannedMaterial->update([
            'status' => \App\Enums\MaterialStatus::Completed,
            'test_result' => $this->resultData['test_result'],
            'test_remarks' => $this->resultData['test_remarks'] ?? null,
            'result_file_path' => $this->resultData['result_file_path'] ?? null,
            'test_completed_at' => now(),
        ]);

        $this->addToHistory($this->scannedMaterial, 'Completed');

        Notification::make()->title('Test Result Saved')->success()->send();
        $this->reset(['scannedMaterial', 'resultData']);
    }

    public function resultForm(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('test_result')
                    ->options(['pass' => 'Pass', 'fail' => 'Fail'])
                    ->required(),
                FileUpload::make('result_file_path')
                    ->label('PDF Report')
                    ->acceptedFileTypes(['application/pdf'])
                    ->directory('test-results'),
                Textarea::make('test_remarks'),
            ])
            ->statePath('resultData');
    }
}
