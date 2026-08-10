<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Models\Expense;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

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
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->numeric()
                    ->minValue(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('value')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('This month')
                     ->query(fn (Builder $query): Builder => $query->whereYear('created_at', Carbon::now()->year)
                     ->whereMonth('created_at', Carbon::now()->month)),
                Filter::make('This day')
                     ->query(fn (Builder $query): Builder => $query->whereDate('created_at', Carbon::today())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'view' => Pages\ViewExpense::route('/{record}'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
