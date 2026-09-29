<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChiropracticRegionResource\Pages;
use App\Models\ChiropracticRegion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ChiropracticRegionResource extends Resource
{
    protected static ?string $model = ChiropracticRegion::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = 'الخدمات والإعدادات';
    protected static ?string $navigationLabel = 'الكيروبراكتيك العلاجي';
    protected static ?string $pluralModelLabel = 'مناطق وتكنيكات الكيروبراكتيك';
    protected static ?string $modelLabel = 'منطقة وتكنيكات';
    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool
    {
        return !in_array(auth()->user()?->type, ['specialist']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات المنطقة والتسعير والمدة')
                    ->description('تحديد نوع البروتوكول، رقم المنطقة، الأرقام البيانية، سعر التكنيك ومدة كل حركة بالثواني')
                    ->schema([
                        Forms\Components\Select::make('plan_type')
                            ->label('نوع البروتوكول / الخطة')
                            ->options([
                                'intensive' => 'علاجي مكثف',
                                'economy' => 'اقتصادي',
                            ])
                            ->required()
                            ->default('intensive'),

                        Forms\Components\Select::make('region_number')
                            ->label('رقم المنطقة')
                            ->options([
                                1 => 'منطقة 1',
                                2 => 'منطقة 2',
                                3 => 'منطقة 3',
                                4 => 'منطقة 4',
                                5 => 'منطقة 5',
                            ])
                            ->required()
                            ->default(1),

                        Forms\Components\TextInput::make('name')
                            ->label('اسم المنطقة')
                            ->placeholder('مثال: منطقة 1 العنقية')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('price_per_technique')
                            ->label('سعر التكنيك الواحد')
                            ->required()
                            ->numeric()
                            ->suffix('ج.م')
                            ->default(13.00),

                        Forms\Components\TextInput::make('duration_seconds')
                            ->label('مدة التكنيك الواحد')
                            ->required()
                            ->numeric()
                            ->suffix('ثانية')
                            ->default(15),

                        Forms\Components\TagsInput::make('diagram_numbers')
                            ->label('الأرقام البيانية للمنطقة')
                            ->placeholder('أدخل الرقم واضغط Enter (مثال: 15, 16, 37)')
                            ->helperText('أرقام خريطة الجسم البيانية التابعة لهذه المنطقة')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('مفعلة')
                            ->default(true),
                    ])->columns(3),

                Forms\Components\Section::make('جدول التكنيكات المطبقة للمنطقة')
                    ->description('إضافة، تعديل، وترتيب التكنيكات والوضعيات والاتجاهات الخاصة بهذه المنطقة')
                    ->schema([
                        Forms\Components\Repeater::make('techniques')
                            ->relationship('techniques')
                            ->schema([
                                Forms\Components\TextInput::make('order')
                                    ->label('العدد / م')
                                    ->numeric()
                                    ->default(fn ($get) => 1)
                                    ->required()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('target_region_code')
                                    ->label('ر منطقه')
                                    ->placeholder('مثال: 16 أو 13/14')
                                    ->required()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('name')
                                    ->label('التكنيك')
                                    ->placeholder('مثال: الاذن اليمنى')
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('position')
                                    ->label('الوضعية')
                                    ->placeholder('مثال: الجلوس أو النوم على الظهر')
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('direction')
                                    ->label('الاتجاه')
                                    ->placeholder('مثال: للخارج أو كتف مرتفع')
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('rep')
                                    ->label('مللي')
                                    ->numeric()
                                    ->default(2)
                                    ->required()
                                    ->columnSpan(1),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('مفعل')
                                    ->default(true)
                                    ->columnSpan(1),
                            ])
                            ->columns(10)
                            ->defaultItems(0)
                            ->reorderableWithButtons()
                            ->addActionLabel('➕ إضافة تكنيك جديد')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plan_type')
                    ->label('البروتوكول')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'intensive' => 'warning',
                        'economy' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'intensive' => 'علاجي مكثف',
                        'economy' => 'اقتصادي',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('region_number')
                    ->label('رقم المنطقة')
                    ->formatStateUsing(fn ($state) => "منطقة {$state}")
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name')
                    ->label('اسم المنطقة')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('price_per_technique')
                    ->label('سعر التكنيك')
                    ->suffix(' ج.م')
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration_seconds')
                    ->label('المدة')
                    ->suffix(' ثانية')
                    ->sortable(),

                Tables\Columns\TextColumn::make('diagram_numbers')
                    ->label('الأرقام البيانية')
                    ->badge()
                    ->separator(', '),

                Tables\Columns\TextColumn::make('techniques_count')
                    ->counts('techniques')
                    ->label('عدد التكنيكات')
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('مفعل')
                    ->boolean(),
            ])
            ->defaultSort('region_number')
            ->filters([
                Tables\Filters\SelectFilter::make('plan_type')
                    ->label('البروتوكول')
                    ->options([
                        'intensive' => 'علاجي مكثف',
                        'economy' => 'اقتصادي',
                    ]),
                Tables\Filters\SelectFilter::make('region_number')
                    ->label('المنطقة')
                    ->options([
                        1 => 'منطقة 1',
                        2 => 'منطقة 2',
                        3 => 'منطقة 3',
                        4 => 'منطقة 4',
                        5 => 'منطقة 5',
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
            'index' => Pages\ListChiropracticRegions::route('/'),
            'create' => Pages\CreateChiropracticRegion::route('/create'),
            'edit' => Pages\EditChiropracticRegion::route('/{record}/edit'),
        ];
    }
}
