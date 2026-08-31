<?php

namespace App\Helpers;

use App\Models\Request as BookingRequest;
use App\Models\Visit;

class MassageHelper
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
            return new \Illuminate\Support\HtmlString('لم يتم العثور على حجز (طلب) مساج مطابق لعرض التكنيكات.');
        }

        $isMassage = str_contains($request->service_type ?? '', 'مساج');
        if (!$isMassage) {
            return new \Illuminate\Support\HtmlString('هذا الحجز لا يحتوي على خدمة مساج.');
        }

        // Determine style
        $style = 'economy';
        $isFullBody = false;
        if (is_array($request->packages)) {
            if (in_array('intensive', $request->packages)) {
                $style = 'intensive';
                $isFullBody = true;
            } elseif (in_array('economy', $request->packages)) {
                $style = 'economy';
                $isFullBody = true;
            }
        }
        if (!$isFullBody) {
            if (str_contains($request->description ?? '', 'مكثف')) {
                $style = 'intensive';
            }
        }

        // Determine intensity
        $intensity = str_contains($request->description ?? '', 'هارد') ? 'hard' : 'medium';

        // Get selected regions
        $regions = \App\Models\RequestRegion::where('request_id', $request->id)->pluck('region_number')->toArray();

        return self::generateTechniquesHtml($style, $intensity, $regions, $isFullBody);
    }

    public static function renderTechniquesTableForForm(callable $get)
    {
        $bookingType = $get('booking_type') ?: 'وقائية';
        if ($bookingType !== 'وقائية') {
            return new \Illuminate\Support\HtmlString('هذا الحجز لا يحتوي على خدمة مساج.');
        }

        $packages = $get('packages') ?: [];
        $massageRegions = $get('massage_regions') ?: [];
        
        $massageActive = !empty($packages) || !empty($massageRegions);
        if (!$massageActive) {
            return new \Illuminate\Support\HtmlString('هذا الحجز لا يحتوي على خدمة مساج.');
        }

        $style = 'economy';
        $isFullBody = false;
        if (in_array('intensive', $packages)) {
            $style = 'intensive';
            $isFullBody = true;
        } elseif (in_array('economy', $packages)) {
            $style = 'economy';
            $isFullBody = true;
        }
        
        if (!$isFullBody) {
            $massageStyle = $get('massage_style') ?: 'intensive';
            if ($massageStyle === 'intensive') {
                $style = 'intensive';
            }
        }

        $massageIntensity = $get('massage_intensity') ?: 'medium';
        $intensity = ($massageIntensity === 'hard') ? 'hard' : 'medium';

        $regions = array_map('intval', $massageRegions);

        return self::generateTechniquesHtml($style, $intensity, $regions, $isFullBody);
    }

    public static function generateTechniquesHtml($style, $intensity, $regions, $isFullBody)
    {
        $intensiveList = [
            ['m' => 1, 'region' => 1, 'name' => 'الخلفيه الوسطى', 'method' => 'كلوه / قبضه/م', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 2, 'region' => 3, 'name' => 'السمانه', 'method' => 'كلوه / قبضه/م', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 3, 'region' => 4, 'name' => 'الكعب الخارجي', 'method' => 'ابهام', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 4, 'region' => 4, 'name' => 'الكعب الداخلي', 'method' => 'ابهام', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 5, 'region' => 4, 'name' => 'باطن القدم', 'method' => 'كلوه / قبضه/م', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 6, 'region' => 3, 'name' => 'السمانه', 'method' => 'كلوه / قبضه/م', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 7, 'region' => 1, 'name' => 'الخلفيه الوسطى', 'method' => 'ابهام / قبضه', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 8, 'region' => 5, 'name' => 'الخلفيه الوسطى', 'method' => 'كلوه / قبضه/م', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 9, 'region' => 7, 'name' => 'السمانه', 'method' => 'كلوه / قبضه/م', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 10, 'region' => 8, 'name' => 'الكعب الخارجي', 'method' => 'ابهام', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 11, 'region' => 8, 'name' => 'الكعب الداخلي', 'method' => 'ابهام', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 12, 'region' => 8, 'name' => 'باطن القدم', 'method' => 'كلوه / قبضه/م', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 13, 'region' => 7, 'name' => 'السمانه', 'method' => 'كلوه / قبضه/م', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 14, 'region' => 5, 'name' => 'الخلفيه الوسطى', 'method' => 'ابهام / قبضه م', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 15, 'region' => 9, 'name' => 'الجلوتس', 'method' => 'ابهام', 'direction' => 'داخل للخارج', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 16, 'region' => 11, 'name' => 'الالقطنيه المربعه', 'method' => 'كلوه / ابهامين', 'direction' => 'داخل للخارج', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 17, 'region' => 11, 'name' => 'القطنيه الانقباضيه', 'method' => 'كلوه / قبضه/م', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 18, 'region' => 11, 'name' => 'القطنيه المربعه', 'method' => 'ابهام / ابهامين', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 19, 'region' => 11, 'name' => 'القطنيه المحوريه', 'method' => 'ابهام', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 20, 'region' => 10, 'name' => 'الجلوتس', 'method' => 'ابهام', 'direction' => 'داخل للخارج', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 21, 'region' => 12, 'name' => 'القطنيه المربعه', 'method' => 'كلوه / ابهامين', 'direction' => 'داخل للخارج', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 22, 'region' => 12, 'name' => 'القطنيه الانقباضيه', 'method' => 'كلوه / قبضه/م', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 23, 'region' => 12, 'name' => 'القطنيه المربعه', 'method' => 'ابهام / ابهامين', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 24, 'region' => 12, 'name' => 'القطنيه المحوريه', 'method' => 'ابهام', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 25, 'region' => 13, 'name' => 'الابهر', 'method' => 'ابهام / ابهامين', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 26, 'region' => 15, 'name' => 'الترابس العلويه', 'method' => 'قبضه م', 'direction' => 'للخارج', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 27, 'region' => 14, 'name' => 'الابهر', 'method' => 'ابهام / ابهامين', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 28, 'region' => 16, 'name' => 'الترابس العلويه', 'method' => 'قبضه م', 'direction' => 'للخارج', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 29, 'region' => 37, 'name' => 'الرقبة', 'method' => 'اصابع', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 30, 'region' => 17, 'name' => 'الكتف الخلفي', 'method' => 'قبضه م / ابهام', 'direction' => 'داخل للخارج', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 31, 'region' => 17, 'name' => 'الكتف الجانبي', 'method' => 'قبضه م / ابهام', 'direction' => 'داخل للخارج', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 32, 'region' => 18, 'name' => 'الذراع من الابط', 'method' => 'ابهام', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 33, 'region' => 18, 'name' => 'التراي', 'method' => 'كلوه / قبضه/م', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 34, 'region' => 19, 'name' => 'الريست والساعد', 'method' => 'اصابع', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 35, 'region' => 20, 'name' => 'الكتف الخلفي', 'method' => 'قبضه م / ابهام', 'direction' => 'داخل للخارج', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 36, 'region' => 20, 'name' => 'الكتف الجانبي', 'method' => 'قبضه م / ابهام', 'direction' => 'داخل للخارج', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 37, 'region' => 18, 'name' => 'الذراع من الابط', 'method' => 'ابهام', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 38, 'region' => 18, 'name' => 'التراي', 'method' => 'كلوه / قبضه/م', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 39, 'region' => 19, 'name' => 'الريست والساعد', 'method' => 'اصابع', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 40, 'region' => 28, 'name' => 'الاماميه', 'method' => 'كف / قبضه م', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 41, 'region' => 30, 'name' => 'التيبيال', 'method' => 'قبضه/م / ابهام', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 42, 'region' => 30, 'name' => 'التيبيال', 'method' => 'قبضه/م / ابهام', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 43, 'region' => 28, 'name' => 'الاماميه والضامه', 'method' => 'ابهام / ابهامين', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 44, 'region' => 25, 'name' => 'الاماميه', 'method' => 'كف / قبضه م', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 45, 'region' => 27, 'name' => 'التيبيال', 'method' => 'قبضه/م / ابهام', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 46, 'region' => 27, 'name' => 'التيبيال', 'method' => 'قبضه/م / ابهام', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 47, 'region' => 25, 'name' => 'الاماميه والضامه', 'method' => 'ابهام / ابهامين', 'direction' => 'اسفل لاعلى', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 48, 'region' => 31, 'name' => 'الشيست', 'method' => 'كلوه / قبضه/م', 'direction' => 'داخل للخارج', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 49, 'region' => 33, 'name' => 'الكتف الامامي', 'method' => 'قبضه م / ابهام', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 50, 'region' => 34, 'name' => 'الباي', 'method' => 'قبضه م / ابهام', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 51, 'region' => 32, 'name' => 'الشيست', 'method' => 'كلوه / قبضه/م', 'direction' => 'داخل للخارج', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 52, 'region' => 35, 'name' => 'الكتف الامامي', 'method' => 'قبضه م / ابهام', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 53, 'region' => 36, 'name' => 'الباي', 'method' => 'قبضه م / ابهام', 'direction' => 'اعلى لاسفل', 'rep' => 18, 'intensity' => '40%/70%', 'speed' => '10%'],
        ];

        $economyList = [
            ['m' => 1, 'region' => 1, 'name' => 'الخلفيه الوسطى', 'method' => 'كلوه / قبضه/م', 'direction' => 'اعلى لاسفل', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 2, 'region' => 3, 'name' => 'السمانه', 'method' => 'كلوه / قبضه/م', 'direction' => 'اعلى لاسفل', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 3, 'region' => 3, 'name' => 'السمانه', 'method' => 'كلوه / قبضه/م', 'direction' => 'اسفل لاعلى', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 4, 'region' => 1, 'name' => 'الخلفيه الوسطى', 'method' => 'ابهام / قبضه', 'direction' => 'اسفل لاعلى', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 5, 'region' => 5, 'name' => 'الخلفيه الوسطى', 'method' => 'كلوه / قبضه/م', 'direction' => 'اعلى لاسفل', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 6, 'region' => 7, 'name' => 'السمانه', 'method' => 'كلوه / قبضه/م', 'direction' => 'اعلى لاسفل', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 7, 'region' => 7, 'name' => 'السمانه', 'method' => 'كلوه / قبضه/م', 'direction' => 'اسفل لاعلى', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 8, 'region' => 5, 'name' => 'الخلفيه الوسطى', 'method' => 'ابهام / قبضه م', 'direction' => 'اسفل لاعلى', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 9, 'region' => 9, 'name' => 'الجلوتس', 'method' => 'ابهام', 'direction' => 'داخل للخارج', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 10, 'region' => 11, 'name' => 'القطنيه الانقباضيه', 'method' => 'كلوه / قبضه/م', 'direction' => 'اسفل لاعلى', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 11, 'region' => 11, 'name' => 'القطنيه المحوريه', 'method' => 'ابهام', 'direction' => 'اسفل لاعلى', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 12, 'region' => 10, 'name' => 'الجلوتس', 'method' => 'ابهام', 'direction' => 'داخل للخارج', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 13, 'region' => 12, 'name' => 'القطنيه الانقباضيه', 'method' => 'كلوه / قبضه/م', 'direction' => 'اسفل لاعلى', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 14, 'region' => 12, 'name' => 'القطنيه المحوريه', 'method' => 'ابهام', 'direction' => 'اسفل لاعلى', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 15, 'region' => 13, 'name' => 'الابهر', 'method' => 'ابهام / ابهامين', 'direction' => 'اعلى لاسفل', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 16, 'region' => 15, 'name' => 'الترابس العلويه', 'method' => 'قبضه م', 'direction' => 'للخارج', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 17, 'region' => 14, 'name' => 'الابهر', 'method' => 'ابهام / ابهامين', 'direction' => 'اعلى لاسفل', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 18, 'region' => 16, 'name' => 'الترابس العلويه', 'method' => 'قبضه م', 'direction' => 'للخارج', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 19, 'region' => 37, 'name' => 'الرقبة', 'method' => 'اصابع', 'direction' => 'اسفل لاعلى', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 20, 'region' => 17, 'name' => 'الكتف الخلفي', 'method' => 'قبضه م / ابهام', 'direction' => 'داخل للخارج', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 21, 'region' => 24, 'name' => 'الكتف الجانبي', 'method' => 'قبضه م / ابهام', 'direction' => 'داخل للخارج', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 22, 'region' => 18, 'name' => 'التراي', 'method' => 'كلوه / قبضه/م', 'direction' => 'اسفل لاعلى', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 23, 'region' => 19, 'name' => 'الريست والساعد', 'method' => 'اصابع', 'direction' => 'اعلى لاسفل', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 24, 'region' => 20, 'name' => 'الكتف الخلفي', 'method' => 'قبضه م / ابهام', 'direction' => 'داخل للخارج', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 25, 'region' => 23, 'name' => 'الكتف الجانبي', 'method' => 'قبضه م / ابهام', 'direction' => 'داخل للخارج', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 26, 'region' => 21, 'name' => 'التراي', 'method' => 'كلوه / قبضه/م', 'direction' => 'اسفل لاعلى', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 27, 'region' => 22, 'name' => 'الريست والساعد', 'method' => 'اصابع', 'direction' => 'اعلى لاسفل', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 28, 'region' => 28, 'name' => 'الاماميه', 'method' => 'كف / قبضه م', 'direction' => 'اعلى لاسفل', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 29, 'region' => 30, 'name' => 'التيبيال', 'method' => 'قبضه/م / ابهام', 'direction' => 'اعلى لاسفل', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 30, 'region' => 30, 'name' => 'التيبيال', 'method' => 'قبضه/م / ابهام', 'direction' => 'اسفل لاعلى', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 31, 'region' => 28, 'name' => 'الاماميه والضامه', 'method' => 'ابهام / ابهامين', 'direction' => 'اسفل لاعلى', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 32, 'region' => 25, 'name' => 'الاماميه', 'method' => 'كف / قبضه م', 'direction' => 'اعلى لاسفل', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 33, 'region' => 27, 'name' => 'التيبيال', 'method' => 'قبضه/م / ابهام', 'direction' => 'اعلى لاسفل', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 34, 'region' => 27, 'name' => 'التيبيال', 'method' => 'قبضه/م / ابهام', 'direction' => 'اسفل لاعلى', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 35, 'region' => 25, 'name' => 'الاماميه والضامه', 'method' => 'ابهام / ابهامين', 'direction' => 'اسفل لاعلى', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 36, 'region' => 31, 'name' => 'الشيست', 'method' => 'كلوه / قبضه/م', 'direction' => 'داخل للخارج', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 37, 'region' => 33, 'name' => 'الكتف الامامي', 'method' => 'قبضه م / ابهام', 'direction' => 'اعلى لاسفل', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 38, 'region' => 34, 'name' => 'الباي', 'method' => 'قبضه م / ابهام', 'direction' => 'اعلى لاسفل', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 39, 'region' => 32, 'name' => 'الشيست', 'method' => 'كلوه / قبضه/م', 'direction' => 'داخل للخارج', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 40, 'region' => 35, 'name' => 'الكتف الامامي', 'method' => 'قبضه م / ابهام', 'direction' => 'اعلى لاسفل', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
            ['m' => 41, 'region' => 36, 'name' => 'الباي', 'method' => 'قبضه م / ابهام', 'direction' => 'اعلى لاسفل', 'rep' => 14, 'intensity' => '40%/70%', 'speed' => '10%'],
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
            $intensityVal = $tech['intensity'];
            $speed = $tech['speed'];
            
            $rows .= "<tr style='border-bottom:1px solid #ddd;'>
                <td style='padding:6px; border:1px solid #ddd;'>{$m}</td>
                <td style='padding:6px; border:1px solid #ddd; font-weight:bold; color:#e27c1d;'>{$regionNum}</td>
                <td style='padding:6px; border:1px solid #ddd; font-weight:bold; color:#e27c1d;'>{$techName}</td>
                <td style='padding:6px; border:1px solid #ddd;'>{$method}</td>
                <td style='padding:6px; border:1px solid #ddd;'>{$dir}</td>
                <td style='padding:6px; border:1px solid #ddd; font-weight:bold; color:#1da83a;'>{$rep}</td>
                <td style='padding:6px; border:1px solid #ddd;'>{$intensityVal}</td>
                <td style='padding:6px; border:1px solid #ddd;'>{$speed}</td>
            </tr>";
        }

        $styleLabel = ($style === 'intensive') ? 'مكثف (Intensive)' : 'اقتصادي (Economy)';
        $intensityLabel = ($intensity === 'hard') ? 'هارد (Hard - شديد)' : 'ميديم (Medium - متوسط)';

        return new \Illuminate\Support\HtmlString("
            <div style='direction: rtl; text-align: right; line-height: 1.6; font-family: sans-serif; color: #333; background: #fafafa; padding: 15px; border-radius: 8px; border: 1px solid #ddd;'>
                <div style='margin-bottom:15px; font-size:1.05rem;'>
                    <span><strong>باقة الجلسة:</strong> <span style='color: #e27c1d; font-weight:bold;'>{$styleLabel}</span></span> | 
                    <span><strong>الشدة المطلوبة:</strong> <span style='color: #0b7bb0; font-weight:bold;'>{$intensityLabel}</span></span>
                </div>
                <div style='overflow-x: auto; -webkit-overflow-scrolling: touch;'>
                    <table style='width:100%; min-width: 650px; border-collapse:collapse; text-align:center; font-size:0.85rem; border:1px solid #ddd; background: #fff;'>
                        <thead>
                            <tr style='background:#f1f1f1; color:#333; font-weight:bold;'>
                                <th style='padding:8px; border:1px solid #ddd;'>م</th>
                                <th style='padding:8px; border:1px solid #ddd;'>رقم المنطقة</th>
                                <th style='padding:8px; border:1px solid #ddd;'>اسم المنطقة</th>
                                <th style='padding:8px; border:1px solid #ddd;'>ميديام / هارد (التكنيك)</th>
                                <th style='padding:8px; border:1px solid #ddd;'>الاتجاه</th>
                                <th style='padding:8px; border:1px solid #ddd;'>العدد</th>
                                <th style='padding:8px; border:1px solid #ddd;'>الشدة</th>
                                <th style='padding:8px; border:1px solid #ddd;'>السرعة</th>
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

    public static function calculateServiceBasePrices($record)
    {
        $bookingType = $record->booking_type ?? 'وقائية';
        if ($bookingType !== 'وقائية') {
            $price = 0;
            if ($bookingType === 'علاجية') {
                $price = 600;
            } elseif ($bookingType === 'رياضية') {
                $price = 800;
            }
            return [
                'massage' => $price,
                'cracking' => 0,
                'hijama' => 0,
            ];
        }

        $packages = $record->packages ?: [];
        $massageRegions = $record->massage_regions ?: [];
        $massageStyle = $record->massage_style ?: 'intensive';
        $massageIntensity = $record->massage_intensity ?: 'medium';
        $crackingType = $record->cracking_type ?: 'none';
        $crackingStyle = $record->cracking_style ?? 'intensive';
        $crackingRegions = $record->cracking_regions ?: [];
        $hijamaType = $record->hijama_type ?: 'none';
        $hijamaStyle = $record->hijama_style ?: 'intensive';
        $hijamaRegions = $record->hijama_regions ?: [];

        $description = null;
        if ($record instanceof \App\Models\Request) {
            $description = $record->description;
        } elseif ($record instanceof \App\Models\Visit) {
            $description = $record->complaint;
        } elseif (is_object($record) && isset($record->description)) {
            $description = $record->description;
        } elseif (is_object($record) && isset($record->complaint)) {
            $description = $record->complaint;
        } elseif (is_array($record) && isset($record['description'])) {
            $description = $record['description'];
        } elseif (is_array($record) && isset($record['complaint'])) {
            $description = $record['complaint'];
        }

        if (!empty($description)) {
            $parsed = \App\Filament\Resources\RequestResource::parseDescription($description);
            if (empty($packages) && !empty($parsed['packages'])) $packages = $parsed['packages'];
            if (empty($massageRegions) && !empty($parsed['massage_regions'])) $massageRegions = $parsed['massage_regions'];
            if (empty($massageStyle) && !empty($parsed['massage_style'])) $massageStyle = $parsed['massage_style'];
            if (empty($massageIntensity) && !empty($parsed['massage_intensity'])) $massageIntensity = $parsed['massage_intensity'];
            if ($crackingType === 'none' && $parsed['cracking_type'] !== 'none') $crackingType = $parsed['cracking_type'];
            if (empty($crackingStyle) && !empty($parsed['cracking_style'])) $crackingStyle = $parsed['cracking_style'];
            if (empty($crackingRegions) && !empty($parsed['cracking_regions'])) $crackingRegions = $parsed['cracking_regions'];
            if ($hijamaType === 'none' && $parsed['hijama_type'] !== 'none') $hijamaType = $parsed['hijama_type'];
            if (empty($hijamaStyle) && !empty($parsed['hijama_style'])) $hijamaStyle = $parsed['hijama_style'];
            if (empty($hijamaRegions) && !empty($parsed['hijama_regions'])) $hijamaRegions = $parsed['hijama_regions'];
        }

        if (is_string($packages)) $packages = empty($packages) ? [] : array_map('trim', explode(',', $packages));
        elseif (!is_array($packages)) $packages = [];

        if (is_string($massageRegions)) $massageRegions = empty($massageRegions) ? [] : array_map('trim', explode(',', $massageRegions));
        elseif (!is_array($massageRegions)) $massageRegions = [];

        if (is_string($crackingRegions)) $crackingRegions = empty($crackingRegions) ? [] : array_map('trim', explode(',', $crackingRegions));
        elseif (!is_array($crackingRegions)) $crackingRegions = [];

        if (is_string($hijamaRegions)) $hijamaRegions = empty($hijamaRegions) ? [] : array_map('trim', explode(',', $hijamaRegions));
        elseif (!is_array($hijamaRegions)) $hijamaRegions = [];

        // Massage
        $massagePrice = 0;
        $massageActive = (!empty($packages) || !empty($massageRegions));
        if ($massageActive) {
            $regionRepetitionsIntensive = [
                1 => 2, 3 => 2, 4 => 3, 5 => 2, 7 => 2, 8 => 3, 9 => 1, 10 => 1,
                11 => 4, 12 => 4, 13 => 1, 14 => 1, 15 => 1, 16 => 1, 17 => 2, 18 => 4,
                19 => 2, 20 => 2, 25 => 2, 27 => 2, 28 => 2, 30 => 2, 31 => 1,
                32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1, 37 => 1
            ];

            $regionRepetitionsEconomy = [
                1 => 2, 3 => 2, 5 => 2, 7 => 2, 9 => 1, 10 => 1, 11 => 2, 12 => 2,
                13 => 1, 14 => 1, 15 => 1, 16 => 1, 17 => 1, 18 => 2, 19 => 2,
                20 => 1, 21 => 1, 22 => 1, 23 => 1, 24 => 1, 25 => 2, 27 => 2,
                28 => 2, 30 => 2, 31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1,
                36 => 1, 37 => 1
            ];

            $style = 'economy';
            if (in_array('intensive', $packages)) {
                $style = 'intensive';
            } elseif (in_array('economy', $packages)) {
                $style = 'economy';
            } else {
                $style = $massageStyle;
            }

            $repsMap = ($style === 'intensive') ? $regionRepetitionsIntensive : $regionRepetitionsEconomy;
            $totalRepetitions = 0;
            foreach ($massageRegions as $rNum) {
                if (isset($repsMap[$rNum])) {
                    $totalRepetitions += $repsMap[$rNum];
                }
            }

            $isHard = ($massageIntensity === 'hard');
            if (in_array('intensive', $packages)) {
                $massagePrice = $isHard ? 1600.00 + ($totalRepetitions * 17) : 1200.00 + ($totalRepetitions * 13);
            } elseif (in_array('economy', $packages)) {
                $massagePrice = $isHard ? 950.00 + ($totalRepetitions * 17) : 725.00 + ($totalRepetitions * 13);
            } else {
                $massagePrice = $isHard ? ($totalRepetitions * 17) : ($totalRepetitions * 13);
            }
        }

        // Cracking
        $crackingPrice = 0;
        if ($crackingType !== 'none') {
            if ($crackingType === 'whole_body') {
                if ($crackingStyle === 'intensive') {
                    $crackingPrice = 600.00;
                } else {
                    $crackingPrice = 450.00;
                }
            } else {
                $crackingCountMap = \App\Helpers\CrackingHelper::getRegionTechniquesCount($crackingStyle);
                $totalCrackingCount = 0;
                foreach ($crackingRegions as $rNum) {
                    $rNum = (int)$rNum;
                    if (isset($crackingCountMap[$rNum])) {
                        $totalCrackingCount += $crackingCountMap[$rNum];
                    }
                }
                $crackingPrice = $totalCrackingCount * 12.00;
            }
        }

        // Hijama
        $hijamaPrice = 0;
        if ($hijamaType !== 'none') {
            $resolvedHijamaRegions = [];
            if ($hijamaType === 'whole_back') {
                $resolvedHijamaRegions = [1, 3, 5, 7, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 37];
            } elseif ($hijamaType === 'whole_front') {
                $resolvedHijamaRegions = [19, 22, 23, 24, 25, 27, 28, 30, 31, 32, 33, 34, 35, 36];
            } else {
                $resolvedHijamaRegions = array_map('intval', $hijamaRegions);
            }

            $totalCups = 0;
            foreach ($resolvedHijamaRegions as $rNum) {
                $regionCups = 0;
                $reps = $rNum;
                if ($reps === 1) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 2; }
                elseif ($reps === 2) { $regionCups = $hijamaStyle === 'intensive' ? 1 : 1; }
                elseif ($reps === 3) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 4) { $regionCups = $hijamaStyle === 'intensive' ? 4 : 2; }
                elseif ($reps === 5) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 2; }
                elseif ($reps === 6) { $regionCups = $hijamaStyle === 'intensive' ? 1 : 1; }
                elseif ($reps === 7) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 8) { $regionCups = $hijamaStyle === 'intensive' ? 4 : 2; }
                elseif ($reps === 9) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 10) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 11) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 12) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 13) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 14) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 15) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 16) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 17) { $regionCups = $hijamaStyle === 'intensive' ? 1 : 1; }
                elseif ($reps === 18) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 19) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 20) { $regionCups = $hijamaStyle === 'intensive' ? 1 : 1; }
                elseif ($reps === 21) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 22) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 23) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 24) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 25) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 26) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 27) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 28) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 29) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 30) { $regionCups = $hijamaStyle === 'intensive' ? 3 : 1; }
                elseif ($reps === 31) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 32) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 33) { $regionCups = $hijamaStyle === 'intensive' ? 1 : 1; }
                elseif ($reps === 34) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 35) { $regionCups = $hijamaStyle === 'intensive' ? 1 : 1; }
                elseif ($reps === 36) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 37) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 38) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                elseif ($reps === 39) { $regionCups = $hijamaStyle === 'intensive' ? 2 : 1; }
                $totalCups += $regionCups;
            }

            if ($totalCups > 20) {
                $cupPrice = 35;
            } elseif ($totalCups >= 16) {
                $cupPrice = 37;
            } elseif ($totalCups >= 11) {
                $cupPrice = 40;
            } else {
                $cupPrice = 45;
            }
            $hijamaPrice = $totalCups * $cupPrice;
        }

        return [
            'massage' => $massagePrice,
            'cracking' => $crackingPrice,
            'hijama' => $hijamaPrice,
        ];
    }

    public static function getRegionRepetitions($style = 'intensive')
    {
        $regionRepetitionsIntensive = [
            1 => 2, 3 => 2, 4 => 3, 5 => 2, 7 => 2, 8 => 3, 9 => 1, 10 => 1,
            11 => 4, 12 => 4, 13 => 1, 14 => 1, 15 => 1, 16 => 1, 17 => 2, 18 => 4,
            19 => 2, 20 => 2, 25 => 2, 27 => 2, 28 => 2, 30 => 2, 31 => 1,
            32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1, 37 => 1
        ];

        $regionRepetitionsEconomy = [
            1 => 2, 3 => 2, 5 => 2, 7 => 2, 9 => 1, 10 => 1, 11 => 2, 12 => 2,
            13 => 1, 14 => 1, 15 => 1, 16 => 1, 17 => 1, 18 => 2, 19 => 2,
            20 => 1, 21 => 1, 22 => 1, 23 => 1, 24 => 1, 25 => 2, 27 => 2,
            28 => 2, 30 => 2, 31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1,
            36 => 1, 37 => 1
        ];

        return ($style === 'intensive') ? $regionRepetitionsIntensive : $regionRepetitionsEconomy;
    }
}
