<?php

namespace App\Filament\Pages;

use App\Models\Client;
use App\Models\Session;
use App\Models\Setting;
use App\Models\Visit;
use App\Filament\Resources\SpecialistDayOffResource;
use App\Filament\Resources\VisitResource;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;

class RoomRentalPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static string $view = 'filament.pages.room-rental';

    protected static ?string $title = 'إيجار غرفة للمختصين';
    protected static ?string $navigationLabel = 'إيجار غرفة';
    protected static ?string $navigationGroup = 'إدارة المواعيد والعمليات';
    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public function mount(): void
    {
        $defaultDuration = (int)Setting::get('room_rental_duration', 60);

        $this->form->fill([
            'duration' => $defaultDuration > 0 ? $defaultDuration : 60,
            'date' => now()->format('Y-m-d'),
            'hour' => now()->format('H:i'),
            'attendees' => [
                [
                    'name' => '',
                    'phone' => '',
                    'gender' => 'male',
                    'age' => null,
                    'notes' => '',
                ]
            ],
            'notes' => '',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('معلومات ورسوم إيجار الغرفة')
                    ->schema([
                        Placeholder::make('rental_info')
                            ->label('تفاصيل الرسوم واحتساب السعر')
                            ->content(function (Get $get) {
                                $pricePerMinute = (float)Setting::get('room_rental_price_per_minute', 5);
                                $duration = (int)($get('duration') ?: Setting::get('room_rental_duration', 60));
                                $totalPrice = round($duration * $pricePerMinute, 2);

                                return new HtmlString("
                                    <div style='direction: rtl; font-size: 1rem; line-height: 1.8; padding: 1.25rem; background: linear-gradient(135deg, rgba(230, 126, 34, 0.08), rgba(230, 126, 34, 0.03)); border: 1px solid rgba(230, 126, 34, 0.25); border-right: 5px solid #ff9d42; border-radius: 10px;'>
                                        <div style='display: flex; flex-wrap: wrap; gap: 1.75rem; align-items: center;'>
                                            <div>⏱️ <strong>مدة الحجز:</strong> <span style='color: #d97706; font-weight: bold;'>{$duration} دقيقة</span></div>
                                            <div>🏷️ <strong>سعر الدقيقة:</strong> <span style='color: #2563eb; font-weight: bold;'>{$pricePerMinute} ج.م / دقيقة</span></div>
                                            <div>💰 <strong>إجمالي السعر المستحق:</strong> <span style='color: #059669; font-weight: 800; font-size: 1.25rem; background: #ecfdf5; padding: 0.25rem 0.75rem; border-radius: 6px; border: 1px solid #a7f3d0;'>{$totalPrice} ج.م</span></div>
                                        </div>
                                        <div style='font-size: 0.88rem; color: #6b7280; margin-top: 0.5rem;'>
                                            * يتم احتساب السعر تلقائياً بناءً على مدة الحجز وسعر الدقيقة المحدد في الإعدادات ({$pricePerMinute} ج.م × {$duration} دقيقة).
                                        </div>
                                    </div>
                                ");
                            })
                            ->columnSpanFull(),

                        TextInput::make('duration')
                            ->label('مدة حجز الغرفة (بالدقائق)')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->default(fn () => (int)Setting::get('room_rental_duration', 60))
                            ->live(debounce: 500)
                            ->helperText('أدخل المدة بالدقائق ليتم احتساب السعر فورياً'),

                        DatePicker::make('date')
                            ->label('تاريخ حجز الغرفة')
                            ->required()
                            ->minDate(now()->toDateString())
                            ->default(now()->toDateString()),

                        TimePicker::make('hour')
                            ->label('وقت / ساعة البدء')
                            ->required()
                            ->default(now()->format('H:i')),
                    ])
                    ->columns(3),

                Section::make('بيانات الأشخاص / العملاء المرافقين (شخص أو أكثر)')
                    ->description('أدخل بيانات الشخص أو الأشخاص المستفيدين من الغرفة')
                    ->schema([
                        Repeater::make('attendees')
                            ->label('قائمة الأشخاص')
                            ->schema([
                                TextInput::make('name')
                                    ->label('الاسم بالكامل')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('phone')
                                    ->label('رقم الهاتف')
                                    ->tel()
                                    ->required()
                                    ->maxLength(255),

                                Select::make('gender')
                                    ->label('الجنس')
                                    ->options([
                                        'male' => 'ذكر',
                                        'female' => 'أنثى',
                                    ])
                                    ->required()
                                    ->default('male'),

                                TextInput::make('age')
                                    ->label('السن')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(120),

                                Textarea::make('notes')
                                    ->label('الشكوى / الملاحظات الخاصة بالشخص')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->minItems(1)
                            ->defaultItems(1)
                            ->addActionLabel('➕ إضافة شخص آخر للغرفة')
                            ->columnSpanFull(),
                    ]),

                Section::make('ملاحظات إضافية')
                    ->schema([
                        Textarea::make('notes')
                            ->label('ملاحظات عامة حول إيجار الغرفة')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();
        $specialistName = $user?->name ?: 'المختص';
        $specialistPhone = $user?->phone ?? '01000000000';

        // 1. Find or create client for the specialist so the visit is booked in their name
        $client = Client::firstOrCreate(
            ['name' => trim($specialistName)],
            [
                'phone' => $specialistPhone,
                'gender' => 'male',
                'code' => (string)now()->timestamp,
            ]
        );

        $duration = max(1, (int)($data['duration'] ?? Setting::get('room_rental_duration', 60)));
        $pricePerMinute = (float)Setting::get('room_rental_price_per_minute', 5);
        $price = round($duration * $pricePerMinute, 2);

        $attendees = $data['attendees'] ?? [];
        $attendeeCount = count($attendees);

        $hourStr = $data['hour'];
        $parts = explode(':', (string)$hourStr);
        $hourFloat = count($parts) >= 2 ? (float)$parts[0] + ((float)$parts[1] / 60) : (float)$hourStr;
        $startMin = \App\Http\Controllers\BookingController::timeToMinutes($data['hour']);

        // Check concurrent capacity before booking
        $attendeeGenders = [];
        foreach ($attendees as $att) {
            $attendeeGenders[] = [
                'gender' => $att['gender'] ?? 'male',
                'duration' => $duration,
            ];
        }
        if (empty($attendeeGenders)) {
            $attendeeGenders[] = [
                'gender' => 'male',
                'duration' => $duration,
            ];
        }

        $bookingController = new \App\Http\Controllers\BookingController();
        if (!$bookingController->isSlotAvailable($data['date'], $attendeeGenders, $startMin, $duration)) {
            Notification::make()
                ->title('عذراً، هذا الوقت غير متاح')
                ->body('تم تجاوز الحد الأقصى للجلسات المتزامنة في هذا الوقت وفقاً لإعدادات النظام.')
                ->danger()
                ->send();
            return;
        }

        $complaint = "إيجار غرفة [المختص: {$specialistName}] | عدد الأشخاص: {$attendeeCount} | المدة: {$duration} دقيقة ({$pricePerMinute} ج.م/دقيقة)";

        $notesList = [];
        foreach ($attendees as $idx => $att) {
            $num = $idx + 1;
            $gLabel = ($att['gender'] ?? 'male') === 'female' ? 'أنثى' : 'ذكر';
            $attDesc = "{$num}. {$att['name']} ({$gLabel}) - هاتف: {$att['phone']}";
            if (!empty($att['age'])) {
                $attDesc .= " - السن: {$att['age']}";
            }
            if (!empty($att['notes'])) {
                $attDesc .= " - ملاحظات: {$att['notes']}";
            }
            $notesList[] = $attDesc;
        }

        if (!empty($data['notes'])) {
            $notesList[] = "ملاحظات إضافية: " . $data['notes'];
        }

        // 2. Create the Visit of type 'حجز غرفة' in specialist name
        $visit = Visit::create([
            'client_id' => $client->id,
            'type' => 'حجز غرفة',
            'complaint' => $complaint,
            'price' => $price,
            'date' => $data['date'],
            'hour' => $hourFloat,
            'notes' => implode("\n", $notesList),
            'paid' => $price,
            'numbness' => 'لا يوجد',
        ]);

        // 3. Create Session with specialist employee if found
        $empId = SpecialistDayOffResource::getCurrentEmployeeId();
        if ($empId) {
            Session::create([
                'visit_id' => $visit->id,
                'employee_id' => $empId,
                'type' => 'حجز غرفة',
                'price' => $price,
            ]);
        }

        Notification::make()
            ->title('تم تسجيل حجز الغرفة بنجاح')
            ->body("تم إنشاء الزيارة برقم #{$visit->id} باسم المختص [{$specialistName}] بإجمالي رسوم {$price} ج.م ({$duration} دقيقة)")
            ->success()
            ->send();

        $this->redirect(VisitResource::getUrl('index'));
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('تأكيد حجز الغرفة')
                ->submit('create'),
        ];
    }

    public static function canAccess(): bool
    {
        $type = auth()->user()?->type;
        return in_array($type, ['admin', 'specialist', 'sr.specialist']);
    }
}
