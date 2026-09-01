<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function canViewAny(): bool
    {
        return auth()->user()?->type == 'admin' ?? false;
    }

    public static function canView($record): bool
    {
        return auth()->user()?->type == 'admin' ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->type == 'admin' ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->type == 'admin' ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->type == 'admin' ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->numeric(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('gender')
                    ->required()
                    ->options([
                        "male" => "Male","female"=> "Female"
                    ]),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255)
                    ->visible(!in_array(auth()->user()?->type,['specialist'])),

                
                Forms\Components\TextInput::make('work'),
                 Forms\Components\TextInput::make('injury')
                    ->maxLength(255),
                Forms\Components\TextInput::make('age')
                    ->required()
                    ->minValue(1)
                    ->numeric(),
                Forms\Components\DatePicker::make('date_of_birth'),
                Forms\Components\TextInput::make('weight')
                    ->required()
                    ->minValue(1)
                    ->numeric(),
                Forms\Components\TextInput::make('governorate'),
                Forms\Components\TextInput::make('is_previous_surgery')
                    ->maxLength(255),
                Forms\Components\TextInput::make('suggested_by'),
                Forms\Components\TextInput::make('doctor_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('doctor_report')
                    ->maxLength(255),
                Forms\Components\TextInput::make('scan_type')
                    ->maxLength(255),
                Forms\Components\TextInput::make('injury_first_date')
                    ->maxLength(255),
                Forms\Components\TextInput::make('injury_reason')
                    ->maxLength(255),
                Forms\Components\TextInput::make('numbness_in_limbs')
                    ->maxLength(255),
                Forms\Components\TextInput::make('most_paineful_position')
                    ->maxLength(255),
                Forms\Components\TextInput::make('most_restful_position')
                    ->maxLength(255),
                Forms\Components\TextInput::make('num_sessions_available')
                    ->maxLength(255),
                Forms\Components\TextInput::make('best_dates_for_sessions')
                    ->maxLength(255),
                Forms\Components\Textarea::make('notes'),
                Forms\Components\Select::make('type')
                    ->options([
                        "A+" => "A+","A"=>"A","B"=> "B","C"=> "C","D"=> "D"
                    ])->default("D"),
                // Forms\Components\DateTimePicker::make('last_call_at'),
               
                
                
                
                
                
                              
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->copyable()
                    ->searchable()
                    ->visible(!in_array(auth()->user()?->type,['specialist'])),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->sortable(),
                // Tables\Columns\TextColumn::make('last_call_at')
                //     ->dateTime()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\Filter::make('birthday')
                    ->form([
                        Forms\Components\Select::make('birth_month')
                            ->label('Birth Month')
                            ->options([
                                '01' => 'January',
                                '02' => 'February',
                                '03' => 'March',
                                '04' => 'April',
                                '05' => 'May',
                                '06' => 'June',
                                '07' => 'July',
                                '08' => 'August',
                                '09' => 'September',
                                '10' => 'October',
                                '11' => 'November',
                                '12' => 'December',
                            ]),
                        Forms\Components\Select::make('birth_day')
                            ->label('Birth Day')
                            ->options(
                                array_combine(
                                    array_map(fn($i) => str_pad($i, 2, '0', STR_PAD_LEFT), range(1, 31)),
                                    range(1, 31)
                                )
                            ),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['birth_month'],
                                fn (Builder $query, $month): Builder => $query->whereMonth('date_of_birth', $month),
                            )
                            ->when(
                                $data['birth_day'],
                                fn (Builder $query, $day): Builder => $query->whereDay('date_of_birth', $day),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['birth_month'] ?? null) {
                            $months = [
                                '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
                                '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
                                '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
                            ];
                            $indicators[] = 'Birth Month: ' . $months[$data['birth_month']];
                        }
                        if ($data['birth_day'] ?? null) {
                            $indicators[] = 'Birth Day: ' . ltrim($data['birth_day'], '0');
                        }
                        return $indicators;
                    })
            ])
            ->actions([
                Tables\Actions\Action::make('new_visit')
                    ->label('New Visit')
                    ->modalHeading(fn (Client $record) => "New Visit for {$record->name}")
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('complaint')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan('full'),
                        Forms\Components\DatePicker::make('date')
                            ->required()
                            ->reactive()
                            ->default(now()),
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
                        Forms\Components\TextInput::make('improvement_percentage')
                            ->numeric()
                            ->prefix('%')
                            ->default(0),
                        Forms\Components\Select::make('protocol_id')
                            ->label('Protocol')
                            ->options(\App\Models\Protocol::pluck('title', 'id'))
                            ->searchable(),
                        Forms\Components\TextInput::make('notes')
                            ->columnSpan('full'),
                        Forms\Components\Repeater::make('Sessions')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->options([
                                        "كيروبراكتيك علاجي" => "كيروبراكتيك علاجي",
                                        "كيروبراكتيك وقائي" => "كيروبراكتيك وقائي",
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
                                        "(كاس)حجامة تشريطية" => "(كاس)حجامة تشريطية",
                                        "حجامة سليكونية" => "حجامة سليكونية",
                                        "حجامة نارية" => "حجامة نارية",
                                        "حجامة خشبية" => "حجامة خشبية",
                                        "حجامة باكيدج اقتصادي ٦ كاسات" => "حجامة باكيدج اقتصادي ٦ كاسات",
                                        "حجامة باكيدج متوسط ١٠ كاسات" => "حجامة باكيدج متوسط ١٠ كاسات",
                                        "حجامة باكيدج مكثف ٢٠ كاس" => "حجامة باكيدج مكثف ٢٠ كاس",
                                        "(30 min)تنشيط عضلي" => "(30 min)تنشيط عضلي",
                                        "مساج وقائي (جزئي)" => "مساج وقائي (جزئي)",
                                        "مساج علاجي (جزئي)" => "مساج علاجي (جزئي)",
                                        "مساج علاجي مكثف (جزئي)" => "مساج علاجي مكثف (جزئي)",
                                        "مساج تقويمي (جزئي)" => "مساج تقويمي (جزئي)",
                                        "مساج تقويمي مكثف (جزئي)" => "مساج تقويمي مكثف (جزئي)",
                                        "مساج جسم كامل وقائي اقتصادي" => "مساج جسم كامل وقائي اقتصادي",
                                        "مساج جسم كامل علاجي" => "مساج جسم كامل علاجي",
                                        "مساج جسم كامل علاجي مكثف" => "مساج جسم كامل علاجي مكثف",
                                        "فحص رياضي" => "فحص رياضي",
                                        "كيروبراكتيك وقائي + مساج وقائي + ٦ كاسات حجامه" => "كيروبراكتيك وقائي + مساج وقائي + ٦ كاسات حجامه",
                                        "كيروبراكتيك علاجي + مساج علاجي + ١٠ كاسات حجامه" => "كيروبراكتيك علاجي + مساج علاجي + ١٠ كاسات حجامه",
                                        "كيروبراكتيك علاجي مكثف + مساج علاجي مكثف + ٢٠ كاس حجامه" => "كيروبراكتيك علاجي مكثف + مساج علاجي مكثف + ٢٠ كاس حجامه",
                                        "كيروبراكتيك وقائي + مساج وقائي كامل الجسم + ٦ حجامه فئه اقتصاديه" => "كيروبراكتيك وقائي + مساج وقائي كامل الجسم + ٦ حجامه فئه اقتصاديه",
                                        "كيروبراكتيك علاجي + مساج علاجي + ١٠ كاسات حجامه" => "كيروبراكتيك علاجي + مساج علاجي + ١٠ كاسات حجامه",
                                        "كيروبراكتيك علاجي مكثف + مساج علاجي مكثف + حجامه ٢٠ كاس حجامه" => "كيروبراكتيك علاجي مكثف + مساج علاجي مكثف + حجامه ٢٠ كاس حجامه",
                                    ])
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $type = $get('type');
                                        $timeOrNum = $get('time_or_num') ?? 1;

                                        $prices = [
                                            "كيروبراكتيك علاجي" => 200,
                                            "كيروبراكتيك وقائي" => 150,
                                            "كيروبراكتيك علاجي مكثف" => 350,
                                            "كيروبراكتيك تقويمي" => 400,
                                            "كيروبراكتيك تقويمي مكثف" => 800,
                                            "(15 min)شيروث" => 70,
                                            "(30 min)تصريف ليمفاوي" => 160,
                                            "ستيل ستون" => 55,
                                            "(15 min)تمارين تقوية" => 40,
                                            "(15 min)توك سين" => 55,
                                            "(1)ابرة تنشيطية" => 150,
                                            "(1)ابرة جافة" => 100,
                                            "(كاس)حجامة تشريطية" => 50,
                                            "حجامة سليكونية" => 20,
                                            "حجامة نارية" => 20,
                                            "حجامة خشبية" => 20,
                                            "حجامة باكيدج اقتصادي ٦ كاسات" => 250,
                                            "حجامة باكيدج متوسط ١٠ كاسات" => 350,
                                            "حجامة باكيدج مكثف ٢٠ كاس" => 600,
                                            "(30 min)تنشيط عضلي" => 150,
                                            "مساج وقائي (جزئي)" => 80,
                                            "مساج علاجي (جزئي)" => 160,
                                            "مساج علاجي مكثف (جزئي)" => 200,
                                            "مساج تقويمي (جزئي)" => 300,
                                            "مساج تقويمي مكثف (جزئي)" => 500,
                                            "مساج جسم كامل وقائي اقتصادي" => 400,
                                            "مساج جسم كامل علاجي" => 600,
                                            "مساج جسم كامل علاجي مكثف" => 800,
                                            "فحص رياضي" => 100,
                                            "كيروبراكتيك وقائي + مساج وقائي + ٦ كاسات حجامه" => 350,
                                            "كيروبراكتيك علاجي + مساج علاجي + ١٠ كاسات حجامه" => 550,
                                            "كيروبراكتيك علاجي مكثف + مساج علاجي مكثف + ٢٠ كاس حجامه" => 950,
                                            "كيروبراكتيك وقائي + مساج وقائي كامل الجسم + ٦ حجامه فئه اقتصاديه" => 600,
                                            "كيروبراكتيك علاجي + مساج علاجي + ١٠ كاسات حجامه" => 950,
                                            "كيروبراكتيك علاجي مكثف + مساج علاجي مكثف + حجامه ٢٠ كاس حجامه" => 1350,
                                        ];

                                        $price = (int)($prices[$type] ?? 0) * (int)$timeOrNum;
                                        $set('price', $price);

                                        // Update total visit price
                                        $sessions = $get('../../Sessions') ?? [];
                                        $discount = (float) ($get('../../discount_percentage') ?? 0);

                                        $total = collect($sessions)->sum('price');
                                        $discountedTotal = $total - ($total * ($discount / 100));

                                        $set('../../price', round($discountedTotal, 2));
                                    }),
                                Forms\Components\TextInput::make('time_or_num')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->step(1)
                                    ->minValue(1)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $type = $get('type');
                                        $timeOrNum = $get('time_or_num') ?? 1;

                                        $prices = [
                                            "كيروبراكتيك علاجي" => 200,
                                            "كيروبراكتيك وقائي" => 150,
                                            "كيروبراكتيك علاجي مكثف" => 350,
                                            "كيروبراكتيك تقويمي" => 400,
                                            "كيروبراكتيك تقويمي مكثف" => 800,
                                            "(15 min)شيروث" => 70,
                                            "(30 min)تصريف ليمفاوي" => 160,
                                            "ستيل ستون" => 55,
                                            "(15 min)تمارين تقوية" => 40,
                                            "(15 min)توك سين" => 55,
                                            "(1)ابرة تنشيطية" => 150,
                                            "(1)ابرة جافة" => 100,
                                            "(كاس)حجامة تشريطية" => 50,
                                            "حجامة سليكونية" => 20,
                                            "حجامة نارية" => 20,
                                            "حجامة خشبية" => 20,
                                            "حجامة باكيدج اقتصادي ٦ كاسات" => 250,
                                            "حجامة باكيدج متوسط ١٠ كاسات" => 350,
                                            "حجامة باكيدج مكثف ٢٠ كاس" => 600,
                                            "(30 min)تنشيط عضلي" => 150,
                                            "مساج وقائي (جزئي)" => 80,
                                            "مساج علاجي (جزئي)" => 160,
                                            "مساج علاجي مكثف (جزئي)" => 200,
                                            "مساج تقويمي (جزئي)" => 300,
                                            "مساج تقويمي مكثف (جزئي)" => 500,
                                            "مساج جسم كامل وقائي اقتصادي" => 400,
                                            "مساج جسم كامل علاجي" => 600,
                                            "مساج جسم كامل علاجي مكثف" => 800,
                                            "فحص رياضي" => 100,
                                            "كيروبراكتيك وقائي + مساج وقائي + ٦ كاسات حجامه" => 350,
                                            "كيروبراكتيك علاجي + مساج علاجي + ١٠ كاسات حجامه" => 550,
                                            "كيروبراكتيك علاجي مكثف + مساج علاجي مكثف + ٢٠ كاس حجامه" => 950,
                                            "كيروبراكتيك وقائي + مساج وقائي كامل الجسم + ٦ حجامه فئه اقتصاديه" => 600,
                                            "كيروبراكتيك علاجي + مساج علاجي + ١٠ كاسات حجامه" => 950,
                                            "كيروبراكتيك علاجي مكثف + مساج علاجي مكثف + حجامه ٢٠ كاس حجامه" => 1350,
                                        ];

                                        $price = (int)($prices[$type] ?? 0) * (int)$timeOrNum;
                                        $set('price', $price);

                                        // Update total visit price
                                        $sessions = $get('../../Sessions') ?? [];
                                        $discount = (float) ($get('../../discount_percentage') ?? 0);

                                        $total = collect($sessions)->sum('price');
                                        $discountedTotal = $total - ($total * ($discount / 100));

                                        $set('../../price', round($discountedTotal, 2));
                                    }),
                                Forms\Components\TextInput::make('price')
                                    ->readOnly()
                                    ->numeric()
                                    ->prefix('EGP'),
                                Forms\Components\TextInput::make('improvement_percentage')
                                    ->numeric()
                                    ->prefix('%'),
                                Forms\Components\Select::make('employee_id')
                                    ->label('Employee')
                                    ->options(function (callable $get) {
                                        $parentDate = $get('../../date');
                                        $dayOfWeek = $parentDate ? strtolower(\Carbon\Carbon::parse($parentDate)->format('l')) : null;

                                        return \App\Models\Employee::all()->mapWithKeys(function ($employee) use ($dayOfWeek) {
                                            $label = $employee->name;
                                            if ($dayOfWeek && in_array($dayOfWeek, $employee->work_days ?? [])) {
                                                $label = "✅ " . $label;
                                            }
                                            return [$employee->id => $label];
                                        })->toArray();
                                    })
                                    ->required(),
                                Forms\Components\Textarea::make('notes'),
                            ])->columns(3)->columnSpan('full'),
                        Forms\Components\TextInput::make('price')
                            ->readOnly()
                            ->numeric()
                            ->prefix('EGP')
                            ->default(0),
                        Forms\Components\TextInput::make('paid')
                            ->required()
                            ->numeric()
                            ->prefix('EGP')
                            ->default(0),
                        Forms\Components\TextInput::make('due_to')
                            ->numeric()
                            ->prefix('EGP')
                            ->default(0),
                        Forms\Components\TextInput::make('due_from')
                            ->numeric()
                            ->prefix('EGP')
                            ->default(0),
                        Forms\Components\TextInput::make('discount_percentage')
                            ->numeric()
                            ->prefix('%')
                            ->default(fn (Client $record) => [
                                'A+' => 100,
                                'A' => 50,
                                'B' => 25,
                                'C' => 10,
                                'D' => 0,
                            ][$record->type] ?? 0)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $sessions = $get('Sessions') ?? [];
                                $discount = (float) $state;

                                // Sum all session prices
                                $total = collect($sessions)->sum('price');

                                // Apply discount
                                $discountedTotal = $total - ($total * ($discount / 100));

                                $set('price', round($discountedTotal, 2));
                            }),
                    ])
                    ->action(function (Client $record, array $data): void {
                        $sessionsData = $data['Sessions'] ?? [];
                        unset($data['Sessions']);

                        $visit = $record->visits()->create($data);

                        foreach ($sessionsData as $session) {
                            $visit->sessions()->create($session);
                        }
                    })
                    ->successNotificationTitle('Visit created successfully.'),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            RelationManagers\VisitsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
