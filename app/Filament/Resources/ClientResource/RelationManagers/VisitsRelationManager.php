<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'visits';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('complaint')
                    ->required()
                    ->maxLength(255)->columnSpan('full'),
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
                
                Forms\Components\TextInput::make('improvement_percentage')
                    ->numeric()
                    ->prefix('%'),
                // Forms\Components\Select::make('client_id')
                //     ->relationship(name: 'client', titleAttribute: 'name')
                //     ->required()->columnSpan('full')
                //     ->reactive()
                //     ->afterStateUpdated(function ($state, callable $set, callable $get) {
                //         $client = $get('client_id');
                //        $type= Client::find($client)->type ?? 'D';
                //         $discount = [
                //             "A+" => 100,
                //             "A" => 50,
                //             "B" => 25,
                //             "C" => 10,
                //             "D" => 0,
                //         ];

                //         $set('discount_percentage', (int)($discount[$type]));
                //     }),
                Forms\Components\Select::make('protocol_id')
                        ->relationship(name: 'protocol', titleAttribute: 'title'),
                Forms\Components\TextInput::make('notes')->columnSpan('full'),
                Forms\Components\Repeater::make('Sessions')
                ->relationship('sessions')
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
                        Forms\Components\Textarea::make('notes'),
                    ])->columns(3)->columnSpan('full'),
                Forms\Components\TextInput::make('price')
                    ->readOnly()
                    ->numeric()
                    ->prefix('EGP'),
                Forms\Components\TextInput::make('paid')
                    ->required()
                    ->numeric()
                    ->prefix('EGP'),
                Forms\Components\TextInput::make('due_to')
                    ->numeric()
                    ->prefix('EGP'),
                Forms\Components\TextInput::make('due_from')
                    ->numeric()
                    ->prefix('EGP'),
                Forms\Components\TextInput::make('discount_percentage')
                    ->numeric()
                    ->prefix('%')
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('client_id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('complaint')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money("EGP")
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->searchable(),
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('protocol.title')
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
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
