<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.manage-settings';

    protected static ?string $title = 'إعدادات النظام';
    protected static ?string $navigationLabel = 'إعدادات النظام';

    public ?array $data = [];

    public function mount(): void
    {
        $defaultPricePerMinute = Setting::get('room_rental_price_per_minute');
        if ($defaultPricePerMinute === null) {
            $oldPrice = (float)Setting::get('room_rental_price', 0);
            $oldDuration = (float)Setting::get('room_rental_duration', 0);
            $defaultPricePerMinute = ($oldPrice > 0 && $oldDuration > 0) ? round($oldPrice / $oldDuration, 2) : 5;
        }

        $this->form->fill([
            'urgent_booking_fee' => Setting::get('urgent_booking_fee', 200),
            'max_female_bookings' => Setting::get('max_female_bookings', 1),
            'max_male_bookings' => Setting::get('max_male_bookings', 3),
            'max_total_bookings' => Setting::get('max_total_bookings', 3),
            'min_booking_amount' => Setting::get('min_booking_amount', 2100),
            'room_rental_duration' => Setting::get('room_rental_duration', 60),
            'room_rental_price_per_minute' => $defaultPricePerMinute,
            'kashier_mid' => Setting::get('kashier_mid', config('kashier.mid', 'MID-48236-645')),
            'kashier_api_key' => Setting::get('kashier_api_key', config('kashier.api_key', '93330df5-2d64-4146-8439-d1a97ad0b580')),
            'kashier_secret_key' => Setting::get('kashier_secret_key', config('kashier.secret_key', '87c0f30a0bc90e6806b3fceb1a78e3ab$037f909f6f512ef3e3be35b84b51453195ee2b4672bfadd868ba319e75cea6a07b609c7c9b0220365b0fc85898c327cc')),
            'kashier_mode' => Setting::get('kashier_mode', config('kashier.mode', 'test')),
            'kashier_commission_percent' => Setting::get('kashier_commission_percent', config('kashier.commission_percent', 2.75)),
            'kashier_commission_fixed' => Setting::get('kashier_commission_fixed', config('kashier.commission_fixed', 3.00)),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('urgent_booking_fee')
                    ->label('رسوم الحجز المستعجل (EGP)')
                    ->numeric()
                    ->required()
                    ->default(200),
                TextInput::make('max_female_bookings')
                    ->label('الحد الأقصى لحجوزات السيدات المتزامنة')
                    ->numeric()
                    ->required()
                    ->default(1),
                TextInput::make('max_male_bookings')
                    ->label('الحد الأقصى لحجوزات الرجال المتزامنة')
                    ->numeric()
                    ->required()
                    ->default(3),
                TextInput::make('max_total_bookings')
                    ->label('الحد الأقصى لإجمالي الحجوزات المتزامنة')
                    ->numeric()
                    ->required()
                    ->default(3),
                TextInput::make('min_booking_amount')
                    ->label('الحد الأدنى لقيمة الحجز الإجمالية (EGP)')
                    ->numeric()
                    ->required()
                    ->default(2100),
                TextInput::make('room_rental_price_per_minute')
                    ->label('سعر حجز الغرفة للدقيقة (EGP/دقيقة)')
                    ->numeric()
                    ->required()
                    ->default(5)
                    ->helperText('يتم احتساب إجمالي تكلفة حجز الغرفة بناءً على سعر الدقيقة × مدة الحجز بالدقائق'),
                TextInput::make('room_rental_duration')
                    ->label('المدة الافتراضية لحجز الغرفة (بالدقائق)')
                    ->numeric()
                    ->required()
                    ->default(60)
                    ->helperText('المدة الافتراضية المقترحة في صفحة حجز الغرفة للمختصين'),
                Section::make('💳 إعدادات بوابة الدفع الإلكتروني (Kashier)')
                    ->description('التحكم في بيانات الربط مع كاشير ووضع التشغيل (تجريبي / مباشر)')
                    ->schema([
                        Select::make('kashier_mode')
                            ->label('وضع التشغيل (Mode)')
                            ->options([
                                'test' => 'وضع التجربة (Test / Sandbox)',
                                'live' => 'وضع الإنتاج الحقيقي (Live / Production)',
                            ])
                            ->required()
                            ->default('test'),
                        TextInput::make('kashier_mid')
                            ->label('معرف التاجر (Merchant ID - MID)')
                            ->placeholder('MID-48236-645')
                            ->required(),
                        TextInput::make('kashier_api_key')
                            ->label('مفتاح الدفع (Payment API Key)')
                            ->placeholder('93330df5-2d64-4146-8439-d1a97ad0b580')
                            ->required(),
                        TextInput::make('kashier_secret_key')
                            ->label('المفتاح السري (Secret Key)')
                            ->placeholder('87c0f30a0bc90e6806b3fceb1a78e3ab$...')
                            ->required(),
                        TextInput::make('kashier_commission_percent')
                            ->label('نسبة عمولة كاشير (%)')
                            ->helperText('النسبة المئوية التي تخصمها كاشير وتضاف للمبلغ أونلاين (افتراضي 2.75%)')
                            ->numeric()
                            ->default(2.75)
                            ->suffix('%')
                            ->required(),
                        TextInput::make('kashier_commission_fixed')
                            ->label('العمولة الثابتة لكل معاملة (EGP)')
                            ->helperText('القيمة الثابتة التي تخصمها كاشير وتضاف للمبلغ أونلاين (افتراضي 3 جنيه)')
                            ->numeric()
                            ->default(3.00)
                            ->suffix('EGP')
                            ->required(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        Setting::set('urgent_booking_fee', $state['urgent_booking_fee'] ?? 200);
        Setting::set('max_female_bookings', $state['max_female_bookings'] ?? 1);
        Setting::set('max_male_bookings', $state['max_male_bookings'] ?? 3);
        Setting::set('max_total_bookings', $state['max_total_bookings'] ?? 3);
        Setting::set('min_booking_amount', $state['min_booking_amount'] ?? 2100);
        Setting::set('room_rental_price_per_minute', $state['room_rental_price_per_minute'] ?? 5);
        Setting::set('room_rental_duration', $state['room_rental_duration'] ?? 60);
        Setting::set('kashier_mode', $state['kashier_mode'] ?? 'test');
        Setting::set('kashier_mid', $state['kashier_mid'] ?? 'MID-48236-645');
        Setting::set('kashier_api_key', $state['kashier_api_key'] ?? '');
        Setting::set('kashier_secret_key', $state['kashier_secret_key'] ?? '');
        Setting::set('kashier_commission_percent', $state['kashier_commission_percent'] ?? 2.75);
        Setting::set('kashier_commission_fixed', $state['kashier_commission_fixed'] ?? 3.00);

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('حفظ التعديلات')
                ->submit('save'),
        ];
    }

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->type, ['admin']);
    }
}
