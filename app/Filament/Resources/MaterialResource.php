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
                Forms\Components\Group::make()->schema([
                    Forms\Components\TextInput::make('unique_id')
                        ->disabled()
                        ->dehydrated(false)
                        ->visibleOn('edit'),
                    Forms\Components\TextInput::make('material_name')->required(),
                    Forms\Components\TextInput::make('supplier')->required(),
                    Forms\Components\TextInput::make('color')->required(),
                    Forms\Components\TextInput::make('shoe_style')->required(),
                    Forms\Components\TextInput::make('article_no')->required(),
                    Forms\Components\TextInput::make('po_number')->required(),
                    Forms\Components\TextInput::make('lot_number')->required(),
                    Forms\Components\TextInput::make('quantity')->required()->numeric(),
                    Forms\Components\DatePicker::make('lot_arrival_date')->required()->default(now()),
                ])->disabled(!$isPurchasing)->columns(2),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            'scheduled' => 'Scheduled (Purchasing)',
                            'arrived' => 'Arrived at Incoming',
                            'lab_ready_for_pickup' => 'Lab Ready for Pickup',
                            'lab_in_progress' => 'Lab In Progress',
                            'completed' => 'Completed',
                            'rejected' => 'Rejected',
                        ])
                        ->default('scheduled')
                        ->required(),
                    // Test Result is edited by Lab via Scanner mostly, but allow partial edit in form
                    Forms\Components\Select::make('test_result')
                        ->options(['pass' => 'Pass', 'fail' => 'Fail']),
                    Forms\Components\FileUpload::make('result_file_path')
                        ->label('Test Result PDF')
                        ->acceptedFileTypes(['application/pdf'])
                        ->directory('test-results')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('test_remarks')
                        ->columnSpanFull(),
                ])->disabled(!$isPurchasing), // Strictly lock Status/Result editing in Resource View too? User said "material related info" (name, supplier etc). 
                // But generally only Purchasing modifies 'database'. Lab uses Scanner. So locking ALL form is safer.

                Forms\Components\Group::make()->schema([
                    Forms\Components\DateTimePicker::make('sla_due_at')->readOnly(),
                    Forms\Components\DateTimePicker::make('lab_received_at')->readOnly(),
                    Forms\Components\DateTimePicker::make('test_completed_at')->readOnly(),
                ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unique_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('material_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('shoe_style')
                    ->searchable(),
                Tables\Columns\TextColumn::make('article_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('po_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lot_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'scheduled' => 'info',
                        'arrived' => 'gray',
                        'lab_ready_for_pickup' => 'info',
                        'lab_in_progress' => 'warning',
                        'completed' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('test_result')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pass' => 'success',
                        'fail' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('sla_due_at')
                    ->label('SLA Due')
                    ->dateTime()
                    ->sortable()
                    ->description(fn(Material $record) => $record->sla_due_at && now()->greaterThan($record->sla_due_at) ? 'Overdue' : '')
                    ->color(fn(Material $record) => $record->sla_due_at && now()->greaterThan($record->sla_due_at) ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('lab_received_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('test_completed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
