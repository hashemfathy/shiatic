<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RequestResource\Pages;
use App\Filament\Resources\RequestResource\RelationManagers;
use App\Models\Request;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RequestResource extends Resource
{
    protected static ?string $model = Request::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canAccess(): bool
    {
        return !in_array(auth()->user()?->type,['specialist']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('gender')
                    ->required()
                    ->reactive()
                    ->options([
                        "male" => "Male","female"=> "Female"
                    ]),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('تفاصيل السيشن والخدمات')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\Group::make([
                    Forms\Components\TextInput::make('deposit')
                        ->numeric()
                        ->prefix('EGP'),
                    Forms\Components\DatePicker::make('date')
                        ->reactive()
                        ->required(),
                    Forms\Components\TimePicker::make('time')
                        ->label('Time')
                        ->required()
                        ->rules([
                            fn (callable $get, $record) => \App\Filament\Resources\RequestResource::getOverlapValidationRule($get, $record, false)
                        ])
                ])->visible(fn (string $operation) => $operation === 'edit'),

                Forms\Components\Repeater::make('dates_times')
                    ->schema([
                        Forms\Components\TextInput::make('deposit')
                            ->numeric()
                            ->prefix('EGP'),
                        Forms\Components\DatePicker::make('date')
                            ->reactive()
                            ->required(),
                        Forms\Components\TimePicker::make('time')
                            ->label('Time')
                            ->required()
                            ->rules([
                                fn (callable $get, $record) => \App\Filament\Resources\RequestResource::getOverlapValidationRule($get, $record, true)
                            ])
                    ])
                    ->visible(fn (string $operation) => $operation === 'create')
                    ->minItems(1)
                    ->required(),
                Forms\Components\Section::make('تفاصيل الحجز')
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('تاريخ تقديم الطلب (Created At)')
                            ->content(fn ($record) => $record && $record->created_at ? $record->created_at->format('d-m-Y h:i A') : '-'),
                        Forms\Components\Placeholder::make('formatted_description')
                            ->label('تفاصيل السيشن المفصلة (مساج / تقويم / حجامة)')
                            ->content(function (callable $get) {
                                $description = $get('description');
                                if (!$description) return '-';
                                $parts = explode(' | ', $description);
                                $html = '<div style="line-height: 1.6; font-size: 0.95rem; direction: rtl; text-align: right;">';
                                foreach ($parts as $part) {
                                    $html .= '<div style="margin-bottom: 0.5rem; padding: 0.75rem; background: #262626; border-right: 4px solid #ff9d42; border-radius: 6px; color: #fff;">';
                                    $html .= e($part);
                                    $html .= '</div>';
                                }
                                $html .= '</div>';
                                return new \Illuminate\Support\HtmlString($html);
                            })
                            ->columnSpanFull(),
                        Forms\Components\Select::make('booking_type')
                            ->label('نوع الحجز')
                            ->options([
                                'وقائية' => 'وقائية',
                                'علاجية' => 'علاجية',
                                'رياضية' => 'رياضية',
                            ])
                            ->reactive()
                            ->required()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                self::updateTotals($set, $get);
                            }),
                        Forms\Components\Toggle::make('is_urgent')
                            ->label('حجز مستعجل (Urgent Booking)')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                self::updateTotals($set, $get);
                            }),
                        Forms\Components\TextInput::make('service_type')
                            ->label('الخدمة')
                            ->disabled(),
                        Forms\Components\TextInput::make('total_price')
                            ->label('السعر الإجمالي')
                            ->numeric()
                            ->suffix(' EGP'),
                        Forms\Components\TextInput::make('total_duration')
                            ->label('المدة الإجمالية')
                            ->numeric()
                            ->suffix(' دقائق'),
                        Forms\Components\TextInput::make('user_agreement')
                            ->label('شروط الحجز ومقدم الجدية')
                            ->disabled(),
                    ])
                    ->collapsible()
                    ->collapsed(false)
                    ->visible(fn (callable $get) => $get('booking_type') !== null),

                Forms\Components\Section::make('💆‍♂️ المساج (Massage)')
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn (callable $get) => $get('booking_type') === 'وقائية')
                    ->schema([
                        Forms\Components\CheckboxList::make('packages')
                            ->label('الباقات المطلوبة')
                            ->options([
                                'intensive' => 'الجسم كامل مكثف (Intensive Luxury)',
                                'economy' => 'الجسم كامل اقتصادي (Economy Plan)',
                            ])
                            ->columns(2)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                if (!empty($state)) {
                                    $set('massage_regions', []);
                                }
                                self::updateTotals($set, $get);
                            }),
                        Forms\Components\Select::make('massage_regions')
                            ->label('مناطق المساج المحددة من الصورة')
                            ->multiple()
                            ->options(array_combine(range(1, 39), range(1, 39)))
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                if (!empty($state)) {
                                    $set('packages', []);
                                }
                                self::updateTotals($set, $get);
                            }),
                        Forms\Components\Radio::make('massage_style')
                            ->label('طريقة المساج')
                            ->options([
                                'intensive' => 'مكثف',
                                'economy' => 'اقتصادي',
                            ])
                            ->inline()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                self::updateTotals($set, $get);
                            }),
                        Forms\Components\Radio::make('massage_intensity')
                            ->label('شدة المساج')
                            ->options([
                                'medium' => 'ميديم (Medium)',
                                'hard' => 'هارد (Hard)',
                            ])
                            ->default('medium')
                            ->inline()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                self::updateTotals($set, $get);
                            }),
                    ]),
                
                Forms\Components\Section::make('⚡ تقويم عمود فقري (Cracking)')
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn (callable $get) => $get('booking_type') === 'وقائية')
                    ->schema([
                        Forms\Components\Radio::make('cracking_type')
                            ->label('نوع تقويم العمود الفقري')
                            ->options([
                                'none' => 'بدون تقويم عمود فقري',
                                'whole_body' => 'تقويم الجسم كامل',
                                'regions' => 'اختيار مناطق من الصورة',
                            ])
                            ->inline()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                if ($state === 'none' || $state === 'whole_body') {
                                    $set('cracking_regions', []);
                                }
                                self::updateTotals($set, $get);
                            }),
                        Forms\Components\Radio::make('cracking_style')
                            ->label('طريقة تقويم العمود الفقري')
                            ->options([
                                'intensive' => 'مكثف',
                                'economy' => 'اقتصادي',
                            ])
                            ->default('intensive')
                            ->inline()
                            ->reactive()
                            ->visible(fn (callable $get) => $get('cracking_type') !== 'none')
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                self::updateTotals($set, $get);
                            }),
                        Forms\Components\Select::make('cracking_regions')
                            ->label('مناطق التقويم المحددة')
                            ->multiple()
                            ->options([
                                1 => 'منطقة 1',
                                2 => 'منطقة 2',
                                3 => 'منطقة 3',
                                4 => 'منطقة 4',
                                5 => 'منطقة 5',
                             ])
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                if (!empty($state)) {
                                    $set('cracking_type', 'regions');
                                }
                                self::updateTotals($set, $get);
                            }),
                    ]),

                Forms\Components\Section::make('🩸 الحجامة (Hijama / Cupping)')
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn (callable $get) => $get('booking_type') === 'وقائية')
                    ->schema([
                        Forms\Components\Radio::make('hijama_type')
                            ->label('نوع الحجامة')
                            ->options([
                                'none' => 'بدون حجامة',
                                'whole_back' => 'خلفيات الجسم كامل',
                                'whole_front' => 'اماميات الجسم كامل',
                                'regions' => 'اختيار مناطق من الصورة',
                            ])
                            ->inline()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                if ($state === 'none') {
                                    $set('hijama_regions', []);
                                } elseif ($state === 'whole_back') {
                                    $set('hijama_regions', [1, 3, 5, 7, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 37]);
                                } elseif ($state === 'whole_front') {
                                    $set('hijama_regions', [19, 22, 23, 24, 25, 27, 28, 30, 31, 32, 33, 34, 35, 36]);
                                }
                                self::updateTotals($set, $get);
                            }),
                        Forms\Components\Radio::make('hijama_style')
                            ->label('طريقة سيشن الحجامة')
                            ->options([
                                'intensive' => 'مكثف',
                                'economy' => 'اقتصادي',
                            ])
                            ->inline()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                self::updateTotals($set, $get);
                            }),
                        Forms\Components\Select::make('hijama_regions')
                            ->label('مناطق الحجامة المحددة')
                            ->multiple()
                            ->options(array_combine(range(1, 39), range(1, 39)))
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                $wholeBackPreset = [1, 3, 5, 7, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 37];
                                $wholeFrontPreset = [19, 22, 23, 24, 25, 27, 28, 30, 31, 32, 33, 34, 35, 36];
                                
                                $stateArray = array_map('intval', (array)$state);
                                sort($stateArray);
                                sort($wholeBackPreset);
                                sort($wholeFrontPreset);
                                
                                if (empty($stateArray)) {
                                    $set('hijama_type', 'none');
                                } elseif ($stateArray === $wholeBackPreset) {
                                    $set('hijama_type', 'whole_back');
                                } elseif ($stateArray === $wholeFrontPreset) {
                                    $set('hijama_type', 'whole_front');
                                } else {
                                    $set('hijama_type', 'regions');
                                }
                                self::updateTotals($set, $get);
                            }),
                    ]),
                Forms\Components\Section::make('التكنيكات الخاصة بجلسة المساج (Massage Techniques)')
                    ->collapsible()
                    ->collapsed(false)
                    ->visible(fn (callable $get) => $get('booking_type') === 'وقائية' && (!empty($get('packages')) || !empty($get('massage_regions'))))
                    ->schema([
                        Forms\Components\Placeholder::make('massage_techniques_table')
                            ->label('')
                            ->content(fn (callable $get) => \App\Helpers\MassageHelper::renderTechniquesTableForForm($get)),
                    ])
                    ->columnSpanFull(),
                Forms\Components\Section::make('التكنيكات الخاصة بجلسة تقويم العمود الفقري (Cracking Techniques)')
                    ->collapsible()
                    ->collapsed(false)
                    ->visible(fn (callable $get) => $get('booking_type') === 'وقائية' && $get('cracking_type') !== 'none')
                    ->schema([
                        Forms\Components\Placeholder::make('cracking_techniques_table')
                            ->label('')
                            ->content(fn (callable $get) => \App\Helpers\CrackingHelper::renderTechniquesTableForForm($get)),
                    ])
                    ->columnSpanFull(),
                Forms\Components\Section::make('أفراد المجموعة المشتركة بالحجز')
                    ->visible(fn ($record) => $record && ($record->parent_id || $record->children()->exists()))
                    ->schema([
                        Forms\Components\Placeholder::make('group_members')
                            ->label('')
                            ->content(function ($record) {
                                if (!$record) return '-';
                                $leader = $record->parent_id ? $record->parent : $record;
                                if (!$leader) return '-';
                                $members = collect([$leader])->concat($leader->children);
                                
                                $html = '<div style="line-height: 1.6; font-size: 0.95rem; direction: rtl; text-align: right;">';
                                foreach ($members as $member) {
                                    $isCurrent = $member->id === $record->id;
                                    $style = $isCurrent ? 'font-weight: bold; background: #3f3f46; border-right: 4px solid #ff9d42;' : 'background: #27272a; border-right: 4px solid #71717a;';
                                    $html .= "<div style=\"margin-bottom: 0.5rem; padding: 0.75rem; {$style} border-radius: 6px; color: #fff;\">";
                                    $html .= e($member->name) . " (" . ($member->gender === 'male' ? 'ذكر' : 'أنثى') . ") - الهاتف: " . e($member->phone);
                                    $html .= " | الخدمة: " . e($member->service_type ?: 'غير حدد') . " | السعر: " . e($member->total_price) . " ج.م";
                                    if ($member->id === $leader->id) {
                                        $html .= " <span style=\"background: #d97706; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-right: 5px;\">قائد المجموعة</span>";
                                    }
                                    if ($isCurrent) {
                                        $html .= " <span style=\"background: #15803d; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-right: 5px;\">الحالي</span>";
                                    }
                                    $html .= '</div>';
                                }
                                $html .= '</div>';
                                return new \Illuminate\Support\HtmlString($html);
                            })
                    ])
                    ->columnSpanFull(),
                Forms\Components\Group::make()
                    ->schema(function ($record) {
                        if (!$record) return [];
                        $isGroup = $record->children()->exists() || $record->parent_id;
                        if (!$isGroup) return [];

                        $leader = $record->parent_id ? $record->parent : $record;
                        if (!$leader) return [];

                        $members = collect([$leader])->concat($leader->children);
                        $sections = [];
                        
                        foreach ($members as $index => $member) {
                            if ($member->id === $record->id) continue;

                            $sections[] = Forms\Components\Section::make("تفاصيل حجز: " . $member->name . " (" . ($member->id === $leader->id ? 'قائد المجموعة' : 'مرافق ' . ($index + 1)) . ")")
                                ->collapsible()
                                ->schema([
                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\TextInput::make("member_name_{$member->id}")
                                                ->label('الاسم')
                                                ->default($member->name)
                                                ->disabled(),
                                            Forms\Components\TextInput::make("member_gender_{$member->id}")
                                                ->label('الجنس')
                                                ->default($member->gender === 'male' ? 'ذكر' : 'أنثى')
                                                ->disabled(),
                                            Forms\Components\TextInput::make("member_phone_{$member->id}")
                                                ->label('الهاتف')
                                                ->default($member->phone)
                                                ->disabled(),
                                        ]),
                                    Forms\Components\Placeholder::make("member_desc_placeholder_{$member->id}")
                                        ->label('تفاصيل الجلسة')
                                        ->content(function () use ($member) {
                                            $description = $member->description;
                                            if (!$description) return '-';
                                            $parts = explode(' | ', $description);
                                            $html = '<div style="line-height: 1.6; font-size: 0.95rem; direction: rtl; text-align: right;">';
                                            foreach ($parts as $part) {
                                                $html .= '<div style="margin-bottom: 0.5rem; padding: 0.75rem; background: #262626; border-right: 4px solid #38bdf8; border-radius: 6px; color: #fff;">';
                                                $html .= e($part);
                                                $html .= '</div>';
                                            }
                                            $html .= '</div>';
                                            return new \Illuminate\Support\HtmlString($html);
                                        })
                                        ->columnSpanFull(),
                                    Forms\Components\Grid::make(4)
                                        ->schema([
                                            Forms\Components\TextInput::make("member_booking_type_{$member->id}")
                                                ->label('نوع الحجز')
                                                ->default($member->booking_type)
                                                ->disabled(),
                                            Forms\Components\TextInput::make("member_service_type_{$member->id}")
                                                ->label('الخدمة')
                                                ->default($member->service_type)
                                                ->disabled(),
                                            Forms\Components\TextInput::make("member_price_{$member->id}")
                                                ->label('السعر')
                                                ->default($member->total_price)
                                                ->suffix(' EGP')
                                                ->disabled(),
                                            Forms\Components\TextInput::make("member_duration_{$member->id}")
                                                ->label('المدة')
                                                ->default($member->total_duration)
                                                ->suffix(' دقائق')
                                                ->disabled(),
                                        ]),
                                ]);
                        }
                        return $sections;
                    })
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->defaultSort('date', 'asc')
            ->modifyQueryUsing(function (Builder $query) { 
                return $query->select([
                    'id',
                    'parent_id',
                    'name',
                    'gender',
                    'phone',
                    'time',
                    'date',
                    'deposit',
                    'description',
                    'status',
                    'is_urgent',
                    'booking_type',
                    'total_price',
                    'total_duration',
                    'user_agreement',
                    'coupon_code',
                    'coupon_discount',
                    'created_at',
                ]); 
            })
            ->recordClasses(fn (Model $record) => match (true) {
                $record->parent_id !== null || $record->children()->exists() => 'border-l-4 border-warning-500 bg-amber-500/5 dark:bg-amber-500/10',
                default => null,
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->color(fn ($record) => $record->gender === 'female' ? Color::Pink : Color::Blue)
                    ->description(function ($record) {
                        if ($record->children()->exists()) {
                            return "👥 قائد مجموعة (الأفراد: " . ($record->children()->count() + 1) . ")";
                        }
                        if ($record->parent_id) {
                            return "🔗 عضو بمجموعة (القائد: " . ($record->parent?->name ?: 'غير معروف') . ")";
                        }
                        return null;
                    }),
                Tables\Columns\TextColumn::make('phone')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('time')
                    ->label('الساعة')
                    ->formatStateUsing(function (string $state) {
                        $parts = explode(':', $state);
                        if (count($parts) < 2) return $state;
                        $hrs = (int)$parts[0];
                        $mins = (int)$parts[1];
                        
                        // Treat hours 1-8 as PM (for legacy 12-hour values stored as 01:00 to 08:00 without PM)
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
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Day')
                    ->date("d-m-Y")
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->orderBy('date', $direction)
                            ->orderBy('time', $direction);
                    }),
                // Tables\Columns\TextColumn::make('description')
                //     ->searchable()
                //     ->wrap(),
                Tables\Columns\TextColumn::make('booking_type')
                    ->label('نوع الحجز')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_urgent')
                    ->label('نوع الموعد')
                    ->badge()
                    ->state(fn ($record) => $record->is_urgent ? 'مستعجل' : 'عادي')
                    ->color(fn ($record) => $record->is_urgent ? 'warning' : 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('السعر الإجمالي')
                    ->money("EGP")
                    ->description(function ($record) {
                        $desc = [];
                        if ($record->is_urgent) {
                            $desc[] = 'يشمل رسوم مستعجل';
                        }
                        if ($record->coupon_code) {
                            $desc[] = "🎟️ كوبون خصم ({$record->coupon_code}) بقيمة -{$record->coupon_discount} EGP";
                        }
                        return implode(' | ', $desc) ?: null;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('coupon_code')
                    ->label('الكوبون المطبق')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state, $record) => $state ? "🎟️ {$state} (-{$record->coupon_discount} EGP)" : null)
                    ->placeholder('لا يوجد')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_duration')
                    ->label('المدة (دقيقة)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deposit')
                    ->label('مقدم الحجز')
                    ->money("EGP")
                    ->description(fn ($record) => $record->is_urgent ? 'يشمل رسوم مستعجل' : null)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d-m-Y h:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'canceled' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Filter::make('Today')
                     ->default()
                     ->query(fn (Builder $query): Builder => $query->whereDate('date', \Carbon\Carbon::today())),
                Filter::make('Tomorrow')
                     ->query(fn (Builder $query): Builder => $query->whereDate('date', \Carbon\Carbon::today()->addDay(1))),
                Filter::make('filter_date')
                    ->form([
                        Forms\Components\DatePicker::make('date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('date', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('Accept')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->modalHeading('قبول الحجز وإنشاء زيارة')
                    ->modalSubmitActionLabel('قبول الحجز')
                    ->form(fn (Request $record) => [
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Section::make('بيانات العميل')
                                    ->description('البيانات الشخصية للعميل')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('اسم العميل')
                                            ->required(),
                                        Forms\Components\TextInput::make('phone')
                                            ->label('رقم الهاتف')
                                            ->required(),
                                        Forms\Components\Select::make('gender')
                                            ->label('الجنس')
                                            ->options([
                                                'male' => 'ذكر',
                                                'female' => 'أنثى',
                                            ])
                                            ->required(),
                                    ])->columnSpan(1),

                                Forms\Components\Section::make('تفاصيل الحجز')
                                    ->description('معلومات موعد الحجز والخدمات')
                                    ->schema([
                                        Forms\Components\DatePicker::make('visit_date')
                                            ->label('تاريخ الزيارة')
                                            ->required(),
                                        Forms\Components\TextInput::make('visit_hour')
                                            ->label('ساعة الحجز')
                                            ->required(),
                                        Forms\Components\Textarea::make('visit_complaint')
                                            ->label('الشكوى / تفاصيل الحجز')
                                            ->required()
                                            ->rows(3),
                                    ])->columnSpan(1),

                                Forms\Components\Repeater::make('sessions')
                                    ->label('جلسات الزيارة والمختصين (Sessions & Specialists) *')
                                    ->schema([
                                        Forms\Components\Select::make('type')
                                            ->label('نوع الخدمة')
                                            ->options([
                                                'مساج' => 'مساج (Massage)',
                                                'تقويم' => 'تقويم (Cracking)',
                                                'حجامة' => 'حجامة (Hijama)',
                                            ])
                                            ->required(),
                                        Forms\Components\Select::make('employee_id')
                                            ->label('المختص المعالج')
                                            ->options(function (callable $get) {
                                                $date = $get('../../visit_date');
                                                $dayOfWeek = $date ? strtolower(\Carbon\Carbon::parse($date)->format('l')) : null;

                                                return \App\Models\Employee::all()->mapWithKeys(function ($employee) use ($dayOfWeek) {
                                                    $label = $employee->name;
                                                    if ($dayOfWeek && in_array($dayOfWeek, $employee->work_days ?? [])) {
                                                        $label = "✅ " . $label;
                                                    }
                                                    return [$employee->id => $label];
                                                })->toArray();
                                            })
                                            ->required(),
                                    ])
                                    ->columns(3)
                                    ->columnSpan(2),

                                Forms\Components\Section::make('الحسابات والمالية')
                                    ->description('حساب السعر والخصم والمدفوع والمتبقي للزيارة')
                                    ->schema([
                                        Forms\Components\TextInput::make('total_price')
                                            ->label('السعر الإجمالي قبل الخصم')
                                            ->numeric()
                                            ->readOnly()
                                            ->prefix('EGP')
                                            ->reactive(),
                                        Forms\Components\TextInput::make('deposit')
                                            ->label('مقدم الحجز (المدفوع مسبقاً)')
                                            ->numeric()
                                            ->readOnly()
                                            ->prefix('EGP'),
                                        Forms\Components\TextInput::make('discount_percentage')
                                            ->label('نسبة الخصم (%)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->default(0)
                                            ->reactive()
                                            ->suffix('%')
                                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                                $totalPrice = (float)($get('total_price') ?? 0);
                                                $discountPercent = (float)($state ?? 0);
                                                $finalPrice = $totalPrice - ($totalPrice * ($discountPercent / 100));
                                                $set('price', round($finalPrice, 2));

                                                $paid = (float)($get('paid') ?? 0);
                                                if ($paid > $finalPrice) {
                                                    $set('due_to', round($paid - $finalPrice, 2));
                                                    $set('due_from', 0);
                                                } else {
                                                    $set('due_from', round($finalPrice - $paid, 2));
                                                    $set('due_to', 0);
                                                }
                                            }),
                                        Forms\Components\TextInput::make('price')
                                            ->label('السعر النهائي (بعد الخصم)')
                                            ->numeric()
                                            ->readOnly()
                                            ->prefix('EGP')
                                            ->reactive(),
                                        Forms\Components\TextInput::make('paid')
                                            ->label('إجمالي المدفوع (مقدم + كاش/فيزا)')
                                            ->numeric()
                                            ->required()
                                            ->reactive()
                                            ->prefix('EGP')
                                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                                $finalPrice = (float)($get('price') ?? 0);
                                                $paid = (float)($state ?? 0);
                                                if ($paid > $finalPrice) {
                                                    $set('due_to', round($paid - $finalPrice, 2));
                                                    $set('due_from', 0);
                                                } else {
                                                    $set('due_from', round($finalPrice - $paid, 2));
                                                    $set('due_to', 0);
                                                }
                                            }),
                                        Forms\Components\TextInput::make('due_from')
                                            ->label('المتبقي على العميل (عجز)')
                                            ->numeric()
                                            ->readOnly()
                                            ->prefix('EGP')
                                            ->reactive(),
                                        Forms\Components\TextInput::make('due_to')
                                            ->label('الزيادة / مستحق للعميل')
                                            ->numeric()
                                            ->readOnly()
                                            ->prefix('EGP')
                                            ->reactive(),
                                        Forms\Components\TextInput::make('coupon_code')
                                            ->label('كود كوبون الخصم')
                                            ->placeholder('أدخل كود الكوبون إن وجد')
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                $code = trim($state);
                                                if (!$code) {
                                                    $set('coupon_discount', 0);
                                                    
                                                    // Recalculate totals
                                                    $total = (float)($get('total_price') ?? 0);
                                                    $discount = (float)($get('discount_percentage') ?? 0);
                                                    $final = $total - ($total * ($discount / 100));
                                                    $set('price', max(0, round($final, 2)));
                                                    
                                                    $paid = (float)($get('paid') ?? 0);
                                                    if ($paid > $final) {
                                                        $set('due_to', round($paid - $final, 2));
                                                        $set('due_from', 0);
                                                    } else {
                                                        $set('due_from', round($final - $paid, 2));
                                                        $set('due_to', 0);
                                                    }
                                                    return;
                                                }

                                                $coupon = \App\Models\Coupon::where('code', $code)->first();
                                                if (!$coupon) {
                                                    $coupon = \App\Models\Coupon::whereRaw('UPPER(code) = ?', [strtoupper($code)])->first();
                                                }

                                                if (!$coupon || !$coupon->is_active) {
                                                    $set('coupon_discount', 0);
                                                    
                                                    // Recalculate totals
                                                    $total = (float)($get('total_price') ?? 0);
                                                    $discount = (float)($get('discount_percentage') ?? 0);
                                                    $final = $total - ($total * ($discount / 100));
                                                    $set('price', max(0, round($final, 2)));
                                                    
                                                    $paid = (float)($get('paid') ?? 0);
                                                    if ($paid > $final) {
                                                        $set('due_to', round($paid - $final, 2));
                                                        $set('due_from', 0);
                                                    } else {
                                                        $set('due_from', round($final - $paid, 2));
                                                        $set('due_to', 0);
                                                    }
                                                    return;
                                                }

                                                $total = (float)($get('total_price') ?? 0);
                                                $discount = (float)($get('discount_percentage') ?? 0);
                                                $totalAfterPercentage = $total - ($total * ($discount / 100));

                                                $couponDiscount = $coupon->calculateDiscountFor($totalAfterPercentage);
                                                $set('coupon_discount', $couponDiscount);
                                                
                                                $final = max(0, $totalAfterPercentage - $couponDiscount);
                                                $set('price', round($final, 2));

                                                $paid = (float)($get('paid') ?? 0);
                                                if ($paid > $final) {
                                                    $set('due_to', round($paid - $final, 2));
                                                    $set('due_from', 0);
                                                } else {
                                                    $set('due_from', round($final - $paid, 2));
                                                    $set('due_to', 0);
                                                }
                                            }),
                                        Forms\Components\TextInput::make('coupon_discount')
                                            ->label('قيمة خصم الكوبون')
                                            ->numeric()
                                            ->readOnly()
                                            ->prefix('EGP')
                                            ->placeholder('0.00'),
                                    ])->columns(2)->columnSpan(2),
                            ])
                    ])
                    ->fillForm(function (Request $record): array {
                        $client = \App\Models\Client::where('phone', $record->phone)->first();
                        $discountPercentage = 0;
                        if ($client) {
                            $type = $client->type ?? 'D';
                            $discounts = [
                                "A+" => 100,
                                "A" => 50,
                                "B" => 25,
                                "C" => 10,
                                "D" => 0,
                            ];
                            $discountPercentage = $discounts[$type] ?? 0;
                        }
                        
                        $totalPrice = $record->total_price ?? 0;
                        $deposit = $record->deposit ?? 0;
                        $finalPrice = $totalPrice - ($totalPrice * ($discountPercentage / 100));
                        $paid = $deposit; // Default to deposit

                        // Prepare default sessions
                        $services = array_filter(explode(' + ', $record->service_type ?? ''));
                        if (empty($services)) {
                            $services = ['مساج'];
                        }

                        $basePrices = \App\Helpers\MassageHelper::calculateServiceBasePrices($record);
                        $sumBase = array_sum($basePrices);

                        $defaultSessions = [];
                        foreach ($services as $service) {
                            $serviceBase = $basePrices[($service === 'مساج' ? 'massage' : ($service === 'تقويم' ? 'cracking' : 'hijama'))] ?? 0;
                            $sessionPrice = $sumBase > 0 ? ($serviceBase / $sumBase) * $finalPrice : $finalPrice / count($services);

                            $defaultSessions[] = [
                                'type' => $service,
                                'employee_id' => null,
                                'price' => round($sessionPrice, 2),
                            ];
                        }
                        
                        return [
                            'name' => $record->name,
                            'phone' => $record->phone,
                            'gender' => $record->gender ?? 'male',
                            'visit_date' => $record->date,
                            'visit_hour' => $record->time,
                            'visit_complaint' => $record->description ?? 'حجز من الموقع: ' . ($record->service_type ?? 'مساج'),
                            'total_price' => $totalPrice,
                            'deposit' => $deposit,
                            'discount_percentage' => $discountPercentage,
                            'price' => $finalPrice,
                            'paid' => $paid,
                            'due_from' => max(0, $finalPrice - $paid),
                            'due_to' => max(0, $paid - $finalPrice),
                            'coupon_code' => $record->coupon_code,
                            'coupon_discount' => $record->coupon_discount,
                            'sessions' => $defaultSessions,
                        ];
                    })
                    ->action(function (Request $record, array $data): void {
                        // 1. Find or create Client
                        $client = \App\Models\Client::where('phone', $data['phone'])->first();
                        if (!$client) {
                            $client = \App\Models\Client::create([
                                'name' => $data['name'],
                                'phone' => $data['phone'],
                                'gender' => $data['gender'],
                                'code' => (string) \Carbon\Carbon::now()->timestamp,
                            ]);
                        } else {
                            $client->update([
                                'name' => $data['name'],
                                'gender' => $data['gender'],
                            ]);
                        }

                        // 2. Create the Visit
                        $visit = \App\Models\Visit::create([
                            'client_id' => $client->id,
                            'request_id' => $record->id,
                            'complaint' => $data['visit_complaint'],
                            'price' => $data['price'],
                            'date' => $data['visit_date'],
                            'hour' => (function() use ($data) {
                                $parts = explode(':', $data['visit_hour']);
                                return count($parts) >= 2 ? (float)$parts[0] + ((float)$parts[1] / 60) : (float)$data['visit_hour'];
                            })(),
                            'paid' => $data['paid'],
                            'due_from' => $data['due_from'] > 0 ? $data['due_from'] : null,
                            'due_to' => $data['due_to'] > 0 ? $data['due_to'] : null,
                            'discount_percentage' => $data['discount_percentage'],
                            'type' => 'وقائية',
                            'coupon_code' => $data['coupon_code'] ?? null,
                            'coupon_discount' => $data['coupon_discount'] ?? 0,
                        ]);

                        // 3. Create Session for each active service
                        $sessionsData = $data['sessions'] ?? [];
                        if (empty($sessionsData)) {
                            // Fallback if empty
                            $services = array_filter(explode(' + ', $record->service_type ?? ''));
                            if (empty($services)) $services = ['مساج'];
                            
                            $basePrices = \App\Helpers\MassageHelper::calculateServiceBasePrices($record);
                            $sumBase = array_sum($basePrices);

                            foreach ($services as $service) {
                                $sessionPrice = $basePrices[($service === 'مساج' ? 'massage' : ($service === 'تقويم' ? 'cracking' : 'hijama'))] ?? 0;

                                \App\Models\Session::create([
                                    'visit_id' => $visit->id,
                                    'employee_id' => null,
                                    'type' => $service === 'مساج' ? 'مساج وقائي (جزئي)' : ($service === 'تقويم' ? 'كيروبراكتيك وقائي' : '(كاس)حجامة تشريطية'),
                                    'price' => round($sessionPrice, 2),
                                    'time_or_num' => 1,
                                    'notes' => $data['visit_complaint'],
                                ]);
                            }
                        } else {
                            foreach ($sessionsData as $sessionItem) {
                                \App\Models\Session::create([
                                    'visit_id' => $visit->id,
                                    'employee_id' => $sessionItem['employee_id'],
                                    'type' => $sessionItem['type'] === 'مساج' ? 'مساج وقائي (جزئي)' : ($sessionItem['type'] === 'تقويم' ? 'كيروبراكتيك وقائي' : '(كاس)حجامة تشريطية'),
                                    'price' => $sessionItem['price'] ?? 0,
                                    'time_or_num' => 1,
                                    'notes' => $data['visit_complaint'],
                                ]);
                            }
                        }

                        // 4. Update Request status to confirmed
                        $record->update(['status' => 'confirmed']);

                        // Show success notification
                        \Filament\Notifications\Notification::make()
                            ->title('تم قبول الحجز وإنشاء زيارة بنجاح')
                            ->success()
                            ->send();
                    }),
                // Action::make('Decline')
                //     ->url(fn (Request $record): string => route('requests.decline', ['id'=>$record]))->color('danger'),
                Tables\Actions\Action::make('duplicate')
                ->label('Duplicate')
                ->icon('heroicon-o-document-duplicate')
                ->color('info')
                ->action(function ($record, $livewire) {

                    // 1) Duplicate Request (بدون الـ id)
                    $newRequest = $record->replicate();
                    $newRequest->date = now(); // optional: تغيير التاريخ
                    $newRequest->push();

                    // 3) Redirect to edit page of new request
                    return redirect()->to(RequestResource::getUrl('edit', ['record' => $newRequest->id]));
                }),
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
            'index' => Pages\ListRequests::route('/'),
            'create' => Pages\CreateRequest::route('/create'),
            'view' => Pages\ViewRequest::route('/{record}'),
            'edit' => Pages\EditRequest::route('/{record}/edit'),
        ];
    }

    public static function getPossibleTimesForGender($gender)
    {
        $times = [];
        $gender = $gender ?: 'male'; // fallback
        if ($gender === 'female') {
            // 2:30 PM to 9:00 PM -> 14:30 to 21:00 -> 870 to 1260
            for ($min = 870; $min <= 1260; $min += 30) {
                $hrs = floor($min / 60);
                $m = $min % 60;
                $key = sprintf('%02d:%02d', $hrs, $m);
                
                $displayHrs = $hrs % 12;
                if ($displayHrs === 0) {
                    $displayHrs = 12;
                }
                $amPm = ($hrs >= 12) ? 'PM' : 'AM';
                $label = sprintf('%02d:%02d %s', $displayHrs, $m, $amPm);
                
                $times[$key] = $label;
            }
        } else {
            // male: 9:00 AM to 1:00 AM next day -> 540 to 1500
            for ($min = 540; $min <= 1500; $min += 30) {
                $hrs = floor($min / 60);
                $m = $min % 60;
                $key = sprintf('%02d:%02d', $hrs, $m);
                
                $displayHrs = $hrs % 12;
                if ($displayHrs === 0) {
                    $displayHrs = 12;
                }
                $amPm = (($hrs % 24) >= 12) ? 'PM' : 'AM';
                
                $label = sprintf('%02d:%02d %s', $displayHrs, $m, $amPm);
                if ($hrs >= 24) {
                    $label .= ' (اليوم التالي)';
                }
                
                $times[$key] = $label;
            }
        }
        return $times;
    }

    public static function timeToMinutes($timeStr)
    {
        if (!$timeStr) return 0;
        
        $parts = explode(':', $timeStr);
        if (count($parts) < 2) return 0;
        $hrs = (int)$parts[0];
        $mins = (int)$parts[1];
        
        if ($hrs >= 1 && $hrs <= 8) {
            $hrs += 12;
        }
        
        return ($hrs * 60) + $mins;
    }

    public static function getOverlapValidationRule(callable $get, $record, bool $isRepeater = false)
    {
        return function (string $attribute, $value, \Closure $fail) use ($get, $record, $isRepeater) {
            $prefix = $isRepeater ? '../../' : '';
            $gender = $get($prefix . 'gender');
            $date = $get('date');
            if (!$gender || !$date || !$value) {
                return;
            }
            
            // 1. Validate that the appointment is at least 40 minutes from now (for new bookings)
            if (!$record) {
                $bookingDateTime = \Carbon\Carbon::parse($date . ' ' . $value);
                if ($bookingDateTime->lt(now()->addMinutes(40))) {
                    $fail("يجب أن يكون موعد الحجز بعد 40 دقيقة من الآن على الأقل.");
                    return;
                }
            }

            // 2. Parse time value to minutes
            $candidateStart = self::timeToMinutes($value);
            
            // Resolve form inputs using either parent path or local path
            $bookingType = $get($prefix . 'booking_type') ?: 'وقائية';
            $packages = $get($prefix . 'packages') ?: [];
            $massageRegions = $get($prefix . 'massage_regions') ?: [];
            $massageStyle = $get($prefix . 'massage_style') ?: 'intensive';
            $massageIntensity = $get($prefix . 'massage_intensity') ?: 'medium';
            $crackingType = $get($prefix . 'cracking_type') ?: 'none';
            $crackingRegions = $get($prefix . 'cracking_regions') ?: [];
            $hijamaType = $get($prefix . 'hijama_type') ?: 'none';
            $hijamaStyle = $get($prefix . 'hijama_style') ?: 'intensive';
            $hijamaRegions = $get($prefix . 'hijama_regions') ?: [];
            $isUrgent = $get($prefix . 'is_urgent') ?: false;
            
            $pricing = self::calculatePricing([
                'booking_type' => $bookingType,
                'packages' => $packages,
                'massage_regions' => $massageRegions,
                'massage_style' => $massageStyle,
                'massage_intensity' => $massageIntensity,
                'cracking_type' => $crackingType,
                'cracking_regions' => $crackingRegions,
                'hijama_type' => $hijamaType,
                'hijama_style' => $hijamaStyle,
                'hijama_regions' => $hijamaRegions,
                'is_urgent' => $isUrgent,
            ]);
            $duration = $pricing['total_duration'] ?: 30;
            $candidateEnd = $candidateStart + $duration;
            
            // Fetch capacity settings
            $maxFemaleBookings = (int)\App\Models\Setting::get('max_female_bookings', 1);
            $maxMaleBookings = (int)\App\Models\Setting::get('max_male_bookings', 3);
            $maxTotalBookings = (int)\App\Models\Setting::get('max_total_bookings', 3);

            // Fetch bookings (not gender filtered since we need to check total capacity)
            $existing = \App\Models\Request::whereDate('date', $date)
                ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                ->where(function($query) {
                    $query->whereIn('status', ['pending', 'confirmed'])
                          ->orWhereNull('status');
                })
                ->get(['time', 'total_duration', 'gender']);
                
            // Find overlapping intervals
            $overlapping = [];
            foreach ($existing as $booking) {
                if (!$booking->time) continue;
                $startMin = self::timeToMinutes($booking->time);
                $dur = $booking->total_duration ?: (int)$booking->duration ?: 30;
                $endMin = $startMin + $dur;
                
                if (max($candidateStart, $startMin) < min($candidateEnd, $endMin) - 5) {
                    $overlapping[] = [
                        'start' => $startMin,
                        'end' => $endMin,
                        'gender' => $booking->gender ?: 'male',
                    ];
                }
            }

            // Test points: candidate start and the start times of any overlapping slots
            $testPoints = [$candidateStart];
            foreach ($overlapping as $o) {
                if ($o['start'] > $candidateStart && $o['start'] < $candidateEnd) {
                    $testPoints[] = $o['start'];
                }
            }

            foreach ($testPoints as $t) {
                $femalesCount = ($gender === 'female') ? 1 : 0;
                $malesCount = ($gender === 'male') ? 1 : 0;

                foreach ($overlapping as $o) {
                    if ($t >= $o['start'] && $t < $o['end'] - 5) {
                        if ($o['gender'] === 'female') {
                            $femalesCount++;
                        } else {
                            $malesCount++;
                        }
                    }
                }

                if ($femalesCount > $maxFemaleBookings) {
                    $fail("عذراً، تم تجاوز الحد الأقصى لحجوزات السيدات المتزامنة في هذا الوقت (الحد الأقصى: $maxFemaleBookings).");
                    return;
                }
                if ($malesCount > $maxMaleBookings) {
                    $fail("عذراً، تم تجاوز الحد الأقصى لحجوزات الرجال المتزامنة في هذا الوقت (الحد الأقصى: $maxMaleBookings).");
                    return;
                }
                if (($femalesCount + $malesCount) > $maxTotalBookings) {
                    $fail("عذراً، تم تجاوز الحد الأقصى لإجمالي الحجوزات المتزامنة في هذا الوقت (الحد الأقصى: $maxTotalBookings).");
                    return;
                }
            }
        };
    }

    public static function parseDescription($description) {
        $data = [
            'packages' => [],
            'massage_style' => '',
            'massage_intensity' => '',
            'massage_regions' => [],
            'cracking_type' => 'none',
            'cracking_style' => '',
            'cracking_regions' => [],
            'hijama_type' => 'none',
            'hijama_style' => '',
            'hijama_regions' => [],
            'hijama_cups' => 0,
        ];

        if (!$description) return $data;

        // Parse Massage Intensity from either format
        if (str_contains($description, 'هارد (Hard)') || str_contains($description, 'الشدة: هارد')) {
            $data['massage_intensity'] = 'hard';
        } else {
            $data['massage_intensity'] = 'medium';
        }

        // Parse Massage (Support new format "المساج [الشدة: ...]" and old format "مساج (ميديم (Medium)): ...")
        if (preg_match('/المساج\s*\[([^\]]+)\]/', $description, $m)) {
            $inner = $m[1];
            $parts = explode(' | ', $inner);
            foreach ($parts as $part) {
                if (str_contains($part, 'الباقة:')) {
                    $pkgStr = str_replace('الباقة:', '', $part);
                    if (str_contains($pkgStr, 'جسم كامل مكثف')) $data['packages'][] = 'intensive';
                    if (str_contains($pkgStr, 'جسم كامل اقتصادي')) $data['packages'][] = 'economy';
                }
            }
            if (preg_match('/المناطق:\s*([\d,\s]+)/', $inner, $regMatches)) {
                $data['massage_regions'] = array_map('intval', explode(',', $regMatches[1]));
            }
        }

        // Parse Cracking
        $data['cracking_style'] = 'intensive'; // default fallback
        if (preg_match('/التقويم\s*\[([^\]]+)\]/', $description, $m)) {
            $inner = $m[1];
            $parts = explode(' | ', $inner);
            foreach ($parts as $part) {
                if (str_contains($part, 'النوع:')) {
                    $typeStr = trim(str_replace('النوع:', '', $part));
                    if ($typeStr === 'جسم كامل') $data['cracking_type'] = 'whole_body';
                    elseif ($typeStr === 'مناطق') $data['cracking_type'] = 'regions';
                }
                if (str_contains($part, 'الاستايل:')) {
                    $styleStr = trim(str_replace('الاستايل:', '', $part));
                    if ($styleStr === 'مكثف') $data['cracking_style'] = 'intensive';
                    elseif ($styleStr === 'اقتصادي') $data['cracking_style'] = 'economy';
                }
                if (str_contains($part, 'مناطق:')) {
                    $regionsStr = trim(str_replace('مناطق:', '', $part));
                    $data['cracking_regions'] = array_map('intval', explode(',', $regionsStr));
                }
            }
            // For backwards compatibility: if "النوع:" is missing but "جسم كامل" or "مناطق:" is present
            if (!str_contains($inner, 'النوع:')) {
                if (str_contains($inner, 'جسم كامل')) {
                    $data['cracking_type'] = 'whole_body';
                } elseif (str_contains($inner, 'مناطق:')) {
                    $data['cracking_type'] = 'regions';
                    $regionsStr = str_replace('مناطق:', '', $inner);
                    $data['cracking_regions'] = array_map('intval', explode(',', $regionsStr));
                }
            }
        }

        // Parse Hijama
        if (preg_match('/الحجامة\s*\[([^\]]+)\]/', $description, $m)) {
            $inner = $m[1];
            $parts = explode(' | ', $inner);
            foreach ($parts as $part) {
                if (str_contains($part, 'النوع:')) {
                    $typeStr = trim(str_replace('النوع:', '', $part));
                    if ($typeStr === 'خلفيات كامل') $data['hijama_type'] = 'whole_back';
                    elseif ($typeStr === 'أماميات كامل') $data['hijama_type'] = 'whole_front';
                    else $data['hijama_type'] = 'regions';
                }
                if (str_contains($part, 'الاستايل:')) {
                    $styleStr = trim(str_replace('الاستايل:', '', $part));
                    if ($styleStr === 'مكثف') $data['hijama_style'] = 'intensive';
                    elseif ($styleStr === 'اقتصادي') $data['hijama_style'] = 'economy';
                }
                if (str_contains($part, 'المناطق:')) {
                    $regionsStr = str_replace('المناطق:', '', $part);
                    $data['hijama_regions'] = array_map('intval', explode(',', $regionsStr));
                }
            }
        }

        return $data;
    }

    public static function calculatePricing($data) {
        $bookingType = $data['booking_type'] ?? 'وقائية';
        
        $packages = $data['packages'] ?? [];
        if (is_string($packages)) $packages = empty($packages) ? [] : array_map('trim', explode(',', $packages));
        elseif (!is_array($packages)) $packages = [];

        $massageRegions = $data['massage_regions'] ?? [];
        if (is_string($massageRegions)) $massageRegions = empty($massageRegions) ? [] : array_map('trim', explode(',', $massageRegions));
        elseif (!is_array($massageRegions)) $massageRegions = [];

        $massageStyle = $data['massage_style'] ?? 'intensive';
        $massageIntensity = $data['massage_intensity'] ?? 'medium';

        $crackingType = $data['cracking_type'] ?? 'none';
        $crackingStyle = $data['cracking_style'] ?? 'intensive';
        $crackingRegions = $data['cracking_regions'] ?? [];
        if (is_string($crackingRegions)) $crackingRegions = empty($crackingRegions) ? [] : array_map('trim', explode(',', $crackingRegions));
        elseif (!is_array($crackingRegions)) $crackingRegions = [];

        $hijamaType = $data['hijama_type'] ?? 'none';
        $hijamaStyle = $data['hijama_style'] ?? 'intensive';
        $hijamaRegions = $data['hijama_regions'] ?? [];
        if (is_string($hijamaRegions)) $hijamaRegions = empty($hijamaRegions) ? [] : array_map('trim', explode(',', $hijamaRegions));
        elseif (!is_array($hijamaRegions)) $hijamaRegions = [];

        $regionRepetitionsIntensive = [
            1 => 2, 3 => 2, 4 => 3, 5 => 2, 7 => 2, 8 => 3, 9 => 1, 10 => 1,
            11 => 4, 12 => 4, 13 => 1, 14 => 1, 15 => 1, 16 => 1, 17 => 2, 18 => 4,
            19 => 2, 20 => 2, 25 => 2, 27 => 2, 28 => 2, 30 => 2, 31 => 1,
            32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1, 37 => 1
        ];

        $regionRepetitionsEconomy = [
            1 => 2, 3 => 2, 5 => 2, 7 => 2, 9 => 1, 10 => 1, 11 => 2, 12 => 2,
            13 => 1, 14 => 1, 15 => 1, 16 => 1, 17 => 1, 18 => 2, 19 => 2,
            20 => 1, 21 => 1, 22 => 1, 23 => 1, 24 => 1, 25 => 2, 27 => 2,
            28 => 2, 30 => 2, 31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1,
            36 => 1, 37 => 1
        ];

        $style = 'economy';
        if (in_array('intensive', $packages)) {
            $style = 'intensive';
        } elseif (in_array('economy', $packages)) {
            $style = 'economy';
        } else {
            $style = $massageStyle;
        }

        $repsMap = ($style === 'intensive') ? $regionRepetitionsIntensive : $regionRepetitionsEconomy;
        $totalRepetitions = 0;
        foreach ($massageRegions as $rNum) {
            if (isset($repsMap[$rNum])) {
                $totalRepetitions += $repsMap[$rNum];
            }
        }

        // Massage
        $massagePrice = 0;
        $massageDuration = 0;
        $massageActive = ($bookingType === 'وقائية') && (!empty($packages) || !empty($massageRegions));
        
        if ($massageActive) {
            $isHard = ($massageIntensity === 'hard');
            if (in_array('intensive', $packages)) {
                $massageDuration = 95.4 + ($totalRepetitions * 1.8);
                $massagePrice = $isHard ? 1600.00 + ($totalRepetitions * 17) : 1200.00 + ($totalRepetitions * 13);
            } elseif (in_array('economy', $packages)) {
                $massageDuration = 57.4 + ($totalRepetitions * 1.4);
                $massagePrice = $isHard ? 950.00 + ($totalRepetitions * 17) : 725.00 + ($totalRepetitions * 13);
            } else {
                $massageDuration = $totalRepetitions * ($style === 'intensive' ? 1.8 : 1.4);
                $massagePrice = $isHard ? ($totalRepetitions * 17) : ($totalRepetitions * 13);
            }
        }

        // Cracking
        $crackingPrice = 0;
        $crackingDuration = 0;
        $crackingActive = ($bookingType === 'وقائية') && ($crackingType !== 'none');
        if ($crackingActive) {
            $crackingRepsMap = \App\Helpers\CrackingHelper::getRegionRepetitions($crackingStyle);
            $crackingCountMap = \App\Helpers\CrackingHelper::getRegionTechniquesCount($crackingStyle);

            $totalCrackingReps = 0;
            $totalCrackingCount = 0;
            foreach ($crackingRegions as $rNum) {
                $rNum = (int)$rNum;
                if (isset($crackingRepsMap[$rNum])) {
                    $totalCrackingReps += $crackingRepsMap[$rNum];
                }
                if (isset($crackingCountMap[$rNum])) {
                    $totalCrackingCount += $crackingCountMap[$rNum];
                }
            }

            if ($crackingType === 'whole_body') {
                if ($crackingStyle === 'intensive') {
                    $crackingPrice = 600.00;
                    $crackingDuration = 16.1; // 161 reps * 0.1 min
                } else {
                    $crackingPrice = 450.00;
                    $crackingDuration = 13.0; // 130 reps * 0.1 min
                }
            } else {
                $crackingPrice = $totalCrackingCount * 12.00;
                $crackingDuration = $totalCrackingReps * 0.1;
            }
        }

        // Hijama
        $hijamaPrice = 0;
        $hijamaDuration = 0;
        $hijamaActive = ($bookingType === 'وقائية') && ($hijamaType !== 'none');
        if ($hijamaActive) {
            $resolvedHijamaRegions = [];
            if ($hijamaType === 'whole_back') {
                $resolvedHijamaRegions = [1, 3, 5, 7, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 37];
            } elseif ($hijamaType === 'whole_front') {
                $resolvedHijamaRegions = [19, 22, 23, 24, 25, 27, 28, 30, 31, 32, 33, 34, 35, 36];
            } else {
                $resolvedHijamaRegions = array_map('intval', $hijamaRegions);
            }

            $totalCups = 0;
            foreach ($resolvedHijamaRegions as $rNum) {
                $regionCups = 0;
                $reps = $rNum;
                if ($reps === 1) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 2; }
                elseif ($reps === 2) { $regionCups = $hijamaStyle === 'intensive' ? 1 : 1; }
                elseif ($reps === 3) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 4) { $regionCups = $hijamaStyle === 'intensive' ? 4 : 2; }
                elseif ($reps === 5) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 2; }
                elseif ($reps === 6) { $regionCups = $hijamaStyle === 'intensive' ? 1 : 1; }
                elseif ($reps === 7) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 8) { $regionCups = $hijamaStyle === 'intensive' ? 4 : 2; }
                elseif ($reps === 9) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 10) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 11) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 12) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 13) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 14) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 15) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 16) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 17) { $regionCups = $hijamaStyle === 'intensive' ? 1 : 1; }
                elseif ($reps === 18) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 19) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 20) { $regionCups = $hijamaStyle === 'intensive' ? 1 : 1; }
                elseif ($reps === 21) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 22) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 23) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 24) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 25) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 26) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 27) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 28) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 29) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 30) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 31) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 32) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 33) { $regionCups = $hijamaStyle === 'intensive' ? 1 : 1; }
                elseif ($reps === 34) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 35) { $regionCups = $hijamaStyle === 'intensive' ? 1 : 1; }
                elseif ($reps === 36) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 37) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 38) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 39) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                $totalCups += $regionCups;
            }

            $hijamaDuration = 10 + $totalCups;

            $cupPrice = 45;
            if ($totalCups > 20) {
                $cupPrice = 35;
            } elseif ($totalCups >= 16) {
                $cupPrice = 37;
            } elseif ($totalCups >= 11) {
                $cupPrice = 40;
            } else {
                $cupPrice = 45;
            }

            $hijamaPrice = $totalCups * $cupPrice;
        }

        $totalPrice = $massagePrice + $crackingPrice + $hijamaPrice;
        if ($data['is_urgent'] ?? false) {
            $urgentFee = (int)\App\Models\Setting::get('urgent_booking_fee', 200);
            $totalPrice += $urgentFee;
        }
        $totalDuration = $massageDuration + $crackingDuration + $hijamaDuration;

        return [
            'total_price' => $totalPrice,
            'total_duration' => $totalDuration,
            'deposit' => ceil($totalPrice * 0.40)
        ];
    }

    public static function buildDescription($bookingType, $packages, $massageRegions, $massageStyle, $massageIntensity, $crackingType, $crackingRegions, $hijamaType, $hijamaStyle, $hijamaRegions, $regionRepetitions, $crackingStyle = 'intensive') {
        if (is_string($packages)) $packages = empty($packages) ? [] : array_map('trim', explode(',', $packages));
        elseif (!is_array($packages)) $packages = [];

        if (is_string($massageRegions)) $massageRegions = empty($massageRegions) ? [] : array_map('trim', explode(',', $massageRegions));
        elseif (!is_array($massageRegions)) $massageRegions = [];

        if (is_string($crackingRegions)) $crackingRegions = empty($crackingRegions) ? [] : array_map('trim', explode(',', $crackingRegions));
        elseif (!is_array($crackingRegions)) $crackingRegions = [];

        if (is_string($hijamaRegions)) $hijamaRegions = empty($hijamaRegions) ? [] : array_map('trim', explode(',', $hijamaRegions));
        elseif (!is_array($hijamaRegions)) $hijamaRegions = [];

        $activeServices = [];
        $descriptionParts = [
            "نوع الجلسة: " . $bookingType
        ];

        // Massage
        $massageActive = !empty($packages) || !empty($massageRegions);
        if ($massageActive) {
            $activeServices[] = 'مساج';
            $packageNames = [];
            if (in_array('intensive', $packages)) $packageNames[] = 'جسم كامل مكثف';
            if (in_array('economy', $packages)) $packageNames[] = 'جسم كامل اقتصادي';

            $intensityStr = ($massageIntensity === 'hard') ? 'هارد' : 'ميديم';
            $massageDesc = "المساج [الشدة: {$intensityStr}";
            if (!empty($packageNames)) {
                $massageDesc .= " | الباقة: " . implode(' + ', $packageNames);
            } else {
                $massageDesc .= " | مناطق مخصصة فقط (" . ($massageStyle === 'intensive' ? 'مكثف' : 'اقتصادي') . ")";
            }
            if (!empty($massageRegions)) {
                $massageDesc .= " | المناطق: " . implode(', ', $massageRegions);
            }
            $massageDesc .= "]";
            $descriptionParts[] = $massageDesc;
        }

        // Cracking
        $crackingActive = $crackingType !== 'none';
        if ($crackingActive) {
            $activeServices[] = 'تقويم';
            $crackingDesc = "التقويم [النوع: " . ($crackingType === 'whole_body' ? 'جسم كامل' : 'مناطق') . " | الاستايل: " . ($crackingStyle === 'intensive' ? 'مكثف' : 'اقتصادي');
            if ($crackingType === 'regions' && !empty($crackingRegions)) {
                $crackingDesc .= " | مناطق: " . implode(', ', $crackingRegions);
            }
            $crackingDesc .= "]";
            $descriptionParts[] = $crackingDesc;
        }

        // Hijama
        $hijamaActive = $hijamaType !== 'none';
        if ($hijamaActive) {
            $activeServices[] = 'حجامة';
            $resolvedHijamaRegions = [];
            if ($hijamaType === 'whole_back') {
                $resolvedHijamaRegions = [1, 3, 5, 7, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 37];
            } elseif ($hijamaType === 'whole_front') {
                $resolvedHijamaRegions = [19, 22, 23, 24, 25, 27, 28, 30, 31, 32, 33, 34, 35, 36];
            } else {
                $resolvedHijamaRegions = array_map('intval', $hijamaRegions);
            }

            $totalCups = 0;
            foreach ($resolvedHijamaRegions as $rNum) {
                $regionCups = 0;
                $reps = $rNum;
                if ($reps === 1) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 2; }
                elseif ($reps === 2) { $regionCups = $hijamaStyle === 'intensive' ? 1 : 1; }
                elseif ($reps === 3) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 4) { $regionCups = $hijamaStyle === 'intensive' ? 4 : 2; }
                elseif ($reps === 5) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 2; }
                elseif ($reps === 6) { $regionCups = $hijamaStyle === 'intensive' ? 1 : 1; }
                elseif ($reps === 7) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 8) { $regionCups = $hijamaStyle === 'intensive' ? 4 : 2; }
                elseif ($reps === 9) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 10) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 11) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 12) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 13) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 14) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 15) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 16) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 17) { $regionCups = $hijamaStyle === 'intensive' ? 1 : 1; }
                elseif ($reps === 18) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 19) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 20) { $regionCups = $hijamaStyle === 'intensive' ? 1 : 1; }
                elseif ($reps === 21) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 22) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 23) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 24) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 25) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 26) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 27) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 28) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 29) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 30) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 31) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 32) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 33) { $regionCups = $hijamaStyle === 'intensive' ? 1 : 1; }
                elseif ($reps === 34) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 35) { $regionCups = $hijamaStyle === 'intensive' ? 1 : 1; }
                elseif ($reps === 36) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 37) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 38) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 39) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                $totalCups += $regionCups;
            }

            $hijamaTypeLabel = 'مناطق مخصصة';
            if ($hijamaType === 'whole_back') $hijamaTypeLabel = 'خلفيات كامل';
            if ($hijamaType === 'whole_front') $hijamaTypeLabel = 'أماميات كامل';

            $hijamaDesc = "الحجامة [النوع: {$hijamaTypeLabel} | الاستايل: " . ($hijamaStyle === 'intensive' ? 'مكثف' : 'اقتصادي') . " | الكاسات: {$totalCups} كاس | المناطق: " . implode(', ', $resolvedHijamaRegions) . "]";
            $descriptionParts[] = $hijamaDesc;
        }

        $serviceType = implode(' + ', $activeServices);
        if (empty($serviceType)) $serviceType = 'مساج';

        return [
            'service_type' => $serviceType,
            'description' => implode(' | ', $descriptionParts)
        ];
    }

    public static function updateTotals(callable $set, callable $get)
    {
        $bookingType = $get('booking_type') ?: 'وقائية';
        
        if ($bookingType !== 'وقائية') {
            $set('packages', []);
            $set('massage_regions', []);
            $set('massage_style', null);
            $set('massage_intensity', null);
            $set('cracking_type', 'none');
            $set('cracking_regions', []);
            $set('hijama_type', 'none');
            $set('hijama_style', null);
            $set('hijama_regions', []);
            
            $set('total_price', 0);
            $set('total_duration', 0);
            $set('service_type', '');
            $set('description', 'نوع الجلسة: ' . $bookingType);
            return;
        }

        $packages = $get('packages') ?: [];
        $massageRegions = $get('massage_regions') ?: [];
        $massageStyle = $get('massage_style') ?: 'intensive';
        $massageIntensity = $get('massage_intensity') ?: 'medium';
        $crackingType = $get('cracking_type') ?: 'none';
        $crackingStyle = $get('cracking_style') ?: 'intensive';
        $crackingRegions = $get('cracking_regions') ?: [];
        $hijamaType = $get('hijama_type') ?: 'none';
        $hijamaStyle = $get('hijama_style') ?: 'intensive';
        $hijamaRegions = $get('hijama_regions') ?: [];

        // Apply rules on regions when services/types change:
        if ($crackingType === 'none' || $crackingType === 'whole_body') {
            if (!empty($crackingRegions)) {
                $set('cracking_regions', []);
                $crackingRegions = [];
            }
        }

        if ($hijamaType === 'none') {
            if (!empty($hijamaRegions)) {
                $set('hijama_regions', []);
                $hijamaRegions = [];
            }
        } elseif ($hijamaType === 'whole_back') {
            $preset = [1, 3, 5, 7, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 37];
            if ($hijamaRegions !== $preset) {
                $set('hijama_regions', $preset);
                $hijamaRegions = $preset;
            }
        } elseif ($hijamaType === 'whole_front') {
            $preset = [19, 22, 23, 24, 25, 27, 28, 30, 31, 32, 33, 34, 35, 36];
            if ($hijamaRegions !== $preset) {
                $set('hijama_regions', $preset);
                $hijamaRegions = $preset;
            }
        }

        // Calculate pricing
        $pricing = self::calculatePricing([
            'booking_type' => $bookingType,
            'packages' => $packages,
            'massage_regions' => $massageRegions,
            'massage_style' => $massageStyle,
            'massage_intensity' => $massageIntensity,
            'cracking_type' => $crackingType,
            'cracking_style' => $crackingStyle,
            'cracking_regions' => $crackingRegions,
            'hijama_type' => $hijamaType,
            'hijama_style' => $hijamaStyle,
            'hijama_regions' => $hijamaRegions,
            'is_urgent' => $get('is_urgent') ?: false,
        ]);

        $set('total_price', $pricing['total_price']);
        $set('total_duration', $pricing['total_duration']);

        // Rebuild description and service_type
        $regionRepetitions = [
            1 => 2, 3 => 2, 4 => 3, 5 => 2, 7 => 2, 8 => 3, 9 => 1, 10 => 1,
            11 => 4, 12 => 4, 13 => 1, 14 => 1, 15 => 1, 16 => 1, 17 => 2, 18 => 4,
            19 => 2, 20 => 2, 25 => 2, 27 => 2, 28 => 2, 30 => 2, 31 => 1,
            32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1, 37 => 1
        ];
        $built = self::buildDescription(
            $bookingType,
            $packages,
            $massageRegions,
            $massageStyle,
            $massageIntensity,
            $crackingType,
            $crackingRegions,
            $hijamaType,
            $hijamaStyle,
            $hijamaRegions,
            $regionRepetitions,
            $crackingStyle
        );

        $set('service_type', $built['service_type']);
        $set('description', $built['description']);
    }
}

