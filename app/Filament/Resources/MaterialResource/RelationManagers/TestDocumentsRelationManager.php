<?php

namespace App\Filament\Resources\MaterialResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TestDocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'testDocuments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('file_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('file_path')
                    ->disk('public')
                    ->directory('material-documents')
                    ->required()
                    ->openable()
                    ->maxSize(10240), // 10MB
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_name')
            ->columns([
                Tables\Columns\TextColumn::make('file_name'),
                Tables\Columns\TextColumn::make('uploaded_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('uploader.name')
                    ->label('Uploaded By'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn () => auth()->user()->hasRole('lab_tech') || auth()->user()->isAdmin())
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['uploaded_by'] = auth()->id();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()->isAdmin()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                         ->visible(fn () => auth()->user()->isAdmin()),
                ]),
            ]);
    }
}
