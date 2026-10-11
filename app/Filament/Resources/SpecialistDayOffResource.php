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

    public static function getCurrentEmployeeId(): ?int
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }

        // 1. Direct match by email
        if (!empty($user->email)) {
            $userEmail = trim(strtolower($user->email));
            $emp = Employee::whereRaw('LOWER(TRIM(email)) = ?', [$userEmail])->first();
            if ($emp) {
                return $emp->id;
            }
        }

        // 2. Direct match by exact name
        if (!empty($user->name)) {
            $userName = trim($user->name);
            $emp = Employee::where('name', $userName)->first();
            if ($emp) {
                return $emp->id;
            }

            // 3. Match by name LIKE
            $emp = Employee::where('name', 'like', '%' . $userName . '%')
                ->orWhereRaw('? LIKE CONCAT("%", name, "%")', [$userName])
                ->first();
            if ($emp) {
                return $emp->id;
            }

            // 4. Match using Arabic normalization across all employees
            $normalizedUserName = self::normalizeArabic($userName);
            if (!empty($normalizedUserName)) {
                $allEmployees = Employee::all();
                foreach ($allEmployees as $employee) {
                    $normalizedEmpName = self::normalizeArabic($employee->name ?? '');
                    if (!empty($normalizedEmpName)) {
                        if ($normalizedUserName === $normalizedEmpName || 
                            str_contains($normalizedEmpName, $normalizedUserName) || 
                            str_contains($normalizedUserName, $normalizedEmpName)) {
                            return $employee->id;
                        }
                    }
                }
            }
        }

        // 5. If still not found and user is specialist, ensure an employee record exists with defaults
        if (self::isSpecialist() && !empty($user->name)) {
            $emp = Employee::firstOrCreate(
                ['name' => trim($user->name)],
                [
                    'email' => $user->email ?? '',
                    'phone' => '00000000000',
                    'age' => 30,
                    'employment_date' => now()->format('Y-m-d'),
                    'work_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
                ]
            );
            return $emp->id;
        }

        return null;
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

                        Forms\Components\DatePicker::make('date')
                            ->label('تاريخ الإجازة')
                            ->required()
                            ->default(now()->format('Y-m-d'))
                            ->minDate(now()->startOfDay())
                            ->reactive(),

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

                Tables\Columns\TextColumn::make('date')
                    ->label('تاريخ الإجازة')
                    ->date('Y-m-d')
                    ->sortable(),

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
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('employee_id')
                    ->label('المختص')
                    ->options(Employee::pluck('name', 'id'))
                    ->visible(!$isSpecialist),

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
