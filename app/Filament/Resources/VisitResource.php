<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitResource\Pages;
use App\Filament\Resources\VisitResource\RelationManagers;
use App\Models\Visit;
use App\Models\Employee;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('request_id'),
                
                // Section 1: Basic Info (Only name, gender, date, hour)
                Forms\Components\Section::make('بيانات العميل والزيارة الأساسية')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->relationship(name: 'client', titleAttribute: 'name')
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $client = \App\Models\Client::find($state);
                                if ($client) {
                                    $set('gender', $client->gender);
                                    $discount = [
                                        "A+" => 100,
                                        "A" => 50,
                                        "B" => 25,
                                        "C" => 10,
                                        "D" => 0,
                                    ];
                                    $set('discount_percentage', (int)($discount[$client->type ?? 'D']));
                                    self::updateVisitTotals($set, $get);
                                }
                            }),
                        
                        Forms\Components\Select::make('gender')
                            ->label('الجنس')
                            ->options([
                                'male' => 'ذكر',
                                'female' => 'أنثى',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateHydrated(function ($state, callable $set, ?Visit $record) {
                                if ($record && $record->client) {
                                    $set('gender', $record->client->gender);
                                }
                            }),

                        Forms\Components\DatePicker::make('date')
                            ->required()
                            ->reactive(),

                        Forms\Components\TextInput::make('hour')
                            ->required()
                            ->numeric()
                            ->step(0.5)
                            ->minValue(1)
                            ->maxValue(24),
                    ])->columns(2),

                // Section 2: Visit Details (Allow editing complaint & notes)
                Forms\Components\Section::make('تفاصيل الزيارة')
                    ->schema([
                        Forms\Components\TextInput::make('complaint')
                            ->label('الشكوى / تفاصيل الخدمة')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('notes')
                            ->label('الملاحظات')
                            ->rows(3),
                    ])->columns(1),

                // Section 3: Booking Prices & Techniques (Read-only dynamic section)
                Forms\Components\Section::make('أسعار خدمات الحجز والتكنيكات')
                    ->visible(fn (callable $get, ?Visit $record) => $record?->request_id !== null || $get('request_id') !== null)
                    ->schema([
                        Forms\Components\Placeholder::make('booking_prices_techniques')
                            ->label('')
                            ->content(function (callable $get, ?Visit $record) {
                                $requestId = $record?->request_id ?? $get('request_id');
                                if (!$requestId) return 'لا توجد تفاصيل حجز.';
                                $request = \App\Models\Request::find($requestId);
                                if (!$request) return 'لا توجد تفاصيل حجز.';

                                $basePrices = \App\Helpers\MassageHelper::calculateServiceBasePrices($request);
                                $massagePrice = $basePrices['massage'] ?? 0;
                                $crackingPrice = $basePrices['cracking'] ?? 0;
                                $hijamaPrice = $basePrices['hijama'] ?? 0;
                                
                                $urgentFee = $request->is_urgent ? (int)\App\Models\Setting::get('urgent_booking_fee', 200) : 0;
                                $totalBase = array_sum($basePrices) + $urgentFee;

                                $discount = (float)($get('discount_percentage') ?? 0);
                                $finalPrice = $totalBase - ($totalBase * ($discount / 100));

                                // Render pricing sections
                                $urgentFeeBox = '';
                                if ($urgentFee > 0) {
                                    $urgentFeeBox = "
                                        <div style='background: #1e293b; padding: 1rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); text-align: center;'>
                                            <div style='color: #ff8c00; font-size: 0.85rem; margin-bottom: 0.25rem;'>🔥 رسوم مستعجل</div>
                                            <div style='color: #ff9d42; font-size: 1.25rem; font-weight: bold;'>{$urgentFee} EGP</div>
                                        </div>
                                    ";
                                }

                                $pricingHtml = "
                                    <div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;'>
                                        <div style='background: #1e293b; padding: 1rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); text-align: center;'>
                                            <div style='color: #94a3b8; font-size: 0.85rem; margin-bottom: 0.25rem;'>💆‍♂️ سعر المساج</div>
                                            <div style='color: #ff9d42; font-size: 1.25rem; font-weight: bold;'>{$massagePrice} EGP</div>
                                        </div>
                                        <div style='background: #1e293b; padding: 1rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); text-align: center;'>
                                            <div style='color: #94a3b8; font-size: 0.85rem; margin-bottom: 0.25rem;'>⚡ سعر التقويم</div>
                                            <div style='color: #a855f7; font-size: 1.25rem; font-weight: bold;'>{$crackingPrice} EGP</div>
                                        </div>
                                        <div style='background: #1e293b; padding: 1rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); text-align: center;'>
                                            <div style='color: #94a3b8; font-size: 0.85rem; margin-bottom: 0.25rem;'>🏺 سعر الحجامة</div>
                                            <div style='color: #22c55e; font-size: 1.25rem; font-weight: bold;'>{$hijamaPrice} EGP</div>
                                        </div>
                                        {$urgentFeeBox}
                                        <div style='background: #0f172a; padding: 1rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); text-align: center;'>
                                            <div style='color: #e2e8f0; font-size: 0.85rem; margin-bottom: 0.25rem;'>💰 الإجمالي بعد الخصم</div>
                                            <div style='color: #38bdf8; font-size: 1.25rem; font-weight: bold;'>{$finalPrice} EGP</div>
                                        </div>
                                    </div>
                                ";

                                $techniquesHtml = '';
                                if ($massagePrice > 0) {
                                    $techniquesTable = \App\Helpers\MassageHelper::renderTechniquesTable($request);
                                    $techniquesHtml = $techniquesTable instanceof \Illuminate\Contracts\Support\Htmlable
                                        ? $techniquesTable->toHtml()
                                        : $techniquesTable;
                                }

                                return new \Illuminate\Support\HtmlString("
                                    <div style='direction: rtl; text-align: right;'>
                                        {$pricingHtml}
                                        {$techniquesHtml}
                                    </div>
                                ");
                            })
                    ])
                    ->columnSpanFull(),

                // Virtual Form Sections for editing Request details:
                Forms\Components\Section::make('💆‍♂️ المساج (Massage)')
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn (callable $get) => $get('request_id') !== null)
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
                                self::updateSessionRepeaterPrices($set, $get);
                            })
                            ->afterStateHydrated(function ($state, callable $set, ?\Illuminate\Database\Eloquent\Model $record) {
                                if ($record && $record->request) {
                                    $set('packages', $record->request->packages);
                                }
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
                                self::updateSessionRepeaterPrices($set, $get);
                            })
                            ->afterStateHydrated(function ($state, callable $set, ?\Illuminate\Database\Eloquent\Model $record) {
                                if ($record && $record->request) {
                                    $regions = \App\Models\RequestRegion::where('request_id', $record->request_id)
                                        ->pluck('region_number')
                                        ->toArray();
                                    $set('massage_regions', $regions);
                                }
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
                                self::updateSessionRepeaterPrices($set, $get);
                            })
                            ->afterStateHydrated(function ($state, callable $set, ?\Illuminate\Database\Eloquent\Model $record) {
                                if ($record && $record->request) {
                                    $parsed = \App\Filament\Resources\RequestResource::parseDescription($record->request->description);
                                    $set('massage_style', $parsed['massage_style'] ?: 'intensive');
                                }
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
                                self::updateSessionRepeaterPrices($set, $get);
                            })
                            ->afterStateHydrated(function ($state, callable $set, ?\Illuminate\Database\Eloquent\Model $record) {
                                if ($record && $record->request) {
                                    $parsed = \App\Filament\Resources\RequestResource::parseDescription($record->request->description);
                                    $set('massage_intensity', $parsed['massage_intensity'] ?: 'medium');
                                }
                            }),
                    ]),

                Forms\Components\Section::make('⚡ تقويم عمود فقري (Cracking)')
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn (callable $get) => $get('request_id') !== null)
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
                                self::updateSessionRepeaterPrices($set, $get);
                            })
                            ->afterStateHydrated(function ($state, callable $set, ?\Illuminate\Database\Eloquent\Model $record) {
                                if ($record && $record->request) {
                                    $parsed = \App\Filament\Resources\RequestResource::parseDescription($record->request->description);
                                    $set('cracking_type', $parsed['cracking_type'] ?: 'none');
                                }
                            }),
                        Forms\Components\Select::make('cracking_regions')
                            ->label('مناطق التقويم المحددة')
                            ->multiple()
                            ->options([
                                1 => 'منطقة 1',
                                2 => 'منطقة 2',
                                3 => 'منطقة 3',
                            ])
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                if (!empty($state)) {
                                    $set('cracking_type', 'regions');
                                }
                                self::updateSessionRepeaterPrices($set, $get);
                            })
                            ->afterStateHydrated(function ($state, callable $set, ?\Illuminate\Database\Eloquent\Model $record) {
                                if ($record && $record->request) {
                                    $regions = \App\Models\RequestRegion::where('request_id', $record->request_id)
                                        ->pluck('region_number')
                                        ->toArray();
                                    $set('cracking_regions', $regions);
                                }
                            }),
                    ]),

                Forms\Components\Section::make('🩸 الحجامة (Hijama / Cupping)')
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn (callable $get) => $get('request_id') !== null)
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
                                self::updateSessionRepeaterPrices($set, $get);
                            })
                            ->afterStateHydrated(function ($state, callable $set, ?\Illuminate\Database\Eloquent\Model $record) {
                                if ($record && $record->request) {
                                    $parsed = \App\Filament\Resources\RequestResource::parseDescription($record->request->description);
                                    $set('hijama_type', $parsed['hijama_type'] ?: 'none');
                                }
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
                                self::updateSessionRepeaterPrices($set, $get);
                            })
                            ->afterStateHydrated(function ($state, callable $set, ?\Illuminate\Database\Eloquent\Model $record) {
                                if ($record && $record->request) {
                                    $parsed = \App\Filament\Resources\RequestResource::parseDescription($record->request->description);
                                    $set('hijama_style', $parsed['hijama_style'] ?: 'intensive');
                                }
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
                                self::updateSessionRepeaterPrices($set, $get);
                            })
                            ->afterStateHydrated(function ($state, callable $set, ?\Illuminate\Database\Eloquent\Model $record) {
                                if ($record && $record->request) {
                                    $regions = \App\Models\RequestRegion::where('request_id', $record->request_id)
                                        ->pluck('region_number')
                                        ->toArray();
                                    $set('hijama_regions', $regions);
                                }
                            }),
                    ]),

                // Section 5: Body Maps
                Forms\Components\Grid::make(2)
                    ->visible(fn (callable $get) => $get('request_id') !== null)
                    ->schema([
                        Forms\Components\Section::make('خريطة المساج (Massage Chart)')
                            ->visible(function (callable $get) {
                                $tempRecord = (object)[
                                    'booking_type' => 'وقائية',
                                    'packages' => $get('packages') ?? [],
                                    'massage_regions' => $get('massage_regions') ?? [],
                                    'massage_style' => $get('massage_style') ?? 'intensive',
                                    'massage_intensity' => $get('massage_intensity') ?? 'medium',
                                    'cracking_type' => $get('cracking_type') ?? 'none',
                                    'cracking_regions' => $get('cracking_regions') ?? [],
                                    'hijama_type' => $get('hijama_type') ?? 'none',
                                    'hijama_style' => $get('hijama_style') ?? 'intensive',
                                    'hijama_regions' => $get('hijama_regions') ?? [],
                                ];
                                $basePrices = \App\Helpers\MassageHelper::calculateServiceBasePrices($tempRecord);
                                return ($basePrices['massage'] ?? 0) > 0;
                            })
                            ->schema([
                                Forms\Components\Placeholder::make('massage_chart_img')
                                    ->label('')
                                    ->content(new \Illuminate\Support\HtmlString('
                                        <div style="text-align: center;">
                                            <img src="/images/body.jpg" alt="Massage Chart" style="max-width: 600px; width: 100%; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);" />
                                        </div>
                                    '))
                            ])
                            ->columnSpan(fn (callable $get) => (($get('cracking_type') ?? 'none') === 'none') ? 2 : 1)
                            ->collapsible()
                            ->collapsed(false),

                        Forms\Components\Section::make('خريطة التقويم (Cracking Chart)')
                            ->visible(function (callable $get) {
                                $tempRecord = (object)[
                                    'booking_type' => 'وقائية',
                                    'packages' => $get('packages') ?? [],
                                    'massage_regions' => $get('massage_regions') ?? [],
                                    'massage_style' => $get('massage_style') ?? 'intensive',
                                    'massage_intensity' => $get('massage_intensity') ?? 'medium',
                                    'cracking_type' => $get('cracking_type') ?? 'none',
                                    'cracking_regions' => $get('cracking_regions') ?? [],
                                    'hijama_type' => $get('hijama_type') ?? 'none',
                                    'hijama_style' => $get('hijama_style') ?? 'intensive',
                                    'hijama_regions' => $get('hijama_regions') ?? [],
                                ];
                                $basePrices = \App\Helpers\MassageHelper::calculateServiceBasePrices($tempRecord);
                                return ($basePrices['cracking'] ?? 0) > 0;
                            })
                            ->schema([
                                Forms\Components\Placeholder::make('cracking_chart_img')
                                    ->label('')
                                    ->content(new \Illuminate\Support\HtmlString('
                                        <div style="text-align: center;">
                                            <img src="/images/cracking.png" alt="Cracking Chart" style="max-width: 100%; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);" />
                                        </div>
                                    '))
                            ])
                            ->columnSpan(1)
                            ->collapsible()
                            ->collapsed(false),
                    ])
                    ->columnSpanFull(),

                // Section 6: Sessions Repeater
                Forms\Components\Section::make('جلسات الزيارة والمختصين (Sessions & Specialists)')
                    ->schema([
                        Forms\Components\Repeater::make('Sessions')
                            ->relationship('sessions')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                self::updateVisitTotals($set, $get);
                            })
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('نوع الخدمة')
                                    ->options([
                                        'مساج' => 'مساج (Massage)',
                                        'تقويم' => 'تقويم (Cracking)',
                                        'حجامة' => 'حجامة (Hijama)',
                                        // Database values
                                        'مساج وقائي (جزئي)' => 'مساج (Massage)',
                                        'كيروبراكتيك وقائي' => 'تقويم (Cracking)',
                                        '(كاس)حجامة تشريطية' => 'حجامة (Hijama)',
                                        
                                        // Subtypes for compatibility
                                        "كيروبراكتيك علاجي" => "كيروبراكتيك علاجي",
                                        "كيروبراكتيك علاجي مكثف" => "كيروبراكتيك علاجي مكثف",
                                        "كيروبراكتيك تقويمي" => "كيروبراكتيك تقويمي",
                                        "كيروبراكتيك تقويمي مكثف" => "كيروبراكتيك تقويمي مكثف",
                                        "(15 min)شيروث" => "(15 min)شيروث",
                                        "(30 min)تصريف ليمفاوي" => "(30 min)تصريف ليمفاوي",
                                        "ستيل ستون" => "ستيل ستون",
                                        "(15 min)تمارين تقوية" => "(15 min)تمارين تقوية",
                                        "(15 min)توك سين" => "(15 min)توك سين",
                                        "(1)ابرة تنشيطية" => "(1)ابرة تنشيطية",
                                        "(1)ابرة جافة" => "(1)ابرة جافة",
                                        "حجامة سليكونية" => "حجامة سليكونية",
                                        "حجامة نارية" => "حجامة نارية",
                                        "حجامة خشبية" => "حجامة خشبية",
                                        "حجامة باكيدج اقتصادي ٦ كاسات" => "حجامة باكيدج اقتصادي ٦ كاسات",
                                        "حجامة باكيدج متوسط ١٠ كاسات" => "حجامة باكيدج متوسط ١٠ كاسات",
                                        "حجامة باكيدج مكثف ٢٠ كاس" => "حجامة باكيدج مكثف ٢٠ كاس",
                                        "(30 min)تنشيط عضلي" => "(30 min)تنشيط عضلي",
                                        "مساج علاجي (جزئي)" => "مساج علاجي (جزئي)",
                                        "مساج علاجي مكثف (جزئي)" => "مساج علاجي مكثف (جزئي)",
                                        "مساج تقويمي (جزئي)" => "مساج تقويمي (جزئي)",
                                        "مساج تقويمي مكثف (جزئي)" => "مساج تقويمي مكثف (جزئي)",
                                        "مساج جسم كامل وقائي اقتصادي" => "مساج جسم كامل وقائي اقتصادي",
                                        "مساج جسم كامل علاجي" => "مساج جسم كامل علاجي",
                                        "مساج جسم كامل علاجي مكثف" => "مساج جسم كامل علاجي مكثف",
                                        "فحص رياضي" => "فحص رياضي",
                                    ])
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $type = $get('type');
                                        $timeOrNum = 1;
                                        $price = self::calculateSessionPrice($get, $type, $timeOrNum);
                                        $set('price', $price);
                                        self::updateVisitTotals($set, $get);
                                    }),

                                Forms\Components\Select::make('employee_id')
                                    ->label('المختص المعالج')
                                    ->relationship('employee', 'name')
                                    ->reactive()
                                    ->options(function (callable $get) {
                                        $parentDate = $get('../../date');
                                        $dayOfWeek = $parentDate ? strtolower(\Carbon\Carbon::parse($parentDate)->format('l')) : null;

                                        return Employee::all()->mapWithKeys(function ($employee) use ($dayOfWeek) {
                                            $label = $employee->name;
                                            if ($dayOfWeek && in_array($dayOfWeek, $employee->work_days ?? [])) {
                                                $label = "✅ " . $label;
                                            }
                                            return [$employee->id => $label];
                                        })->toArray();
                                    })
                                    ->required(),

                                Forms\Components\TextInput::make('price')
                                    ->label('السعر')
                                    ->numeric()
                                    ->prefix('EGP')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        self::updateVisitTotals($set, $get);
                                    })
                                    ->afterStateHydrated(function ($state, callable $set, callable $get, ?\Illuminate\Database\Eloquent\Model $record) {
                                        if (!$state || $state == 0) {
                                            $type = $get('type');
                                            $timeOrNum = 1;
                                            $price = self::calculateSessionPrice($get, $type, $timeOrNum, $record);
                                            if ($price > 0) {
                                                $set('price', $price);
                                            }
                                        }
                                    }),
                            ])->columns(3)->columnSpan('full')
                    ])->columnSpanFull(),

                // Section 7: Accounts & Finance
                Forms\Components\Section::make('الحسابات والمالية')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('السعر النهائي (بعد الخصم)')
                            ->readOnly()
                            ->numeric()
                            ->prefix('EGP')
                            ->reactive(),

                        Forms\Components\TextInput::make('paid')
                            ->label('إجمالي المدفوع')
                            ->required()
                            ->numeric()
                            ->prefix('EGP')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                self::updateVisitTotals($set, $get);
                            }),

                        Forms\Components\TextInput::make('due_to')
                            ->label('الزيادة / مستحق للعميل')
                            ->numeric()
                            ->readOnly()
                            ->prefix('EGP')
                            ->reactive(),

                        Forms\Components\TextInput::make('due_from')
                            ->label('المتبقي على العميل (عجز)')
                            ->numeric()
                            ->readOnly()
                            ->prefix('EGP')
                            ->reactive(),

                        Forms\Components\TextInput::make('discount_percentage')
                            ->label('نسبة الخصم (%)')
                            ->numeric()
                            ->prefix('%')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                self::updateVisitTotals($set, $get);
                            }),
                    ])->columns(2)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('request.is_urgent')
                    ->label('نوع الموعد')
                    ->badge()
                    ->state(fn ($record) => $record->request?->is_urgent ? 'مستعجل' : 'عادي')
                    ->color(fn ($record) => $record->request?->is_urgent ? 'warning' : 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('complaint')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('السعر النهائي')
                    ->money("EGP")
                    ->description(fn ($record) => $record->request?->is_urgent ? 'يشمل رسوم مستعجل' : null)
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hour')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('specialist.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('id','desc')
            ->filters([
                Filter::make('Today')
                ->query(fn (Builder $query): Builder => $query->where('date', today()->toDateString())),
                Filter::make('This Month')
                ->query(fn (Builder $query): Builder => $query->whereYear('date', Carbon::now()->year)
                                                        ->whereMonth('date', Carbon::now()->month))
            ])
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
            // RelationManagers\SessionsRelationManager::class,
        ];
    }

    public static function updateVisitTotals(callable $set, callable $get)
    {
        $isInsideRepeater = $get('../../discount_percentage') !== null;
        $prefix = $isInsideRepeater ? '../../' : '';

        $sessions = $get($prefix . 'Sessions') ?: [];
        $discount = (float)($get($prefix . 'discount_percentage') ?? 0);
        $paid = (float)($get($prefix . 'paid') ?? 0);

        $total = collect($sessions)->sum('price');
        $requestId = $get($prefix . 'request_id');
        if ($requestId) {
            $request = \App\Models\Request::find($requestId);
            if ($request && $request->is_urgent) {
                $urgentFee = (int)\App\Models\Setting::get('urgent_booking_fee', 200);
                $total += $urgentFee;
            }
        }
        $discountedTotal = $total - ($total * ($discount / 100));
        $finalPrice = round($discountedTotal, 2);

        $set($prefix . 'price', $finalPrice);

        if ($paid > $finalPrice) {
            $set($prefix . 'due_to', round($paid - $finalPrice, 2));
            $set($prefix . 'due_from', 0);
        } else {
            $set($prefix . 'due_from', round($finalPrice - $paid, 2));
            $set($prefix . 'due_to', 0);
        }
    }

    public static function calculateSessionPrice(callable $get, $sessionType, $timeOrNum, ?\Illuminate\Database\Eloquent\Model $record = null)
    {
        $requestId = $record?->visit?->request_id ?? $get('../../request_id');
        if ($requestId) {
            $request = \App\Models\Request::find($requestId);
            if ($request) {
                $tempRecord = (object)[
                    'booking_type' => 'وقائية',
                    'packages' => $get('../../packages') ?? $request->packages ?? [],
                    'massage_regions' => $get('../../massage_regions') ?? [],
                    'massage_style' => $get('../../massage_style') ?? 'intensive',
                    'massage_intensity' => $get('../../massage_intensity') ?? 'medium',
                    'cracking_type' => $get('../../cracking_type') ?? 'none',
                    'cracking_regions' => $get('../../cracking_regions') ?? [],
                    'hijama_type' => $get('../../hijama_type') ?? 'none',
                    'hijama_style' => $get('../../hijama_style') ?? 'intensive',
                    'hijama_regions' => $get('../../hijama_regions') ?? [],
                ];

                if (empty($tempRecord->massage_regions) && $record) {
                    $tempRecord->massage_regions = \App\Models\RequestRegion::where('request_id', $requestId)->pluck('region_number')->toArray();
                }
                if (empty($tempRecord->cracking_regions) && $record) {
                    $tempRecord->cracking_regions = \App\Models\RequestRegion::where('request_id', $requestId)->pluck('region_number')->toArray();
                }
                if (empty($tempRecord->hijama_regions) && $record) {
                    $tempRecord->hijama_regions = \App\Models\RequestRegion::where('request_id', $requestId)->pluck('region_number')->toArray();
                }

                $basePrices = \App\Helpers\MassageHelper::calculateServiceBasePrices($tempRecord);
                
                $serviceBase = 0;
                $isMassage = (str_contains($sessionType, 'مساج') || str_contains($sessionType, 'تنشيط عضلي'));
                $isCracking = (str_contains($sessionType, 'كيروبراكتيك') || str_contains($sessionType, 'تمارين') || str_contains($sessionType, 'شيروث') || str_contains($sessionType, 'توك سين') || str_contains($sessionType, 'تصريف ليمفاوي') || str_contains($sessionType, 'ابرة') || str_contains($sessionType, 'تقويم'));
                $isHijama = (str_contains($sessionType, 'حجامة') || str_contains($sessionType, 'ستون'));

                if ($isMassage) {
                    $serviceBase = $basePrices['massage'] ?? 0;
                } elseif ($isCracking) {
                    $serviceBase = $basePrices['cracking'] ?? 0;
                } elseif ($isHijama) {
                    $serviceBase = $basePrices['hijama'] ?? 0;
                }

                return round($serviceBase, 2);
            }
        }
        return 0;
    }

    public static function updateSessionRepeaterPrices(callable $set, callable $get)
    {
        $sessions = $get('Sessions') ?? [];
        if (empty($sessions)) return;

        $tempRecord = (object)[
            'booking_type' => 'وقائية',
            'packages' => $get('packages') ?? [],
            'massage_regions' => $get('massage_regions') ?? [],
            'massage_style' => $get('massage_style') ?? 'intensive',
            'massage_intensity' => $get('massage_intensity') ?? 'medium',
            'cracking_type' => $get('cracking_type') ?? 'none',
            'cracking_regions' => $get('cracking_regions') ?? [],
            'hijama_type' => $get('hijama_type') ?? 'none',
            'hijama_style' => $get('hijama_style') ?? 'intensive',
            'hijama_regions' => $get('hijama_regions') ?? [],
        ];

        $basePrices = \App\Helpers\MassageHelper::calculateServiceBasePrices($tempRecord);

        foreach ($sessions as $uuid => $session) {
            $type = $session['type'];
            $price = 0;
            $isMassage = (str_contains($type, 'مساج') || str_contains($type, 'تنشيط عضلي'));
            $isCracking = (str_contains($type, 'كيروبراكتيك') || str_contains($type, 'تمارين') || str_contains($type, 'شيروث') || str_contains($type, 'توك سين') || str_contains($type, 'تصريف ليمفاوي') || str_contains($type, 'ابرة') || str_contains($type, 'تقويم'));
            $isHijama = (str_contains($type, 'حجامة') || str_contains($type, 'ستون'));

            if ($isMassage) {
                $price = $basePrices['massage'] ?? 0;
            } elseif ($isCracking) {
                $price = $basePrices['cracking'] ?? 0;
            } elseif ($isHijama) {
                $price = $basePrices['hijama'] ?? 0;
            }
            $sessions[$uuid]['price'] = round($price, 2);
        }

        $set('Sessions', $sessions);
        self::updateVisitTotals($set, $get);
    }

    public static function syncRequestFromForm(array $data, ?\Illuminate\Database\Eloquent\Model $record = null)
    {
        $requestId = $record?->request_id ?? ($data['request_id'] ?? null);
        if (!$requestId) return;

        $request = \App\Models\Request::find($requestId);
        if (!$request) return;

        $regionRepetitions = [
            1 => 2, 2 => 1, 3 => 2, 4 => 3, 5 => 2, 6 => 1, 7 => 2, 8 => 3,
            9 => 1, 10 => 1, 11 => 1, 12 => 1, 13 => 1, 14 => 1, 15 => 1, 16 => 1,
            17 => 1, 18 => 1, 19 => 1, 20 => 1, 21 => 1, 22 => 1, 23 => 1, 24 => 1,
            25 => 2, 26 => 3, 27 => 2, 28 => 2, 29 => 3, 30 => 2,
            31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1,
            37 => 1, 38 => 2, 39 => 2
        ];

        $built = \App\Filament\Resources\RequestResource::buildDescription(
            'وقائية',
            $data['packages'] ?? [],
            $data['massage_regions'] ?? [],
            $data['massage_style'] ?? 'intensive',
            $data['massage_intensity'] ?? 'medium',
            $data['cracking_type'] ?? 'none',
            $data['cracking_regions'] ?? [],
            $data['hijama_type'] ?? 'none',
            $data['hijama_style'] ?? 'intensive',
            $data['hijama_regions'] ?? [],
            $regionRepetitions
        );

        $request->update([
            'packages' => $data['packages'] ?? [],
            'booking_type' => 'وقائية',
            'service_type' => $built['service_type'],
            'description' => $built['description'],
        ]);

        $basePrices = \App\Helpers\MassageHelper::calculateServiceBasePrices($request);
        $request->update([
            'total_price' => array_sum($basePrices),
        ]);

        \App\Models\RequestRegion::where('request_id', $request->id)->delete();
        $allRegions = array_merge(
            $data['massage_regions'] ?? [],
            $data['hijama_regions'] ?? []
        );
        foreach (array_unique($allRegions) as $rNum) {
            \App\Models\RequestRegion::create([
                'request_id' => $request->id,
                'region_number' => $rNum,
            ]);
        }
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisits::route('/'),
            'create' => Pages\CreateVisit::route('/create'),
            'view' => Pages\ViewVisit::route('/{record}'),
            'edit' => Pages\EditVisit::route('/{record}/edit'),
        ];
    }
}
