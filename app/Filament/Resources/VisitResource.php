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

                        Forms\Components\TimePicker::make('hour')
                            ->label('الساعة')
                            ->required()
                            ->formatStateUsing(function ($state) {
                                if (is_null($state) || $state === '') return null;
                                $floatVal = (float)$state;
                                $hrs = (int)floor($floatVal);
                                $mins = (int)round(($floatVal - $hrs) * 60);
                                if ($mins >= 60) {
                                    $hrs += 1;
                                    $mins -= 60;
                                }
                                return sprintf('%02d:%02d', $hrs, $mins);
                            })
                            ->dehydrateStateUsing(function ($state) {
                                if (is_null($state) || $state === '') return null;
                                $parts = explode(':', $state);
                                if (count($parts) < 2) return (float)$state;
                                return (float)$parts[0] + ((float)$parts[1] / 60);
                            }),
                    ])->columns(2),

                // Section 2: Visit Details (Allow editing complaint & notes)
                Forms\Components\Section::make('تفاصيل الزيارة')
                    ->schema([
                        Forms\Components\Textarea::make('complaint')
                            ->label('الشكوى / تفاصيل الخدمة')
                            ->required()
                            ->rows(3),
                        Forms\Components\Textarea::make('notes')
                            ->label('الملاحظات')
                            ->rows(3),
                    ])->columns(1),

                // Group Booking Members Section
                Forms\Components\Section::make('👥 أفراد المجموعة والمرافقين (Group Members)')
                    ->collapsible()
                    ->collapsed(false)
                    ->visible(fn (callable $get, ?Visit $record) => ($record?->request && ($record->request->parent_id || $record->request->children()->exists())) || ($get('request_id') && \App\Models\Request::where('id', $get('request_id'))->where(fn ($q) => $q->whereNotNull('parent_id')->orWhereHas('children'))->exists()))
                    ->schema([
                        Forms\Components\Placeholder::make('visit_group_members_list')
                            ->label('')
                            ->content(function (callable $get, ?Visit $record) {
                                $requestId = $record?->request_id ?? $get('request_id');
                                if (!$requestId) return '-';
                                $req = \App\Models\Request::with(['parent', 'children'])->find($requestId);
                                if (!$req) return '-';
                                
                                $leader = $req->parent_id ? $req->parent : $req;
                                if (!$leader) return '-';
                                $members = collect([$leader])->concat($leader->children);
                                
                                $html = '<div style="line-height: 1.6; font-size: 0.95rem; direction: rtl; text-align: right;">';
                                foreach ($members as $member) {
                                    $isCurrent = $member->id === $req->id;
                                    $style = $isCurrent ? 'font-weight: bold; background: #3f3f46; border-right: 4px solid #ff9d42;' : 'background: #27272a; border-right: 4px solid #71717a;';
                                    $html .= "<div style=\"margin-bottom: 0.5rem; padding: 0.75rem; {$style} border-radius: 6px; color: #fff;\">";
                                    $html .= e($member->name) . " (" . ($member->gender === 'female' ? 'أنثى' : 'ذكر') . ") - الهاتف: " . e($member->phone);
                                    $html .= " | الخدمة: " . e($member->service_type ?: 'غير محدد') . " | السعر: " . e($member->total_price) . " ج.م";
                                    if ($member->id === $leader->id) {
                                        $html .= " <span style=\"background: #d97706; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-right: 5px;\">قائد المجموعة</span>";
                                    }
                                    if ($isCurrent) {
                                        $html .= " <span style=\"background: #15803d; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-right: 5px;\">الزيارة الحالية</span>";
                                    }
                                    $html .= '</div>';
                                }
                                $html .= '</div>';
                                return new \Illuminate\Support\HtmlString($html);
                            })
                    ])
                    ->columnSpanFull(),

                // Section 3: Booking Prices & Techniques (Read-only dynamic section)
                Forms\Components\Section::make('أسعار خدمات الحجز والتكنيكات')
                    ->label(fn () => auth()->user()?->type === 'specialist' ? 'تكنيكات الحجز' : 'أسعار خدمات الحجز والتكنيكات')
                    ->visible(fn (callable $get, ?Visit $record) => ($record?->request_id !== null || $get('request_id') !== null))
                    ->schema([
                        Forms\Components\Placeholder::make('booking_prices_techniques')
                            ->label('')
                            ->content(function (callable $get, ?Visit $record) {
                                $requestId = $record?->request_id ?? $get('request_id');
                                $request = $requestId ? \App\Models\Request::find($requestId) : null;
                                $isTherapeutic = ($get('type') === 'علاجية' || $record?->type === 'علاجية' || ($request && $request->booking_type === 'علاجية'));

                                if ($isTherapeutic) {
                                    $protocol = $get('therapeutic_protocol') ?: 'intensive';
                                    $bloodType = $get('therapeutic_blood_type') ?: 'O';
                                    $weight = (float)($get('therapeutic_weight') ?: 75);
                                    $age = (int)($get('therapeutic_age') ?: 30);
                                    $severe = (array)($get('therapeutic_severe_regions') ?: []);
                                    $moderate = (array)($get('therapeutic_moderate_regions') ?: []);
                                    $isUrgent = (bool)($get('is_urgent') ?: ($request?->is_urgent ?? false));
                                    $couponCode = $get('coupon_code') ?? $record?->coupon_code;
                                    $couponDiscount = (float)($get('coupon_discount') ?? $record?->coupon_discount ?? 0);

                                    $calc = \App\Helpers\TherapeuticMassageHelper::buildTherapeuticDescription(
                                        $protocol,
                                        $bloodType,
                                        $weight,
                                        $age,
                                        $severe,
                                        $moderate,
                                        $isUrgent,
                                        $couponCode,
                                        $couponDiscount
                                    );

                                    $massagePrice = $calc['massage']['total_price'] ?? 0;
                                    $crackingPrice = $calc['chiro']['total_price'] ?? 0;
                                    $rehabPrice = $calc['rehab_price'] ?? 0;
                                    $urgentFee = $calc['urgent_fee'] ?? 0;
                                    $finalPrice = $calc['total_price'] ?? 0;

                                    $urgentFeeBox = '';
                                    if ($urgentFee > 0) {
                                        $urgentFeeBox = "
                                            <div style='background: #1e293b; padding: 1rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); text-align: center;'>
                                                <div style='color: #ff8c00; font-size: 0.85rem; margin-bottom: 0.25rem;'>🔥 رسوم مستعجل</div>
                                                <div style='color: #ff9d42; font-size: 1.25rem; font-weight: bold;'>{$urgentFee} EGP</div>
                                            </div>
                                        ";
                                    }

                                    $rehabBox = '';
                                    if ($rehabPrice > 0) {
                                        $rehabBox = "
                                            <div style='background: #1e293b; padding: 1rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); text-align: center;'>
                                                <div style='color: #94a3b8; font-size: 0.85rem; margin-bottom: 0.25rem;'>🏃‍♂️ سعر التأهيل</div>
                                                <div style='color: #06b6d4; font-size: 1.25rem; font-weight: bold;'>{$rehabPrice} EGP</div>
                                            </div>
                                        ";
                                    }

                                    $couponBox = '';
                                    if ($couponDiscount > 0) {
                                        $couponBox = "
                                            <div style='background: #1e293b; padding: 1rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); text-align: center;'>
                                                <div style='color: #10b981; font-size: 0.85rem; margin-bottom: 0.25rem;'>🎟️ كوبون ({$couponCode})</div>
                                                <div style='color: #10b981; font-size: 1.25rem; font-weight: bold;'>-{$couponDiscount} EGP</div>
                                            </div>
                                        ";
                                    }

                                    $pricingHtml = '';
                                    if (auth()->user()?->type !== 'specialist') {
                                        $pricingHtml = "
                                            <div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;'>
                                                <div style='background: #1e293b; padding: 1rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); text-align: center;'>
                                                    <div style='color: #94a3b8; font-size: 0.85rem; margin-bottom: 0.25rem;'>💆‍♂️ سعر المساج</div>
                                                    <div style='color: #ff9d42; font-size: 1.25rem; font-weight: bold;'>{$massagePrice} EGP</div>
                                                </div>
                                                <div style='background: #1e293b; padding: 1rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); text-align: center;'>
                                                    <div style='color: #94a3b8; font-size: 0.85rem; margin-bottom: 0.25rem;'>⚡ سعر الكيروبراكتيك</div>
                                                    <div style='color: #a855f7; font-size: 1.25rem; font-weight: bold;'>{$crackingPrice} EGP</div>
                                                </div>
                                                {$rehabBox}
                                                {$urgentFeeBox}
                                                {$couponBox}
                                                <div style='background: #0f172a; padding: 1rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); text-align: center;'>
                                                    <div style='color: #e2e8f0; font-size: 0.85rem; margin-bottom: 0.25rem;'>💰 الإجمالي بعد الخصم</div>
                                                    <div style='color: #38bdf8; font-size: 1.25rem; font-weight: bold;'>{$finalPrice} EGP</div>
                                                </div>
                                            </div>
                                        ";
                                    }

                                    $techniquesTable = \App\Helpers\TherapeuticMassageHelper::renderTherapeuticTechniquesForForm($get, $record);
                                    $techniquesHtml = $techniquesTable instanceof \Illuminate\Contracts\Support\Htmlable
                                        ? $techniquesTable->toHtml()
                                        : $techniquesTable;

                                    return new \Illuminate\Support\HtmlString("
                                        <div style='direction: rtl; text-align: right;'>
                                            {$pricingHtml}
                                            {$techniquesHtml}
                                        </div>
                                    ");
                                }

                                if (!$request) return 'لا توجد تفاصيل حجز.';

                                $basePrices = \App\Helpers\MassageHelper::calculateServiceBasePrices($request);
                                $massagePrice = $basePrices['massage'] ?? 0;
                                $crackingPrice = $basePrices['cracking'] ?? 0;
                                $hijamaPrice = $basePrices['hijama'] ?? 0;
                                $rehabPrice = $basePrices['rehab'] ?? 0;
                                
                                $urgentFee = $request->is_urgent ? (int)\App\Models\Setting::get('urgent_booking_fee', 200) : 0;
                                $totalBase = array_sum($basePrices) + $urgentFee;

                                $discount = (float)($get('discount_percentage') ?? 0);
                                $couponDiscount = (float)($get('coupon_discount') ?? $record?->coupon_discount ?? 0);
                                $finalPrice = max(0, $totalBase - ($totalBase * ($discount / 100)) - $couponDiscount);

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

                                $rehabBox = '';
                                if ($rehabPrice > 0) {
                                    $rehabBox = "
                                        <div style='background: #1e293b; padding: 1rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); text-align: center;'>
                                            <div style='color: #94a3b8; font-size: 0.85rem; margin-bottom: 0.25rem;'>🏃‍♂️ سعر التأهيل</div>
                                            <div style='color: #06b6d4; font-size: 1.25rem; font-weight: bold;'>{$rehabPrice} EGP</div>
                                        </div>
                                    ";
                                }

                                $couponBox = '';
                                if ($couponDiscount > 0) {
                                    $couponCode = $get('coupon_code') ?? $record?->coupon_code ?? 'خصم الكوبون';
                                    $couponBox = "
                                        <div style='background: #1e293b; padding: 1rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); text-align: center;'>
                                            <div style='color: #10b981; font-size: 0.85rem; margin-bottom: 0.25rem;'>🎟️ كوبون ({$couponCode})</div>
                                            <div style='color: #10b981; font-size: 1.25rem; font-weight: bold;'>-{$couponDiscount} EGP</div>
                                        </div>
                                    ";
                                }

                                $pricingHtml = '';
                                if (auth()->user()?->type !== 'specialist') {
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
                                            {$rehabBox}
                                            {$urgentFeeBox}
                                            {$couponBox}
                                            <div style='background: #0f172a; padding: 1rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); text-align: center;'>
                                                <div style='color: #e2e8f0; font-size: 0.85rem; margin-bottom: 0.25rem;'>💰 الإجمالي بعد الخصم</div>
                                                <div style='color: #38bdf8; font-size: 1.25rem; font-weight: bold;'>{$finalPrice} EGP</div>
                                            </div>
                                        </div>
                                    ";
                                }

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

                // Section: Therapeutic Details & Pain Maps (Visible for therapeutic visits)
                Forms\Components\Section::make('🗺️ خرائط ومناطق الألم للسيشن العلاجية (Therapeutic Pain Maps & Details)')
                    ->collapsible()
                    ->collapsed(false)
                    ->visible(fn (callable $get, ?Visit $record) => $get('type') === 'علاجية' || $record?->type === 'علاجية' || ($record?->request && $record->request->booking_type === 'علاجية'))
                    ->schema([
                        Forms\Components\Radio::make('therapeutic_protocol')
                            ->label('البروتوكول العلاجي')
                            ->options([
                                'intensive' => 'البروتوكول المكثف (Intensive Protocol)',
                                'economy' => 'البروتوكول الاقتصادي (Economy Protocol)',
                            ])
                            ->default('intensive')
                            ->inline()
                            ->reactive()
                            ->afterStateHydrated(function ($state, callable $set, ?Visit $record) {
                                if ($record) {
                                    $desc = $record->request?->description ?: $record->complaint;
                                    $parsed = \App\Helpers\TherapeuticMassageHelper::parseTherapeuticDescription($desc);
                                    $set('therapeutic_protocol', $parsed['protocol'] ?? ($record->request?->packages[0] ?? 'intensive'));
                                }
                            })
                            ->afterStateUpdated(fn (callable $set, callable $get) => self::updateTherapeuticVisitTotals($set, $get)),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('therapeutic_blood_type')
                                    ->label('فصيلة الدم')
                                    ->options([
                                        'A' => 'فصيلة A',
                                        'B' => 'فصيلة B',
                                        'AB' => 'فصيلة AB',
                                        'O' => 'فصيلة O',
                                    ])
                                    ->default('O')
                                    ->reactive()
                                    ->afterStateHydrated(function ($state, callable $set, ?Visit $record) {
                                        if ($record) {
                                            $desc = $record->request?->description ?: $record->complaint;
                                            $parsed = \App\Helpers\TherapeuticMassageHelper::parseTherapeuticDescription($desc);
                                            $set('therapeutic_blood_type', $parsed['blood_type'] ?? 'O');
                                        }
                                    })
                                    ->afterStateUpdated(fn (callable $set, callable $get) => self::updateTherapeuticVisitTotals($set, $get)),

                                Forms\Components\TextInput::make('therapeutic_weight')
                                    ->label('الوزن (كجم)')
                                    ->numeric()
                                    ->default(75)
                                    ->suffix('كجم')
                                    ->reactive()
                                    ->afterStateHydrated(function ($state, callable $set, ?Visit $record) {
                                        if ($record) {
                                            $desc = $record->request?->description ?: $record->complaint;
                                            $parsed = \App\Helpers\TherapeuticMassageHelper::parseTherapeuticDescription($desc);
                                            $set('therapeutic_weight', $parsed['weight'] ?? 75);
                                        }
                                    })
                                    ->afterStateUpdated(fn (callable $set, callable $get) => self::updateTherapeuticVisitTotals($set, $get)),

                                Forms\Components\TextInput::make('therapeutic_age')
                                    ->label('السن')
                                    ->numeric()
                                    ->default(30)
                                    ->suffix('سنة')
                                    ->reactive()
                                    ->afterStateHydrated(function ($state, callable $set, ?Visit $record) {
                                        if ($record) {
                                            $desc = $record->request?->description ?: $record->complaint;
                                            $parsed = \App\Helpers\TherapeuticMassageHelper::parseTherapeuticDescription($desc);
                                            $set('therapeutic_age', $parsed['age'] ?? 30);
                                        }
                                    })
                                    ->afterStateUpdated(fn (callable $set, callable $get) => self::updateTherapeuticVisitTotals($set, $get)),
                            ]),

                        Forms\Components\Select::make('therapeutic_severe_regions')
                            ->label('🔴 مناطق شديدة الألم (Severe Pain Regions)')
                            ->multiple()
                            ->options(array_combine(range(1, 39), array_map(fn($n) => "المنطقة رقم {$n}", range(1, 39))))
                            ->searchable()
                            ->reactive()
                            ->afterStateHydrated(function ($state, callable $set, ?Visit $record) {
                                if ($record) {
                                    $desc = $record->request?->description ?: $record->complaint;
                                    $parsed = \App\Helpers\TherapeuticMassageHelper::parseTherapeuticDescription($desc);
                                    $severe = $parsed['severe_regions'] ?? [];
                                    if (empty($severe) && empty($parsed['moderate_regions'] ?? []) && $record->request) {
                                        $severe = $record->request->regions()->pluck('region_number')->toArray();
                                    }
                                    $set('therapeutic_severe_regions', array_values(array_map('intval', $severe)));
                                }
                            })
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                $moderate = $get('therapeutic_moderate_regions') ?: [];
                                if (is_array($state) && is_array($moderate)) {
                                    $filteredMod = array_values(array_diff($moderate, $state));
                                    if (count($filteredMod) !== count($moderate)) {
                                        $set('therapeutic_moderate_regions', $filteredMod);
                                    }
                                }
                                self::updateTherapeuticVisitTotals($set, $get);
                            }),

                        Forms\Components\Select::make('therapeutic_moderate_regions')
                            ->label('🟠 مناطق متوسطة الألم (Moderate Pain Regions)')
                            ->multiple()
                            ->options(array_combine(range(1, 39), array_map(fn($n) => "المنطقة رقم {$n}", range(1, 39))))
                            ->searchable()
                            ->reactive()
                            ->afterStateHydrated(function ($state, callable $set, ?Visit $record) {
                                if ($record) {
                                    $desc = $record->request?->description ?: $record->complaint;
                                    $parsed = \App\Helpers\TherapeuticMassageHelper::parseTherapeuticDescription($desc);
                                    $set('therapeutic_moderate_regions', array_values(array_map('intval', $parsed['moderate_regions'] ?? [])));
                                }
                            })
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                $severe = $get('therapeutic_severe_regions') ?: [];
                                if (is_array($state) && is_array($severe)) {
                                    $filteredSev = array_values(array_diff($severe, $state));
                                    if (count($filteredSev) !== count($severe)) {
                                        $set('therapeutic_severe_regions', $filteredSev);
                                    }
                                }
                                self::updateTherapeuticVisitTotals($set, $get);
                            }),

                        Forms\Components\Placeholder::make('therapeutic_body_maps_display')
                            ->label('🗺️ خريطة مناطق الألم المحددة (تحديث فوري)')
                            ->content(function (callable $get, ?Visit $record) {
                                $severe = (array)($get('therapeutic_severe_regions') ?? []);
                                $moderate = (array)($get('therapeutic_moderate_regions') ?? []);
                                return \App\Helpers\TherapeuticMassageHelper::renderTherapeuticBodyMaps($record, $severe, $moderate);
                            }),

                        Forms\Components\Placeholder::make('therapeutic_live_techniques_display')
                            ->label('📋 جدول التكنيكات المعتمدة لمناطق الألم المختارة (تحديث فوري)')
                            ->content(function (callable $get, ?Visit $record) {
                                return \App\Helpers\TherapeuticMassageHelper::renderTherapeuticTechniquesForForm($get, $record);
                            }),
                    ])
                    ->columnSpanFull(),

                // Virtual Form Sections for editing Request details (Preventative visits):
                Forms\Components\Section::make('💆‍♂️ المساج (Massage)')
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn (callable $get, ?Visit $record) => $get('request_id') !== null && $get('type') !== 'علاجية' && $record?->type !== 'علاجية')
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
                                    $parsed = \App\Filament\Resources\RequestResource::parseDescription($record->request->description);
                                    $regions = $parsed['massage_regions'] ?? [];
                                    if (empty($regions) && empty($parsed['packages'] ?? [])) {
                                        $regions = \App\Models\RequestRegion::where('request_id', $record->request_id)
                                            ->pluck('region_number')
                                            ->toArray();
                                    }
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
                    ->visible(fn (callable $get, ?Visit $record) => $get('request_id') !== null && $get('type') !== 'علاجية' && $record?->type !== 'علاجية')
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
                        Forms\Components\Radio::make('cracking_style')
                            ->label('باقة تقويم العمود الفقري')
                            ->options([
                                'intensive' => 'مكثف',
                                'economy' => 'اقتصادي',
                            ])
                            ->inline()
                            ->reactive()
                            ->visible(fn (callable $get) => $get('cracking_type') !== 'none')
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                self::updateSessionRepeaterPrices($set, $get);
                            })
                            ->afterStateHydrated(function ($state, callable $set, ?\Illuminate\Database\Eloquent\Model $record) {
                                if ($record && $record->request) {
                                    $parsed = \App\Filament\Resources\RequestResource::parseDescription($record->request->description);
                                    $set('cracking_style', $parsed['cracking_style'] ?: 'intensive');
                                }
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
                                self::updateSessionRepeaterPrices($set, $get);
                            })
                            ->afterStateHydrated(function ($state, callable $set, ?\Illuminate\Database\Eloquent\Model $record) {
                                if ($record && $record->request) {
                                    $parsed = \App\Filament\Resources\RequestResource::parseDescription($record->request->description);
                                    $regions = $parsed['cracking_regions'] ?? [];
                                    if (empty($regions) && ($parsed['cracking_type'] ?? 'none') === 'regions') {
                                        $regions = \App\Models\RequestRegion::where('request_id', $record->request_id)
                                            ->pluck('region_number')
                                            ->toArray();
                                    }
                                    $set('cracking_regions', $regions);
                                }
                            }),
                    ]),

                Forms\Components\Section::make('🩸 الحجامة (Hijama / Cupping)')
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn (callable $get, ?Visit $record) => $get('request_id') !== null && $get('type') !== 'علاجية' && $record?->type !== 'علاجية')
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
                                    $parsed = \App\Filament\Resources\RequestResource::parseDescription($record->request->description);
                                    $hijamaType = $parsed['hijama_type'] ?? 'none';
                                    if ($hijamaType === 'whole_back') {
                                        $regions = [1, 3, 5, 7, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 37];
                                    } elseif ($hijamaType === 'whole_front') {
                                        $regions = [19, 22, 23, 24, 25, 27, 28, 30, 31, 32, 33, 34, 35, 36];
                                    } else {
                                        $regions = $parsed['hijama_regions'] ?? [];
                                        if (empty($regions) && $hijamaType === 'regions') {
                                            $regions = \App\Models\RequestRegion::where('request_id', $record->request_id)
                                                ->pluck('region_number')
                                                ->toArray();
                                        }
                                    }
                                    $set('hijama_regions', $regions);
                                }
                            }),
                    ]),

                // Section 5: Body Maps (Preventative)
                Forms\Components\Grid::make(3)
                    ->visible(fn (callable $get, ?Visit $record) => $get('request_id') !== null && $get('type') !== 'علاجية' && $record?->type !== 'علاجية')
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
                                    'cracking_style' => $get('cracking_style') ?? 'intensive',
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
                                    ->content(function (callable $get) {
                                        $selected = array_map('intval', (array)($get('massage_regions') ?? []));
                                        $regionCoords = [
                                            1 => ['top' => 59, 'left' => 82.8],
                                            2 => ['top' => 69.8, 'left' => 82.2],
                                            3 => ['top' => 77.5, 'left' => 82.2],
                                            4 => ['top' => 90.5, 'left' => 82.2],
                                            5 => ['top' => 59.5, 'left' => 88.2],
                                            6 => ['top' => 70.5, 'left' => 88.2],
                                            7 => ['top' => 78.5, 'left' => 89.2],
                                            8 => ['top' => 91.5, 'left' => 88.2],
                                            9 => ['top' => 45.5, 'left' => 82.2],
                                            10 => ['top' => 45.5, 'left' => 88.2],
                                            11 => ['top' => 36.5, 'left' => 82.2],
                                            12 => ['top' => 37.5, 'left' => 89.2],
                                            13 => ['top' => 25.5, 'left' => 83.2],
                                            14 => ['top' => 26.5, 'left' => 89.2],
                                            15 => ['top' => 17.5, 'left' => 83.2],
                                            16 => ['top' => 17.5, 'left' => 88.5],
                                            17 => ['top' => 20.5, 'left' => 77.8],
                                            18 => ['top' => 28.5, 'left' => 77],
                                            19 => ['top' => 38.5, 'left' => 76.5],
                                            20 => ['top' => 20.5, 'left' => 93.8],
                                            21 => ['top' => 29.5, 'left' => 94.6],
                                            22 => ['top' => 39.5, 'left' => 95.2],
                                            23 => ['top' => 20.5, 'left' => 64],
                                            24 => ['top' => 18.5, 'left' => 45.2],
                                            25 => ['top' => 54.5, 'left' => 10],
                                            26 => ['top' => 69.5, 'left' => 10],
                                            27 => ['top' => 78.5, 'left' => 10],
                                            28 => ['top' => 55, 'left' => 17.2],
                                            29 => ['top' => 69.5, 'left' => 17.2],
                                            30 => ['top' => 79.5, 'left' => 17.2],
                                            31 => ['top' => 23.5, 'left' => 15.5],
                                            32 => ['top' => 23.5, 'left' => 10.5],
                                            33 => ['top' => 19.5, 'left' => 20],
                                            34 => ['top' => 26.5, 'left' => 21.5],
                                            35 => ['top' => 19.5, 'left' => 6],
                                            36 => ['top' => 27.5, 'left' => 5.5],
                                            37 => ['top' => 9.5, 'left' => 85.8],
                                            38 => ['top' => 89.5, 'left' => 16.2],
                                            39 => ['top' => 88.5, 'left' => 10]
                                        ];

                                        $hotspotsHtml = '';
                                        foreach ($regionCoords as $num => $coord) {
                                            $isSelected = in_array($num, $selected);
                                            $class = $isSelected ? 'hotspot selected' : 'hotspot';
                                            $hotspotsHtml .= "<div class='{$class}' style='top: {$coord['top']}%; left: {$coord['left']}%;' data-region='{$num}' onclick='toggleFilamentRegion(this, \"massage_regions\")'>{$num}</div>";
                                        }

                                        return new \Illuminate\Support\HtmlString("
                                            <div style='text-align: center; display: flex; justify-content: center;'>
                                                <div style='position: relative; display: inline-block; max-width: 600px; width: 100%; aspect-ratio: 438 / 166.32;'>
                                                    <img src='/images/body.jpg' alt='Massage Chart' style='width: 100%; height: auto; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);' />
                                                    {$hotspotsHtml}
                                                </div>
                                            </div>
                                            " . self::getMapStylesAndScript());
                                    })
                            ])
                            ->columnSpan(1)
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
                                    'cracking_style' => $get('cracking_style') ?? 'intensive',
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
                                    ->content(function (callable $get) {
                                        $selected = array_map('intval', (array)($get('cracking_regions') ?? []));
                                        $crackingRegionCoords = [
                                            1 => [['top' => 17.5, 'left' => 50.0]],
                                            2 => [['top' => 27.8, 'left' => 29.0], ['top' => 27.8, 'left' => 71.0]],
                                            3 => [['top' => 26.0, 'left' => 50.0]],
                                            4 => [['top' => 36.5, 'left' => 50.0]],
                                            5 => [['top' => 63.8, 'left' => 50.0]]
                                        ];

                                        $hotspotsHtml = '';
                                        foreach ($crackingRegionCoords as $num => $coordsList) {
                                            $isSelected = in_array($num, $selected);
                                            $class = $isSelected ? 'hotspot selected' : 'hotspot';
                                            foreach ($coordsList as $coord) {
                                                $hotspotsHtml .= "<div class='{$class}' style='top: {$coord['top']}%; left: {$coord['left']}%;' data-region='{$num}' onclick='toggleFilamentRegion(this, \"cracking_regions\")'>{$num}</div>";
                                            }
                                        }

                                        return new \Illuminate\Support\HtmlString("
                                            <div style='text-align: center; display: flex; justify-content: center;'>
                                                <div style='position: relative; display: inline-block; max-width: 250px; width: 100%; aspect-ratio: 200 / 420; margin: 0 auto;'>
                                                    <img src='/images/cracking.png' alt='Cracking Chart' style='width: 100%; height: auto; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);' />
                                                    {$hotspotsHtml}
                                                </div>
                                            </div>
                                            " . self::getMapStylesAndScript());
                                    })
                            ])
                            ->columnSpan(1)
                            ->collapsible()
                            ->collapsed(false),

                        Forms\Components\Section::make('خريطة الحجامة (Hijama Chart)')
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
                                return ($basePrices['hijama'] ?? 0) > 0;
                            })
                            ->schema([
                                Forms\Components\Placeholder::make('hijama_chart_img')
                                    ->label('')
                                    ->content(function (callable $get) {
                                        $selected = array_map('intval', (array)($get('hijama_regions') ?? []));
                                        $regionCoords = [
                                            1 => ['top' => 59, 'left' => 82.8],
                                            2 => ['top' => 69.8, 'left' => 82.2],
                                            3 => ['top' => 77.5, 'left' => 82.2],
                                            4 => ['top' => 90.5, 'left' => 82.2],
                                            5 => ['top' => 59.5, 'left' => 88.2],
                                            6 => ['top' => 70.5, 'left' => 88.2],
                                            7 => ['top' => 78.5, 'left' => 89.2],
                                            8 => ['top' => 91.5, 'left' => 88.2],
                                            9 => ['top' => 45.5, 'left' => 82.2],
                                            10 => ['top' => 45.5, 'left' => 88.2],
                                            11 => ['top' => 36.5, 'left' => 82.2],
                                            12 => ['top' => 37.5, 'left' => 89.2],
                                            13 => ['top' => 25.5, 'left' => 83.2],
                                            14 => ['top' => 26.5, 'left' => 89.2],
                                            15 => ['top' => 17.5, 'left' => 83.2],
                                            16 => ['top' => 17.5, 'left' => 88.5],
                                            17 => ['top' => 20.5, 'left' => 77.8],
                                            18 => ['top' => 28.5, 'left' => 77],
                                            19 => ['top' => 38.5, 'left' => 76.5],
                                            20 => ['top' => 20.5, 'left' => 93.8],
                                            21 => ['top' => 29.5, 'left' => 94.6],
                                            22 => ['top' => 39.5, 'left' => 95.2],
                                            23 => ['top' => 20.5, 'left' => 64],
                                            24 => ['top' => 18.5, 'left' => 45.2],
                                            25 => ['top' => 54.5, 'left' => 10],
                                            26 => ['top' => 69.5, 'left' => 10],
                                            27 => ['top' => 78.5, 'left' => 10],
                                            28 => ['top' => 55, 'left' => 17.2],
                                            29 => ['top' => 69.5, 'left' => 17.2],
                                            30 => ['top' => 79.5, 'left' => 17.2],
                                            31 => ['top' => 23.5, 'left' => 15.5],
                                            32 => ['top' => 23.5, 'left' => 10.5],
                                            33 => ['top' => 19.5, 'left' => 20],
                                            34 => ['top' => 26.5, 'left' => 21.5],
                                            35 => ['top' => 19.5, 'left' => 6],
                                            36 => ['top' => 27.5, 'left' => 5.5],
                                            37 => ['top' => 9.5, 'left' => 85.8],
                                            38 => ['top' => 89.5, 'left' => 16.2],
                                            39 => ['top' => 88.5, 'left' => 10]
                                        ];

                                        $hotspotsHtml = '';
                                        foreach ($regionCoords as $num => $coord) {
                                            $isSelected = in_array($num, $selected);
                                            $class = $isSelected ? 'hotspot selected' : 'hotspot';
                                            $hotspotsHtml .= "<div class='{$class}' style='top: {$coord['top']}%; left: {$coord['left']}%;' data-region='{$num}' onclick='toggleFilamentRegion(this, \"hijama_regions\")'>{$num}</div>";
                                        }

                                        return new \Illuminate\Support\HtmlString("
                                            <div style='text-align: center; display: flex; justify-content: center;'>
                                                <div style='position: relative; display: inline-block; max-width: 600px; width: 100%; aspect-ratio: 438 / 166.32;'>
                                                    <img src='/images/body.jpg' alt='Hijama Chart' style='width: 100%; height: auto; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);' />
                                                    {$hotspotsHtml}
                                                </div>
                                            </div>
                                            " . self::getMapStylesAndScript());
                                    })
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
                                        'مساج علاجي' => 'مساج علاجي',
                                        'تأهيل حركي' => 'تأهيل حركي',
                                        'تأهيل' => 'تأهيل',
                                        'كيروبراكتيك علاجي' => 'كيروبراكتيك علاجي',
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
                                    ->hidden(fn () => auth()->user()?->type === 'specialist')
                                    ->dehydrated(fn () => auth()->user()?->type !== 'specialist')
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

                        // Forms\Components\TextInput::make('discount_percentage')
                        //     ->label('نسبة الخصم (%)')
                        //     ->numeric()
                        //     ->prefix('%')
                        //     ->reactive()
                        //     ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        //         self::updateVisitTotals($set, $get);
                        //     }),

                        Forms\Components\TextInput::make('coupon_code')
                            ->label('كود كوبون الخصم')
                            ->placeholder('أدخل كود الكوبون إن وجد')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $code = trim($state);
                                if (!$code) {
                                    $set('coupon_discount', 0);
                                    self::updateVisitTotals($set, $get);
                                    return;
                                }

                                $coupon = \App\Models\Coupon::where('code', $code)->first();
                                if (!$coupon) {
                                    $coupon = \App\Models\Coupon::whereRaw('UPPER(code) = ?', [strtoupper($code)])->first();
                                }

                                if (!$coupon || !$coupon->is_active) {
                                    $set('coupon_discount', 0);
                                    self::updateVisitTotals($set, $get);
                                    return;
                                }

                                // Calculate the discount based on the sum of sessions
                                $sessions = $get('Sessions') ?: [];
                                $total = collect($sessions)->sum('price');
                                
                                // Subtract discount percentage if any
                                $discountPercentage = (float)($get('discount_percentage') ?? 0);
                                $totalAfterPercentage = $total - ($total * ($discountPercentage / 100));

                                $discount = $coupon->calculateDiscountFor($totalAfterPercentage);
                                $set('coupon_discount', $discount);
                                self::updateVisitTotals($set, $get);
                            }),

                        Forms\Components\TextInput::make('coupon_discount')
                            ->label('قيمة خصم الكوبون')
                            ->numeric()
                            ->readOnly()
                            ->prefix('EGP')
                            ->placeholder('0.00'),
                    ])->columns(2)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable()
                    ->description(function (Visit $record) {
                        if (!$record->request) return null;
                        if ($record->request->children()->exists()) {
                            return '👥 حجز جماعي (قائد)';
                        }
                        if ($record->request->parent_id) {
                            $leaderName = $record->request->parent?->name;
                            return '🔗 حجز جماعي (مرافق' . ($leaderName ? ' مع: ' . $leaderName : '') . ')';
                        }
                        return null;
                    }),
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
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('التاريخ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sessions.employee.name')
                    ->label('المختصين')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('نوع السيشن')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'علاجية' => 'danger',
                        'وقائية' => 'success',
                        'رياضية' => 'info',
                        default => 'warning'
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('request.is_urgent')
                    ->label('نوع الموعد')
                    ->badge()
                    ->state(fn ($record) => $record->request?->is_urgent ? 'مستعجل' : 'عادي')
                    ->color(fn ($record) => $record->request?->is_urgent ? 'warning' : 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('السعر النهائي')
                    ->money("EGP")
                    ->description(fn ($record) => $record->request?->is_urgent ? 'يشمل رسوم مستعجل' : null)
                    ->sortable(),
                Tables\Columns\TextColumn::make('coupon_code')
                    ->label('الكوبون المطبق')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state, $record) => $state ? "🎟️ {$state} (-{$record->coupon_discount} EGP)" : null)
                    ->placeholder('لا يوجد')
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')
                    ->label('الملاحظات'),
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
                Tables\Filters\SelectFilter::make('type')
                    ->label('نوع السيشن')
                    ->options([
                        'وقائية' => 'وقائية',
                        'علاجية' => 'علاجية',
                        'رياضية' => 'رياضية',
                    ]),
                Filter::make('Today')
                    ->default()
                    ->query(fn (Builder $query): Builder => $query->where('date', today()->toDateString())),
                Filter::make('This Month')
                    ->query(fn (Builder $query): Builder => $query->whereYear('date', Carbon::now()->year)
                                                            ->whereMonth('date', Carbon::now()->month)),
                Tables\Filters\Filter::make('date_filter')
                    ->label('الفلترة بالتاريخ')
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('to_date')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['to_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
                Tables\Filters\SelectFilter::make('employee_id')
                    ->label('المختص')
                    ->options(\App\Models\Employee::pluck('name', 'id'))
                    ->default(fn () => \App\Models\Employee::where('name', auth()->user()?->name)->first()?->id)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $employeeId): Builder => $query->whereHas('sessions', function (Builder $q) use ($employeeId) {
                                $q->where('employee_id', $employeeId);
                            })
                        );
                    }),
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
        $couponDiscount = (float)($get($prefix . 'coupon_discount') ?? 0);

        $total = collect($sessions)->sum('price');
        $requestId = $get($prefix . 'request_id');
        if ($requestId) {
            $request = \App\Models\Request::find($requestId);
            if ($request && $request->is_urgent) {
                $urgentFee = (int)\App\Models\Setting::get('urgent_booking_fee', 200);
                $total += $urgentFee;
            }
        }
        $discountedTotal = $total - ($total * ($discount / 100)) - $couponDiscount;
        $finalPrice = max(0, round($discountedTotal, 2));

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
                if ($request->booking_type === 'علاجية') {
                    $basePrices = \App\Helpers\MassageHelper::calculateServiceBasePrices($request);
                    if (str_contains($sessionType, 'مساج')) {
                        return round($basePrices['massage'] ?? 0, 2);
                    } elseif (str_contains($sessionType, 'كيروبراكتيك') || str_contains($sessionType, 'تقويم')) {
                        return round($basePrices['cracking'] ?? 0, 2);
                    } elseif (str_contains($sessionType, 'حجامة')) {
                        return round($basePrices['hijama'] ?? 0, 2);
                    } elseif (str_contains($sessionType, 'تأهيل')) {
                        return round($basePrices['rehab'] ?? 0, 2);
                    }
                    return 0;
                }

                $parsed = \App\Filament\Resources\RequestResource::parseDescription($request->description);
                $packages = $get('../../packages') ?? $request->packages ?? [];

                $massageRegions = $get('../../massage_regions');
                if ($massageRegions === null) {
                    $massageRegions = $parsed['massage_regions'] ?? [];
                    if (empty($massageRegions) && empty($packages)) {
                        $massageRegions = \App\Models\RequestRegion::where('request_id', $requestId)->pluck('region_number')->toArray();
                    }
                }

                $crackingType = $get('../../cracking_type') ?? $parsed['cracking_type'] ?? 'none';
                $crackingRegions = $get('../../cracking_regions');
                if ($crackingRegions === null) {
                    $crackingRegions = $parsed['cracking_regions'] ?? [];
                    if (empty($crackingRegions) && $crackingType === 'regions') {
                        $crackingRegions = \App\Models\RequestRegion::where('request_id', $requestId)->pluck('region_number')->toArray();
                    }
                }

                $hijamaType = $get('../../hijama_type') ?? $parsed['hijama_type'] ?? 'none';
                $hijamaRegions = $get('../../hijama_regions');
                if ($hijamaRegions === null) {
                    if ($hijamaType === 'whole_back') {
                        $hijamaRegions = [1, 3, 5, 7, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 37];
                    } elseif ($hijamaType === 'whole_front') {
                        $hijamaRegions = [19, 22, 23, 24, 25, 27, 28, 30, 31, 32, 33, 34, 35, 36];
                    } else {
                        $hijamaRegions = $parsed['hijama_regions'] ?? [];
                        if (empty($hijamaRegions) && $hijamaType === 'regions') {
                            $hijamaRegions = \App\Models\RequestRegion::where('request_id', $requestId)->pluck('region_number')->toArray();
                        }
                    }
                }

                $tempRecord = (object)[
                    'booking_type' => 'وقائية',
                    'packages' => $packages,
                    'massage_regions' => $massageRegions,
                    'massage_style' => $get('../../massage_style') ?? $parsed['massage_style'] ?? 'intensive',
                    'massage_intensity' => $get('../../massage_intensity') ?? $parsed['massage_intensity'] ?? 'medium',
                    'cracking_type' => $crackingType,
                    'cracking_style' => $get('../../cracking_style') ?? $parsed['cracking_style'] ?? 'intensive',
                    'cracking_regions' => $crackingRegions,
                    'hijama_type' => $hijamaType,
                    'hijama_style' => $get('../../hijama_style') ?? $parsed['hijama_style'] ?? 'intensive',
                    'hijama_regions' => $hijamaRegions,
                ];

                $basePrices = \App\Helpers\MassageHelper::calculateServiceBasePrices($tempRecord);
                
                $serviceBase = 0;
                $isMassage = (str_contains($sessionType, 'مساج') || str_contains($sessionType, 'تنشيط عضلي'));
                $isCracking = (str_contains($sessionType, 'كيروبراكتيك') || str_contains($sessionType, 'تمارين') || str_contains($sessionType, 'شيروث') || str_contains($sessionType, 'توك سين') || str_contains($sessionType, 'تصريف ليمفاوي') || str_contains($sessionType, 'ابرة') || str_contains($sessionType, 'تقويم'));
                $isHijama = (str_contains($sessionType, 'حجامة') || str_contains($sessionType, 'ستون'));

                $isRehab = str_contains($sessionType, 'تأهيل');

                if ($isMassage) {
                    $serviceBase = $basePrices['massage'] ?? 0;
                } elseif ($isCracking) {
                    $serviceBase = $basePrices['cracking'] ?? 0;
                } elseif ($isHijama) {
                    $serviceBase = $basePrices['hijama'] ?? 0;
                } elseif ($isRehab) {
                    $serviceBase = $basePrices['rehab'] ?? 0;
                }

                return round($serviceBase, 2);
            }
        }

        $fallbackPrices = [
            'مساج علاجي' => 160,
            'كيروبراكتيك علاجي' => 200,
            'حجامة' => 50,
            '(كاس)حجامة تشريطية' => 50,
            'تأهيل حركي' => 120,
            'تأهيل' => 120,
        ];
        if (isset($fallbackPrices[$sessionType])) {
            return $fallbackPrices[$sessionType];
        }

        return 0;
    }

    public static function updateSessionRepeaterPrices(callable $set, callable $get)
    {
        $sessions = $get('Sessions') ?? [];
        if (empty($sessions)) return;

        $requestId = $get('request_id');
        $request = $requestId ? \App\Models\Request::find($requestId) : null;
        if ($request && $request->booking_type === 'علاجية') {
            $basePrices = \App\Helpers\MassageHelper::calculateServiceBasePrices($request);
            foreach ($sessions as $uuid => $session) {
                $type = $session['type'] ?? '';
                $price = 0;
                if (str_contains($type, 'مساج')) {
                    $price = $basePrices['massage'] ?? 0;
                } elseif (str_contains($type, 'كيروبراكتيك') || str_contains($type, 'تقويم')) {
                    $price = $basePrices['cracking'] ?? 0;
                } elseif (str_contains($type, 'حجامة')) {
                    $price = $basePrices['hijama'] ?? 0;
                } elseif (str_contains($type, 'تأهيل')) {
                    $price = $basePrices['rehab'] ?? 0;
                }
                $sessions[$uuid]['price'] = round($price, 2);
            }
            $set('Sessions', $sessions);
            self::updateVisitTotals($set, $get);
            return;
        }

        $tempRecord = (object)[
            'booking_type' => 'وقائية',
            'packages' => $get('packages') ?? [],
            'massage_regions' => $get('massage_regions') ?? [],
            'massage_style' => $get('massage_style') ?? 'intensive',
            'massage_intensity' => $get('massage_intensity') ?? 'medium',
            'cracking_type' => $get('cracking_type') ?? 'none',
            'cracking_style' => $get('cracking_style') ?? 'intensive',
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
            $isRehab = str_contains($type, 'تأهيل');

            if ($isMassage) {
                $price = $basePrices['massage'] ?? 0;
            } elseif ($isCracking) {
                $price = $basePrices['cracking'] ?? 0;
            } elseif ($isHijama) {
                $price = $basePrices['hijama'] ?? 0;
            } elseif ($isRehab) {
                $price = $basePrices['rehab'] ?? 0;
            }
            $sessions[$uuid]['price'] = round($price, 2);
        }

        $set('Sessions', $sessions);
        self::updateVisitTotals($set, $get);
    }

    public static function updateTherapeuticVisitTotals(callable $set, callable $get)
    {
        $protocol = $get('therapeutic_protocol') ?: 'intensive';
        $bloodType = $get('therapeutic_blood_type') ?: 'O';
        $weight = (float)($get('therapeutic_weight') ?: 75);
        $age = (int)($get('therapeutic_age') ?: 30);
        $severe = (array)($get('therapeutic_severe_regions') ?: []);
        $moderate = (array)($get('therapeutic_moderate_regions') ?: []);
        $isUrgent = (bool)($get('is_urgent') ?: false);
        $couponCode = $get('coupon_code');
        $couponDiscount = (float)($get('coupon_discount') ?: 0);

        $calc = \App\Helpers\TherapeuticMassageHelper::buildTherapeuticDescription(
            $protocol,
            $bloodType,
            $weight,
            $age,
            $severe,
            $moderate,
            $isUrgent,
            $couponCode,
            $couponDiscount
        );

        $set('complaint', $calc['description']);

        $sessions = $get('Sessions') ?? [];
        if (!empty($sessions)) {
            $basePrices = [
                'massage' => $calc['massage']['total_price'],
                'cracking' => $calc['chiro']['total_price'],
                'rehab' => $calc['rehab_price'],
            ];

            foreach ($sessions as $uuid => $session) {
                $type = $session['type'] ?? '';
                $price = 0;
                if (str_contains($type, 'مساج')) {
                    $price = $basePrices['massage'] ?? 0;
                } elseif (str_contains($type, 'كيروبراكتيك') || str_contains($type, 'تقويم')) {
                    $price = $basePrices['cracking'] ?? 0;
                } elseif (str_contains($type, 'تأهيل')) {
                    $price = $basePrices['rehab'] ?? 0;
                }
                $sessions[$uuid]['price'] = round($price, 2);
            }
            $set('Sessions', $sessions);
        }

        self::updateVisitTotals($set, $get);
    }

    public static function syncRequestFromForm(array &$data, ?\Illuminate\Database\Eloquent\Model $record = null)
    {
        $requestId = $record?->request_id ?? ($data['request_id'] ?? null);
        $request = $requestId ? \App\Models\Request::find($requestId) : null;

        $bookingType = $data['type'] ?? $record?->type ?? $request?->booking_type ?? 'وقائية';

        if ($bookingType === 'علاجية') {
            $protocol = $data['therapeutic_protocol'] ?? 'intensive';
            $bloodType = $data['therapeutic_blood_type'] ?? 'O';
            $weight = (float)($data['therapeutic_weight'] ?? 75);
            $age = (int)($data['therapeutic_age'] ?? 30);
            $severe = (array)($data['therapeutic_severe_regions'] ?? []);
            $moderate = (array)($data['therapeutic_moderate_regions'] ?? []);

            $calc = \App\Helpers\TherapeuticMassageHelper::buildTherapeuticDescription(
                $protocol,
                $bloodType,
                $weight,
                $age,
                $severe,
                $moderate,
                (bool)($request->is_urgent ?? false),
                $data['coupon_code'] ?? $request?->coupon_code,
                (float)($data['coupon_discount'] ?? $request?->coupon_discount ?? 0)
            );

            $data['complaint'] = $calc['description'];
            if ($record) {
                $record->update(['complaint' => $calc['description']]);
            }

            if ($request) {
                $request->update([
                    'packages' => [$protocol],
                    'booking_type' => 'علاجية',
                    'service_type' => $calc['service_type'],
                    'description' => $calc['description'],
                    'total_price' => $calc['total_price'],
                    'total_duration' => $calc['total_duration'],
                ]);

                \App\Models\RequestRegion::where('request_id', $request->id)->delete();
                $bloodTypeKey = in_array(strtoupper($bloodType), ['A', 'B', 'AB', 'O']) ? strtoupper($bloodType) : 'O';
                $bracket = \App\Helpers\TherapeuticMassageHelper::getWeightBracket($weight);
                $sevRepMap = \App\Helpers\TherapeuticMassageHelper::$severeTechniqueMap[$bloodTypeKey][$bracket] ?? [];
                $modRepMap = \App\Helpers\TherapeuticMassageHelper::$moderateTechniqueMap ?? [];

                foreach ($severe as $rNum) {
                    $rNum = (int)$rNum;
                    \App\Models\RequestRegion::create([
                        'request_id' => $request->id,
                        'region_number' => $rNum,
                        'repetitions' => $sevRepMap[$rNum] ?? 1,
                    ]);
                }
                foreach ($moderate as $rNum) {
                    $rNum = (int)$rNum;
                    \App\Models\RequestRegion::create([
                        'request_id' => $request->id,
                        'region_number' => $rNum,
                        'repetitions' => $modRepMap[$rNum] ?? 1,
                    ]);
                }
            }
            return;
        }

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
            $regionRepetitions,
            $data['cracking_style'] ?? 'intensive'
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


    protected static function getMapStylesAndScript(): string
    {
        return '
            <style>
                .hotspot {
                    position: absolute;
                    width: 22px;
                    aspect-ratio: 1;
                    border-radius: 50%;
                    transform: translate(-50%, -50%);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 0.65rem;
                    font-weight: 800;
                    user-select: none;
                    z-index: 10;
                    border: 2px solid transparent;
                    background: transparent;
                    color: transparent;
                    transition: all 0.2s ease;
                }
                .hotspot.selected {
                    background: #2ecc71 !important;
                    color: #ffffff !important;
                    box-shadow: 0 0 12px rgba(46, 204, 113, 0.5);
                    border-color: #ffffff !important;
                }
                .hotspot:hover {
                    border-color: rgba(46, 204, 113, 0.5);
                    background: rgba(46, 204, 113, 0.25);
                    color: #2ecc71;
                    cursor: pointer;
                    transform: translate(-50%, -50%) scale(1.15);
                }
            </style>
            <script>
                if (typeof window.toggleFilamentRegion === "undefined") {
                    window.toggleFilamentRegion = function(el, fieldName) {
                        let region = parseInt(el.dataset.region);
                        let wireEl = el;
                        while (wireEl && !wireEl.hasAttribute("wire:id")) {
                            wireEl = wireEl.parentElement;
                        }
                        if (!wireEl) {
                            let allElements = document.querySelectorAll("*");
                            for (let i = 0; i < allElements.length; i++) {
                                if (allElements[i].hasAttribute("wire:id")) {
                                    wireEl = allElements[i];
                                    break;
                                }
                            }
                        }
                        if (wireEl && window.Livewire) {
                            let component = window.Livewire.find(wireEl.getAttribute("wire:id"));
                            if (component) {
                                let current = (component.get("data." + fieldName) || []).map(Number);
                                let idx = current.indexOf(region);
                                if (idx > -1) {
                                    current.splice(idx, 1);
                                } else {
                                    current.push(region);
                                }
                                component.set("data." + fieldName, current);
                            }
                        }
                    };
                }
            </script>
        ';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderBy('date', 'asc')
            ->orderBy('hour', 'asc');
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
