<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialResource\Pages;
use App\Filament\Resources\MaterialResource\RelationManagers;
use App\Models\Material;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Master Data (Purchasing)')
                            ->schema([
                                Forms\Components\TextInput::make('lab_po_number')->label('Lab PO No.'),
                                Forms\Components\TextInput::make('po_number')->label('PO No.'),
                                Forms\Components\TextInput::make('lot_number')->label('Lot No.'),
                                Forms\Components\TextInput::make('supplier')->label('Supplier'),
                                Forms\Components\TextInput::make('country_of_supplier')->label('Country'),
                                Forms\Components\TextInput::make('material_group')->label('Material Group'),
                                Forms\Components\TextInput::make('material_type')->label('Material Type'),
                                Forms\Components\TextInput::make('material_name')->label('Material Name')->required(),
                                Forms\Components\TextInput::make('color')->label('Color'),
                                Forms\Components\TextInput::make('color_key')->label('Color Key'),
                                Forms\Components\TextInput::make('mpn')->label('MPN'),
                                Forms\Components\TextInput::make('article_style')->label('Article Style'),
                                Forms\Components\TextInput::make('component')->label('Component'),
                                Forms\Components\TextInput::make('qty')->label('Quantity')->numeric(),
                                Forms\Components\TextInput::make('bm')->label('BM'),
                            ])
                            ->columns(2)
                            ->disabled(fn ($record) => 
                                !auth()->user()->isAdmin() && 
                                ($record && $record->status !== \App\Enums\MaterialStatus::Scheduled) &&
                                !auth()->user()->hasRole('purchasing')
                            ),
                        
                        Forms\Components\Section::make('Arrival Info')
                            ->schema([
                                Forms\Components\DatePicker::make('date_incoming'),
                            ])
                            ->columns(1)
                            ->disabled(fn () => !auth()->user()->isAdmin() && !auth()->user()->hasRole('incoming') && !auth()->user()->hasRole('purchasing')),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status & Workflow')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options(\App\Enums\MaterialStatus::class)
                                    ->required()
                                    ->disabled(fn () => !auth()->user()->isAdmin()),
                                Forms\Components\Placeholder::make('created_at')
                                    ->content(fn ($record) => $record?->created_at?->diffForHumans()),
                                Forms\Components\Placeholder::make('updated_at')
                                    ->content(fn ($record) => $record?->updated_at?->diffForHumans()),
                            ]),
                            
                        Forms\Components\Section::make('Lab Timing')
                            ->schema([
                                Forms\Components\DateTimePicker::make('lab_received_at')->disabled(),
                                Forms\Components\DateTimePicker::make('testing_started_at')->disabled(),
                                Forms\Components\DateTimePicker::make('test_completed_at')->disabled(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('lab_po_number')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('material_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('supplier')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('lot_number')->searchable(),
                Tables\Columns\TextColumn::make('qty')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('lab_received_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(\App\Enums\MaterialStatus::class),
                Tables\Filters\SelectFilter::make('supplier')
                    ->options(fn () => Material::distinct()->pluck('supplier', 'supplier')->toArray()),
            ])
            ->actions([
                Tables\Actions\Action::make('arrive')
                    ->label('Arrive')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->visible(fn (Material $record) => $record->status === \App\Enums\MaterialStatus::Scheduled && (auth()->user()->hasRole('incoming') || auth()->user()->isAdmin()))
                    ->action(function (Material $record) {
                        $record->update([
                            'status' => \App\Enums\MaterialStatus::Arrived,
                            'date_incoming' => now(),
                        ]);
                    }),
                // 'receive_lab' action removed to enforce QR scanning workflow
                Tables\Actions\Action::make('start_test')
                    ->label('Start Test')
                    ->icon('heroicon-o-play')
                    ->color('primary')
                    ->visible(fn (Material $record) => $record->status === \App\Enums\MaterialStatus::LabReceived && (auth()->user()->hasRole('lab_tech') || auth()->user()->isAdmin()))
                    ->action(function (Material $record) {
                        $record->update([
                            'status' => \App\Enums\MaterialStatus::InProgress,
                            'testing_started_at' => now(),
                        ]);
                    }),
                Tables\Actions\Action::make('complete_test')
                    ->label('Complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Material $record) => $record->status === \App\Enums\MaterialStatus::InProgress && (auth()->user()->hasRole('lab_tech') || auth()->user()->isAdmin()))
                    ->action(function (Material $record) {
                        $record->update([
                            'status' => \App\Enums\MaterialStatus::Completed,
                            'test_completed_at' => now(),
                        ]);
                    })
                    ->requiresConfirmation(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('qr')
                    ->label('QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->modalContent(fn (Material $record) => view('partials.qr-label', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalWidth('4xl'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TestResultsRelationManager::class,
            RelationManagers\TestDocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaterials::route('/'),
            'create' => Pages\CreateMaterial::route('/create'),
            'edit' => Pages\EditMaterial::route('/{record}/edit'),
        ];
    }
}
