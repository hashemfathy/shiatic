<?php

namespace Database\Seeders;

use App\Models\ChiropracticRegion;
use App\Models\ChiropracticTechnique;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TherapeuticChiropracticSeeder extends Seeder
{
    public function run(): void
    {
        // Use transaction to ensure complete setup
        DB::transaction(function () {
            // Clear existing records safely
            ChiropracticTechnique::query()->delete();
            ChiropracticRegion::query()->delete();

            $data = [
                // ==========================================
                // 1. علاجي مكثف (Intensive Protocol)
                // ==========================================
                [
                    'plan_type' => 'intensive',
                    'region_number' => 1,
                    'name' => 'منطقة 1 العنقية',
                    'diagram_numbers' => [15, 16, 37],
                    'price_per_technique' => 13.00,
                    'duration_seconds' => 15,
                    'techniques' => [
                        ['order' => 1, 'target_region_code' => '16', 'name' => 'الاذن اليمنى', 'position' => 'الجلوس', 'direction' => 'للخارج', 'rep' => 2],
                        ['order' => 2, 'target_region_code' => '15', 'name' => 'الاذن اليسرى', 'position' => 'الجلوس', 'direction' => 'للخارج', 'rep' => 2],
                        ['order' => 3, 'target_region_code' => '37', 'name' => 'اماله 1', 'position' => 'الجلوس', 'direction' => 'كتف مرتفع', 'rep' => 2],
                        ['order' => 4, 'target_region_code' => '37', 'name' => 'اماله 2', 'position' => 'الجلوس', 'direction' => 'كتف منخفض', 'rep' => 2],
                        ['order' => 5, 'target_region_code' => '37', 'name' => 'دائري 1', 'position' => 'النوم على الوجه', 'direction' => 'كتف مرتفع', 'rep' => 2],
                        ['order' => 6, 'target_region_code' => '37', 'name' => 'دائري 2', 'position' => 'النوم على الوجه', 'direction' => 'كتف منخفض', 'rep' => 2],
                        ['order' => 7, 'target_region_code' => '37', 'name' => 'دائري 3', 'position' => 'النوم على الظهر', 'direction' => 'كتف مرتفع', 'rep' => 2],
                        ['order' => 8, 'target_region_code' => '37', 'name' => 'دائري 4', 'position' => 'النوم على الظهر', 'direction' => 'كتف منخفض', 'rep' => 2],
                        ['order' => 9, 'target_region_code' => '16', 'name' => 'الفك الايمن', 'position' => 'النوم على الظهر', 'direction' => 'امام اسفل', 'rep' => 2],
                        ['order' => 10, 'target_region_code' => '15', 'name' => 'الفك الايسر', 'position' => 'النوم على الظهر', 'direction' => 'امام اسفل', 'rep' => 2],
                    ]
                ],
                [
                    'plan_type' => 'intensive',
                    'region_number' => 2,
                    'name' => 'منطقة 2 الاكتاف والذراعين',
                    'diagram_numbers' => [17, 18, 19, 20, 21, 22, 23, 24, 33, 34, 35, 36],
                    'price_per_technique' => 7.00,
                    'duration_seconds' => 15,
                    'techniques' => [
                        ['order' => 1, 'target_region_code' => '20', 'name' => 'تيبس كتف ايمن', 'position' => 'جلوس', 'direction' => 'للاعلى', 'rep' => 3],
                        ['order' => 2, 'target_region_code' => '20', 'name' => 'اختناق وتر كتف ايمن', 'position' => 'جلوس', 'direction' => 'للخلف', 'rep' => 3],
                        ['order' => 3, 'target_region_code' => '17', 'name' => 'تيبس كتف ايسر', 'position' => 'جلوس', 'direction' => 'للاعلى', 'rep' => 3],
                        ['order' => 4, 'target_region_code' => '17', 'name' => 'اختناق وتر كتف ايسر', 'position' => 'جلوس', 'direction' => 'للخلف', 'rep' => 3],
                        ['order' => 5, 'target_region_code' => '22', 'name' => 'رسغ ايمن', 'position' => 'نوم على الظهر', 'direction' => 'فصل', 'rep' => 3],
                        ['order' => 6, 'target_region_code' => '21', 'name' => 'جولف ايمن', 'position' => 'نوم على الظهر', 'direction' => 'للاعلى', 'rep' => 3],
                        ['order' => 7, 'target_region_code' => '20/21/22', 'name' => 'رسغ كوع كتف ايمن', 'position' => 'نوم على الظهر', 'direction' => 'نطر', 'rep' => 3],
                        ['order' => 8, 'target_region_code' => '19', 'name' => 'رسغ ايسر', 'position' => 'نوم على الظهر', 'direction' => 'فصل', 'rep' => 3],
                        ['order' => 9, 'target_region_code' => '18', 'name' => 'جولف ايسر', 'position' => 'نوم على الظهر', 'direction' => 'للاعلى', 'rep' => 3],
                        ['order' => 10, 'target_region_code' => '17/18/19', 'name' => 'رسغ كوع كتف ايسر', 'position' => 'نوم على الظهر', 'direction' => 'نطر', 'rep' => 3],
                    ]
                ],
                [
                    'plan_type' => 'intensive',
                    'region_number' => 3,
                    'name' => 'منطقة 3 الصدريه',
                    'diagram_numbers' => [13, 14],
                    'price_per_technique' => 7.00,
                    'duration_seconds' => 15,
                    'techniques' => [
                        ['order' => 1, 'target_region_code' => '13/14', 'name' => 'فراشه مغلقه فوطه', 'position' => 'الوقوف او الجلوس', 'direction' => 'للاعلى', 'rep' => 5],
                        ['order' => 2, 'target_region_code' => '13/14', 'name' => 'فراشه مفتوحه فوطه', 'position' => 'الوقوف او الجلوس', 'direction' => 'للاعلى', 'rep' => 5],
                        ['order' => 3, 'target_region_code' => '14', 'name' => 'السفليه اليمنى', 'position' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
                        ['order' => 4, 'target_region_code' => '14', 'name' => 'علويه يمنى', 'position' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
                        ['order' => 5, 'target_region_code' => '13', 'name' => 'سفليه يسرى', 'position' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
                        ['order' => 6, 'target_region_code' => '13', 'name' => 'علويه يسرى', 'position' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
                        ['order' => 7, 'target_region_code' => '14', 'name' => 'تريجر سفلي ايمن', 'position' => 'النوم على الوجه', 'direction' => 'للاسفل', 'rep' => 2],
                        ['order' => 8, 'target_region_code' => '14', 'name' => 'تريجر علوي ايمن', 'position' => 'النوم على الوجه', 'direction' => 'للاعلى', 'rep' => 3],
                        ['order' => 9, 'target_region_code' => '13', 'name' => 'تريجر سفلي ايسر', 'position' => 'النوم على الوجه', 'direction' => 'للاسفل', 'rep' => 2],
                        ['order' => 10, 'target_region_code' => '13', 'name' => 'تريجر علوي ايسر', 'position' => 'النوم على الوجه', 'direction' => 'للاعلى', 'rep' => 3],
                    ]
                ],
                [
                    'plan_type' => 'intensive',
                    'region_number' => 4,
                    'name' => 'منطقة 4 القطنيه',
                    'diagram_numbers' => [9, 10, 11, 12],
                    'price_per_technique' => 13.00,
                    'duration_seconds' => 15,
                    'techniques' => [
                        ['order' => 1, 'target_region_code' => '11/12', 'name' => 'دائري 1', 'position' => 'الجلوس او الوقوف', 'direction' => 'قدم المنخفض', 'rep' => 4],
                        ['order' => 2, 'target_region_code' => '11/12', 'name' => 'دائري 2', 'position' => 'الجلوس او الوقوف', 'direction' => 'قدم البروز', 'rep' => 4],
                        ['order' => 3, 'target_region_code' => '11/12', 'name' => 'فراشه قطنيه', 'position' => 'الجلوس او الوقوف', 'direction' => 'للاعلى', 'rep' => 4],
                        ['order' => 4, 'target_region_code' => '10', 'name' => 'الحوض الايمن', 'position' => 'النوم على الوجه', 'direction' => 'اسفل واعلى', 'rep' => 3],
                        ['order' => 5, 'target_region_code' => '12', 'name' => 'قطنيه سفليه يمنى', 'position' => 'النوم على الوجه', 'direction' => 'للاعلى', 'rep' => 2],
                        ['order' => 6, 'target_region_code' => '12', 'name' => 'قطنيه علويه يمنى', 'position' => 'النوم على الوجه', 'direction' => 'للاعلى', 'rep' => 2],
                        ['order' => 7, 'target_region_code' => '9', 'name' => 'الحوض الايسر', 'position' => 'النوم على الوجه', 'direction' => 'اسفل واعلى', 'rep' => 3],
                        ['order' => 8, 'target_region_code' => '11', 'name' => 'قطنيه سفليه يسرى', 'position' => 'النوم على الوجه', 'direction' => 'للاعلى', 'rep' => 2],
                        ['order' => 9, 'target_region_code' => '11', 'name' => 'قطنيه علويه يسرى', 'position' => 'النوم على الوجه', 'direction' => 'للاعلى', 'rep' => 2],
                        ['order' => 10, 'target_region_code' => '9/10', 'name' => 'شد القدم الطويله', 'position' => 'النوم على الظهر', 'direction' => 'للاسفل', 'rep' => 2],
                        ['order' => 11, 'target_region_code' => '9/10', 'name' => 'شد القدم القصيره', 'position' => 'النوم على الظهر', 'direction' => 'للاسفل', 'rep' => 4],
                    ]
                ],
                [
                    'plan_type' => 'intensive',
                    'region_number' => 5,
                    'name' => 'منطقة 5 القدمين',
                    'diagram_numbers' => [1, 2, 3, 4, 5, 6, 7, 8, 25, 26, 27, 28, 29, 30],
                    'price_per_technique' => 7.00,
                    'duration_seconds' => 15,
                    'techniques' => [
                        ['order' => 1, 'target_region_code' => '8', 'name' => 'انكل ايمن 1', 'position' => 'النوم على الوجه', 'direction' => 'للداخل', 'rep' => 2],
                        ['order' => 2, 'target_region_code' => '8', 'name' => 'انكل ايمن 2', 'position' => 'النوم على الوجه', 'direction' => 'للخارج', 'rep' => 2],
                        ['order' => 3, 'target_region_code' => '8', 'name' => 'انكل ايمن 3', 'position' => 'النوم على الوجه', 'direction' => 'اخليس', 'rep' => 2],
                        ['order' => 4, 'target_region_code' => '6', 'name' => 'ركبه يمنى 1', 'position' => 'النوم على الوجه', 'direction' => 'ثني', 'rep' => 2],
                        ['order' => 5, 'target_region_code' => '4', 'name' => 'انكل ايسر 1', 'position' => 'النوم على الوجه', 'direction' => 'للداخل', 'rep' => 2],
                        ['order' => 6, 'target_region_code' => '4', 'name' => 'انكل ايسر 2', 'position' => 'النوم على الوجه', 'direction' => 'للخارج', 'rep' => 2],
                        ['order' => 7, 'target_region_code' => '4', 'name' => 'انكل ايسر 3', 'position' => 'النوم على الوجه', 'direction' => 'اخليس', 'rep' => 2],
                        ['order' => 8, 'target_region_code' => '2', 'name' => 'ركبه يسرى 1', 'position' => 'النوم على الوجه', 'direction' => 'ثني', 'rep' => 2],
                        ['order' => 9, 'target_region_code' => '8', 'name' => 'انكل ايمن 6', 'position' => 'النوم على الظهر', 'direction' => 'جذب', 'rep' => 2],
                        ['order' => 10, 'target_region_code' => '6', 'name' => 'ركبه يمنى 2', 'position' => 'النوم على الظهر', 'direction' => 'تقويم خ', 'rep' => 2],
                        ['order' => 11, 'target_region_code' => '4', 'name' => 'انكل ايسر 6', 'position' => 'النوم على الظهر', 'direction' => 'جذب', 'rep' => 2],
                        ['order' => 12, 'target_region_code' => '4', 'name' => 'ركبه يسرى 2', 'position' => 'النوم على الظهر', 'direction' => 'تقويم خ', 'rep' => 2],
                    ]
                ],

                // ==========================================
                // 2. اقتصادي (Economy Protocol)
                // ==========================================
                [
                    'plan_type' => 'economy',
                    'region_number' => 1,
                    'name' => 'منطقة 1 العنقية',
                    'diagram_numbers' => [15, 16, 37],
                    'price_per_technique' => 13.00,
                    'duration_seconds' => 15,
                    'techniques' => [
                        ['order' => 1, 'target_region_code' => '16', 'name' => 'الاذن اليمنى', 'position' => 'الجلوس', 'direction' => 'للخارج', 'rep' => 2],
                        ['order' => 2, 'target_region_code' => '15', 'name' => 'الاذن اليسرى', 'position' => 'الجلوس', 'direction' => 'للخارج', 'rep' => 2],
                        ['order' => 3, 'target_region_code' => '37', 'name' => 'اماله 1', 'position' => 'الجلوس', 'direction' => 'كتف مرتفع', 'rep' => 2],
                        ['order' => 4, 'target_region_code' => '37', 'name' => 'اماله 2', 'position' => 'الجلوس', 'direction' => 'كتف منخفض', 'rep' => 2],
                        ['order' => 5, 'target_region_code' => '37', 'name' => 'دائري 3', 'position' => 'النوم على الظهر', 'direction' => 'كتف مرتفع', 'rep' => 2],
                        ['order' => 6, 'target_region_code' => '37', 'name' => 'دائري 4', 'position' => 'النوم على الظهر', 'direction' => 'كتف منخفض', 'rep' => 2],
                        ['order' => 7, 'target_region_code' => '16', 'name' => 'الفك الايمن', 'position' => 'النوم على الظهر', 'direction' => 'امام اسفل', 'rep' => 2],
                        ['order' => 8, 'target_region_code' => '15', 'name' => 'الفك الايسر', 'position' => 'النوم على الظهر', 'direction' => 'امام اسفل', 'rep' => 2],
                    ]
                ],
                [
                    'plan_type' => 'economy',
                    'region_number' => 2,
                    'name' => 'منطقة 2 الاكتاف والذراعين',
                    'diagram_numbers' => [17, 18, 19, 20, 21, 22, 23, 24, 33, 34, 35, 36],
                    'price_per_technique' => 7.00,
                    'duration_seconds' => 15,
                    'techniques' => [
                        ['order' => 1, 'target_region_code' => '20', 'name' => 'تيبس كتف ايمن', 'position' => 'جلوس', 'direction' => 'للاعلى', 'rep' => 3],
                        ['order' => 2, 'target_region_code' => '17', 'name' => 'تيبس كتف ايسر', 'position' => 'جلوس', 'direction' => 'للاعلى', 'rep' => 3],
                        ['order' => 3, 'target_region_code' => '22', 'name' => 'رسغ ايمن', 'position' => 'نوم على الظهر', 'direction' => 'فصل', 'rep' => 3],
                        ['order' => 4, 'target_region_code' => '21', 'name' => 'جولف ايمن', 'position' => 'نوم على الظهر', 'direction' => 'للاعلى', 'rep' => 3],
                        ['order' => 5, 'target_region_code' => '20/21/22', 'name' => 'رسغ كوع كتف ايمن', 'position' => 'نوم على الظهر', 'direction' => 'نطر', 'rep' => 3],
                        ['order' => 6, 'target_region_code' => '19', 'name' => 'رسغ ايسر', 'position' => 'نوم على الظهر', 'direction' => 'فصل', 'rep' => 3],
                        ['order' => 7, 'target_region_code' => '18', 'name' => 'جولف ايسر', 'position' => 'نوم على الظهر', 'direction' => 'للاعلى', 'rep' => 3],
                        ['order' => 8, 'target_region_code' => '17/18/19', 'name' => 'رسغ كوع كتف ايسر', 'position' => 'نوم على الظهر', 'direction' => 'نطر', 'rep' => 3],
                    ]
                ],
                [
                    'plan_type' => 'economy',
                    'region_number' => 3,
                    'name' => 'منطقة 3 الصدريه',
                    'diagram_numbers' => [13, 14],
                    'price_per_technique' => 7.00,
                    'duration_seconds' => 15,
                    'techniques' => [
                        ['order' => 1, 'target_region_code' => '14', 'name' => 'السفليه اليمنى', 'position' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
                        ['order' => 2, 'target_region_code' => '14', 'name' => 'علويه يمنى', 'position' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
                        ['order' => 3, 'target_region_code' => '13', 'name' => 'سفليه يسرى', 'position' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
                        ['order' => 4, 'target_region_code' => '13', 'name' => 'علويه يسرى', 'position' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
                        ['order' => 5, 'target_region_code' => '14', 'name' => 'تريجر سفلي ايمن', 'position' => 'النوم على الوجه', 'direction' => 'للاسفل', 'rep' => 2],
                        ['order' => 6, 'target_region_code' => '14', 'name' => 'تريجر علوي ايمن', 'position' => 'النوم على الوجه', 'direction' => 'للاعلى', 'rep' => 3],
                        ['order' => 7, 'target_region_code' => '13', 'name' => 'تريجر سفلي ايسر', 'position' => 'النوم على الوجه', 'direction' => 'للاسفل', 'rep' => 2],
                        ['order' => 8, 'target_region_code' => '13', 'name' => 'تريجر علوي ايسر', 'position' => 'النوم على الوجه', 'direction' => 'للاعلى', 'rep' => 3],
                    ]
                ],
                [
                    'plan_type' => 'economy',
                    'region_number' => 4,
                    'name' => 'منطقة 4 القطنيه',
                    'diagram_numbers' => [9, 10, 11, 12],
                    'price_per_technique' => 13.00,
                    'duration_seconds' => 15,
                    'techniques' => [
                        ['order' => 1, 'target_region_code' => '10', 'name' => 'الحوض الايمن', 'position' => 'النوم على الوجه', 'direction' => 'اسفل واعلى', 'rep' => 3],
                        ['order' => 2, 'target_region_code' => '12', 'name' => 'قطنيه سفليه يمنى', 'position' => 'النوم على الوجه', 'direction' => 'للاعلى', 'rep' => 2],
                        ['order' => 3, 'target_region_code' => '12', 'name' => 'قطنيه علويه يمنى', 'position' => 'النوم على الوجه', 'direction' => 'للاعلى', 'rep' => 2],
                        ['order' => 4, 'target_region_code' => '9', 'name' => 'الحوض الايسر', 'position' => 'النوم على الوجه', 'direction' => 'اسفل واعلى', 'rep' => 3],
                        ['order' => 5, 'target_region_code' => '11', 'name' => 'قطنيه سفليه يسرى', 'position' => 'النوم على الوجه', 'direction' => 'للاعلى', 'rep' => 2],
                        ['order' => 6, 'target_region_code' => '11', 'name' => 'قطنيه علويه يسرى', 'position' => 'النوم على الوجه', 'direction' => 'للاعلى', 'rep' => 2],
                        ['order' => 7, 'target_region_code' => '9/10', 'name' => 'شد القدم الطويله', 'position' => 'النوم على الظهر', 'direction' => 'للاسفل', 'rep' => 2],
                        ['order' => 8, 'target_region_code' => '9/10', 'name' => 'شد القدم القصيره', 'position' => 'النوم على الظهر', 'direction' => 'للاسفل', 'rep' => 4],
                    ]
                ],
                [
                    'plan_type' => 'economy',
                    'region_number' => 5,
                    'name' => 'منطقة 5 القدمين',
                    'diagram_numbers' => [1, 2, 3, 4, 5, 6, 7, 8, 25, 26, 27, 28, 29, 30],
                    'price_per_technique' => 7.00,
                    'duration_seconds' => 15,
                    'techniques' => [
                        ['order' => 1, 'target_region_code' => '8', 'name' => 'انكل ايمن 1', 'position' => 'النوم على الوجه', 'direction' => 'للداخل', 'rep' => 2],
                        ['order' => 2, 'target_region_code' => '8', 'name' => 'انكل ايمن 2', 'position' => 'النوم على الوجه', 'direction' => 'للخارج', 'rep' => 2],
                        ['order' => 3, 'target_region_code' => '8', 'name' => 'انكل ايمن 3', 'position' => 'النوم على الوجه', 'direction' => 'اخليس', 'rep' => 2],
                        ['order' => 4, 'target_region_code' => '6', 'name' => 'ركبه يمنى 1', 'position' => 'النوم على الوجه', 'direction' => 'ثني', 'rep' => 2],
                        ['order' => 5, 'target_region_code' => '4', 'name' => 'انكل ايسر 1', 'position' => 'النوم على الوجه', 'direction' => 'للداخل', 'rep' => 2],
                        ['order' => 6, 'target_region_code' => '4', 'name' => 'انكل ايسر 2', 'position' => 'النوم على الوجه', 'direction' => 'للخارج', 'rep' => 2],
                        ['order' => 7, 'target_region_code' => '4', 'name' => 'انكل ايسر 3', 'position' => 'النوم على الوجه', 'direction' => 'اخليس', 'rep' => 2],
                        ['order' => 8, 'target_region_code' => '2', 'name' => 'ركبه يسرى 1', 'position' => 'النوم على الوجه', 'direction' => 'ثني', 'rep' => 2],
                    ]
                ],
            ];

            foreach ($data as $item) {
                $techniques = $item['techniques'];
                unset($item['techniques']);

                $region = ChiropracticRegion::create($item);

                foreach ($techniques as $tech) {
                    $tech['chiropractic_region_id'] = $region->id;
                    $tech['is_active'] = true;
                    ChiropracticTechnique::create($tech);
                }
            }
        });
    }
}
