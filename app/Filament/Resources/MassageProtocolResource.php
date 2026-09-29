<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MassageProtocolResource\Pages;
use App\Models\MassageProtocol;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MassageProtocolResource extends Resource
{
    protected static ?string $model = MassageProtocol::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    protected static ?string $navigationGroup = 'الخدمات والإعدادات';
    protected static ?string $navigationLabel = 'المساج العلاجي (فصائل الدم)';
    protected static ?string $pluralModelLabel = 'بروتوكولات وتكنيكات المساج العلاجي';
    protected static ?string $modelLabel = 'بروتوكول مساج';
    protected static ?int $navigationSort = 6;

    public static function canAccess(): bool
    {
        return !in_array(auth()->user()?->type, ['specialist']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('البيانات الأساسية لبروتوكول المساج')
                    ->description('تحديد فصيلة الدم، مستوى الألم، فئة الوزن، والشدة والسرعة')
                    ->schema([
                        Forms\Components\Select::make('blood_type')
                            ->label('فصيلة الدم')
                            ->options([
                                'O' => '🩸 فصيلة O',
                                'A' => '🩸 فصيلة A',
                                'B' => '🩸 فصيلة B',
                                'AB' => '🩸 فصيلة AB',
                            ])
                            ->required(),

                        Forms\Components\Select::make('pain_level')
                            ->label('مستوى الألم')
                            ->options([
                                'severe' => 'ألم شديد',
                                'moderate' => 'ألم متوسط',
                            ])
                            ->required(),

                        Forms\Components\Select::make('weight_bracket')
                            ->label('فئة الوزن')
                            ->options([
                                '30_55' => '30 كجم : 55 كجم',
                                '55_100' => '55 كجم : 100 كجم',
                                '100_300' => '100 كجم : 300 كجم',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('name')
                            ->label('اسم البروتوكول التعريفي')
                            ->placeholder('مثال: فصيلة O - ألم شديد (30-55 كجم)')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('intensity_percent')
                            ->label('الشدة الافتراضية')
                            ->numeric()
                            ->suffix('%')
                            ->default(30)
                            ->required(),

                        Forms\Components\TextInput::make('speed_percent')
                            ->label('السرعة الافتراضية')
                            ->numeric()
                            ->suffix('%')
                            ->default(20)
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('مفعل')
                            ->default(true),
                    ])->columns(3),

                Forms\Components\Section::make('التسعير والمدد والتكرارات (فاخر / مكثف مقابل اقتصادي)')
                    ->description('التحكم في سعر ودقيقة وتكرارات التكنيك الواحد لكل فئة وزن ونوع باقة')
                    ->schema([
                        Forms\Components\Fieldset::make('الباقة الفاخرة / المكثفة (Luxury)')
                            ->schema([
                                Forms\Components\TextInput::make('luxury_price_per_technique')
                                    ->label('سعر التكنيك الواحد')
                                    ->numeric()
                                    ->suffix('ج.م')
                                    ->required(),

                                Forms\Components\TextInput::make('luxury_duration_minutes')
                                    ->label('مدة التكنيك الواحد')
                                    ->numeric()
                                    ->suffix('دقيقة')
                                    ->required(),

                                Forms\Components\TextInput::make('luxury_reps')
                                    ->label('عدد التكرارات')
                                    ->numeric()
                                    ->required(),
                            ])->columns(3),

                        Forms\Components\Fieldset::make('الباقة الاقتصادية (Economy)')
                            ->schema([
                                Forms\Components\TextInput::make('economy_price_per_technique')
                                    ->label('سعر التكنيك الواحد')
                                    ->numeric()
                                    ->suffix('ج.م')
                                    ->required(),

                                Forms\Components\TextInput::make('economy_duration_minutes')
                                    ->label('مدة التكنيك الواحد')
                                    ->numeric()
                                    ->suffix('دقيقة')
                                    ->required(),

                                Forms\Components\TextInput::make('economy_reps')
                                    ->label('عدد التكرارات')
                                    ->numeric()
                                    ->required(),
                            ])->columns(3),
                    ]),

                Forms\Components\Section::make('جدول التكنيكات المطبقة للمساج')
                    ->description('قائمة التكنيكات التفصيلية (المنطقة التشريحية، ر م، نوع المساج، الأداة، والاتجاه)')
                    ->schema([
                        Forms\Components\Repeater::make('techniques')
                            ->relationship('techniques')
                            ->schema([
                                Forms\Components\TextInput::make('order')
                                    ->label('العدد / م')
                                    ->numeric()
                                    ->required()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('region_number')
                                    ->label('ر م')
                                    ->numeric()
                                    ->required()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('region_name')
                                    ->label('المنطقة')
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('massage_type')
                                    ->label('نوع المساج')
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('tool')
                                    ->label('الأداة')
                                    ->placeholder('إبهام، كلوة، قبضة...')
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('direction')
                                    ->label('الاتجاه')
                                    ->placeholder('أعلى لأسفل، داخل للخارج...')
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('reps_display')
                                    ->label('العدد لكل وزن')
                                    ->placeholder('15/10')
                                    ->columnSpan(1),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('مفعل')
                                    ->default(true)
                                    ->columnSpan(1),
                            ])
                            ->columns(12)
                            ->defaultItems(0)
                            ->reorderableWithButtons()
                            ->addActionLabel('➕ إضافة تكنيك مساج جديد')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('blood_type')
                    ->label('فصيلة الدم')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'O' => 'danger',
                        'A' => 'info',
                        'B' => 'warning',
                        'AB' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => "🩸 فصيلة {$state}")
                    ->sortable(),

                Tables\Columns\TextColumn::make('pain_level')
                    ->label('مستوى الألم')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'severe' => 'danger',
                        'moderate' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'severe' => 'ألم شديد',
                        'moderate' => 'ألم متوسط',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('weight_bracket')
                    ->label('فئة الوزن')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '30_55' => '30 - 55 كجم',
                        '55_100' => '55 - 100 كجم',
                        '100_300' => '100 - 300 كجم',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('luxury_price_per_technique')
                    ->label('سعر فاخر')
                    ->suffix(' ج.م')
                    ->sortable(),

                Tables\Columns\TextColumn::make('luxury_duration_minutes')
                    ->label('مدة فاخر')
                    ->suffix(' دقيقة')
                    ->sortable(),

                Tables\Columns\TextColumn::make('economy_price_per_technique')
                    ->label('سعر اقتصادي')
                    ->suffix(' ج.م')
                    ->sortable(),

                Tables\Columns\TextColumn::make('economy_duration_minutes')
                    ->label('مدة اقتصادي')
                    ->suffix(' دقيقة')
                    ->sortable(),

                Tables\Columns\TextColumn::make('intensity_percent')
                    ->label('الشدة')
                    ->suffix('%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('techniques_count')
                    ->counts('techniques')
                    ->label('التكنيكات')
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('مفعل')
                    ->boolean(),
            ])
            ->defaultSort('blood_type')
            ->filters([
                Tables\Filters\SelectFilter::make('blood_type')
                    ->label('فصيلة الدم')
                    ->options([
                        'O' => 'فصيلة O',
                        'A' => 'فصيلة A',
                        'B' => 'فصيلة B',
                        'AB' => 'فصيلة AB',
                    ]),
                Tables\Filters\SelectFilter::make('pain_level')
                    ->label('مستوى الألم')
                    ->options([
                        'severe' => 'ألم شديد',
                        'moderate' => 'ألم متوسط',
                    ]),
                Tables\Filters\SelectFilter::make('weight_bracket')
                    ->label('فئة الوزن')
                    ->options([
                        '30_55' => '30 - 55 كجم',
                        '55_100' => '55 - 100 كجم',
                        '100_300' => '100 - 300 كجم',
                    ]),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMassageProtocols::route('/'),
            'create' => Pages\CreateMassageProtocol::route('/create'),
            'edit' => Pages\EditMassageProtocol::route('/{record}/edit'),
        ];
    }
}
