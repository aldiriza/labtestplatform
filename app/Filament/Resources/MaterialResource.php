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
        $isPurchasing = auth()->user()->hasRole('purchasing') || auth()->user()->hasRole('admin');

        return $form
            ->schema([
                Forms\Components\Section::make('Material Details')->schema([
                    Forms\Components\TextInput::make('item_description')
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('part_number'),
                    Forms\Components\TextInput::make('specification'),
                    Forms\Components\TextInput::make('brand'),
                    Forms\Components\TextInput::make('category')
                        ->label('Material Type'),
                    Forms\Components\Select::make('unit')
                        ->options([
                            'LOT' => 'LOT', 'EA' => 'EA', 'PAC' => 'PAC', 'SET' => 'SET',
                            'BTL' => 'BTL', 'CAN' => 'CAN', 'BOX' => 'BOX', 'KGS' => 'KGS',
                            'MTR' => 'MTR', 'PCS' => 'PCS', 'ROL' => 'ROL', 'PC' => 'PC',
                            'DRUM' => 'DRUM', 'PAIL' => 'PAIL'
                        ]),
                    Forms\Components\TextInput::make('location'),
                    Forms\Components\TextInput::make('minimum_stock')
                        ->numeric()
                        ->default(0),
                    Forms\Components\TextInput::make('quantity')
                        ->label('Current Stock (Qty)')
                        ->numeric()
                        ->required(),
                ])->columns(2),

                Forms\Components\Section::make('Additional Info')->schema([
                     Forms\Components\TextInput::make('unique_id')->disabled()->dehydrated(false)->visibleOn('edit'),
                     Forms\Components\TextInput::make('supplier'),
                     Forms\Components\TextInput::make('po_number'),
                     Forms\Components\TextInput::make('lot_number')->label('Batch No'),
                     Forms\Components\DatePicker::make('lot_arrival_date')->label('Date'),
                ])->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unique_id')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('item_description')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('part_number')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('specification')->searchable(),
                Tables\Columns\TextColumn::make('brand')->searchable(),
                Tables\Columns\TextColumn::make('category')->label('Material Type')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('unit')->sortable(),
                Tables\Columns\TextColumn::make('location')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('minimum_stock')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('quantity')->label('Current Stock')->numeric()->sortable(),
                
                // Keeping status hidden if needed, or just removing for now as requested "only yellow".
                // Date/Created_at
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('qr_code')
                    ->icon('heroicon-o-qr-code')
                    ->label('QR Code')
                    ->modalHeading('Material QR Code')
                    ->modalSubmitAction(false)
                    ->modalContent(fn(Material $record) => view('filament.resources.materials.qr-code', ['record' => $record])),
                Tables\Actions\Action::make('receive')
                    ->label('Receive at Lab')
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn(Material $record) => $record->status === 'incoming')
                    ->action(function (Material $record) {
                        $record->update([
                            'status' => 'received_at_lab',
                            'lab_received_at' => now(),
                        ]);
                    }),
                Tables\Actions\Action::make('record_result')
                    ->label('Record Result')
                    ->icon('heroicon-o-beaker')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('test_result')
                            ->options(['pass' => 'Pass', 'fail' => 'Fail'])
                            ->required(),
                        Forms\Components\Textarea::make('test_remarks'),
                    ])
                    ->visible(fn(Material $record) => in_array($record->status, ['received_at_lab', 'testing_in_progress']))
                    ->action(function (Material $record, array $data) {
                        $record->update([
                            'status' => 'completed',
                            'test_result' => $data['test_result'],
                            'test_remarks' => $data['test_remarks'] ?? null,
                            'test_completed_at' => now(),
                        ]);
                    }),
                Tables\Actions\EditAction::make(),
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
            //
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
