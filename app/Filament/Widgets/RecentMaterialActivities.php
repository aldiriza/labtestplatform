<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentMaterialActivities extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.recent-material-activities';

    public string $activeTab = 'all';

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    protected function getTabs(): array
    {
        return [
            'all' => [
                'label' => 'All Materials',
                'count' => null,
                'color' => null,
            ],
            'scheduled' => [
                'label' => 'Scheduled',
                'count' => \App\Models\Material::where('status', 'scheduled')->count(),
                'color' => 'gray',
            ],
            'arrived' => [
                'label' => 'Arrived',
                'count' => \App\Models\Material::where('status', 'arrived')->count(),
                'color' => 'info',
            ],
            'lab' => [
                'label' => 'In Lab',
                'count' => \App\Models\Material::whereIn('status', ['lab_ready_for_pickup', 'lab_in_progress', 'received_at_lab'])->count(),
                'color' => 'warning',
            ],
            'completed' => [
                'label' => 'Lab Out (Completed)',
                'count' => \App\Models\Material::where('status', 'completed')->count(),
                'color' => 'success',
            ],
        ];
    }

    protected function getViewData(): array
    {
        return [
            'tabs' => $this->getTabs(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\Material::query()
                    ->latest('updated_at')
                    ->when($this->activeTab !== 'all', function ($query) {
                        return match ($this->activeTab) {
                            'scheduled' => $query->where('status', 'scheduled'),
                            'arrived' => $query->where('status', 'arrived'),
                            'lab' => $query->whereIn('status', ['lab_ready_for_pickup', 'lab_in_progress', 'received_at_lab']),
                            'completed' => $query->where('status', 'completed'),
                            default => $query,
                        };
                    })
            )
            ->heading('Recent Activities')
            ->columns([
                Tables\Columns\TextColumn::make('unique_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('material_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('color')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('shoe_style')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('po_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'scheduled' => 'info',
                        'arrived' => 'gray',
                        'lab_in_progress' => 'warning',
                        'completed' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Update')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->form(fn(\Filament\Forms\Form $form) => \App\Filament\Resources\MaterialResource::form($form)),

                Tables\Actions\Action::make('view_result')
                    ->label('View PDF')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn(\App\Models\Material $record) => \Illuminate\Support\Facades\Storage::url($record->result_file_path))
                    ->openUrlInNewTab()
                    ->visible(fn(\App\Models\Material $record) => $record->result_file_path),

                Tables\Actions\Action::make('download_result')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn(\App\Models\Material $record) => response()->download(\Illuminate\Support\Facades\Storage::disk('public')->path($record->result_file_path)))
                    ->visible(fn(\App\Models\Material $record) => $record->result_file_path),
            ])
            ->recordAction('view')
            ->paginated([5, 10, 25, 50]);
    }
}
