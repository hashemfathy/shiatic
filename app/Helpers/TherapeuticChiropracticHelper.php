<?php

namespace App\Helpers;

use App\Models\Request as BookingRequest;
use App\Models\Visit;

class TherapeuticChiropracticHelper
{
    /**
     * Price per chiropractic technique in EGP
     */
    public static float $pricePerTechnique = 19.0;

    /**
     * Price per chiropractic technique by protocol style in EGP
     */
    public static array $pricePerTechniqueMap = [
        'intensive' => 19.0,
        'economy' => 19.23,
    ];

    /**
     * Price per chiropractic technique by region group and protocol style in EGP
     * Extracted from كيروبراكتيك علاجي.pdf
     */
    public static array $groupPricePerTechniqueMap = [
        'intensive' => [
            1 => 20.0, // منطقة 1 العنقية (20 ج للتكنيك)
            2 => 7.0,  // منطقة 2 الأكتاف والذراعين (7 ج للتكنيك)
            3 => 12.0, // منطقة 3 الصدرية (12 ج للتكنيك)
            4 => 20.0, // منطقة 4 القطنية (20 ج للتكنيك)
            5 => 10.0, // منطقة 5 القدمين والطرف السفلي (10 ج للتكنيك)
        ],
        'economy' => [
            1 => 18.0, // منطقة 1 العنقية (18 ج للتكنيك)
            2 => 7.0,  // منطقة 2 الأكتاف والذراعين (7 ج للتكنيك)
            3 => 12.0, // منطقة 3 الصدرية (12 ج للتكنيك)
            4 => 20.0, // منطقة 4 القطنية (20 ج للتكنيك)
            5 => 10.0, // منطقة 5 القدمين والطرف السفلي (10 ج للتكنيك)
        ],
    ];

    /**
     * Duration per chiropractic technique in minutes (15 seconds = 0.25 min)
     */
    public static float $durationPerTechnique = 0.25;

    /**
     * Map of 5 Main Body Region Groups with assigned region numbers (1 to 39)
     */
    public static array $regionGroups = [
        1 => [
            'name' => 'منطقة 1 العنقية',
            'regions' => [15, 16, 37]
        ],
        2 => [
            'name' => 'منطقة 2 الأكتاف والذراعين',
            'regions' => [17, 18, 19, 20, 21, 22, 23, 24, 33, 34, 35, 36]
        ],
        3 => [
            'name' => 'منطقة 3 الصدرية',
            'regions' => [13, 14]
        ],
        4 => [
            'name' => 'منطقة 4 القطنية',
            'regions' => [9, 10, 11, 12]
        ],
        5 => [
            'name' => 'منطقة 5 القدمين والطرف السفلي',
            'regions' => [1, 2, 3, 4, 5, 6, 7, 8, 25, 26, 27, 28, 29, 30, 31, 32, 38, 39]
        ]
    ];

    /**
     * Total techniques count per region group based on style (intensive vs economy)
     */
    public static array $groupTechniquesMap = [
        'intensive' => [
            1 => 10, // العنقية (10 تكنيكات)
            2 => 10, // الأكتاف والذراعين (10 تكنيكات)
            3 => 10, // الصدرية (10 تكنيكات)
            4 => 11, // القطنية (11 تكنيك)
            5 => 12  // القدمين والطرف السفلي (12 تكنيك)
        ],
        'economy' => [
            1 => 8,  // العنقية (8 تكنيكات)
            2 => 8,  // الأكتاف والذراعين (8 تكنيكات)
            3 => 8,  // الصدرية (8 تكنيكات)
            4 => 8,  // القطنية (8 تكنيكات)
            5 => 8   // القدمين والطرف السفلي (8 تكنيكات)
        ]
    ];

