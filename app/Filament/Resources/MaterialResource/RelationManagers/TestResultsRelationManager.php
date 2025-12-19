<?php

namespace App\Filament\Resources\MaterialResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TestResultsRelationManager extends RelationManager
{
    protected static string $relationship = 'testResults';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('test_name')
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('test_method')
                    ->maxLength(150),
                Forms\Components\TextInput::make('spec_min'),
                Forms\Components\TextInput::make('spec_max'),
                Forms\Components\TextInput::make('actual_value'),
                Forms\Components\TextInput::make('unit'),
                Forms\Components\Select::make('result')
                    ->options([
                        'pass' => 'Pass',
                        'fail' => 'Fail',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('remark'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('test_name')
            ->columns([
                Tables\Columns\TextColumn::make('test_name'),
                Tables\Columns\TextColumn::make('test_method'),
                Tables\Columns\TextColumn::make('spec_min'),
                Tables\Columns\TextColumn::make('spec_max'),
                Tables\Columns\TextColumn::make('actual_value'),
                Tables\Columns\TextColumn::make('unit'),
                Tables\Columns\TextColumn::make('result')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pass' => 'success',
                        'fail' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('remark')->limit(50),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn () => auth()->user()->hasRole('lab_tech') || auth()->user()->isAdmin()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->hasRole('lab_tech') || auth()->user()->isAdmin()),
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
