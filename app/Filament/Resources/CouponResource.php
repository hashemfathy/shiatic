<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Filament\Resources\CouponResource\RelationManagers;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'الكوبونات';
    protected static ?string $pluralModelLabel = 'الكوبونات';
    protected static ?string $modelLabel = 'كوبون';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل كوبون الخصم')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('كود الكوبون (كبير/مفرغ بدون مسافات)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('مثال: SAVE150'),
                        Forms\Components\Select::make('type')
                            ->label('نوع الخصم')
                            ->options([
                                'fixed' => 'مبلغ ثابت (ج.م)',
                                'percentage' => 'نسبة مئوية (%)',
                            ])
                            ->required()
                            ->default('fixed'),
                        Forms\Components\TextInput::make('value')
                            ->label('قيمة الخصم')
                            ->required()
                            ->numeric()
                            ->placeholder('150'),
                        Forms\Components\DatePicker::make('expires_at')
                            ->label('تاريخ نهاية الصلاحية')
                            ->placeholder('اختر تاريخاً...'),
                        Forms\Components\TextInput::make('max_uses')
                            ->label('الحد الأقصى لمرات الاستخدام (اختياري)')
                            ->numeric()
                            ->placeholder('مثال: 100'),
                        Forms\Components\TextInput::make('uses')
                            ->label('مرات الاستخدام الحالية')
                            ->required()
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->default(0),
                        Forms\Components\TextInput::make('min_booking_value')
                            ->label('الحد الأدنى لقيمة الحجز لتفعيل الكوبون')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->prefix('EGP'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('حالة الكوبون (نشط / غير نشط)')
                            ->required()
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('الكود')
                    ->searchable()
                    ->fontFamily('mono')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('type')
                    ->label('نوع الخصم')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fixed' => 'info',
                        'percentage' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'fixed' => 'مبلغ ثابت',
                        'percentage' => 'نسبة مئوية',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('value')
                    ->label('القيمة')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('تاريخ الانتهاء')
                    ->date('d-m-Y')
                    ->sortable()
                    ->placeholder('لا ينتهي'),
                Tables\Columns\TextColumn::make('max_uses')
                    ->label('أقصى استخدام')
                    ->numeric()
                    ->sortable()
                    ->placeholder('غير محدود'),
                Tables\Columns\TextColumn::make('uses')
                    ->label('الاستخدام الحالي')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_booking_value')
                    ->label('الحد الأدنى للحجز')
                    ->money('EGP')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
