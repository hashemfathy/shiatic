<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpecialistDayOffResource\Pages;
use App\Models\Employee;
use App\Models\SpecialistDayOff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SpecialistDayOffResource extends Resource
{
    protected static ?string $model = SpecialistDayOff::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'إجازات وساعات الراحة للمختصين';

    protected static ?string $modelLabel = 'إجازة مختص';

    protected static ?string $pluralModelLabel = 'إجازات المختصين';

    protected static ?string $navigationGroup = 'إدارة المواعيد والعمليات';

    protected static ?int $navigationSort = 4;

    public static function isSpecialist(): bool
    {
        $type = auth()->user()?->type;
        return in_array($type, ['specialist', 'sr.specialist']);
    }

    public static function normalizeArabic(string $str): string
    {
        $str = trim(mb_strtolower($str));
        $str = preg_replace('/[أإآ]/u', 'ا', $str);
        $str = preg_replace('/ة/u', 'ه', $str);
        $str = preg_replace('/[ىي]/u', 'ي', $str);
        $str = preg_replace('/^(د\.|دكتور|كابتن|أخصائي|اخصائي)\s+/u', '', $str);
        return trim($str);
    }

    public static function getCurrentEmployeeIds(): array
    {
        $user = auth()->user();
        if (!$user) {
            return [];
        }

        $ids = [];

        // 1. Direct match by email
        if (!empty($user->email)) {
            $userEmail = trim(strtolower($user->email));
            $empEmails = Employee::whereRaw('LOWER(TRIM(email)) = ?', [$userEmail])->pluck('id')->toArray();
            $ids = array_merge($ids, $empEmails);
        }

        // 2. Matches by name
        if (!empty($user->name)) {
            $userName = trim($user->name);

            // 2.1 Direct match by exact name
            $empNames = Employee::where('name', $userName)->pluck('id')->toArray();
            $ids = array_merge($ids, $empNames);

            // 2.2 Match by name LIKE
            $empLike = Employee::where('name', 'like', '%' . $userName . '%')
                ->orWhereRaw('? LIKE CONCAT("%", name, "%")', [$userName])
                ->pluck('id')->toArray();
            $ids = array_merge($ids, $empLike);

            // 2.3 Match using Arabic normalization across all employees
            $normalizedUserName = self::normalizeArabic($userName);
            if (!empty($normalizedUserName)) {
                $allEmployees = Employee::all();
                foreach ($allEmployees as $employee) {
                    $normalizedEmpName = self::normalizeArabic($employee->name ?? '');
                    if (!empty($normalizedEmpName)) {
                        if ($normalizedUserName === $normalizedEmpName || 
                            str_contains($normalizedEmpName, $normalizedUserName) || 
                            str_contains($normalizedUserName, $normalizedEmpName)) {
                            $ids[] = $employee->id;
                        }
                    }
                }
            }

            // 2.4 Match using common English-Arabic name mappings
            $nameMappings = [
                'aida' => 'عايده',
                'moustafa' => 'مصطفى',
                'mostafa' => 'مصطفى',
                'abdulrahman' => 'عبد الرحمن',
                'abdo' => 'عبد الرحمن',
                'zaky' => 'ذكي',
                'ahmed' => 'احمد',
            ];

            foreach ($nameMappings as $en => $ar) {
                if (stripos($userName, $en) !== false || stripos($user->email ?? '', $en) !== false) {
                    $matching = Employee::all()->filter(function ($e) use ($ar) {
                        return mb_stripos(self::normalizeArabic($e->name ?? ''), self::normalizeArabic($ar)) !== false;
                    })->pluck('id')->toArray();
                    $ids = array_merge($ids, $matching);
                }
            }
        }

        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        // 3. Fallback: if user is specialist and has no employee record, create one
        if (empty($ids) && self::isSpecialist() && !empty($user->name)) {
            $randomPhone = '00' . str_pad((string)mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
            $emp = Employee::firstOrCreate(
                ['name' => trim($user->name)],
                [
                    'email' => $user->email ?? '',
                    'phone' => $randomPhone,
                    'age' => 30,
                    'employment_date' => now()->format('Y-m-d'),
                    'work_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
                ]
            );
            $ids[] = (int)$emp->id;
        }

        return $ids;
    }

    public static function getCurrentEmployeeId(): ?int
    {
        $ids = self::getCurrentEmployeeIds();
        if (empty($ids)) {
            return null;
        }

        if (count($ids) > 1) {
            $best = Employee::whereIn('id', $ids)
                ->withCount('sessions')
                ->orderBy('sessions_count', 'desc')
                ->first();
            return $best?->id ?? $ids[0];
        }

        return $ids[0];
    }

    public static function canViewAny(): bool
    {
        $type = auth()->user()?->type;
        return in_array($type, ['admin', 'specialist', 'sr.specialist']);
    }

    public static function canCreate(): bool
    {
        $type = auth()->user()?->type;
        return in_array($type, ['admin', 'specialist', 'sr.specialist']);
    }

    public static function canEdit($record): bool
    {
        if (auth()->user()?->type === 'admin') {
            return true;
        }

        if (self::isSpecialist()) {
            $empId = self::getCurrentEmployeeId();
            return $empId ? ($record->employee_id === $empId) : true;
        }

        return false;
    }

    public static function canDelete($record): bool
    {
        return self::canEdit($record);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->type !== 'admin') {
            $employeeId = self::getCurrentEmployeeId();
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        $isAdmin = auth()->user()?->type === 'admin';
        $currentEmployeeId = self::getCurrentEmployeeId();

        $employeeSchema = [];
        if ($isAdmin) {
            $employeeSchema = [
                Forms\Components\Select::make('employee_id')
                    ->label('المختص المعالج')
                    ->options(Employee::pluck('name', 'id'))
                    ->default($currentEmployeeId)
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),
            ];
        } else {
            $employeeSchema = [
                Forms\Components\Hidden::make('employee_id')
                    ->default(fn () => self::getCurrentEmployeeId())
                    ->dehydrated(),
            ];
        }

        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل الإجازة / وقت الراحة')
                    ->description('تحديد يوم وساعات عدم التواجد لمنع استقبال زيارات في هذا الوقت')
                    ->schema([
                        ...$employeeSchema,

                        Forms\Components\Select::make('type')
                            ->label('نوع الإجازة')
                            ->options([
                                'specific_date' => 'يوم محدد (تاريخ معين)',
                                'recurring' => 'يوم متكرر (أسبوعياً أو شهرياً)',
                            ])
                            ->required()
                            ->default('specific_date')
                            ->reactive(),

                        Forms\Components\DatePicker::make('date')
                            ->label('تاريخ الإجازة')
                            ->required(fn (callable $get) => $get('type') === 'specific_date' || empty($get('type')))
                            ->visible(fn (callable $get) => $get('type') === 'specific_date' || empty($get('type')))
                            ->default(now()->format('Y-m-d'))
                            ->minDate(now()->startOfDay())
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->reactive(),

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

                        Forms\Components\Toggle::make('is_full_day')
                            ->label('إجازة يوم كامل (Full Day)')
                            ->helperText('عند التفعيل، لن يكون المختص متاحاً طوال اليوم')
                            ->default(true)
                            ->reactive()
                            ->columnSpanFull(),

                        Forms\Components\TimePicker::make('start_time')
                            ->label('من الساعة (Start Time)')
                            ->seconds(false)
                            ->hidden(fn (callable $get) => (bool)$get('is_full_day'))
                            ->required(fn (callable $get) => !(bool)$get('is_full_day'))
                            ->reactive(),

                        Forms\Components\TimePicker::make('end_time')
                            ->label('إلى الساعة (End Time)')
                            ->seconds(false)
                            ->hidden(fn (callable $get) => (bool)$get('is_full_day'))
                            ->required(fn (callable $get) => !(bool)$get('is_full_day'))
                            ->reactive(),

                        Forms\Components\Textarea::make('reason')
                            ->label('سبب الإجازة / ملاحظات')
                            ->placeholder('ظرف خاص، راحة أسبوعية، ...')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isSpecialist = auth()->user()?->type === 'specialist';

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('المختص')
                    ->searchable()
                    ->sortable()
                    ->visible(!$isSpecialist),

                Tables\Columns\TextColumn::make('type')
                    ->label('نوع الإجازة')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'specific_date' => 'warning',
                        'recurring' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'specific_date' => 'تاريخ محدد',
                        'recurring' => 'متكرر',
                        default => $state ?? 'تاريخ محدد',
                    }),

                Tables\Columns\TextColumn::make('date')
                    ->label('التاريخ المحدد')
                    ->date('Y-m-d')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('اليوم')
                    ->formatStateUsing(fn ($state, SpecialistDayOff $record): string => match ($record->type === 'recurring' ? (int)$state : null) {
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
                    ->formatStateUsing(fn ($state, SpecialistDayOff $record): string => match ($record->type === 'recurring' ? (string)$state : null) {
                        'any' => 'كل أسبوع',
                        'first' => 'الأسبوع الأول',
                        'second' => 'الأسبوع الثاني',
                        'third' => 'الأسبوع الثالث',
                        'fourth' => 'الأسبوع الرابع',
                        'last' => 'الأسبوع الأخير',
                        default => '—',
                    }),

                Tables\Columns\IconColumn::make('is_full_day')
                    ->label('يوم كامل')
                    ->boolean(),

                Tables\Columns\TextColumn::make('timing')
                    ->label('فترة الراحة')
                    ->state(function (SpecialistDayOff $record) {
                        if ($record->is_full_day) {
                            return '🚫 طوال اليوم';
                        }
                        $start = $record->start_time ? date('h:i A', strtotime($record->start_time)) : '';
                        $end = $record->end_time ? date('h:i A', strtotime($record->end_time)) : '';
                        return "⏰ من {$start} إلى {$end}";
                    })
                    ->badge()
                    ->color(fn (SpecialistDayOff $record) => $record->is_full_day ? 'danger' : 'warning'),

                Tables\Columns\TextColumn::make('reason')
                    ->label('السبب / ملاحظات')
                    ->limit(40)
                    ->placeholder('بدون ملاحظات'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('Y-m-d H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('employee_id')
                    ->label('المختص')
                    ->options(Employee::pluck('name', 'id'))
                    ->visible(!$isSpecialist),

                Tables\Filters\SelectFilter::make('type')
                    ->label('نوع الإجازة')
                    ->options([
                        'specific_date' => 'تاريخ محدد',
                        'recurring' => 'متكرر',
                    ]),

                Tables\Filters\SelectFilter::make('day_of_week')
                    ->label('اليوم المتكرر')
                    ->options([
                        0 => 'الأحد',
                        1 => 'الأثنين',
                        2 => 'الثلاثاء',
                        3 => 'الأربعاء',
                        4 => 'الخميس',
                        5 => 'الجمعة',
                        6 => 'السبت',
                    ]),

                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('من تاريخ'),
                        Forms\Components\DatePicker::make('until')->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $date) => $q->whereDate('date', '>=', $date))
                            ->when($data['until'], fn (Builder $q, $date) => $q->whereDate('date', '<=', $date));
                    }),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpecialistDaysOff::route('/'),
            'create' => Pages\CreateSpecialistDayOff::route('/create'),
            'edit' => Pages\EditSpecialistDayOff::route('/{record}/edit'),
        ];
    }
}
