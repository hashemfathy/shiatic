<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

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
                // Forms\Components\TextInput::make('id')
                //     ->required()
                //     ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->query(
                \App\Models\Visit::whereHas('sessions', function (Builder $query) {
                    $query->where('employee_id', $this->getOwnerRecord()->id);
                })
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric(),
                Tables\Columns\TextColumn::make('client.name')
                    ->numeric(),
                // Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('paid')
                    ->money("EGP")
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('date')
                    ->sortable(),
                Tables\Columns\TextColumn::make('hour')
                    ->label('الساعة')
                    ->formatStateUsing(function ($state) {
                        if (is_null($state) || $state === '') return null;
                        $floatVal = (float)$state;
                        $hrs = (int)floor($floatVal);
                        $mins = (int)round(($floatVal - $hrs) * 60);
                        if ($mins >= 60) {
                            $hrs += 1;
                            $mins -= 60;
                        }
                        if ($hrs >= 1 && $hrs <= 8) {
                            $hrs += 12;
                        }
                        $displayHrs = $hrs % 12;
                        if ($displayHrs === 0) {
                            $displayHrs = 12;
                        }
                        $amPm = (($hrs % 24) >= 12) ? 'PM' : 'AM';
                        $label = sprintf('%02d:%02d %s', $displayHrs, $mins, $amPm);
                        if ($hrs >= 24) {
                            $label .= ' (اليوم التالي)';
                        }
                        return $label;
                    }),
                Tables\Columns\TextColumn::make('improvement_percentage')
                    ->label('improvement'),
            ])->defaultSort('id','desc')
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('viewVisit')
                    ->label('View Visit')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.admin.resources.visits.view', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
