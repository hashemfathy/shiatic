<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlockedDayResource\Pages;
use App\Filament\Resources\BlockedDayResource\RelationManagers;
use App\Models\BlockedDay;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlockedDayResource extends Resource
{
    protected static ?string $model = BlockedDay::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'أيام إغلاق الحجز';
    protected static ?string $pluralModelLabel = 'أيام إغلاق الحجز';
    protected static ?string $modelLabel = 'يوم مغلق';

    public static function canViewAny(): bool
    {
        return auth()->user()?->type === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل إغلاق الحجز')
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->label('السبب / اسم الإجازة')
                            ->placeholder('مثال: عطلة نهاية الأسبوع، إجازة رسمية')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label('نوع الإغلاق')
                            ->options([
                                'specific_date' => 'يوم محدد (تاريخ معين)',
                                'recurring' => 'يوم متكرر (أسبوعياً أو شهرياً)',
                            ])
                            ->required()
                            ->default('specific_date')
                            ->reactive(),
                        
                        Forms\Components\DatePicker::make('specific_date')
                            ->label('التاريخ المحدد')
                            ->required(fn (callable $get) => $get('type') === 'specific_date')
                            ->visible(fn (callable $get) => $get('type') === 'specific_date')
                            ->native(false)
                            ->displayFormat('Y-m-d'),

                        Forms\Components\Select::make('day_of_week')
                            ->label('اليوم من الأسبوع')
                            ->options([
                                0 => 'الأحد (Sunday)',
                                1 => 'الأثنين (Monday)',
                                2 => 'الثلاثاء (Tuesday)',
                                3 => 'الأربعاء (Wednesday)',
                                4 => 'الخميس (Thursday)',
                                5 => 'الجمعة (Friday)',
                                6 => 'السبت (Saturday)',
                            ])
                            ->required(fn (callable $get) => $get('type') === 'recurring')
                            ->visible(fn (callable $get) => $get('type') === 'recurring'),

                        Forms\Components\Select::make('monthly_week')
                            ->label('التكرار في الشهر')
                            ->options([
                                'any' => 'كل أسابيع الشهر (أسبوعي متكرر)',
                                'first' => 'الأسبوع الأول من كل شهر',
                                'second' => 'الأسبوع الثاني من كل شهر',
                                'third' => 'الأسبوع الثالث من كل شهر',
                                'fourth' => 'الأسبوع الرابع من كل شهر',
                                'last' => 'الأسبوع الأخير من كل شهر',
                            ])
                            ->required(fn (callable $get) => $get('type') === 'recurring')
                            ->visible(fn (callable $get) => $get('type') === 'recurring')
                            ->default('any'),

                        Forms\Components\TimePicker::make('start_time')
                            ->label('وقت البدء (اختياري)')
                            ->placeholder('مثال: 02:00 مساءً')
                            ->native(false)
                            ->displayFormat('h:i A'),

                        Forms\Components\TimePicker::make('end_time')
                            ->label('وقت الانتهاء (اختياري)')
                            ->placeholder('مثال: 05:00 مساءً')
                            ->native(false)
                            ->displayFormat('h:i A')
                            ->after('start_time'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('السبب / اسم الإجازة')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('نوع الإغلاق')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'specific_date' => 'warning',
                        'recurring' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'specific_date' => 'تاريخ محدد',
                        'recurring' => 'متكرر',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('specific_date')
                    ->label('التاريخ المحدد')
                    ->date()
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('اليوم')
                    ->formatStateUsing(fn (?int $state): string => match ($state) {
                        0 => 'الأحد',
                        1 => 'الأثنين',
                        2 => 'الثلاثاء',
                        3 => 'الأربعاء',
                        4 => 'الخميس',
                        5 => 'الجمعة',
                        6 => 'السبت',
                        default => '—',
                    }),
                Tables\Columns\TextColumn::make('monthly_week')
                    ->label('تكرار الشهر')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'any' => 'كل أسبوع',
                        'first' => 'الأسبوع الأول',
                        'second' => 'الأسبوع الثاني',
                        'third' => 'الأسبوع الثالث',
                        'fourth' => 'الأسبوع الرابع',
                        'last' => 'الأسبوع الأخير',
                        default => '—',
                    }),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('من ساعة')
                    ->time('h:i A')
                    ->placeholder('طوال اليوم'),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('إلى ساعة')
                    ->time('h:i A')
                    ->placeholder('طوال اليوم'),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlockedDays::route('/'),
            'create' => Pages\CreateBlockedDay::route('/create'),
            'edit' => Pages\EditBlockedDay::route('/{record}/edit'),
        ];
    }
}