    /**
     * Full detailed techniques catalog extracted from كيروبراكتيك علاجي.pdf
     */
    public static array $techniques = [
        'intensive' => [
            // منطقة 1 العنقية (10 تكنيكات)
            ['group' => 1, 'region' => '16', 'name' => 'الاذن اليمنى', 'position' => 'الجلوس', 'direction' => 'للخارج', 'rep' => 2],
            ['group' => 1, 'region' => '15', 'name' => 'الاذن اليسرى', 'position' => 'الجلوس', 'direction' => 'للخارج', 'rep' => 2],
            ['group' => 1, 'region' => '37', 'name' => 'اماله 1', 'position' => 'الجلوس', 'direction' => 'كتف مرتفع', 'rep' => 2],
            ['group' => 1, 'region' => '37', 'name' => 'اماله 2', 'position' => 'الجلوس', 'direction' => 'كتف منخفض', 'rep' => 2],
            ['group' => 1, 'region' => '37', 'name' => 'دائري 1', 'position' => 'النوم على الوجه', 'direction' => 'كتف مرتفع', 'rep' => 2],
            ['group' => 1, 'region' => '37', 'name' => 'دائري 2', 'position' => 'النوم على الوجه', 'direction' => 'كتف منخفض', 'rep' => 2],
            ['group' => 1, 'region' => '37', 'name' => 'دائري 3', 'position' => 'النوم على الظهر', 'direction' => 'كتف مرتفع', 'rep' => 2],
            ['group' => 1, 'region' => '37', 'name' => 'دائري 4', 'position' => 'النوم على الظهر', 'direction' => 'كتف منخفض', 'rep' => 2],
            ['group' => 1, 'region' => '16', 'name' => 'الفك الايمن', 'position' => 'النوم على الظهر', 'direction' => 'امام اسفل', 'rep' => 2],
            ['group' => 1, 'region' => '15', 'name' => 'الفك الايسر', 'position' => 'النوم على الظهر', 'direction' => 'امام اسفل', 'rep' => 2],

            // منطقة 2 الاكتاف والذراعين (10 تكنيكات)
            ['group' => 2, 'region' => '20', 'name' => 'تيبس كتف ايمن', 'position' => 'جلوس', 'direction' => 'لالعلى', 'rep' => 3],
            ['group' => 2, 'region' => '20', 'name' => 'اختناق وتر كتف ايمن', 'position' => 'جلوس', 'direction' => 'للخلف', 'rep' => 3],
            ['group' => 2, 'region' => '17', 'name' => 'تيبس كتف ايسر', 'position' => 'جلوس', 'direction' => 'لالعلى', 'rep' => 3],
            ['group' => 2, 'region' => '17', 'name' => 'اختناق وتر كتف ايسر', 'position' => 'جلوس', 'direction' => 'للخلف', 'rep' => 3],
            ['group' => 2, 'region' => '22', 'name' => 'رسغ ايمن', 'position' => 'نوم على الظهر', 'direction' => 'فصل', 'rep' => 3],
            ['group' => 2, 'region' => '21', 'name' => 'جولف ايمن', 'position' => 'نوم على الظهر', 'direction' => 'لالعلى', 'rep' => 3],
            ['group' => 2, 'region' => '20/21/22', 'name' => 'رسغ كوع كتف ايمن', 'position' => 'نوم على الظهر', 'direction' => 'نطر', 'rep' => 3],
            ['group' => 2, 'region' => '19', 'name' => 'رسغ ايسر', 'position' => 'نوم على الظهر', 'direction' => 'فصل', 'rep' => 3],
            ['group' => 2, 'region' => '18', 'name' => 'جولف ايسر', 'position' => 'نوم على الظهر', 'direction' => 'لالعلى', 'rep' => 3],
            ['group' => 2, 'region' => '17/18/19', 'name' => 'رسغ كوع كتف ايسر', 'position' => 'نوم على الظهر', 'direction' => 'نطر', 'rep' => 3],

            // منطقة 3 الصدرية (10 تكنيكات)
            ['group' => 3, 'region' => '13/14', 'name' => 'فراشه مغلقه فوطه', 'position' => 'الوقوف او الجلوس', 'direction' => 'لالعلى', 'rep' => 5],
            ['group' => 3, 'region' => '13/14', 'name' => 'فراشه مفتوحه فوطه', 'position' => 'الوقوف او الجلوس', 'direction' => 'لالعلى', 'rep' => 5],
            ['group' => 3, 'region' => '14', 'name' => 'السفليه اليمنى', 'position' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
            ['group' => 3, 'region' => '14', 'name' => 'علويه يمنى', 'position' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
            ['group' => 3, 'region' => '13', 'name' => 'سفليه يسرى', 'position' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
            ['group' => 3, 'region' => '13', 'name' => 'علويه يسرى', 'position' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
            ['group' => 3, 'region' => '14', 'name' => 'تريجر سفلي ايمن', 'position' => 'النوم على الوجه', 'direction' => 'لالسفل', 'rep' => 2],
            ['group' => 3, 'region' => '14', 'name' => 'تريجر علوي ايمن', 'position' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 3],
            ['group' => 3, 'region' => '13', 'name' => 'تريجر سفلي ايسر', 'position' => 'النوم على الوجه', 'direction' => 'لالسفل', 'rep' => 2],
            ['group' => 3, 'region' => '13', 'name' => 'تريجر علوي ايسر', 'position' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 3],

            // منطقة 4 القطنية (11 تكنيك)
            ['group' => 4, 'region' => '11/12', 'name' => 'دائري 1', 'position' => 'الجلوس او الوقوف', 'direction' => 'قدم المنخفض', 'rep' => 4],
            ['group' => 4, 'region' => '11/12', 'name' => 'دائري 2', 'position' => 'الجلوس او الوقوف', 'direction' => 'قدم البروز', 'rep' => 4],
            ['group' => 4, 'region' => '11/12', 'name' => 'فراشه قطنيه', 'position' => 'الجلوس او الوقوف', 'direction' => 'لالعلى', 'rep' => 4],
            ['group' => 4, 'region' => '10', 'name' => 'الحوض الايمن', 'position' => 'النوم على الوجه', 'direction' => 'اسفل واعلى', 'rep' => 3],
            ['group' => 4, 'region' => '12', 'name' => 'قطنيه سفليه يمنى', 'position' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 2],
            ['group' => 4, 'region' => '12', 'name' => 'قطنيه علويه يمنى', 'position' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 2],
            ['group' => 4, 'region' => '9', 'name' => 'الحوض الايسر', 'position' => 'النوم على الوجه', 'direction' => 'اسفل واعلى', 'rep' => 3],
            ['group' => 4, 'region' => '11', 'name' => 'قطنيه سفليه يسرى', 'position' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 2],
            ['group' => 4, 'region' => '11', 'name' => 'قطنيه علويه يسرى', 'position' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 2],
            ['group' => 4, 'region' => '9/10', 'name' => 'شد القدم الطويله', 'position' => 'النوم على الظهر', 'direction' => 'لالسفل', 'rep' => 2],
            ['group' => 4, 'region' => '9/10', 'name' => 'شد القدم القصيره', 'position' => 'النوم على الظهر', 'direction' => 'لالسفل', 'rep' => 4],

            // منطقة 5 القدمين (12 تكنيك)
            ['group' => 5, 'region' => '8', 'name' => 'انكل ايمن 1', 'position' => 'النوم على الوجه', 'direction' => 'للداخل', 'rep' => 2],
            ['group' => 5, 'region' => '8', 'name' => 'انكل ايمن 2', 'position' => 'النوم على الوجه', 'direction' => 'للخارج', 'rep' => 2],
            ['group' => 5, 'region' => '8', 'name' => 'انكل ايمن 3', 'position' => 'النوم على الوجه', 'direction' => 'اخليس', 'rep' => 2],
            ['group' => 5, 'region' => '6', 'name' => 'ركبه يمنى 1', 'position' => 'النوم على الوجه', 'direction' => 'ثني', 'rep' => 2],
            ['group' => 5, 'region' => '4', 'name' => 'انكل ايسر 1', 'position' => 'النوم على الوجه', 'direction' => 'للداخل', 'rep' => 2],
            ['group' => 5, 'region' => '4', 'name' => 'انكل ايسر 2', 'position' => 'النوم على الوجه', 'direction' => 'للخارج', 'rep' => 2],
            ['group' => 5, 'region' => '4', 'name' => 'انكل ايسر 3', 'position' => 'النوم على الوجه', 'direction' => 'اخليس', 'rep' => 2],
            ['group' => 5, 'region' => '2', 'name' => 'ركبه يسرى 1', 'position' => 'النوم على الوجه', 'direction' => 'ثني', 'rep' => 2],
            ['group' => 5, 'region' => '8', 'name' => 'انكل ايمن 6', 'position' => 'النوم على الظهر', 'direction' => 'جذب', 'rep' => 2],
            ['group' => 5, 'region' => '6', 'name' => 'ركبه يمنى 2', 'position' => 'النوم على الظهر', 'direction' => 'تقويم خ', 'rep' => 2],
            ['group' => 5, 'region' => '4', 'name' => 'انكل ايسر 6', 'position' => 'النوم على الظهر', 'direction' => 'جذب', 'rep' => 2],
            ['group' => 5, 'region' => '2', 'name' => 'ركبه يسرى 2', 'position' => 'النوم على الظهر', 'direction' => 'تقويم خ', 'rep' => 2],
        ],
        'economy' => [
            // منطقة 1 العنقية (8 تكنيكات)
            ['group' => 1, 'region' => '16', 'name' => 'الاذن اليمنى', 'position' => 'الجلوس', 'direction' => 'للخارج', 'rep' => 2],
            ['group' => 1, 'region' => '15', 'name' => 'الاذن اليسرى', 'position' => 'الجلوس', 'direction' => 'للخارج', 'rep' => 2],
            ['group' => 1, 'region' => '37', 'name' => 'اماله 1', 'position' => 'الجلوس', 'direction' => 'كتف مرتفع', 'rep' => 2],
            ['group' => 1, 'region' => '37', 'name' => 'اماله 2', 'position' => 'الجلوس', 'direction' => 'كتف منخفض', 'rep' => 2],
            ['group' => 1, 'region' => '37', 'name' => 'دائري 3', 'position' => 'النوم على الظهر', 'direction' => 'كتف مرتفع', 'rep' => 2],
            ['group' => 1, 'region' => '37', 'name' => 'دائري 4', 'position' => 'النوم على الظهر', 'direction' => 'كتف منخفض', 'rep' => 2],
            ['group' => 1, 'region' => '16', 'name' => 'الفك الايمن', 'position' => 'النوم على الظهر', 'direction' => 'امام اسفل', 'rep' => 2],
            ['group' => 1, 'region' => '15', 'name' => 'الفك الايسر', 'position' => 'النوم على الظهر', 'direction' => 'امام اسفل', 'rep' => 2],

            // منطقة 2 الاكتاف والذراعين (8 تكنيكات)
            ['group' => 2, 'region' => '20', 'name' => 'تيبس كتف ايمن', 'position' => 'جلوس', 'direction' => 'لالعلى', 'rep' => 3],
            ['group' => 2, 'region' => '17', 'name' => 'تيبس كتف ايسر', 'position' => 'جلوس', 'direction' => 'لالعلى', 'rep' => 3],
            ['group' => 2, 'region' => '22', 'name' => 'رسغ ايمن', 'position' => 'نوم على الظهر', 'direction' => 'فصل', 'rep' => 3],
            ['group' => 2, 'region' => '21', 'name' => 'جولف ايمن', 'position' => 'نوم على الظهر', 'direction' => 'لالعلى', 'rep' => 3],
            ['group' => 2, 'region' => '20/21/22', 'name' => 'رسغ كوع كتف ايمن', 'position' => 'نوم على الظهر', 'direction' => 'نطر', 'rep' => 3],
            ['group' => 2, 'region' => '19', 'name' => 'رسغ ايسر', 'position' => 'نوم على الظهر', 'direction' => 'فصل', 'rep' => 3],
            ['group' => 2, 'region' => '18', 'name' => 'جولف ايسر', 'position' => 'نوم على الظهر', 'direction' => 'لالعلى', 'rep' => 3],
            ['group' => 2, 'region' => '17/18/19', 'name' => 'رسغ كوع كتف ايسر', 'position' => 'نوم على الظهر', 'direction' => 'نطر', 'rep' => 3],

            // منطقة 3 الصدرية (8 تكنيكات)
            ['group' => 3, 'region' => '14', 'name' => 'السفليه اليمنى', 'position' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
            ['group' => 3, 'region' => '14', 'name' => 'علويه يمنى', 'position' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
            ['group' => 3, 'region' => '13', 'name' => 'سفليه يسرى', 'position' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
            ['group' => 3, 'region' => '13', 'name' => 'علويه يسرى', 'position' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
            ['group' => 3, 'region' => '14', 'name' => 'تريجر سفلي ايمن', 'position' => 'النوم على الوجه', 'direction' => 'لالسفل', 'rep' => 2],
            ['group' => 3, 'region' => '14', 'name' => 'تريجر علوي ايمن', 'position' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 3],
            ['group' => 3, 'region' => '13', 'name' => 'تريجر سفلي ايسر', 'position' => 'النوم على الوجه', 'direction' => 'لالسفل', 'rep' => 2],
            ['group' => 3, 'region' => '13', 'name' => 'تريجر علوي ايسر', 'position' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 3],

            // منطقة 4 القطنية (8 تكنيكات)
            ['group' => 4, 'region' => '10', 'name' => 'الحوض الايمن', 'position' => 'النوم على الوجه', 'direction' => 'اسفل واعلى', 'rep' => 3],
            ['group' => 4, 'region' => '12', 'name' => 'قطنيه سفليه يمنى', 'position' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 2],
            ['group' => 4, 'region' => '12', 'name' => 'قطنيه علويه يمنى', 'position' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 2],
            ['group' => 4, 'region' => '9', 'name' => 'الحوض الايسر', 'position' => 'النوم على الوجه', 'direction' => 'اسفل واعلى', 'rep' => 3],
            ['group' => 4, 'region' => '11', 'name' => 'قطنيه سفليه يسرى', 'position' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 2],
            ['group' => 4, 'region' => '11', 'name' => 'قطنيه علويه يسرى', 'position' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 2],
            ['group' => 4, 'region' => '9/10', 'name' => 'شد القدم الطويله', 'position' => 'النوم على الظهر', 'direction' => 'لالسفل', 'rep' => 2],
            ['group' => 4, 'region' => '9/10', 'name' => 'شد القدم القصيره', 'position' => 'النوم على الظهر', 'direction' => 'لالسفل', 'rep' => 4],

            // منطقة 5 القدمين (8 تكنيكات)
            ['group' => 5, 'region' => '8', 'name' => 'انكل ايمن 1', 'position' => 'النوم على الوجه', 'direction' => 'للداخل', 'rep' => 2],
            ['group' => 5, 'region' => '8', 'name' => 'انكل ايمن 2', 'position' => 'النوم على الوجه', 'direction' => 'للخارج', 'rep' => 2],
            ['group' => 5, 'region' => '8', 'name' => 'انكل ايمن 3', 'position' => 'النوم على الوجه', 'direction' => 'اخليس', 'rep' => 2],
            ['group' => 5, 'region' => '6', 'name' => 'ركبه يمنى 1', 'position' => 'النوم على الوجه', 'direction' => 'ثني', 'rep' => 2],
            ['group' => 5, 'region' => '4', 'name' => 'انكل ايسر 1', 'position' => 'النوم على الوجه', 'direction' => 'للداخل', 'rep' => 2],
            ['group' => 5, 'region' => '4', 'name' => 'انكل ايسر 2', 'position' => 'النوم على الوجه', 'direction' => 'للخارج', 'rep' => 2],
            ['group' => 5, 'region' => '4', 'name' => 'انكل ايسر 3', 'position' => 'النوم على الوجه', 'direction' => 'اخليس', 'rep' => 2],
            ['group' => 5, 'region' => '2', 'name' => 'ركبه يسرى 1', 'position' => 'النوم على الوجه', 'direction' => 'ثني', 'rep' => 2],
        ]
    ];

    /**
     * Get the price per technique for a given style ('intensive' or 'economy')
     */
    public static function getPricePerTechnique(string $style = 'intensive'): float
    {
        $styleKey = ($style === 'economy') ? 'economy' : 'intensive';
        return static::$pricePerTechniqueMap[$styleKey] ?? static::$pricePerTechnique;
    }

    /**
     * Get the region group ID (1 to 5) for a specific region number
     */
    public static function getGroupForRegion(int $regionNumber): int
    {
        foreach (static::$regionGroups as $groupId => $groupInfo) {
            if (in_array((int)$regionNumber, $groupInfo['regions'], true)) {
                return $groupId;
            }
        }
        return 5;
    }

    /**
     * Get all techniques for given group IDs based on style
     * 
     * @param array $groupIds List of active group IDs (1 to 5)
     * @param string $style 'intensive' or 'economy'
     * @return array
     */
    public static function getTechniquesForGroups(array $groupIds, string $style = 'intensive'): array
    {
        $styleKey = ($style === 'economy') ? 'economy' : 'intensive';
        $all = static::$techniques[$styleKey] ?? static::$techniques['intensive'];
        $filtered = [];

        foreach ($all as $item) {
            if (in_array((int)$item['group'], $groupIds, true)) {
                $filtered[] = $item;
            }
        }

        return $filtered;
    }

    /**
     * Calculate total techniques, duration, price and active groups for selected regions
     * 
     * @param array $regions Selected region numbers (1-39)
     * @param string $style 'intensive' or 'economy'
     * @return array
     */
    public static function calculate(array $regions, string $style = 'intensive'): array
    {
        $uniqueRegions = array_unique(array_map('intval', $regions));
        $activeGroupIds = [];

        foreach ($uniqueRegions as $rNum) {
            $groupId = static::getGroupForRegion($rNum);
            if (!in_array($groupId, $activeGroupIds, true)) {
                $activeGroupIds[] = $groupId;
            }
        }

        sort($activeGroupIds);

        $styleKey = ($style === 'economy') ? 'economy' : 'intensive';
        $totalTechniques = 0;
        $rawTotalPrice = 0.0;
        $activeGroupNames = [];

        foreach ($activeGroupIds as $gId) {
            $techCount = static::$groupTechniquesMap[$styleKey][$gId] ?? 0;
            $pricePerTech = static::$groupPricePerTechniqueMap[$styleKey][$gId] ?? (static::$pricePerTechniqueMap[$styleKey] ?? 19.0);
            $totalTechniques += $techCount;
            $rawTotalPrice += ($techCount * $pricePerTech);
            $activeGroupNames[] = static::$regionGroups[$gId]['name'];
        }

        $duration = round($totalTechniques * static::$durationPerTechnique, 2);

        // Apply 15% discount on chiropractic when selecting more than 3 regions (groups)
        $discountAmount = 0.0;
        if (count($activeGroupIds) > 3) {
            $discountAmount = round($rawTotalPrice * 0.15, 2);
        }
        $totalPrice = round($rawTotalPrice - $discountAmount, 2);

        return [
            'active_groups' => $activeGroupIds,
            'active_group_names' => $activeGroupNames,
            'total_techniques' => $totalTechniques,
            'duration_per_technique' => static::$durationPerTechnique,
            'raw_total_price' => $rawTotalPrice,
            'discount_amount' => $discountAmount,
            'total_price' => $totalPrice,
            'duration' => $duration,
            'selected_regions_count' => count($uniqueRegions),
        ];
    }

    /**
     * Render HTML table of chiropractic techniques for therapeutic booking/visit in Filament Admin
     */
    public static function renderChiropracticTechniquesTable($record)
    {
        $request = null;
        if ($record instanceof Visit) {
            if ($record->request_id) {
                $request = BookingRequest::find($record->request_id);
            }
            if (!$request && $record->client) {
                $request = BookingRequest::where('phone', $record->client->phone)
                    ->where('date', $record->date)
                    ->first();
            }
        } elseif ($record instanceof BookingRequest) {
            $request = $record;
        }

        $desc = $request?->description ?? $record->description ?? $record->complaint ?? '';
        if (empty($desc)) {
            return new \Illuminate\Support\HtmlString('');
        }

        // Determine protocol style
        $style = 'intensive';
        if (str_contains($desc, 'اقتصادي')) {
            $style = 'economy';
        }

        // Extract pain regions
        $allRegions = [];
        if (preg_match('/شديد\s*\[([^\]]+)\]/u', $desc, $m)) {
            $str = trim($m[1]);
            if ($str !== 'لا يوجد') {
                $allRegions = array_merge($allRegions, array_filter(array_map('intval', explode(',', $str))));
            }
        }
        if (preg_match('/متوسط\s*\[([^\]]+)\]/u', $desc, $m)) {
            $str = trim($m[1]);
            if ($str !== 'لا يوجد') {
                $allRegions = array_merge($allRegions, array_filter(array_map('intval', explode(',', $str))));
            }
        }

        $allRegions = array_unique($allRegions);
        if (empty($allRegions)) {
            return new \Illuminate\Support\HtmlString('');
        }

        $calc = static::calculate($allRegions, $style);
        $activeGroups = $calc['active_groups'];
        $techniques = static::getTechniquesForGroups($activeGroups, $style);

        if (empty($techniques)) {
            return new \Illuminate\Support\HtmlString('');
        }

        $rowsHtml = '';
        foreach ($techniques as $index => $tech) {
            $counter = $index + 1;
            $groupName = static::$regionGroups[$tech['group']]['name'] ?? "المجموعة {$tech['group']}";

            $rowsHtml .= "
            <tr style='border-bottom: 1px solid #334155; background: rgba(56, 189, 248, 0.03);'>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #94a3b8; font-weight: bold;'>{$counter}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155;'><span style='background: #0284c7; color: #fff; padding: 2px 8px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;'>{$groupName}</span></td>
                <td style='padding: 10px 8px; border: 1px solid #334155; font-weight: bold; color: #ff9d42;'>منطقة {$tech['region']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; font-weight: 600; color: #f8fafc;'>{$tech['name']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #38bdf8;'>{$tech['position']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #cbd5e1;'>{$tech['direction']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; font-weight: bold; color: #34d399;'>{$tech['rep']}</td>
            </tr>";
        }

        $styleLabel = ($style === 'intensive') ? 'البروتوكول المكثف' : 'البروتوكول الاقتصادي';
        $totalCount = count($techniques);
        $totalMinutes = $calc['duration'];
        $totalPrice = number_format($calc['total_price'], 2);

        return new \Illuminate\Support\HtmlString("
        <div style='direction: rtl; text-align: right; font-family: sans-serif; margin-top: 20px;'>
            <div style='background: #0f172a; padding: 14px 18px; border-radius: 12px; border: 1px solid #334155; margin-bottom: 12px; display: flex; flex-wrap: wrap; gap: 15px; align-items: center; justify-content: space-between;'>
                <div>
                    <span style='color: #38bdf8; font-weight: bold; font-size: 1.05rem;'>🦴 تكنيكات الكيروبراكتيك العلاجي المعتمدة ({$styleLabel}):</span>
                </div>
                <div style='display: flex; gap: 8px; flex-wrap: wrap;'>
                    <span style='background: #1e293b; color: #ff9d42; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; border: 1px solid #334155;'>🔢 {$totalCount} تكنيك</span>
                    <span style='background: #1e293b; color: #34d399; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; border: 1px solid #334155;'>⏱️ {$totalMinutes} دقيقة</span>
                    <span style='background: #1e293b; color: #fbbf24; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; border: 1px solid #334155;'>💰 {$totalPrice} ج.م</span>
                </div>
            </div>
            <div style='overflow-x: auto;'>
                <table style='width: 100%; border-collapse: collapse; text-align: center; font-size: 0.85rem; background: #0f172a; color: #f8fafc; border: 1px solid #334155; border-radius: 8px;'>
                    <thead>
                        <tr style='background: #1e293b; color: #38bdf8; border-bottom: 2px solid #334155;'>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>م</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>المجموعة</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>رقم المنطقة</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>اسم التكنيك</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>الوضعية</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>الاتجاه</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>مللي</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$rowsHtml}
                    </tbody>
                </table>
            </div>
        </div>
        ");
    }
}
