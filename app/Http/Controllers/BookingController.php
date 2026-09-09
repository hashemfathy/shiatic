<?php

namespace App\Http\Controllers;

use App\Models\Request as BookingRequest;
use App\Models\RequestRegion;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // List clickable sections
    public function index()
    {
        return view('booking.index');
    }

    // Interactive booking form
    public function form()
    {
        $urgentBookingFee = (int)\App\Models\Setting::get('urgent_booking_fee', 200);
        $minBookingAmount = (int)\App\Models\Setting::get('min_booking_amount', 2100);
        return view('booking.form', compact('urgentBookingFee', 'minBookingAmount'));
    }

    // Store the booking request
    public function store(Request $request)
    {
        if ($request->input('active_tab') === 'علاجية' || $request->input('booking_type') === 'علاجية') {
            return $this->storeTherapeutic($request);
        }

        $request->validate([
            'date' => 'required|date',
            'time' => 'required|string',
            'user_agreement' => 'required|string|in:موافق',
            'is_urgent' => 'nullable|boolean',
            'coupon_code' => 'nullable|string',
            'attendees' => 'required|array|min:1',
            'attendees.*.name' => 'required|string|max:255',
            'attendees.*.phone' => 'required|string|max:255',
            'attendees.*.gender' => 'required|string|in:male,female',
            'attendees.*.booking_type' => 'required|string|in:وقائية,علاجية,رياضية',
            'attendees.*.packages' => 'nullable|array',
            'attendees.*.packages.*' => 'string|in:intensive,economy',
            'attendees.*.regions' => 'nullable|array',
            'attendees.*.regions.*' => 'integer|between:1,39',
            'attendees.*.treatment_style' => 'nullable|string|in:intensive,economy',
            'attendees.*.massage_intensity' => 'nullable|string|in:medium,hard',
            'attendees.*.cracking_type' => 'nullable|string|in:none,whole_body,regions',
            'attendees.*.cracking_regions' => 'nullable|array',
            'attendees.*.cracking_regions.*' => 'integer|between:1,5',
            'attendees.*.hijama_type' => 'nullable|string|in:none,whole_back,whole_front,regions',
            'attendees.*.hijama_style' => 'nullable|string|in:intensive,economy',
            'attendees.*.hijama_regions' => 'nullable|array',
            'attendees.*.hijama_regions.*' => 'integer|between:1,39',
        ]);

        $bookingDate = $request->input('date');
        $bookingTime = $request->input('time');
        $isUrgent = $request->boolean('is_urgent', false);
        $userAgreement = $request->input('user_agreement');

        // Check date in past
        $today = date('Y-m-d');
        if ($bookingDate < $today) {
            return redirect()->back()->withInput()->withErrors(['date' => 'لا يمكن حجز موعد في الماضي.']);
        }

        // Check 40-minute lead time validation (cross-midnight absolute timestamp check)
        $bookingTimestamp = strtotime($bookingDate . ' ' . $bookingTime);
        if (($bookingTimestamp - time()) < (40 * 60)) {
            return redirect()->back()->withInput()->withErrors(['time' => 'يجب أن يكون موعد الحجز بعد 40 دقيقة من الآن على الأقل.']);
        }

        $processedAttendees = [];
        $totalSessionsPrice = 0;
        $totalGroupDuration = 0;
        $groupGendersAndDurations = [];

        foreach ($request->input('attendees') as $index => $attData) {
            $bookingType = $attData['booking_type'];
            $packages = $attData['packages'] ?? [];
            $selectedRegions = $attData['regions'] ?? [];
            $treatmentStyle = $attData['treatment_style'] ?? 'intensive';
            $massageIntensity = $attData['massage_intensity'] ?? 'medium';
            $crackingType = $attData['cracking_type'] ?? 'none';
            $crackingStyle = $attData['cracking_style'] ?? 'intensive';
            $crackingRegions = $attData['cracking_regions'] ?? [];
            $hijamaType = $attData['hijama_type'] ?? 'none';
            $hijamaStyle = $attData['hijama_style'] ?? 'intensive';
            $hijamaRegions = $attData['hijama_regions'] ?? [];

            $style = 'economy';
            if (in_array('intensive', $packages)) {
                $style = 'intensive';
            } elseif (in_array('economy', $packages)) {
                $style = 'economy';
            } else {
                $style = $treatmentStyle;
            }
            $regionRepetitions = \App\Helpers\MassageHelper::getRegionRepetitions($style);

            if ($bookingType === 'وقائية') {
                $massageActive = !empty($packages) || !empty($selectedRegions);
                $crackingActive = ($crackingType === 'whole_body' || $crackingType === 'regions');
                $hijamaActive = ($hijamaType !== 'none');

                if (!$massageActive && !$crackingActive && !$hijamaActive) {
                    return redirect()->back()->withInput()->withErrors(["attendees.{$index}.packages" => 'يرجى اختيار خدمة واحدة على الأقل للشخص رقم ' . ($index + 1)]);
                }
                if ($crackingActive && $crackingType === 'regions' && empty($crackingRegions)) {
                    return redirect()->back()->withInput()->withErrors(["attendees.{$index}.cracking_regions" => 'يرجى تحديد منطقة واحدة على الأقل من صورة تقويم العمود الفقري للشخص رقم ' . ($index + 1)]);
                }
                if ($hijamaActive && $hijamaType === 'regions' && empty($hijamaRegions)) {
                    return redirect()->back()->withInput()->withErrors(["attendees.{$index}.hijama_regions" => 'يرجى تحديد منطقة واحدة على الأقل من صورة الحجامة للشخص رقم ' . ($index + 1)]);
                }
            } else {
                $massageActive = false;
                $crackingActive = false;
                $hijamaActive = false;
                $packages = [];
                $selectedRegions = [];
                $crackingRegions = [];
                $hijamaRegions = [];
            }

            $validRegions = [];
            foreach ($selectedRegions as $rNum) {
                $rNum = (int)$rNum;
                if (isset($regionRepetitions[$rNum])) {
                    $validRegions[] = $rNum;
                }
            }

            // Calculate pricing
            $pricing = \App\Filament\Resources\RequestResource::calculatePricing([
                'booking_type' => $bookingType,
                'packages' => $packages,
                'massage_regions' => $validRegions,
                'massage_style' => $treatmentStyle,
                'massage_intensity' => $massageIntensity,
                'cracking_type' => $crackingType,
                'cracking_style' => $crackingStyle,
                'cracking_regions' => $crackingRegions,
                'hijama_type' => $hijamaType,
                'hijama_style' => $hijamaStyle,
                'hijama_regions' => $hijamaRegions,
                'is_urgent' => false,
            ]);

            $totalPrice = $pricing['total_price'];
            $totalDuration = $pricing['total_duration'];

            $built = \App\Filament\Resources\RequestResource::buildDescription(
                $bookingType,
                $packages,
                $validRegions,
                $treatmentStyle,
                $massageIntensity,
                $crackingType,
                $crackingRegions,
                $hijamaType,
                $hijamaStyle,
                $hijamaRegions,
                $regionRepetitions,
                $crackingStyle
            );

            $processedAttendees[] = [
                'name' => $attData['name'],
                'phone' => $attData['phone'],
                'gender' => $attData['gender'],
                'booking_type' => $bookingType,
                'service_type' => $built['service_type'],
                'packages' => $packages,
                'total_price' => $totalPrice,
                'total_duration' => $totalDuration,
                'description' => $built['description'],
                'massage_active' => $massageActive,
                'valid_regions' => $validRegions,
                'style' => $style,
            ];

            $totalSessionsPrice += $totalPrice;
            $groupGendersAndDurations[] = [
                'gender' => $attData['gender'],
                'duration' => $totalDuration
            ];
            if ($totalDuration > $totalGroupDuration) {
                $totalGroupDuration = $totalDuration;
            }
        }

        // Apply group-level Urgent fee once if the booking is urgent
        $urgentFee = 0;
        if ($isUrgent) {
            $urgentFee = (int)\App\Models\Setting::get('urgent_booking_fee', 200);
        }

        // 1. Validate Minimum Booking Session Price Limit (Only for group bookings)
        $minBookingAmount = (int)\App\Models\Setting::get('min_booking_amount', 2100);
        $countAttendees = count($processedAttendees);
        $groupMinAmount = $countAttendees * ($minBookingAmount / 3);
        if ($countAttendees > 1 && $totalSessionsPrice < $groupMinAmount) {
            $formattedGroupMin = round($groupMinAmount);
            return redirect()->back()->withInput()->withErrors(['booking_limit' => "يجب أن لا يقل إجمالي سعر جلسات المجموعة عن {$formattedGroupMin} ج.م لتأكيد الحجز."]);
        }

        // 2. Blocked Day Check (if not urgent)
        if (!$isUrgent) {
            $matchingBlockedDays = \App\Models\BlockedDay::getMatchingBlockedDays($bookingDate);
            $isBlocked = false;
            $startMin = $this->timeToMinutes($bookingTime);
            $endMin = $startMin + $totalGroupDuration;

            foreach ($matchingBlockedDays as $bd) {
                if (is_null($bd->start_time) && is_null($bd->end_time)) {
                    $isBlocked = true;
                    break;
                } else {
                    $bStart = $this->timeToMinutes($bd->start_time);
                    $bEnd = $this->timeToMinutes($bd->end_time);
                    if (max($startMin, $bStart) < min($endMin, $bEnd)) {
                        $isBlocked = true;
                        break;
                    }
                }
            }

            if ($isBlocked) {
                return redirect()->back()->withInput()->withErrors(['date' => 'عذراً، هذا الوقت/التاريخ غير متاح للحجز (إجازة مغلقة).']);
            }
        }

        // 3. Concurrent Capacity Check for the entire group
        $startMin = $this->timeToMinutes($bookingTime);
        if (!$this->isSlotAvailable($bookingDate, $groupGendersAndDurations, $startMin)) {
            return redirect()->back()->withInput()->withErrors(['time' => 'عذراً، هذا الوقت غير متاح لتجاوز الحد الأقصى للحجوزات المتزامنة لمجموعتكم.']);
        }

        // Process coupon if submitted
        $couponCode = $request->input('coupon_code');
        $coupon = null;
        $couponDiscount = 0;
        if ($couponCode) {
            $coupon = \App\Models\Coupon::where('code', trim($couponCode))->first();
            if (!$coupon) {
                $coupon = \App\Models\Coupon::whereRaw('UPPER(code) = ?', [strtoupper(trim($couponCode))])->first();
            }
            if ($coupon && $coupon->isValidFor($bookingDate, $totalSessionsPrice)) {
                $couponDiscount = $coupon->calculateDiscountFor($totalSessionsPrice);
            }
        }

        // 4. Save requests to database
        $parentBooking = null;
        foreach ($processedAttendees as $index => $att) {
            $isFirst = ($index === 0);

            // Deposit calculation: 40% of the total price (including urgent fee on the parent request only)
            $individualPrice = $att['total_price'] + ($isFirst ? $urgentFee : 0);
            
            // Subtract coupon discount from parent request only
            if ($isFirst && $couponDiscount > 0) {
                $individualPrice = max(0, $individualPrice - $couponDiscount);
            }
            
            $deposit = ceil($individualPrice * 0.40);

            $booking = BookingRequest::create([
                'parent_id' => $isFirst ? null : $parentBooking->id,
                'name' => $att['name'],
                'phone' => $att['phone'],
                'booking_type' => $att['booking_type'],
                'service_type' => $att['service_type'],
                'packages' => $att['packages'],
                'total_price' => $individualPrice,
                'total_duration' => $att['total_duration'],
                'description' => $att['description'],
                'date' => $bookingDate,
                'time' => $bookingTime,
                'gender' => $att['gender'],
                'status' => 'pending',
                'deposit' => $deposit,
                'user_agreement' => $userAgreement,
                'is_urgent' => $isUrgent,
                'coupon_code' => $isFirst && $coupon ? $coupon->code : null,
                'coupon_discount' => $isFirst ? $couponDiscount : 0,
            ]);

            if ($isFirst) {
                $parentBooking = $booking;
                if ($coupon && $couponDiscount > 0) {
                    $coupon->increment('uses');
                }
            }

            // Save regions for this attendee
            if ($att['massage_active']) {
                $regionRepetitions = \App\Helpers\MassageHelper::getRegionRepetitions($att['style'] ?? 'intensive');
                foreach ($att['valid_regions'] as $rNum) {
                    $booking->regions()->create([
                        'region_number' => $rNum,
                        'repetitions' => $regionRepetitions[$rNum],
                    ]);
                }
            }
        }

        // Trigger manual email notification for the parent booking now that children are stored in database
        try {
            $recipient = config('mail.to_address') ?? config('mail.from.address');
            if ($recipient && env('RESEND_API_KEY') && $parentBooking) {
                // Refresh parentBooking to load children relationship
                $parentBooking->load('children');

                \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('RESEND_API_KEY'),
                    'Content-Type' => 'application/json',
                ])->post('https://api.resend.com/emails', [
                    'from' => config('mail.from.address') ?? 'onboarding@resend.dev',
                    'to' => $recipient,
                    'subject' => 'طلب حجز جماعي جديد - ' . $parentBooking->name,
                    'html' => view('emails.new_request', ['bookingRequest' => $parentBooking])->render(),
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send group request email notification: ' . $e->getMessage());
        }

        return redirect()->route('booking.form')->with('success', 'تم تسجيل طلب الحجز بنجاح وسنتواصل معك قريباً!');
    }

    protected function storeTherapeutic(Request $request)
    {
        $request->validate([
            'therapeutic_name' => 'required|string|max:255',
            'therapeutic_phone' => 'required|string|max:255',
            'therapeutic_gender' => 'required|string|in:male,female',
            'therapeutic_weight' => 'required|numeric|min:30|max:300',
            'therapeutic_blood_type' => 'nullable|string',
            'therapeutic_protocol' => 'required|string|in:intensive,economy',
        ]);

        $bookingDate = $request->input('therapeutic_date') ?? $request->input('date');
        $bookingTime = $request->input('therapeutic_time') ?? $request->input('time');
        $isUrgent = $request->boolean('therapeutic_is_urgent', false) || $request->boolean('is_urgent', false);
        $userAgreement = $request->input('therapeutic_user_agreement') ?? $request->input('user_agreement');

        if (!$bookingDate) {
            return redirect()->back()->withInput()->withErrors(['therapeutic_date' => 'يرجى اختيار تاريخ موعد السيشن.']);
        }
        if (!$bookingTime) {
            return redirect()->back()->withInput()->withErrors(['therapeutic_time' => 'يرجى اختيار وقت السيشن المتاح.']);
        }
        if ($userAgreement !== 'موافق') {
            return redirect()->back()->withInput()->withErrors(['therapeutic_user_agreement' => 'يجب الموافقة على شروط الحجز والمقدم المالي لتأكيد الحجز.']);
        }

        $today = date('Y-m-d');
        if ($bookingDate < $today) {
            return redirect()->back()->withInput()->withErrors(['therapeutic_date' => 'لا يمكن حجز موعد في الماضي.']);
        }

        $bookingTimestamp = strtotime($bookingDate . ' ' . $bookingTime);
        if (($bookingTimestamp - time()) < (40 * 60)) {
            return redirect()->back()->withInput()->withErrors(['therapeutic_time' => 'يجب أن يكون موعد الحجز بعد 40 دقيقة من الآن على الأقل.']);
        }

        $name = $request->input('therapeutic_name');
        $phone = $request->input('therapeutic_phone');
        $gender = $request->input('therapeutic_gender');
        $weight = (float)$request->input('therapeutic_weight');
        $bloodType = $request->input('therapeutic_blood_type', 'A');
        $protocol = $request->input('therapeutic_protocol', 'intensive');

        $severeInput = $request->input('therapeutic_severe_regions', '');
        $moderateInput = $request->input('therapeutic_moderate_regions', '');

        $severeRegions = array_filter(array_map('intval', explode(',', $severeInput)));
        $moderateRegions = array_filter(array_map('intval', explode(',', $moderateInput)));

        // 1. Calculate Massage
        $massageCalc = \App\Helpers\TherapeuticMassageHelper::calculate($bloodType, $weight, $severeRegions, $moderateRegions, $protocol);

        // 2. Calculate Chiropractic
        $allPainRegions = array_unique(array_merge($severeRegions, $moderateRegions));
        $chiroCalc = \App\Helpers\TherapeuticChiropracticHelper::calculate($allPainRegions, $protocol);

        // 3. Calculate Hijama
        $intensiveCupsMap = [
            1 => 3, 2 => 1, 3 => 2, 4 => 4, 5 => 3, 6 => 1, 7 => 2, 8 => 4, 9 => 2, 10 => 2,
            11 => 3, 12 => 3, 13 => 2, 14 => 2, 15 => 2, 16 => 2, 17 => 1, 18 => 2, 19 => 3, 20 => 1,
            21 => 2, 22 => 3, 23 => 2, 24 => 2, 25 => 3, 26 => 2, 27 => 2, 28 => 3, 29 => 3, 30 => 3,
            31 => 2, 32 => 2, 33 => 1, 34 => 2, 35 => 1, 36 => 2, 37 => 2, 38 => 2, 39 => 2
        ];
        $economyCupsMap = [
            1 => 2, 2 => 1, 3 => 1, 4 => 2, 5 => 2, 6 => 1, 7 => 1, 8 => 2, 9 => 2, 10 => 1,
            11 => 1, 12 => 1, 13 => 1, 14 => 1, 15 => 1, 16 => 1, 17 => 1, 18 => 1, 19 => 1, 20 => 1,
            21 => 1, 22 => 1, 23 => 1, 24 => 1, 25 => 1, 26 => 1, 27 => 1, 28 => 1, 29 => 1, 30 => 1,
            31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1, 37 => 1, 38 => 1, 39 => 1
        ];
        $cupsMap = ($protocol === 'intensive') ? $intensiveCupsMap : $economyCupsMap;
        $totalCups = 0;
        foreach ($severeRegions as $r) {
            $totalCups += $cupsMap[$r] ?? 1;
        }

        $hijamaPrice = 0;
        $hijamaDuration = 0;
        if ($totalCups > 0) {
            $cupPrice = 45;
            if ($totalCups > 20) $cupPrice = 35;
            elseif ($totalCups >= 16) $cupPrice = 37;
            elseif ($totalCups >= 11) $cupPrice = 40;
            $hijamaPrice = $totalCups * $cupPrice;
            $hijamaDuration = 10 + $totalCups;
        }

        // 4. Calculate Rehabilitation
        $hasAnyPain = (count($severeRegions) > 0 || count($moderateRegions) > 0);
        $rehabDuration = 0;
        $rehabPrice = 0;
        if ($hasAnyPain) {
            $rehabDuration = ($protocol === 'intensive') ? 10 : 5;
            $rehabPrice = $rehabDuration * 12;
        }

        $totalDuration = (int)round($massageCalc['duration'] + $chiroCalc['duration'] + $hijamaDuration + $rehabDuration);
        $totalSessionPrice = $massageCalc['total_price'] + $chiroCalc['total_price'] + $hijamaPrice + $rehabPrice;

        $urgentFee = 0;
        if ($isUrgent) {
            $urgentFee = (int)\App\Models\Setting::get('urgent_booking_fee', 200);
        }

        // Blocked day check
        if (!$isUrgent) {
            $matchingBlockedDays = \App\Models\BlockedDay::getMatchingBlockedDays($bookingDate);
            $isBlocked = false;
            $startMin = $this->timeToMinutes($bookingTime);
            $endMin = $startMin + $totalDuration;

            foreach ($matchingBlockedDays as $bd) {
                if (is_null($bd->start_time) && is_null($bd->end_time)) {
                    $isBlocked = true;
                    break;
                } else {
                    $bStart = $this->timeToMinutes($bd->start_time);
                    $bEnd = $this->timeToMinutes($bd->end_time);
                    if (max($startMin, $bStart) < min($endMin, $bEnd)) {
                        $isBlocked = true;
                        break;
                    }
                }
            }

            if ($isBlocked) {
                return redirect()->back()->withInput()->withErrors(['therapeutic_date' => 'عذراً، هذا الوقت/التاريخ غير متاح للحجز (إجازة مغلقة).']);
            }
        }

        // Capacity check
        $startMin = $this->timeToMinutes($bookingTime);
        if (!$this->isSlotAvailable($bookingDate, [['gender' => $gender, 'duration' => $totalDuration]], $startMin)) {
            return redirect()->back()->withInput()->withErrors(['therapeutic_time' => 'عذراً، هذا الوقت غير متاح لتجاوز الحد الأقصى للحجوزات المتزامنة.']);
        }

        // Coupon discount
        $couponCode = $request->input('therapeutic_coupon_code') ?? $request->input('coupon_code');
        $coupon = null;
        $couponDiscount = 0;
        if ($couponCode) {
            $coupon = \App\Models\Coupon::where('code', trim($couponCode))->first();
            if (!$coupon) {
                $coupon = \App\Models\Coupon::whereRaw('UPPER(code) = ?', [strtoupper(trim($couponCode))])->first();
            }
            if ($coupon && $coupon->isValidFor($bookingDate, $totalSessionPrice)) {
                $couponDiscount = $coupon->calculateDiscountFor($totalSessionPrice);
            }
        }

        $finalTotalPrice = max(0, $totalSessionPrice + $urgentFee - $couponDiscount);
        $deposit = ceil($finalTotalPrice * 0.40);

        // Build detailed description for Filament
        $descParts = [];
        $protocolLabel = ($protocol === 'intensive') ? 'مكثف' : 'اقتصادي';
        $descParts[] = "نوع الجلسة: سيشن علاجية [البروتوكول: {$protocolLabel}]";
        $descParts[] = "بيانات المريض: فصيلة الدم ({$bloodType}) | الوزن ({$weight} كجم)";
        $severeStr = !empty($severeRegions) ? implode(', ', $severeRegions) : 'لا يوجد';
        $moderateStr = !empty($moderateRegions) ? implode(', ', $moderateRegions) : 'لا يوجد';
        $descParts[] = "مناطق الألم: شديد [{$severeStr}] | متوسط [{$moderateStr}]";
        $descParts[] = "المساج العلاجي [التكنيك: {$massageCalc['technique']} | عدد التكنيكات: {$massageCalc['total_techniques']} | السعر: {$massageCalc['total_price']} ج.م | المدة: {$massageCalc['duration']} دقيقة]";
        $chiroGroupsStr = !empty($chiroCalc['active_groups_names']) ? implode(' + ', $chiroCalc['active_groups_names']) : 'لا يوجد';
        $descParts[] = "الكيروبراكتيك العلاجي [المناطق: {$chiroGroupsStr} | عدد التكنيكات: {$chiroCalc['total_techniques']} | السعر: {$chiroCalc['total_price']} ج.م | المدة: {$chiroCalc['duration']} دقيقة]";
        if ($totalCups > 0) {
            $descParts[] = "الحجامة [عدد الكاسات: {$totalCups} كاس على مناطق الألم الشديد | السعر: {$hijamaPrice} ج.م | المدة: {$hijamaDuration} دقيقة]";
        }
        if ($rehabDuration > 0) {
            $descParts[] = "التأهيل [برنامج تمارين تأهيلية | السعر: {$rehabPrice} ج.م | المدة: {$rehabDuration} دقيقة]";
        }
        if ($isUrgent) {
            $descParts[] = "الحجز المستعجل [رسوم إضافية: {$urgentFee} ج.م]";
        }
        if ($couponDiscount > 0 && $coupon) {
            $descParts[] = "كوبون الخصم [الكود: {$coupon->code} | الخصم: {$couponDiscount} ج.م]";
        }

        $fullDescription = implode(' | ', $descParts);

        $serviceTypeParts = ['مساج علاجي', 'كيروبراكتيك علاجي'];
        if ($totalCups > 0) $serviceTypeParts[] = 'حجامة';
        if ($rehabDuration > 0) $serviceTypeParts[] = 'تأهيل';
        $serviceType = implode(' + ', $serviceTypeParts);

        $booking = BookingRequest::create([
            'name' => $name,
            'phone' => $phone,
            'gender' => $gender,
            'booking_type' => 'علاجية',
            'service_type' => $serviceType,
            'packages' => [$protocol],
            'total_price' => $finalTotalPrice,
            'total_duration' => $totalDuration,
            'description' => $fullDescription,
            'date' => $bookingDate,
            'time' => $bookingTime,
            'status' => 'pending',
            'deposit' => $deposit,
            'user_agreement' => $userAgreement,
            'is_urgent' => $isUrgent,
            'coupon_code' => $coupon ? $coupon->code : null,
            'coupon_discount' => $couponDiscount,
        ]);

        if ($coupon && $couponDiscount > 0) {
            $coupon->increment('uses');
        }

        // Save regions with repetitions
        $bloodTypeKey = in_array(strtoupper($bloodType), ['A', 'B', 'AB', 'O']) ? strtoupper($bloodType) : 'A';
        $bracket = \App\Helpers\TherapeuticMassageHelper::getWeightBracket($weight);
        $sevRepMap = \App\Helpers\TherapeuticMassageHelper::$severeTechniqueMap[$bloodTypeKey][$bracket] ?? [];
        $modRepMap = \App\Helpers\TherapeuticMassageHelper::$moderateTechniqueMap ?? [];

        foreach ($severeRegions as $rNum) {
            $booking->regions()->create([
                'region_number' => $rNum,
                'repetitions' => $sevRepMap[$rNum] ?? 1,
            ]);
        }
        foreach ($moderateRegions as $rNum) {
            $booking->regions()->create([
                'region_number' => $rNum,
                'repetitions' => $modRepMap[$rNum] ?? 1,
            ]);
        }

        try {
            $recipient = config('mail.to_address') ?? config('mail.from.address');
            if ($recipient && env('RESEND_API_KEY')) {
                \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('RESEND_API_KEY'),
                    'Content-Type' => 'application/json',
                ])->post('https://api.resend.com/emails', [
                    'from' => config('mail.from.address') ?? 'onboarding@resend.dev',
                    'to' => $recipient,
                    'subject' => 'طلب حجز سيشن علاجية جديدة - ' . $booking->name,
                    'html' => view('emails.new_request', ['bookingRequest' => $booking])->render(),
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send therapeutic request email notification: ' . $e->getMessage());
        }

        return redirect()->route('booking.form')->with('success', 'تم تسجيل طلب الحجز بنجاح وسنتواصل معك قريباً!');
    }

    // AJAX Endpoint to get available times for a date and duration
    public function availableTimes(Request $request)
    {
        $date = $request->input('date');
        $isUrgent = $request->boolean('is_urgent', false);

        if (!$date) {
            return response()->json([]);
        }

        $attendeesInput = $request->input('attendees');
        if ($attendeesInput) {
            $attendees = is_string($attendeesInput) ? json_decode($attendeesInput, true) : $attendeesInput;
        } else {
            $attendees = [[
                'gender' => $request->input('gender'),
                'duration' => (int)$request->input('duration', 60)
            ]];
        }

        $attendees = array_filter($attendees, function($att) {
            return isset($att['gender']) && in_array($att['gender'], ['male', 'female']);
        });

        if (empty($attendees)) {
            return response()->json([]);
        }

        if (!$isUrgent && \App\Models\BlockedDay::isDateBlocked($date)) {
            return response()->json([
                'error' => 'عذراً، هذا اليوم إجازة ولا تتوفر به حجوزات.'
            ]);
        }

        $hasFemale = false;
        $hasMale = false;
        $maxDuration = 0;
        foreach ($attendees as $att) {
            if ($att['gender'] === 'female') $hasFemale = true;
            if ($att['gender'] === 'male') $hasMale = true;
            if ($att['duration'] > $maxDuration) {
                $maxDuration = (int)$att['duration'];
            }
        }

        $duration = $maxDuration;

        // Boundary times
        if ($isUrgent) {
            $minHour = 0;      // Midnight
            $maxHour = 1410;   // 11:30 PM
        } else {
            $minHour = 0;
            $maxHour = 2400;

            if ($hasFemale) {
                $minHour = max($minHour, 870);  // 2:30 PM
                $maxHour = min($maxHour, 1260); // 9:00 PM
            }
            if ($hasMale) {
                $minHour = max($minHour, 540);  // 9:00 AM
                $maxHour = min($maxHour, 1500); // 1:00 AM next day
            }
        }

        // Base slots: Summer (3:00 PM and 7:30 PM) vs Winter (2:00 PM and 5:00 PM)
        $timeVal = strtotime($date);
        $month = (int)date('m', $timeVal);
        $day = (int)date('d', $timeVal);
        $isSummer = (($month >= 5 && $month <= 11) || ($month == 12 && $day == 1));
        $baseSlots = $isSummer ? [900, 1170] : [840, 1020];

        // Filter base slots to only include those within the gender's boundary
        $baseSlots = array_filter($baseSlots, function($base) use ($minHour, $maxHour) {
            return $base >= $minHour && $base <= $maxHour;
        });

        // Load partial block intervals (only if not urgent)
        $intervals = [];
        if (!$isUrgent) {
            $blockedDays = \App\Models\BlockedDay::getMatchingBlockedDays($date);
            foreach ($blockedDays as $bd) {
                if ($bd->start_time && $bd->end_time) {
                    $bStart = $this->timeToMinutes($bd->start_time);
                    $bEnd = $this->timeToMinutes($bd->end_time);
                    $intervals[] = [$bStart, $bEnd];
                }
            }
        }

        $isSlotFree = function($start, $duration) use ($date, $attendees, $intervals, $isUrgent) {
            // Check existing bookings concurrent capacity
            if (!$this->isSlotAvailable($date, $attendees, $start)) {
                return false;
            }
            // Check partial blocked days (only if not urgent)
            if (!$isUrgent) {
                $end = $start + $duration;
                foreach ($intervals as $interval) {
                    if (max($start, $interval[0]) < min($end, $interval[1]) - 5) {
                        return false;
                    }
                }
            }
            return true;
        };

        // Scan and generate available times
        $availableStartTimes = [];
        if ($isUrgent) {
            // Scan the entire 24h day in 30-min steps
            for ($time = $minHour; $time <= $maxHour; $time += 30) {
                if ($isSlotFree($time, $duration)) {
                    $availableStartTimes[] = $time;
                }
            }
        } else {
            // Standard slots: contiguous around existing bookings, base slots, and increment scans
            // Fetch existing requests starts/ends for contiguous checking
            $existing = BookingRequest::whereDate('date', $date)
                ->where(function($query) {
                    $query->whereIn('status', ['pending', 'confirmed'])
                          ->orWhereNull('status');
                })
                ->where(function($query) use ($hasFemale, $hasMale) {
                    $genders = [];
                    if ($hasFemale) $genders[] = 'female';
                    if ($hasMale) $genders[] = 'male';
                    $query->whereIn('gender', $genders);
                    if ($hasMale) {
                        $query->orWhereNull('gender')->orWhere('gender', '');
                    }
                })
                ->get(['time', 'total_duration']);

            $existingIntervals = [];
            foreach ($existing as $booking) {
                if (!$booking->time) continue;
                $startMin = $this->timeToMinutes($booking->time);
                $dur = $booking->total_duration ?: (int)$booking->duration ?: 30;
                $existingIntervals[] = [$startMin, $startMin + $dur];
            }
            usort($existingIntervals, function($a, $b) { return $a[0] <=> $b[0]; });

            if (count($existingIntervals) === 0) {
                $availableStartTimes = $baseSlots;
            } else {
                // 1. First candidate: before the first booked session
                $firstInterval = $existingIntervals[0];
                $slot1 = $firstInterval[0] - $duration;
                if ($slot1 < $minHour) {
                    $slot1 = $minHour;
                }
                if ($isSlotFree($slot1, $duration)) {
                    $availableStartTimes[] = $slot1;
                }

                // 2. Second candidate: after the last booked session
                $lastInterval = $existingIntervals[count($existingIntervals) - 1];
                $slot2 = $lastInterval[1];
                if ($slot2 >= $minHour && $slot2 <= $maxHour && $isSlotFree($slot2, $duration)) {
                    $availableStartTimes[] = $slot2;
                }

                // 3. Try expanding adjacent slots contiguously
                if (count($availableStartTimes) < 2 && in_array($slot1, $availableStartTimes)) {
                    $slot1_prev = $slot1 - $duration;
                    if ($slot1_prev >= $minHour && $isSlotFree($slot1_prev, $duration)) {
                        $availableStartTimes[] = $slot1_prev;
                    }
                }

                if (count($availableStartTimes) < 2 && in_array($slot2, $availableStartTimes)) {
                    $slot2_next = $slot2 + $duration;
                    if ($slot2_next >= $minHour && $slot2_next <= $maxHour && $isSlotFree($slot2_next, $duration)) {
                        $availableStartTimes[] = $slot2_next;
                    }
                }

                // 4. Supplement with base slots
                foreach ($baseSlots as $base) {
                    if (count($availableStartTimes) >= 2) {
                        break;
                    }
                    if ($isSlotFree($base, $duration) && !in_array($base, $availableStartTimes)) {
                        $availableStartTimes[] = $base;
                    }
                }

                // 5. General fallback: scan in 30-minute increments from minHour
                if (count($availableStartTimes) < 2) {
                    for ($time = $minHour; $time <= $maxHour; $time += 30) {
                        if (count($availableStartTimes) >= 2) {
                            break;
                        }
                        if ($isSlotFree($time, $duration) && !in_array($time, $availableStartTimes)) {
                            $availableStartTimes[] = $time;
                        }
                    }
                }
                sort($availableStartTimes);
            }
        }

        // Filter out times that are less than 40 minutes from now (handles midnight transitions)
        $now = time();
        $availableStartTimes = array_filter($availableStartTimes, function($minutes) use ($date, $now) {
            $slotTimestamp = strtotime($date) + ($minutes * 60);
            return ($slotTimestamp - $now) >= 40 * 60;
        });

        // Format output
        $formattedTimes = [];
        foreach ($availableStartTimes as $minutes) {
            $hrs = floor($minutes / 60);
            $mins = $minutes % 60;
            $timeStr = sprintf('%02d:%02d', $hrs, $mins);
            
            $displayHrs = $hrs % 12;
            if ($displayHrs === 0) {
                $displayHrs = 12;
            }
            $amPm = (($hrs % 24) >= 12) ? 'PM' : 'AM';
            
            $displayLabel = sprintf('%02d:%02d %s', $displayHrs, $mins, $amPm);
            if ($hrs >= 24) {
                $displayLabel .= ' (اليوم التالي)';
            }
            $formattedTimes[$timeStr] = $displayLabel;
        }

        return response()->json($formattedTimes);
    }

    public function checkTimeAvailability(Request $request)
    {
        $date = $request->input('date');
        $timeStr = $request->input('time');
        $isUrgent = $request->boolean('is_urgent', false);

        $attendeesInput = $request->input('attendees');
        if ($attendeesInput) {
            $attendees = is_string($attendeesInput) ? json_decode($attendeesInput, true) : $attendeesInput;
        } else {
            $attendees = [[
                'gender' => $request->input('gender'),
                'duration' => (int)$request->input('duration', 60)
            ]];
        }

        $attendees = array_filter($attendees, function($att) {
            return isset($att['gender']) && in_array($att['gender'], ['male', 'female']);
        });

        if (!$date || !$timeStr || empty($attendees)) {
            return response()->json(['available' => false, 'message' => 'الرجاء ملء جميع الحقول المطلوبة (التاريخ، تفاصيل الأشخاص، والوقت).']);
        }

        // 1. Check 40-minute lead time validation (cross-midnight absolute timestamp check)
        $bookingTimestamp = strtotime($date . ' ' . $timeStr);
        if (($bookingTimestamp - time()) < (40 * 60)) {
            return response()->json(['available' => false, 'message' => 'يجب أن يكون موعد الحجز بعد 40 دقيقة من الآن على الأقل.']);
        }

        $today = date('Y-m-d');
        if ($date < $today) {
            return response()->json(['available' => false, 'message' => 'لا يمكن حجز موعد في الماضي.']);
        }

        $startMin = $this->timeToMinutes($timeStr);

        $hasFemale = false;
        $hasMale = false;
        $maxDuration = 0;
        foreach ($attendees as $att) {
            if ($att['gender'] === 'female') $hasFemale = true;
            if ($att['gender'] === 'male') $hasMale = true;
            if ($att['duration'] > $maxDuration) {
                $maxDuration = (int)$att['duration'];
            }
        }

        // 2. Business hours check (only if not urgent)
        if (!$isUrgent) {
            if (\App\Models\BlockedDay::isDateBlocked($date)) {
                return response()->json(['available' => false, 'message' => 'عذراً، هذا اليوم إجازة ولا تتوفر به حجوزات رسمية.']);
            }

            // Boundary times
            $minHour = 0;
            $maxHour = 2400;
            $minLabel = '';
            $maxLabel = '';

            if ($hasFemale) {
                $minHour = max($minHour, 870);  // 2:30 PM
                $maxHour = min($maxHour, 1260); // 9:00 PM
                $minLabel = '2:30 PM';
                $maxLabel = '9:00 PM';
            }
            if ($hasMale) {
                $minHour = max($minHour, 540);  // 9:00 AM
                $maxHour = min($maxHour, 1500); // 1:00 AM next day
                if (empty($minLabel)) {
                    $minLabel = '9:00 AM';
                    $maxLabel = '1:00 AM (اليوم التالي)';
                } else {
                    $minLabel = '2:30 PM';
                    $maxLabel = '9:00 PM';
                }
            }

            $endMin = $startMin + $maxDuration;
            if ($startMin < $minHour || $startMin > $maxHour) {
                return response()->json(['available' => false, 'message' => "مواعيد العمل الرسمية لهذا الحجز الجماعي تبدأ من {$minLabel} وتصل حتى {$maxLabel}."]);
            }

            // Check partial blocked days
            $blockedDays = \App\Models\BlockedDay::getMatchingBlockedDays($date);
            foreach ($blockedDays as $bd) {
                if ($bd->start_time && $bd->end_time) {
                    $bStart = $this->timeToMinutes($bd->start_time);
                    $bEnd = $this->timeToMinutes($bd->end_time);
                    if (max($startMin, $bStart) < min($endMin, $bEnd)) {
                        return response()->json(['available' => false, 'message' => 'هذا الوقت يتقاطع مع فترة استراحة أو وقت مغلق بالنظام.']);
                    }
                }
            }
        }

        // 3. Concurrent capacity check
        if (!$this->isSlotAvailable($date, $attendees, $startMin)) {
            return response()->json(['available' => false, 'message' => 'عذراً، تم تجاوز الحد الأقصى للحجوزات المتزامنة في هذا الوقت لمجموعتكم.']);
        }

        return response()->json(['available' => true, 'message' => 'هذا الوقت متاح للحجز!']);
    }

    private function isSlotAvailable($date, $genderOrAttendees, $candidateStart, $duration = null, $excludeId = null)
    {
        if (is_array($genderOrAttendees)) {
            $attendees = $genderOrAttendees;
            $excludeIds = is_array($excludeId) ? $excludeId : ($excludeId ? [$excludeId] : []);
        } else {
            $attendees = [[
                'gender' => $genderOrAttendees,
                'duration' => $duration ?: 60
            ]];
            $excludeIds = $excludeId ? [$excludeId] : [];
        }

        $maxGroupDuration = collect($attendees)->max('duration') ?: 60;
        $candidateEnd = $candidateStart + $maxGroupDuration;

        // Fetch settings
        $maxFemaleBookings = (int)\App\Models\Setting::get('max_female_bookings', 1);
        $maxMaleBookings = (int)\App\Models\Setting::get('max_male_bookings', 3);
        $maxTotalBookings = (int)\App\Models\Setting::get('max_total_bookings', 3);

        // Fetch existing bookings
        $existing = BookingRequest::whereDate('date', $date)
            ->when(!empty($excludeIds), fn($q) => $q->whereNotIn('id', $excludeIds))
            ->where(function($query) {
                $query->whereIn('status', ['pending', 'confirmed'])
                      ->orWhereNull('status');
            })
            ->get(['time', 'total_duration', 'gender']);

        $overlapping = [];
        foreach ($existing as $booking) {
            if (!$booking->time) continue;
            $startMin = $this->timeToMinutes($booking->time);
            $dur = $booking->total_duration ?: (int)$booking->duration ?: 30;
            $endMin = $startMin + $dur;

            if (max($candidateStart, $startMin) < min($candidateEnd, $endMin) - 5) {
                $overlapping[] = [
                    'start' => $startMin,
                    'end' => $endMin,
                    'gender' => $booking->gender ?: 'male',
                ];
            }
        }

        $testPoints = [$candidateStart];
        foreach ($overlapping as $o) {
            if ($o['start'] > $candidateStart && $o['start'] < $candidateEnd) {
                $testPoints[] = $o['start'];
            }
        }
        foreach ($attendees as $att) {
            $attEnd = $candidateStart + $att['duration'];
            if ($attEnd > $candidateStart && $attEnd < $candidateEnd) {
                $testPoints[] = $attEnd;
            }
        }
        $testPoints = array_unique($testPoints);
        sort($testPoints);

        foreach ($testPoints as $t) {
            $femalesCount = 0;
            $malesCount = 0;

            // Count group attendees that overlap this test point
            foreach ($attendees as $att) {
                $attStart = $candidateStart;
                $attEnd = $candidateStart + $att['duration'];
                if ($t >= $attStart && $t < $attEnd - 5) {
                    if ($att['gender'] === 'female') {
                        $femalesCount++;
                    } else {
                        $malesCount++;
                    }
                }
            }

            // Count database bookings that overlap this test point
            foreach ($overlapping as $o) {
                if ($t >= $o['start'] && $t < $o['end'] - 5) {
                    if ($o['gender'] === 'female') {
                        $femalesCount++;
                    } else {
                        $malesCount++;
                    }
                }
            }

            if ($femalesCount > $maxFemaleBookings) {
                return false;
            }
            if ($malesCount > $maxMaleBookings) {
                return false;
            }
            if (($femalesCount + $malesCount) > $maxTotalBookings) {
                return false;
            }
        }

        return true;
    }

    public function validateCoupon(Request $request)
    {
        $code = $request->input('code');
        $totalPrice = (float)$request->input('total_price', 0);
        $date = $request->input('date');

        if (!$code) {
            return response()->json([
                'valid' => false,
                'message' => 'يرجى إدخال كود الكوبون.'
            ]);
        }

        $coupon = \App\Models\Coupon::where('code', trim($code))->first();
        if (!$coupon) {
            $coupon = \App\Models\Coupon::whereRaw('UPPER(code) = ?', [strtoupper(trim($code))])->first();
        }

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'كود الكوبون غير صحيح.'
            ]);
        }

        if (!$coupon->is_active) {
            return response()->json([
                'valid' => false,
                'message' => 'هذا الكوبون غير نشط حالياً.'
            ]);
        }

        if ($coupon->expires_at && \Carbon\Carbon::parse($date)->greaterThan($coupon->expires_at)) {
            return response()->json([
                'valid' => false,
                'message' => 'انتهت صلاحية هذا الكوبون.'
            ]);
        }

        if ($coupon->max_uses !== null && $coupon->uses >= $coupon->max_uses) {
            return response()->json([
                'valid' => false,
                'message' => 'تم استهلاك الحد الأقصى لاستخدام هذا الكوبون.'
            ]);
        }

        if ($totalPrice < $coupon->min_booking_value) {
            return response()->json([
                'valid' => false,
                'message' => 'قيمة الحجز أقل من الحد الأدنى لتفعيل هذا الكوبون (' . $coupon->min_booking_value . ' ج.م).'
            ]);
        }

        $discount = $coupon->calculateDiscountFor($totalPrice);

        return response()->json([
            'valid' => true,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => $discount,
            'message' => 'تم تطبيق الكوبون بنجاح! خصم بقيمة ' . $discount . ' ج.م.'
        ]);
    }

    private function timeToMinutes($timeStr)
    {
        if (!$timeStr) return 0;
        if (stripos($timeStr, 'PM') !== false || stripos($timeStr, 'AM') !== false) {
            $timestamp = strtotime($timeStr);
            return ((int)date('H', $timestamp) * 60) + (int)date('i', $timestamp);
        }
        
        $parts = explode(':', $timeStr);
        if (count($parts) < 2) return 0;
        $hrs = (int)$parts[0];
        $mins = (int)$parts[1];
        
        if ($hrs >= 1 && $hrs <= 8) {
            $hrs += 12;
        }
        
        return ($hrs * 60) + $mins;
    }
}