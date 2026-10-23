<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\TextInput;
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
