<?php

namespace App\Filament\Widgets;

use App\Models\Material;
use App\Enums\MaterialStatus;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class LabMaterialsTable extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Lab Materials Status';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Material::query()
                    ->whereIn('status', [
                        MaterialStatus::Arrived,
                        MaterialStatus::LabReceived,
                        MaterialStatus::InProgress,
                        MaterialStatus::Completed,
                    ])
                    ->latest('updated_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lab_po_number')
                    ->label('Lab PO')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('po_number')
                    ->label('PO Number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('material_name')
                    ->label('Material')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('supplier')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lot_number')
                    ->label('Lot')
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('qty')
                    ->label('Qty')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_incoming')
                    ->label('Arrival Date')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('lab_received_at')
                    ->label('Lab In')
                    ->dateTime('d M, H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('test_completed_at')
                    ->label('Lab Out')
                    ->dateTime('d M, H:i')
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(MaterialStatus::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn (Material $record) => $record->material_name)
                    ->modalWidth('4xl')
                    ->infolist([
                        Infolists\Components\Section::make('Master Data')
                            ->schema([
                                Infolists\Components\TextEntry::make('lab_po_number')->label('Lab PO No.'),
                                Infolists\Components\TextEntry::make('po_number')->label('PO No.'),
                                Infolists\Components\TextEntry::make('lot_number')->label('Lot No.'),
                                Infolists\Components\TextEntry::make('supplier'),
                                Infolists\Components\TextEntry::make('country_of_supplier')->label('Country'),
                                Infolists\Components\TextEntry::make('material_group')->label('Material Group'),
                                Infolists\Components\TextEntry::make('material_type')->label('Material Type'),
                                Infolists\Components\TextEntry::make('material_name')->label('Material Name'),
                                Infolists\Components\TextEntry::make('color'),
                                Infolists\Components\TextEntry::make('color_key')->label('Color Key'),
                                Infolists\Components\TextEntry::make('mpn')->label('MPN'),
                                Infolists\Components\TextEntry::make('article_style')->label('Article Style'),
                                Infolists\Components\TextEntry::make('component'),
                                Infolists\Components\TextEntry::make('qty')->label('Quantity'),
                                Infolists\Components\TextEntry::make('bm')->label('BM'),
                            ])
                            ->columns(3),
                        Infolists\Components\Section::make('Workflow & Timing')
                            ->schema([
                                Infolists\Components\TextEntry::make('status')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('date_incoming')
                                    ->label('Date Incoming')
                                    ->date('d M Y'),
                                Infolists\Components\TextEntry::make('lab_received_at')
                                    ->label('Lab Received At')
                                    ->dateTime('d M Y, H:i'),
                                Infolists\Components\TextEntry::make('testing_started_at')
                                    ->label('Testing Started At')
                                    ->dateTime('d M Y, H:i'),
                                Infolists\Components\TextEntry::make('test_completed_at')
                                    ->label('Test Completed At')
                                    ->dateTime('d M Y, H:i'),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->dateTime('d M Y, H:i'),
                                Infolists\Components\TextEntry::make('updated_at')
                                    ->dateTime('d M Y, H:i'),
                            ])
                            ->columns(3),
                    ]),
            ])
            ->recordUrl(null)
            ->recordAction('view')
            ->defaultSort('updated_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
