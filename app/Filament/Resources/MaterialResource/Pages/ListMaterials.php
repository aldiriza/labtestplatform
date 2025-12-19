<?php

namespace App\Filament\Resources\MaterialResource\Pages;

use App\Filament\Resources\MaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaterials extends ListRecords
{
    protected static string $resource = MaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import_excel')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('attachment')
                        ->label('Upload Excel File')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                        ->disk('local') // Temporary storage
                        ->directory('imports')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $file = storage_path('app/imports/' . basename($data['attachment']));
                    
                    try {
                        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\MaterialImport, $file);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Import Successful')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                         \Filament\Notifications\Notification::make()
                            ->title('Import Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn() => auth()->user()->hasRole('admin') || auth()->user()->hasRole('purchasing')),
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => \Filament\Resources\Components\Tab::make('All Materials'),
            'scheduled' => \Filament\Resources\Components\Tab::make('Scheduled')
                ->modifyQueryUsing(fn(\Illuminate\Database\Eloquent\Builder $query) => $query->where('status', \App\Enums\MaterialStatus::Scheduled))
                ->badge(\App\Models\Material::query()->where('status', \App\Enums\MaterialStatus::Scheduled)->count())
                ->badgeColor('gray'),
            'incoming' => \Filament\Resources\Components\Tab::make('Incoming')
                ->modifyQueryUsing(fn(\Illuminate\Database\Eloquent\Builder $query) => $query->where('status', \App\Enums\MaterialStatus::Arrived))
                ->badge(\App\Models\Material::query()->where('status', \App\Enums\MaterialStatus::Arrived)->count())
                ->badgeColor('info'),
            'lab' => \Filament\Resources\Components\Tab::make('In Lab')
                ->modifyQueryUsing(fn(\Illuminate\Database\Eloquent\Builder $query) => $query->whereIn('status', [\App\Enums\MaterialStatus::LabReceived, \App\Enums\MaterialStatus::InProgress]))
                ->badge(\App\Models\Material::query()->whereIn('status', [\App\Enums\MaterialStatus::LabReceived, \App\Enums\MaterialStatus::InProgress])->count())
                ->badgeColor('warning'),
            'completed' => \Filament\Resources\Components\Tab::make('Completed')
                ->modifyQueryUsing(fn(\Illuminate\Database\Eloquent\Builder $query) => $query->where('status', \App\Enums\MaterialStatus::Completed))
                ->badge(\App\Models\Material::query()->where('status', \App\Enums\MaterialStatus::Completed)->count())
                ->badgeColor('success'),
        ];
    }
}
