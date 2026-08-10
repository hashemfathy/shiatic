<?php

namespace App\Filament\Resources\SpecialistResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sessions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('specialist_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('specialist_id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('price')
                    ->money("EGP")
                    ->sortable(),
                Tables\Columns\TextColumn::make('visit.date')
                    ->label('date')
                    ->sortable(),
                Tables\Columns\TextColumn::make('visit.hour')
                    ->label('hour'),
            ])->defaultSort('id','desc')
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
