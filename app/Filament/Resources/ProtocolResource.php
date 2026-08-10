<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProtocolResource\Pages;
use App\Filament\Resources\ProtocolResource\RelationManagers;
use App\Models\Protocol;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProtocolResource extends Resource
{
    protected static ?string $model = Protocol::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canViewAny(): bool
    {
        return auth()->user()?->type == 'admin' ?? false;
    }

    public static function canView($record): bool
    {
        return auth()->user()?->type == 'admin' ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->type == 'admin' ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->type == 'admin' ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->type == 'admin' ?? false;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)->columnSpan('full'),
                Forms\Components\TextInput::make('no_of_visits')
                    ->required()
                    ->numeric()
                    ->step(1)
                    ->minValue(1),
                Forms\Components\Select::make('type')
                    ->options([
                        "A+"=>"A+","A"=>"A","B"=> "B","C"=> "C"
                    ]),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('EGP'),
                Forms\Components\TextInput::make('media')
                    ->maxLength(255)->columnSpan('full'),
                Forms\Components\Select::make('client_id')
                    ->relationship(name: 'client', titleAttribute: 'name')
                    ->searchable()
                    ->required()->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money("EGP")
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('id','desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProtocols::route('/'),
            'create' => Pages\CreateProtocol::route('/create'),
            'view' => Pages\ViewProtocol::route('/{record}'),
            'edit' => Pages\EditProtocol::route('/{record}/edit'),
        ];
    }
}
