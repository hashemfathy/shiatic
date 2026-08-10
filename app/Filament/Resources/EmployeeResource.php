<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

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
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('age')
                    ->required()
                    ->numeric(),
                Forms\Components\DateTimePicker::make('employment_date'),
                Forms\Components\Select::make('work_days')
                    ->label('Work Days')
                    ->multiple()
                    ->options([
                        'monday' => 'Monday',
                        'tuesday' => 'Tuesday',
                        'wednesday' => 'Wednesday',
                        'thursday' => 'Thursday',
                        'friday' => 'Friday',
                        'saturday' => 'Saturday',
                        'sunday' => 'Sunday',
                    ])
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('age')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employment_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('work_days')
                    ->label('Work Days')
                    ->formatStateUsing(fn($state) => is_array($state) ? implode(', ', $state) : ($state ?? '—'))
                    ->wrap()
                    ->searchable(),
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
            RelationManagers\SessionsRelationManager::class,
            RelationManagers\TransactionsRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
