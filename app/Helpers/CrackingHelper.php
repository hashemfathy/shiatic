<?php

namespace App\Helpers;

use App\Models\Request as BookingRequest;
use App\Models\Visit;

class CrackingHelper
{
    public static function renderTechniquesTable($record)
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
            if (!$request) {
                return new \Illuminate\Support\HtmlString('لا توجد تفاصيل حجز متاحة لهذه الزيارة.');
            }
        } elseif ($record instanceof BookingRequest) {
            $request = $record;
        }

        if (!$request) {
            return new \Illuminate\Support\HtmlString('لم يتم العثور على حجز (طلب) كيروبراكتيك مطابق لعرض التكنيكات.');
        }

        $isCracking = $request->cracking_type && $request->cracking_type !== 'none';
        if (!$isCracking) {
            return new \Illuminate\Support\HtmlString('هذا الحجز لا يحتوي على خدمة كيروبراكتيك.');
        }

        // Determine style
        $style = 'intensive';
        $isFullBody = ($request->cracking_type === 'whole_body');

        // Check description or metadata for style
        $desc = $request->description ?? '';
        if (str_contains($desc, 'كيروبراكتيك [اقتصادي') || str_contains($desc, 'اقتصادي')) {
            $style = 'economy';
        }

        // Try getting style from field if available in description parsing or request directly
        if (isset($request->cracking_style)) {
            $style = $request->cracking_style;
        }

        // Get selected regions from database relation
        $regions = \App\Models\RequestRegion::where('request_id', $request->id)
            ->where('region_number', '<=', 5) // Chiropractic regions are 1 to 5
            ->pluck('region_number')
            ->toArray();

        // Fallback to description parse if database relation empty
        if (empty($regions) && !$isFullBody) {
            $parsed = \App\Filament\Resources\RequestResource::parseDescription($desc);
            if (!empty($parsed['cracking_regions'])) {
                $regions = $parsed['cracking_regions'];
            }
        }

        return self::generateTechniquesHtml($style, $regions, $isFullBody);
    }

    public static function renderTechniquesTableForForm(callable $get)
    {
        $bookingType = $get('booking_type') ?: 'وقائية';
        if ($bookingType !== 'وقائية') {
            return new \Illuminate\Support\HtmlString('هذا الحجز لا يحتوي على خدمة كيروبراكتيك.');
        }

        $crackingType = $get('cracking_type') ?: 'none';
        $crackingRegions = $get('cracking_regions') ?: [];
        
        $crackingActive = ($crackingType !== 'none');
        if (!$crackingActive) {
            return new \Illuminate\Support\HtmlString('هذا الحجز لا يحتوي على خدمة كيروبراكتيك.');
        }

        $style = $get('cracking_style') ?: 'intensive';
        $isFullBody = ($crackingType === 'whole_body');
        $regions = array_map('intval', $crackingRegions);

        return self::generateTechniquesHtml($style, $regions, $isFullBody);
    }

    public static function generateTechniquesHtml($style, $regions, $isFullBody)
    {
        $intensiveList = [
            // Region 1 (العنقيه)
            ['m' => 1, 'region' => 1, 'name' => 'الاذن اليمنى', 'method' => 'الجلوس', 'direction' => 'للخارج', 'rep' => 2],
            ['m' => 2, 'region' => 1, 'name' => 'الاذن اليسرى', 'method' => 'الجلوس', 'direction' => 'للخارج', 'rep' => 2],
            ['m' => 3, 'region' => 1, 'name' => 'اماله ١', 'method' => 'الجلوس', 'direction' => 'كتف مرتفع', 'rep' => 2],
            ['m' => 4, 'region' => 1, 'name' => 'اماله ٢', 'method' => 'الجلوس', 'direction' => 'كتف منخفض', 'rep' => 2],
            ['m' => 5, 'region' => 1, 'name' => 'دائري ٣', 'method' => 'النوم على الظهر', 'direction' => 'كتف مرتفع', 'rep' => 2],
            ['m' => 6, 'region' => 1, 'name' => 'دائري ٤', 'method' => 'النوم على الظهر', 'direction' => 'كتف منخفض', 'rep' => 2],
            ['m' => 7, 'region' => 1, 'name' => 'الفك الايمن', 'method' => 'النوم على الظهر', 'direction' => 'امام اسفل', 'rep' => 2],
            ['m' => 8, 'region' => 1, 'name' => 'الفك الايسر', 'method' => 'النوم على الظهر', 'direction' => 'امام اسفل', 'rep' => 2],
            ['m' => 9, 'region' => 1, 'name' => 'الانف ١', 'method' => 'النوم على الظهر', 'direction' => 'الانغلاق', 'rep' => 2],
            ['m' => 10, 'region' => 1, 'name' => 'الانف ٢', 'method' => 'النوم على الظهر', 'direction' => 'التوسع', 'rep' => 2],
            ['m' => 11, 'region' => 1, 'name' => 'ضربة الشمس', 'method' => 'النوم على الظهر', 'direction' => 'للاعلى', 'rep' => 2],
            // Region 2 (الاكتاف والذراعين)
            ['m' => 12, 'region' => 2, 'name' => 'اختناق وتر كتف ايمن', 'method' => 'جلوس', 'direction' => 'للخلف', 'rep' => 3],
            ['m' => 13, 'region' => 2, 'name' => 'اختناق وتر كتف ايسر', 'method' => 'جلوس', 'direction' => 'للخلف', 'rep' => 3],
            ['m' => 14, 'region' => 2, 'name' => 'فرس النبي', 'method' => 'النوم على الوجه', 'direction' => 'للداخل خلف', 'rep' => 3],
            ['m' => 15, 'region' => 2, 'name' => 'سباحه خلفي', 'method' => 'نوم على الوجه', 'direction' => 'للداخل خلف', 'rep' => 3],
            ['m' => 16, 'region' => 2, 'name' => 'يوجا خلفي', 'method' => 'نوم على الوجه', 'direction' => 'للخلف', 'rep' => 3],
            ['m' => 17, 'region' => 2, 'name' => 'اصابع يمنى', 'method' => 'نوم على الظهر', 'direction' => 'ثني', 'rep' => 3],
            ['m' => 18, 'region' => 2, 'name' => 'رسغ ايمن', 'method' => 'نوم على الظهر', 'direction' => 'فصل', 'rep' => 3],
            ['m' => 19, 'region' => 2, 'name' => 'شد اصابع يمنى', 'method' => 'نوم على الظهر', 'direction' => 'للخارج', 'rep' => 3],
            ['m' => 20, 'region' => 2, 'name' => 'رسغ كوع كتف ايمن', 'method' => 'نوم على الظهر', 'direction' => 'نطر', 'rep' => 3],
            ['m' => 21, 'region' => 2, 'name' => 'اصابع يسرى', 'method' => 'نوم على الظهر', 'direction' => 'ثني', 'rep' => 3],
            ['m' => 22, 'region' => 2, 'name' => 'رسغ ايسر', 'method' => 'نوم على الظهر', 'direction' => 'فصل', 'rep' => 3],
            ['m' => 23, 'region' => 2, 'name' => 'شد اصابع يسرى', 'method' => 'نوم على الظهر', 'direction' => 'للخارج', 'rep' => 3],
            ['m' => 24, 'region' => 2, 'name' => 'رسغ كوع كتف ايسر', 'method' => 'نوم على الظهر', 'direction' => 'نطر', 'rep' => 3],
            // Region 3 (الصدريه)
            ['m' => 25, 'region' => 3, 'name' => 'فراشه مغلقه', 'method' => 'الوقوف او الجلوس', 'direction' => 'لالعلى', 'rep' => 5],
            ['m' => 26, 'region' => 3, 'name' => 'فراشه مفتوحه', 'method' => 'الوقوف او الجلوس', 'direction' => 'لالعلى', 'rep' => 5],
            ['m' => 27, 'region' => 3, 'name' => 'فراشه مغلقه فوطه', 'method' => 'الوقوف او الجلوس', 'direction' => 'لالعلى', 'rep' => 5],
            ['m' => 28, 'region' => 3, 'name' => 'فراشه مفتوحه فوطه', 'method' => 'الوقوف او الجلوس', 'direction' => 'لالعلى', 'rep' => 5],
            ['m' => 29, 'region' => 3, 'name' => 'مشد ظهر فوطه', 'method' => 'الجلوس', 'direction' => 'نحوك', 'rep' => 4],
            ['m' => 30, 'region' => 3, 'name' => 'السفليه اليمنى', 'method' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
            ['m' => 31, 'region' => 3, 'name' => 'علويه يمنى', 'method' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
            ['m' => 32, 'region' => 3, 'name' => 'سفليه يسرى', 'method' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
            ['m' => 33, 'region' => 3, 'name' => 'علويه يسرى', 'method' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
            // Region 4 (القطنيه)
            ['m' => 34, 'region' => 4, 'name' => 'دائري ١', 'method' => 'الجلوس او الوقوف', 'direction' => 'قدم المنخفض', 'rep' => 4],
            ['m' => 35, 'region' => 4, 'name' => 'دائري ٢', 'method' => 'الجلوس او الوقوف', 'direction' => 'قدم البروز', 'rep' => 4],
            ['m' => 36, 'region' => 4, 'name' => 'فراشه قطنيه', 'method' => 'الجلوس او الوقوف', 'direction' => 'لالعلى', 'rep' => 4],
            ['m' => 37, 'region' => 4, 'name' => 'الحوض الايمن', 'method' => 'النوم على الوجه', 'direction' => 'اسفل واعلى', 'rep' => 3],
            ['m' => 38, 'region' => 4, 'name' => 'قطنيه سفليه يمنى', 'method' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 2],
            ['m' => 39, 'region' => 4, 'name' => 'قطنيه علويه يمنى', 'method' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 2],
            ['m' => 40, 'region' => 4, 'name' => 'الحوض الايسر', 'method' => 'النوم على الوجه', 'direction' => 'اسفل واعلى', 'rep' => 3],
            ['m' => 41, 'region' => 4, 'name' => 'قطنيه سفليه يسرى', 'method' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 2],
            ['m' => 42, 'region' => 4, 'name' => 'قطنيه علويه يسرى', 'method' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 2],
            ['m' => 43, 'region' => 4, 'name' => 'الحرقفي الايمن', 'method' => 'النوم على الوجه', 'direction' => 'للاسفل', 'rep' => 1],
            ['m' => 44, 'region' => 4, 'name' => 'الحرقفي الايسر', 'method' => 'النوم على الوجه', 'direction' => 'للاسفل', 'rep' => 1],
            // Region 5 (القدمين)
            ['m' => 45, 'region' => 5, 'name' => 'انكل ايمن ١', 'method' => 'النوم على الوجه', 'direction' => 'للداخل', 'rep' => 2],
            ['m' => 46, 'region' => 5, 'name' => 'انكل ايمن ٢', 'method' => 'النوم على الوجه', 'direction' => 'للخارج', 'rep' => 2],
            ['m' => 47, 'region' => 5, 'name' => 'انكل ايمن ٣', 'method' => 'النوم على الوجه', 'direction' => 'اخليس', 'rep' => 2],
            ['m' => 48, 'region' => 5, 'name' => 'اصابع يمنى ١', 'method' => 'النوم على الوجه', 'direction' => 'ثني', 'rep' => 2],
            ['m' => 49, 'region' => 5, 'name' => 'ركبه يمنى١', 'method' => 'النوم على الوجه', 'direction' => 'ثني', 'rep' => 2],
            ['m' => 50, 'region' => 5, 'name' => 'انكل ايسر ١', 'method' => 'النوم على الوجه', 'direction' => 'للداخل', 'rep' => 2],
            ['m' => 51, 'region' => 5, 'name' => 'انكل ايسر ٢', 'method' => 'النوم على الوجه', 'direction' => 'للخارج', 'rep' => 2],
            ['m' => 52, 'region' => 5, 'name' => 'انكل ايسر ٣', 'method' => 'النوم على الوجه', 'direction' => 'اخليس', 'rep' => 2],
            ['m' => 53, 'region' => 5, 'name' => 'اصابع يسرى ١', 'method' => 'النوم على الوجه', 'direction' => 'ثني', 'rep' => 2],
            ['m' => 54, 'region' => 5, 'name' => 'ركبه يسرى ١', 'method' => 'النوم على الوجه', 'direction' => 'ثني', 'rep' => 2],
            ['m' => 55, 'region' => 5, 'name' => 'انكل ايمن ٥', 'method' => 'النوم على الظهر', 'direction' => 'اسفل دعامه', 'rep' => 2],
            ['m' => 56, 'region' => 5, 'name' => 'انكل ايمن ٦', 'method' => 'النوم على الظهر', 'direction' => 'جذب', 'rep' => 2],
            ['m' => 57, 'region' => 5, 'name' => 'اصابع يمنى ٢', 'method' => 'النوم على الظهر', 'direction' => 'تباعد', 'rep' => 2],
            ['m' => 58, 'region' => 5, 'name' => 'انكل ايسر ٥', 'method' => 'النوم على الظهر', 'direction' => 'اسفل دعامه', 'rep' => 2],
            ['m' => 59, 'region' => 5, 'name' => 'انكل ايسر ٦', 'method' => 'النوم على الظهر', 'direction' => 'جذب', 'rep' => 2],
            ['m' => 60, 'region' => 5, 'name' => 'اصابع يسرى ٢', 'method' => 'النوم على الظهر', 'direction' => 'تباعد', 'rep' => 2],
        ];

        $economyList = [
            // Region 1 (العنقيه)
            ['m' => 1, 'region' => 1, 'name' => 'الاذن اليمنى', 'method' => 'الجلوس', 'direction' => 'للخارج', 'rep' => 2],
            ['m' => 2, 'region' => 1, 'name' => 'الاذن اليسرى', 'method' => 'الجلوس', 'direction' => 'للخارج', 'rep' => 2],
            ['m' => 3, 'region' => 1, 'name' => 'اماله ١', 'method' => 'الجلوس', 'direction' => 'كتف مرتفع', 'rep' => 2],
            ['m' => 4, 'region' => 1, 'name' => 'اماله ٢', 'method' => 'الجلوس', 'direction' => 'كتف منخفض', 'rep' => 2],
            ['m' => 5, 'region' => 1, 'name' => 'الفك الايمن', 'method' => 'النوم على الظهر', 'direction' => 'امام اسفل', 'rep' => 2],
            ['m' => 6, 'region' => 1, 'name' => 'الفك الايسر', 'method' => 'النوم على الظهر', 'direction' => 'امام اسفل', 'rep' => 2],
            ['m' => 7, 'region' => 1, 'name' => 'الانف ١', 'method' => 'النوم على الظهر', 'direction' => 'الانغلاق', 'rep' => 2],
            ['m' => 8, 'region' => 1, 'name' => 'الانف ٢', 'method' => 'النوم على الظهر', 'direction' => 'التوسع', 'rep' => 2],
            ['m' => 9, 'region' => 1, 'name' => 'ضربة الشمس', 'method' => 'النوم على الظهر', 'direction' => 'لالعلى', 'rep' => 2],
            // Region 2 (الاكتاف والذراعين)
            ['m' => 10, 'region' => 2, 'name' => 'اختناق وتر كتف ايمن', 'method' => 'جلوس', 'direction' => 'للخلف', 'rep' => 3],
            ['m' => 11, 'region' => 2, 'name' => 'اختناق وتر كتف ايسر', 'method' => 'جلوس', 'direction' => 'للخلف', 'rep' => 3],
            ['m' => 12, 'region' => 2, 'name' => 'سباحه خلفي', 'method' => 'نوم على الوجه', 'direction' => 'للداخل خلف', 'rep' => 3],
            ['m' => 13, 'region' => 2, 'name' => 'اصابع يمنى', 'method' => 'نوم على الظهر', 'direction' => 'ثني', 'rep' => 3],
            ['m' => 14, 'region' => 2, 'name' => 'رسغ ايمن', 'method' => 'نوم على الظهر', 'direction' => 'فصل', 'rep' => 3],
            ['m' => 15, 'region' => 2, 'name' => 'شد اصابع يمنى', 'method' => 'نوم على الظهر', 'direction' => 'للخارج', 'rep' => 3],
            ['m' => 16, 'region' => 2, 'name' => 'رسغ كوع كتف ايمن', 'method' => 'نوم على الظهر', 'direction' => 'نطر', 'rep' => 3],
            ['m' => 17, 'region' => 2, 'name' => 'اصابع يسرى', 'method' => 'نوم على الظهر', 'direction' => 'ثني', 'rep' => 3],
            ['m' => 18, 'region' => 2, 'name' => 'رسغ ايسر', 'method' => 'نوم على الظهر', 'direction' => 'فصل', 'rep' => 3],
            ['m' => 19, 'region' => 2, 'name' => 'شد اصابع يسرى', 'method' => 'نوم على الظهر', 'direction' => 'للخارج', 'rep' => 3],
            ['m' => 20, 'region' => 2, 'name' => 'رسغ كوع كتف ايسر', 'method' => 'نوم على الظهر', 'direction' => 'نطر', 'rep' => 3],
            // Region 3 (الصدريه)
            ['m' => 21, 'region' => 3, 'name' => 'فراشه مغلقه', 'method' => 'الوقوف او الجلوس', 'direction' => 'لالعلى', 'rep' => 5],
            ['m' => 22, 'region' => 3, 'name' => 'فراشه مفتوحه', 'method' => 'الوقوف او الجلوس', 'direction' => 'لالعلى', 'rep' => 5],
            ['m' => 23, 'region' => 3, 'name' => 'فراشه مغلقه فوطه', 'method' => 'الوقوف او الجلوس', 'direction' => 'لالعلى', 'rep' => 5],
            ['m' => 24, 'region' => 3, 'name' => 'السفليه اليمنى', 'method' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
            ['m' => 25, 'region' => 3, 'name' => 'علويه يمنى', 'method' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
            ['m' => 26, 'region' => 3, 'name' => 'سفليه يسرى', 'method' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
            ['m' => 27, 'region' => 3, 'name' => 'علويه يسرى', 'method' => 'النوم على الوجه', 'direction' => 'اعلى فقط', 'rep' => 4],
            // Region 4 (القطنيه)
            ['m' => 28, 'region' => 4, 'name' => 'الاصدقاء', 'method' => 'الجلوس او الوقوف', 'direction' => 'لالعلى', 'rep' => 4],
            ['m' => 29, 'region' => 4, 'name' => 'الحوض الايمن', 'method' => 'النوم على الوجه', 'direction' => 'اسفل واعلى', 'rep' => 3],
            ['m' => 30, 'region' => 4, 'name' => 'قطنيه سفليه يمنى', 'method' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 2],
            ['m' => 31, 'region' => 4, 'name' => 'قطنيه علويه يمنى', 'method' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 2],
            ['m' => 32, 'region' => 4, 'name' => 'الحوض الايسر', 'method' => 'النوم على الوجه', 'direction' => 'اسفل واعلى', 'rep' => 3],
            ['m' => 33, 'region' => 4, 'name' => 'قطنيه سفليه يسرى', 'method' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 2],
            ['m' => 34, 'region' => 4, 'name' => 'قطنيه علويه يسرى', 'method' => 'النوم على الوجه', 'direction' => 'لالعلى', 'rep' => 2],
            ['m' => 35, 'region' => 4, 'name' => 'الحرقفي الايمن', 'method' => 'النوم على الوجه', 'direction' => 'للاسفل', 'rep' => 1],
            ['m' => 36, 'region' => 4, 'name' => 'الحرقفي الايسر', 'method' => 'النوم على الوجه', 'direction' => 'للاسفل', 'rep' => 1],
            // Region 5 (القدمين)
            ['m' => 37, 'region' => 5, 'name' => 'انكل ايمن ١', 'method' => 'النوم على الوجه', 'direction' => 'للداخل', 'rep' => 2],
            ['m' => 38, 'region' => 5, 'name' => 'انكل ايمن ٢', 'method' => 'النوم على الوجه', 'direction' => 'للخارج', 'rep' => 2],
            ['m' => 39, 'region' => 5, 'name' => 'انكل ايمن ٣', 'method' => 'النوم على الوجه', 'direction' => 'اخليس', 'rep' => 2],
            ['m' => 40, 'region' => 5, 'name' => 'اصابع يمنى ١', 'method' => 'النوم على الوجه', 'direction' => 'ثني', 'rep' => 2],
            ['m' => 41, 'region' => 5, 'name' => 'انكل ايسر ١', 'method' => 'النوم على الوجه', 'direction' => 'للداخل', 'rep' => 2],
            ['m' => 42, 'region' => 5, 'name' => 'انكل ايسر ٢', 'method' => 'النوم على الوجه', 'direction' => 'للخارج', 'rep' => 2],
            ['m' => 43, 'region' => 5, 'name' => 'انكل ايسر ٣', 'method' => 'النوم على الوجه', 'direction' => 'اخليس', 'rep' => 2],
            ['m' => 44, 'region' => 5, 'name' => 'اصابع يسرى ١', 'method' => 'النوم على الوجه', 'direction' => 'ثني', 'rep' => 2],
            ['m' => 45, 'region' => 5, 'name' => 'انكل ايمن ٥', 'method' => 'النوم على الظهر', 'direction' => 'اسفل دعامه', 'rep' => 2],
            ['m' => 46, 'region' => 5, 'name' => 'انكل ايمن ٦', 'method' => 'النوم على الظهر', 'direction' => 'جذب', 'rep' => 2],
            ['m' => 47, 'region' => 5, 'name' => 'اصابع يمنى ٢', 'method' => 'النوم على الظهر', 'direction' => 'تباعد', 'rep' => 2],
            ['m' => 48, 'region' => 5, 'name' => 'انكل ايسر ٥', 'method' => 'النوم على الظهر', 'direction' => 'اسفل دعامه', 'rep' => 2],
            ['m' => 49, 'region' => 5, 'name' => 'انكل ايسر ٦', 'method' => 'النوم على الظهر', 'direction' => 'جذب', 'rep' => 2],
            ['m' => 50, 'region' => 5, 'name' => 'اصابع يسرى ٢', 'method' => 'النوم على الظهر', 'direction' => 'تباعد', 'rep' => 2],
        ];

        $techniques = ($style === 'intensive') ? $intensiveList : $economyList;
        $filtered = [];
        if ($isFullBody) {
            $filtered = $techniques;
        } else {
            foreach ($techniques as $tech) {
                if (in_array($tech['region'], $regions)) {
                    $filtered[] = $tech;
                }
            }
        }

        if (empty($filtered)) {
            return new \Illuminate\Support\HtmlString('لا توجد تكنيكات مطابقة للمناطق المحددة.');
        }

        // Render HTML Table
        $rows = '';
        foreach ($filtered as $index => $tech) {
            $m = $index + 1;
            $regionNum = $tech['region'];
            $techName = $tech['name'];
            $method = $tech['method'];
            $dir = $tech['direction'];
            $rep = $tech['rep'];
            
            $rows .= "<tr style='border-bottom:1px solid #ddd;'>
                <td style='padding:6px; border:1px solid #ddd;'>{$m}</td>
                <td style='padding:6px; border:1px solid #ddd; font-weight:bold; color:#e27c1d;'>{$regionNum}</td>
                <td style='padding:6px; border:1px solid #ddd; font-weight:bold; color:#e27c1d;'>{$techName}</td>
                <td style='padding:6px; border:1px solid #ddd;'>{$method}</td>
                <td style='padding:6px; border:1px solid #ddd;'>{$dir}</td>
                <td style='padding:6px; border:1px solid #ddd; font-weight:bold; color:#1da83a;'>{$rep}</td>
            </tr>";
        }

        $styleLabel = ($style === 'intensive') ? 'مكثف (Intensive)' : 'اقتصادي (Economy)';

        return new \Illuminate\Support\HtmlString("
            <div style='direction: rtl; text-align: right; line-height: 1.6; font-family: sans-serif; color: #333; background: #fafafa; padding: 15px; border-radius: 8px; border: 1px solid #ddd;'>
                <div style='margin-bottom:15px; font-size:1.05rem;'>
                    <span><strong>باقة تقويم العمود الفقري:</strong> <span style='color: #e27c1d; font-weight:bold;'>{$styleLabel}</span></span>
                </div>
                <div style='overflow-x: auto; -webkit-overflow-scrolling: touch;'>
                    <table style='width:100%; min-width: 650px; border-collapse:collapse; text-align:center; font-size:0.85rem; border:1px solid #ddd; background: #fff;'>
                        <thead>
                            <tr style='background:#f1f1f1; color:#333; font-weight:bold;'>
                                <th style='padding:8px; border:1px solid #ddd;'>م</th>
                                <th style='padding:8px; border:1px solid #ddd;'>رقم المنطقة</th>
                                <th style='padding:8px; border:1px solid #ddd;'>اسم المنطقة (التكنيك)</th>
                                <th style='padding:8px; border:1px solid #ddd;'>الوضعية</th>
                                <th style='padding:8px; border:1px solid #ddd;'>الاتجاه</th>
                                <th style='padding:8px; border:1px solid #ddd;'>مللي</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$rows}
                        </tbody>
                    </table>
                </div>
            </div>
        ");
    }

    public static function getRegionRepetitions($style = 'intensive')
    {
        // Total reps/thrust units per region (sum of rep column)
        $intensive = [
            1 => 22,
            2 => 39,
            3 => 40,
            4 => 28,
            5 => 32,
        ];

        $economy = [
            1 => 18,
            2 => 33,
            3 => 31,
            4 => 20,
            5 => 28,
        ];

        return ($style === 'intensive') ? $intensive : $economy;
    }

    public static function getRegionTechniquesCount($style = 'intensive')
    {
        // Total techniques per region (count of items in list)
        $intensive = [
            1 => 11,
            2 => 13,
            3 => 9,
            4 => 11,
            5 => 16,
        ];

        $economy = [
            1 => 9,
            2 => 11,
            3 => 7,
            4 => 9,
            5 => 14,
        ];

        return ($style === 'intensive') ? $intensive : $economy;
    }
}
