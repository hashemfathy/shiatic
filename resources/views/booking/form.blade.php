<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجز سيشن جديدة - Shiatic</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1e293b;
            --accent: #E67E22;
            --bg-glass: rgba(255, 255, 255, 0.85);
            --border-glass: rgba(15, 23, 42, 0.08);
            --text-muted: #64748b;
        }

        body {
            font-family: 'Cairo', 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            margin: 0;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .glass-container {
            background: var(--bg-glass);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-glass);
            border-radius: 24px;
            padding: 3rem;
            box-shadow: 0 8px 32px 0 rgba(15, 23, 42, 0.05);
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 2rem;
            background: linear-gradient(to left, #0f172a, #475569);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            color: #0f172a;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background-color: #ffffff;
            border-color: #ff9d42;
            color: #0f172a;
            box-shadow: 0 0 0 0.25rem rgba(255, 157, 66, 0.25);
        }

        .form-select option {
            color: #0f172a;
            background-color: #ffffff;
        }

        /* Category Card Selectors */
        .category-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .category-card {
            background: rgba(15, 23, 42, 0.02);
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: #0f172a;
        }

        .category-card:hover {
            background: rgba(15, 23, 42, 0.04);
            border-color: rgba(15, 23, 42, 0.15);
        }

        .category-card.active {
            background: rgba(255, 157, 66, 0.1);
            border-color: #ff9d42;
            box-shadow: 0 0 15px rgba(255, 157, 66, 0.15);
        }

        .category-card h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .category-card p {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin: 0;
        }

        /* Accordions */
        .accordion-item {
            background-color: rgba(15, 23, 42, 0.02) !important;
            border: 1px solid rgba(15, 23, 42, 0.06) !important;
            border-radius: 16px !important;
            margin-bottom: 1rem;
            overflow: hidden;
            scroll-margin-top: 2rem;
        }

        .accordion-button {
            background-color: transparent !important;
            color: #0f172a !important;
            font-weight: 700;
            font-size: 1.2rem;
            padding: 1.25rem 1.5rem;
            box-shadow: none !important;
            border: none !important;
        }

        .accordion-button:not(.collapsed) {
            background-color: rgba(15, 23, 42, 0.03) !important;
            color: #ff9d42 !important;
        }

        .accordion-button::after {
            filter: none;
        }

        .accordion-body {
            padding: 2rem;
            background-color: rgba(15, 23, 42, 0.01);
        }

        /* Body Map and Regions Layout */
        .booking-row {
            display: grid;
            grid-template-columns: 1.5fr 1.2fr;
            grid-template-areas:
                "packages map"
                "intensity map"
                "pricing map";
            gap: 2rem;
        }

        .intensity-col {
            grid-area: intensity;
        }

        .booking-row-hijama {
            display: grid;
            grid-template-columns: 1.5fr 1.2fr;
            grid-template-areas:
                "packages map"
                "style    map"
                "pricing  map";
            gap: 2rem;
        }

        .hijama-style-col {
            grid-area: style;
        }

        .packages-col {
            grid-area: packages;
        }

        .pricing-col {
            grid-area: pricing;
            align-self: start;
        }

        .body-map-col {
            grid-area: map;
            max-width: 600px;
            text-align: center;
            margin: 0 auto;
            align-self: flex-start;
        }

        .body-map-container {
            position: relative;
            display: inline-block;
            width: 100%;
            max-width: 600px;
            aspect-ratio: 438 / 166.32;
            margin: 0 auto;
        }

        .body-map-img {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 16px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
        }

        .hotspot {
            position: absolute;
            width: 22px;
            aspect-ratio: 1;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 800;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            z-index: 10;
            border: 2px solid transparent;
            background: transparent;
            color: transparent;
        }

        @media (max-width: 768px) {
            .hotspot {
                width: 14px;
                font-size: 0.5rem;
                border-width: 1.5px;
            }
            .hotspot.available.selected {
                box-shadow: 0 0 6px rgba(46, 204, 113, 0.5);
            }
        }

        .hotspot.available:hover {
            border-color: rgba(46, 204, 113, 0.5);
            background: rgba(46, 204, 113, 0.25);
            color: #2ecc71;
            transform: translate(-50%, -50%) scale(1.15);
        }

        .hotspot.available.selected {
            background: #2ecc71;
            color: #ffffff;
            box-shadow: 0 0 12px rgba(46, 204, 113, 0.5);
            border-color: #ffffff;
        }

        .hotspot.not-available {
            cursor: not-allowed;
        }

        .hotspot.not-available:hover {
            border-color: rgba(230, 126, 34, 0.5);
            background: rgba(230, 126, 34, 0.25);
            color: #e67e22;
        }

        .cracking-map-container-el .hotspot {
            width: 12%;
            font-size: 0.9rem;
        }

        .package-checkbox-card {
            background: rgba(15, 23, 42, 0.02);
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .package-checkbox-card:hover {
            background: rgba(15, 23, 42, 0.04);
        }

        .package-checkbox-card input[type="checkbox"],
        .package-checkbox-card input[type="radio"] {
            width: 20px;
            height: 20px;
            accent-color: #ff9d42;
            margin-left: 1rem;
            cursor: pointer;
        }

        .package-checkbox-card label {
            cursor: pointer;
            font-weight: 600;
            flex-grow: 1;
            color: #0f172a;
        }

        /* Interactive region grid */
        .region-selector-title {
            font-weight: 700;
            font-size: 1.1rem;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            color: #0f172a;
        }

        /* Price/Duration Table */
        .pricing-table-container {
            background: rgba(15, 23, 42, 0.02);
            border-radius: 16px;
            border: 1px solid rgba(15, 23, 42, 0.06);
            padding: 1.5rem;
        }

        .pricing-table-container h4 {
            font-weight: 700;
            margin-bottom: 1rem;
            color: #0f172a;
        }

        .pricing-table {
            width: 100%;
            border-collapse: collapse;
        }

        .pricing-table th, .pricing-table td {
            padding: 0.75rem 1rem;
            text-align: right;
            border-bottom: 1px solid rgba(15, 23, 42, 0.06);
            color: #0f172a;
        }

        .pricing-table th {
            font-weight: 600;
            color: var(--text-muted);
        }

        .pricing-table td label {
            color: #0f172a;
            cursor: pointer;
        }

        .pricing-table tr:last-child td {
            border-bottom: none;
        }

        .price-value {
            color: #ff9d42;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .duration-value {
            color: #0284c7;
            font-weight: 600;
        }

        /* Submit Button */
        .btn-submit {
            background: linear-gradient(135deg, #ff9d42 0%, #e67e22 100%);
            border: none;
            color: white;
            padding: 1rem 2rem;
            font-weight: 700;
            font-size: 1.2rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 2rem;
            box-shadow: 0 4px 15px rgba(230, 126, 34, 0.2);
        }

        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(230, 126, 34, 0.3);
        }

        .btn-submit:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-submit:disabled {
            background: #cbd5e1;
            color: #94a3b8;
            box-shadow: none;
            cursor: not-allowed;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 1rem;
            margin-top: 1.5rem;
            transition: color 0.3s ease;
        }

        .btn-back:hover {
            color: #0f172a;
        }

        .attendee-card {
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
        }
        
        .attendee-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
        }

        @media (max-width: 992px) {
            .booking-row {
                grid-template-columns: 1fr;
                grid-template-areas:
                    "packages"
                    "map"
                    "intensity"
                    "pricing";
                gap: 2rem;
            }
            .booking-row-hijama {
                grid-template-columns: 1fr;
                grid-template-areas:
                    "packages"
                    "map"
                    "style"
                    "pricing";
                gap: 2rem;
            }
            .body-map-col, .packages-col, .pricing-col, .hijama-style-col, .intensity-col {
                min-width: 100%;
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .glass-container {
                padding: 1.5rem 1rem;
            }
            body {
                padding: 1.5rem 0.5rem;
            }
            h1 {
                font-size: 1.8rem;
                margin-bottom: 1.5rem;
            }
            .category-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            .pricing-table th, .pricing-table td {
                padding: 0.5rem 0.5rem;
                font-size: 0.85rem;
            }
            .pricing-table-container {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="glass-container">
        <h1>حجز سيشن جديدة</h1>

        @if ($errors->any())
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger p-3 rounded-4 mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="text-center py-4" style="direction: rtl;">
                <div class="mb-4 position-relative d-inline-block">
                    <img src="{{ asset('images/welcome_cartoon.png') }}" alt="مرحبًا بك في Shiatic" class="img-fluid rounded-4 shadow-lg" style="max-width: 260px; border: 4px solid #ff9d42;">
                    <div class="position-absolute bottom-0 start-50 translate-middle-x bg-warning text-white px-3 py-1 rounded-pill fw-bold" style="font-size: 0.85rem; transform: translate(-50%, 50%) !important;">
                        نحن بانتظارك! 🤝
                    </div>
                </div>
                
                <h2 class="fw-bold mb-3 text-dark" style="font-size: 1.8rem; line-height: 1.4;">شكرًا لطلب حجزك، مرحبًا بك في Shiatic! 🎉</h2>
                <p class="text-muted fs-6 mb-4" style="max-width: 550px; margin: 0 auto;">يسعدنا ويشرفنا اختيارك لنا. يرجى استكمال الخطوة الأخيرة لتأكيد وتفعيل موعدك.</p>

                <div class="card border-0 mx-auto p-4 mb-4 text-start" style="max-width: 650px; background: rgba(230, 126, 34, 0.04); border-right: 5px solid #ff9d42 !important; border-radius: 16px; box-shadow: 0 4px 15px rgba(230, 126, 34, 0.05);">
                    <h5 class="fw-bold text-warning mb-3" style="font-size: 1.15rem;">⚠️ تنبيه هام جداً لتأكيد الحجز:</h5>
                    <p class="mb-3 text-dark" style="font-size: 1rem; line-height: 1.7;">
                        في حال عدم تحويل المقدم خلال ساعة، سيتم إلغاء طلب الحجز تلقائيًا لإتاحة الموعد للعملاء الآخرين.
                    </p>
                    <div class="row g-3">
                            <div class="col-sm-6 text-center">
                                <div class="p-2 border rounded bg-light bg-opacity-50">
                                    <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                                        <img src="{{ asset('images/vodafone-cash.png') }}" alt="Vodafone Cash" style="height: 25px; object-fit: contain;">
                                        <span class="fw-bold text-dark" style="font-size: 0.9rem;">فودافون كاش</span>
                                    </div>
                                    <img src="{{ asset('images/vodafone-cash-qr.JPG') }}" alt="Vodafone Cash QR" class="img-fluid rounded border mb-2" style="max-height: 160px; width: auto; object-fit: contain;">
                                    <div class="text-muted" style="font-size: 0.8rem;">رقم الهاتف: <strong>01064344092</strong></div>
                                </div>
                            </div>
                            <div class="col-sm-6 text-center">
                                <div class="p-2 border rounded bg-light bg-opacity-50">
                                    <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                                        <img src="{{ asset('images/instapay.png') }}" alt="Instapay" style="height: 25px; object-fit: contain;">
                                        <span class="fw-bold text-dark" style="font-size: 0.9rem;">إنستا باي</span>
                                    </div>
                                    <img src="{{ asset('images/instapay-qr.jpeg') }}" alt="Instapay QR" class="img-fluid rounded border mb-2" style="max-height: 160px; width: auto; object-fit: contain;">
                                    <div class="text-muted" style="font-size: 0.8rem;">العنوان الإملائي: <strong>01064344092</strong></div>
                                </div>
                            </div>
                        </div>
                    <p class="mb-0 text-dark" style="font-size: 0.95rem; line-height: 1.7;">
                        📲 بعد إتمام التحويل، يرجى إرسال <strong>صورة إيصال/تحويل مقدم الحجز</strong> مباشرة عبر الواتساب لتفعيل وتأكيد الموعد فوراً:
                        <a href="https://wa.me/201064344092" target="_blank" class="d-inline-flex align-items-center fw-bold text-success text-decoration-none ms-1">
                            01064344092 💬 (اضغط هنا للمراسلة المباشرة)
                        </a>
                    </p>
                </div>

                <div class="mt-4">
                    <a href="{{ url('/') }}" class="btn btn-submit d-inline-block px-5 py-3 fw-bold text-white text-decoration-none" style="max-width: 250px; margin-top: 0;">
                        العودة للرئيسية
                    </a>
                </div>
            </div>
        @else
            <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
            @csrf

            <!-- Dynamic Attendees Container -->
            <div id="attendees-list">
                <!-- Attendees will be generated here -->
            </div>

            <!-- Add Attendee Button -->
            <div class="text-center mb-5 mt-4">
                <button type="button" id="btn-add-attendee" class="btn btn-outline-warning rounded-4 px-4 py-2 border-2 fw-bold" style="font-size: 1.05rem;">
                    ➕ إضافة شخص آخر للحجز (زوجتك / صديقك)
                </button>
            </div>
            
            <!-- Appointment Date & Time Selection Section -->
            <div class="card mt-4 mb-4" style="background: rgba(15, 23, 42, 0.02); border: 1px solid rgba(15, 23, 42, 0.05); border-radius: 16px;">
                <div class="card-body p-4">
                    <h4 class="mb-4 text-center" style="font-weight: 700; color: #ff9d42;">تحديد موعد السيشن  </h4>
                    
                    <!-- Urgent Booking Toggle -->
                    <div class="mt-4 p-3 rounded-4 mb-4" style="background: rgba(230, 126, 34, 0.05); border: 1px dashed rgba(230, 126, 34, 0.3); border-radius: 16px; text-align: right;">
                        <div class="form-check form-switch d-flex align-items-center">
                            <input class="form-check-input" type="checkbox" id="is_urgent" name="is_urgent" value="1" style="width: 2.5rem; height: 1.25rem; accent-color: #e67e22; margin-left: 1rem; cursor: pointer;">
                            <label class="form-check-label text-dark fw-bold" for="is_urgent" style="cursor: pointer; font-size: 1.1rem; flex-grow: 1;">
                                🔥 تفعيل خيار الحجز المستعجل (Urgent Booking)
                                <div class="text-muted fw-normal mt-1" style="font-size: 0.85rem;">
                                    يتيح لك الحجز في أي تاريخ ووقت (حتى خارج أوقات العمل الرسمية وأيام العطلات).
                                    رسوم الحجز المستعجل الإضافية للطلب: <span class="text-warning fw-bold">{{ $urgentBookingFee }} ج.م</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="appointment_date" class="form-label">اختر التاريخ *</label>
                            <input type="date" class="form-control" id="appointment_date" name="date" required min="{{ date('Y-m-d') }}">
                        </div>
                        <!-- Regular Booking Time Select -->
                        <div class="col-md-6" id="regular_time_container">
                            <label for="appointment_time_select" class="form-label">اختر الوقت المتاح للمجموعة *</label>
                            <select class="form-select" id="appointment_time_select" required>
                                <option value="" disabled selected>يرجى اختيار تاريخ أولاً</option>
                            </select>
                        </div>

                        <!-- Urgent Booking Time Input -->
                        <div class="col-md-6" id="urgent_time_container" style="display: none;">
                            <label for="appointment_time_input" class="form-label">اختر الوقت المطلوب *</label>
                            <input type="time" class="form-control" id="appointment_time_input">
                            <div id="time_validation_feedback" class="mt-2 fw-bold" style="display: none; font-size: 0.9rem;"></div>
                        </div>

                        <!-- Hidden submitted time input -->
                        <input type="hidden" id="appointment_time" name="time" required>
                    </div>

                    <!-- Services Summary -->
                    <div class="mt-4 p-3 rounded-3" style="background: rgba(15, 23, 42, 0.03); border-right: 4px solid #38bdf8;">
                        <h5 class="mb-3 text-dark" style="font-weight: 700;">💰 تفاصيل السعر والمدة الإجمالية :</h5>
                        
                        <!-- Minimum price limit warning banner -->
                        <div id="min_price_warning_banner" class="alert alert-warning border-0 bg-warning bg-opacity-10 text-warning p-3 rounded-4 mb-3" style="display: none; font-size: 0.95rem; font-weight: 700;">
                            ⚠️ يجب أن لا يقل إجمالي سعر جلسات المجموعة عن <span id="min_price_setting_val">2100</span> ج.م لتأكيد الحجز. (السعر الحالي للجلسات: <span id="min_price_current_val">0</span> ج.م)
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>السعر الإجمالي لجميع جلسات :</span>
                            <span class="fw-bold text-dark"><span id="summary_total_price" class="text-warning fs-5">0</span> ج.م</span>
                        </div>
                        <div id="urgent_fee_row" class="justify-content-between mb-2" style="display: none;">
                            <span>رسوم الحجز المستعجل الإضافية:</span>
                            <span class="fw-bold text-warning"><span id="summary_urgent_fee">0</span> ج.م</span>
                        </div>
                        <div class="d-flex justify-content-between" id="duration_row">
                            <span>المدة الأقصى المتوقعة (لجلسة متزامنة):</span>
                            <span class="fw-bold text-dark"><span id="summary_total_duration" class="text-info">0</span> دقيقة</span>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="mt-4 p-3 rounded-3" style="background: rgba(15, 23, 42, 0.02); border-right: 4px solid #ff9d42;">
                        <h6 class="fw-bold mb-3 text-dark" style="font-size: 1.05rem; border-bottom: 1px solid rgba(15, 23, 42, 0.08); padding-bottom: 0.5rem;">⚖️ الأحكام والشروط:</h6>
                        <ul class="mb-0 text-dark" style="list-style-type: none; padding-right: 0.5rem; font-size: 0.95rem; line-height: 1.8;">
                            <li>
                                📌 يرجى ارسال مقدم حجز بقيمة 40% من السعر الإجمالي = <strong class="text-warning" id="deposit_amount">0</strong> جنيه 
                                و ارسال صورة التحويل على واتساب رقم <strong class="text-dark">01064344092</strong>
                            </li>
                            <li>
                                ⚠️ في حال التأخر عن الموعد أكثر من 10 دقائق يتم خصم 50 % من مقدم الحجز
                            </li>
                            <li>
                                ⚠️ في حال التأخر عن الموعد أكثر من 20 دقيقة يتم خصم 100 % من مقدم الحجز ويتم إلغاء الحجز
                            </li>
                            <li>
                                ⚠️ خلال ساعتين اذا لم يتم دفع المقدم يلغى الحجز تلقائيا.
                            </li>
                            <li>
                                ⚠️ لالغاء الحجز يرجى ابلاغنا قبل الميعاد بـ 5 ساعات على الاقل لاسترداد مقدم الحجز.
                            </li>
                        </ul>
                    </div>

                    <!-- Required Agreement Fields -->
                    <div class="mt-4 text-center">
                        <label class="form-label d-block mb-3" style="font-weight: 700;">هل توافق على شروط الحجز والمقدم المالي أعلاه؟ *</label>
                        <div class="d-flex justify-content-center gap-4">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="user_agreement" id="agree_yes" value="موافق" required style="accent-color: #2ecc71; width: 1.3rem; height: 1.3rem; margin-left: 0.5rem;">
                                <label class="form-check-label text-success fw-bold" for="agree_yes" style="cursor: pointer; font-size: 1.1rem;">موافق</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="user_agreement" id="agree_no" value="الغاء الحجز" required onclick="window.location.href='{{ url('/') }}'" style="accent-color: #e67e22; width: 1.3rem; height: 1.3rem; margin-left: 0.5rem;">
                                <label class="form-check-label text-danger fw-bold" for="agree_no" style="cursor: pointer; font-size: 1.1rem;">الغاء الحجز</label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit">تأكيد حجز السيشن</button>

            <div class="text-center">
                <a href="{{ route('booking.index') }}" class="btn-back">← العودة للخيارات</a>
            </div>
            </form>
        @endif
    </div>

    <!-- Hidden Attendee Template -->
    <template id="attendee-template">
        <div class="attendee-card card mb-4 p-4 rounded-4 position-relative" style="background: rgba(15, 23, 42, 0.01); border: 1px solid rgba(15, 23, 42, 0.05);" id="attendee-card-{index}">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3 btn-remove-attendee" data-index="{index}" aria-label="Close" style="display: none;"></button>
            
            <h4 class="mb-4" style="font-weight: 700; color: #ff9d42;">بيانات الشخص رقم <span class="attendee-number">{number}</span></h4>
            
            <!-- Client Info -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label for="name-{index}" class="form-label">الاسم ثلاثي *</label>
                    <input type="text" class="form-control" id="name-{index}" name="attendees[{index}][name]" required placeholder="أدخل الاسم الكامل">
                </div>
                <div class="col-md-4">
                    <label for="phone-{index}" class="form-label">رقم الهاتف *</label>
                    <input type="tel" class="form-control" id="phone-{index}" name="attendees[{index}][phone]" required placeholder="أدخل رقم الهاتف">
                </div>
                <div class="col-md-4">
                    <label for="gender-{index}" class="form-label">الجنس *</label>
                    <select class="form-select gender-select" id="gender-{index}" name="attendees[{index}][gender]" required>
                        <option value="" disabled selected hidden>اختر الجنس...</option>
                        <option value="male">ذكر</option>
                        <option value="female">أنثى</option>
                    </select>
                </div>
            </div>

            <!-- Booking Type -->
            <input type="hidden" name="attendees[{index}][booking_type]" id="booking_type-{index}" value="وقائية">
            <input type="hidden" name="attendees[{index}][treatment_style]" id="treatment_style-{index}" value="intensive">

            <!-- Accordions Container -->
            <div id="accordions-container-{index}">
                <div class="accordion" id="bookingAccordion-{index}">
                    
                    <!-- Accordion 1: كيروبراكتيك -->
                    <div class="accordion-item" id="accordion-physio-{index}">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePhysio-{index}" aria-expanded="false" aria-controls="collapsePhysio-{index}">
                                ⚡ كيروبراكتيك (تقويم العمود الفقري)
                            </button>
                        </h2>
                        <div id="collapsePhysio-{index}" class="accordion-collapse collapse" data-bs-parent="#bookingAccordion-{index}">
                            <div class="accordion-body">
                                <div class="booking-row">
                                    <div class="packages-col">
                                        <label class="form-label d-block mb-3">اختر نوع تقويم العمود الفقري *</label>
                                        
                                        <div class="package-checkbox-card">
                                            <input type="radio" id="cracking_none-{index}" name="attendees[{index}][cracking_type]" value="none" checked>
                                            <label for="cracking_none-{index}" class="ms-2">
                                                بدون تقويم عمود فقري
                                                <div class="fw-normal text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">لا يتم إضافة أي تكلفة إضافية</div>
                                            </label>
                                        </div>
                                        
                                        <div class="package-checkbox-card">
                                            <input type="radio" id="cracking_full_body-{index}" name="attendees[{index}][cracking_type]" value="whole_body">
                                            <label for="cracking_full_body-{index}" class="ms-2">
                                                تقويم الجسم كامل
                                                <div class="fw-normal text-warning" style="font-size: 0.85rem; margin-top: 0.25rem;"> السعر 350 جنيه بدلا من <span style="text-decoration: line-through;">450 جنيه</span></div>
                                            </label>
                                        </div>
                                        
                                        <div class="package-checkbox-card">
                                            <input type="radio" id="cracking_regions_option-{index}" name="attendees[{index}][cracking_type]" value="regions">
                                            <label for="cracking_regions_option-{index}" class="ms-2">
                                                اختيار مناطق من الصورة
                                                <div class="fw-normal text-warning" style="font-size: 0.85rem; margin-top: 0.25rem;"> المنطقة ب 150 جنيه</div>
                                            </label>
                                        </div>
                                        
                                        <!-- Hidden inputs for cracking regions -->
                                        <div id="hidden-cracking-regions-inputs-{index}"></div>
                                    </div>
                                    
                                    <div class="pricing-col">
                                        <div class="pricing-table-container">
                                            <h4>سعر خدمة التقويم المختارة للشخص</h4>
                                            <table class="pricing-table">
                                                <thead>
                                                    <tr>
                                                        <th>النوع</th>
                                                        <th>السعر</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><strong id="cracking_price_desc-{index}">بدون تقويم</strong></td>
                                                        <td><span class="price-value" id="cracking_price_value-{index}">0.00 ج.م</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <div class="body-map-col" id="cracking_map_col-{index}">
                                        <div class="form-label text-center mb-2">
                                            <p style="color: #16a34a; margin-bottom: 0; font-weight: 600;">اضغط على الأرقام لاختيار وتحديد مناطق (1، 2، 3)</p>
                                        </div>
                                        <div class="body-map-container cracking-map-container-el" id="cracking-map-container-{index}" style="max-width: 250px; aspect-ratio: 200 / 420;">
                                            <img src="{{ asset('images/cracking.png') }}" alt="Cracking Spine Chart" class="body-map-img" style="max-width: 250px;">
                                        </div>
                                        <p class="text-muted mt-2" style="font-size: 0.8rem;">يمكنك تحديد منطقة أو أكثر مباشرة من الصورة بالضغط على الأرقام.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Accordion 2: مساج -->
                    <div class="accordion-item" id="accordion-massage-{index}">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMassage-{index}" aria-expanded="false" aria-controls="collapseMassage-{index}">
                                💆‍♂️ مساج
                            </button>
                        </h2>
                        <div id="collapseMassage-{index}" class="accordion-collapse collapse" data-bs-parent="#bookingAccordion-{index}">
                            <div class="accordion-body">
                                <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger p-3 rounded-4 mb-4 text-center" style="font-size: 1.05rem; font-weight: 700;">
                                    ⚠️ لا يوجد مختصين رجال للسيدات أو مختصين سيدات للرجال
                                </div>
                                <div class="booking-row">
                                    <div class="packages-col">
                                        <label class="form-label d-block mb-3">اختر باقة المساج *</label>
                                        
                                        <div class="package-checkbox-card">
                                            <input type="radio" id="massage_none-{index}" name="attendees[{index}][massage_package_choice]" value="none" checked>
                                            <label for="massage_none-{index}" class="ms-2">
                                                بدون مساج
                                                <div class="fw-normal text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">لا يتم إضافة أي تكلفة إضافية للمساج</div>
                                            </label>
                                        </div>

                                        <div class="package-checkbox-card">
                                            <input type="radio" id="package_intensive-{index}" name="attendees[{index}][massage_package_choice]" value="intensive">
                                            <label for="package_intensive-{index}" class="ms-2">
                                                الجسم كامل مكثف (Intensive Luxury)
                                                <div class="fw-normal text-warning" style="font-size: 0.85rem; margin-top: 0.25rem;">نسبة التحسن أعلى والنتائج تدوم أطول</div>
                                            </label>
                                        </div>

                                        <div class="package-checkbox-card">
                                            <input type="radio" id="package_economy-{index}" name="attendees[{index}][massage_package_choice]" value="economy">
                                            <label for="package_economy-{index}" class="ms-2">
                                                الجسم كامل اقتصادي (Economy)
                                                <div class="fw-normal text-warning" style="font-size: 0.85rem; margin-top: 0.25rem;">نسبة التحسن أقل والنتائج تدوم لفترة أقصر</div>
                                            </label>
                                        </div>

                                        <div class="package-checkbox-card">
                                            <input type="radio" id="package_regions_only-{index}" name="attendees[{index}][massage_package_choice]" value="regions_only">
                                            <label for="package_regions_only-{index}" class="ms-2">
                                                اختيار مناطق مخصصة فقط من خريطة الجسم
                                            </label>
                                        </div>

                                        <div id="hidden-regions-inputs-{index}"></div>
                                        <div id="hidden-packages-inputs-{index}"></div>
                                        <input type="hidden" name="attendees[{index}][massage_intensity]" id="massage_intensity_hidden-{index}" value="medium">
                                    </div>

                                    <div class="intensity-col mt-3 p-3 rounded-3" id="massage_intensity_container-{index}" style="background: rgba(15, 23, 42, 0.02); display: none; border-right: 4px solid #ff9d42; margin-bottom: 1rem;">
                                        <label class="form-label d-block mb-2 text-dark" style="font-weight: 600;">اختر شدة المساج (Intensity) *</label>
                                        <div class="d-flex gap-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="attendees[{index}][massage_intensity_radio]" id="intensity_medium-{index}" value="medium" checked>
                                                <label class="form-check-label text-dark ms-2" for="intensity_medium-{index}">
                                                    ميديم (Medium)
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="attendees[{index}][massage_intensity_radio]" id="intensity_hard-{index}" value="hard">
                                                <label class="form-check-label text-dark ms-2" for="intensity_hard-{index}">
                                                    هارد (Hard)
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pricing-col">
                                        <div class="pricing-table-container">
                                            <h4>فئات المساج والأسعار للشخص</h4>
                                            <table class="pricing-table">
                                                <thead>
                                                    <tr>
                                                        <th>فئة الخدمة</th>
                                                        <th>المدة المتوقعة</th>
                                                        <th>السعر الإجمالي</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="pricing-rows-{index}">
                                                    <!-- Dynamic rows -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="body-map-col" id="massage_map_col-{index}" style="display: none;">
                                        <div class="form-label text-center mb-2">
                                            <p style="color: #16a34a; margin-bottom: 0; font-weight: 600;">اضغط على الأرقام لاختيار وتحديد مناطق الجسم</p>
                                        </div>
                                        <div class="body-map-container" id="massage-map-container-{index}">
                                            <img src="{{ asset('images/body.jpg') }}" alt="Body Chart" class="body-map-img">
                                        </div>
                                        <p class="text-muted mt-2" style="font-size: 0.8rem;">يمكنك النقر مباشرة على الأرقام في الصورة لتحديد المناطق الإضافية المطلوبة.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Accordion 3: الحجامة -->
                    <div class="accordion-item" id="accordion-hijama-{index}">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHijama-{index}" aria-expanded="false" aria-controls="collapseHijama-{index}">
                                🏺 الحجامة (Hijama / Cupping)
                            </button>
                        </h2>
                        <div id="collapseHijama-{index}" class="accordion-collapse collapse" data-bs-parent="#bookingAccordion-{index}">
                            <div class="accordion-body">
                                <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger p-3 rounded-4 mb-4 text-center" style="font-size: 1.05rem; font-weight: 700;">
                                    ⚠️ لا يوجد مختصين رجال للسيدات أو مختصين سيدات للرجال
                                </div>
                                <div class="booking-row-hijama">
                                    <div class="packages-col">
                                        <label class="form-label d-block mb-3">اختر نوع الحجامة *</label>
                                        
                                        <div class="package-checkbox-card">
                                            <input type="radio" id="hijama_none-{index}" name="attendees[{index}][hijama_type]" value="none" checked>
                                            <label for="hijama_none-{index}" class="ms-2">
                                                بدون حجامة
                                                <div class="fw-normal text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">لا يتم إضافة أي تكلفة إضافية</div>
                                            </label>
                                        </div>
                                        
                                        <div class="package-checkbox-card">
                                            <input type="radio" id="hijama_whole_back-{index}" name="attendees[{index}][hijama_type]" value="whole_back">
                                            <label for="hijama_whole_back-{index}" class="ms-2">
                                                خلفيات الجسم كامل
                                                <div class="fw-normal text-warning" style="font-size: 0.85rem; margin-top: 0.25rem;"> يحدد تلقائياً مناطق الظهر والخلفيات</div>
                                            </label>
                                        </div>
                                        
                                        <div class="package-checkbox-card">
                                            <input type="radio" id="hijama_whole_front-{index}" name="attendees[{index}][hijama_type]" value="whole_front">
                                            <label for="hijama_whole_front-{index}" class="ms-2">
                                                اماميات الجسم كامل
                                                <div class="fw-normal text-warning" style="font-size: 0.85rem; margin-top: 0.25rem;"> يحدد تلقائياً مناطق الصدر والبطن والأماميات</div>
                                            </label>
                                        </div>

                                        <div class="package-checkbox-card">
                                            <input type="radio" id="hijama_regions_option-{index}" name="attendees[{index}][hijama_type]" value="regions">
                                            <label for="hijama_regions_option-{index}" class="ms-2">
                                                اختيار مناطق من الصورة
                                                <div class="fw-normal text-warning" style="font-size: 0.85rem; margin-top: 0.25rem;"> يتم تحديد عدد الكاسات والسعر بناءً على المناطق المختارة</div>
                                            </label>
                                        </div>

                                        <div id="hidden-hijama-regions-inputs-{index}"></div>
                                    </div>

                                    <div id="hijama_style_section-{index}" class="hijama-style-col mt-4 p-3 rounded-4" style="background: rgba(15, 23, 42, 0.02); border: 1px solid rgba(15, 23, 42, 0.05); display: none;">
                                        <label class="form-label d-block mb-3">اختر طريقة سيشن الحجامة *</label>
                                        <div class="d-flex flex-column gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="attendees[{index}][hijama_style]" id="hijama_style_intensive-{index}" value="intensive" checked>
                                                <label class="form-check-label text-dark" for="hijama_style_intensive-{index}" style="font-weight: 600;">
                                                    مكثف (تحسن 60-100% - كاسات أكثر)
                                                    <div class="fw-normal text-warning" style="font-size: 0.85rem; margin-top: 0.25rem;">نسبة التحسن أعلى والنتائج تدوم أطول</div>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="attendees[{index}][hijama_style]" id="hijama_style_economy-{index}" value="economy">
                                                <label class="form-check-label text-dark" for="hijama_style_economy-{index}" style="font-weight: 600;">
                                                    اقتصادي (تحسن 40-70% - كاسات أقل)
                                                    <div class="fw-normal text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">نسبة التحسن أقل والنتائج تدوم لفترة أقصر</div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="pricing-col">
                                        <div class="pricing-table-container">
                                            <h4>سعر الحجامة المختارة للشخص</h4>
                                            <table class="pricing-table">
                                                <thead>
                                                    <tr>
                                                        <th>النوع</th>
                                                        <th>الكاسات</th>
                                                        <th>السعر</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><strong id="hijama_price_desc-{index}">بدون حجامة</strong></td>
                                                        <td><span class="duration-value" id="hijama_cups_value-{index}">0 كاس</span></td>
                                                        <td><span class="price-value" id="hijama_price_value-{index}">0.00 ج.م</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <div class="body-map-col" id="hijama_map_col-{index}" style="display: none;">
                                        <div class="form-label text-center mb-2">
                                            <p style="color: #16a34a; font-weight: 600;">اضغط على الأرقام لاختيار وتحديد مناطق الحجامة (1 إلى 39)</p>
                                        </div>
                                        <div class="body-map-container" id="hijama-map-container-{index}">
                                            <img src="{{ asset('images/body.jpg') }}" alt="Hijama Body Chart" class="body-map-img">
                                        </div>
                                        <p class="text-muted mt-2" style="font-size: 0.8rem;">يمكنك تعديل وتحديد المناطق مباشرة من الصورة بالضغط على الأرقام.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            
            <!-- Attendee Summary Block -->
            <div class="mt-3 p-3 rounded-3" style="background: rgba(15, 23, 42, 0.01); border-right: 4px solid #e67e22; font-size: 0.95rem;">
                <div class="d-flex justify-content-between">
                    <span>حساب الشخص <span class="attendee-number">{number}</span>:</span>
                    <span class="fw-bold text-dark">السعر: <span id="attendee_total_price_val-{index}">0.00</span> ج.م | المدة: <span id="attendee_total_duration_val-{index}">0</span> دقيقة</span>
                </div>
            </div>
        </div>
    </template>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Repetition definitions
            const regionRepetitions = {
                1: 2, 2: 1, 3: 2, 4: 3, 5: 2, 6: 1, 7: 2, 8: 3,
                9: 1, 10: 1, 11: 1, 12: 1, 13: 1, 14: 1, 15: 1, 16: 1,
                17: 1, 18: 1, 19: 1, 20: 1, 21: 1, 22: 1, 23: 1, 24: 1,
                25: 2, 26: 3, 27: 2, 28: 2, 29: 3, 30: 2,
                31: 1, 32: 1, 33: 1, 34: 1, 35: 1, 36: 1,
                37: 1, 38: 2, 39: 2
            };

            // Percentage coordinates of numbered body labels (1 to 39)
            const regionCoords = {
                1: {top: 59, left: 82.8},
                2: {top: 69.8,left: 82.2},
                3:{top: 77.5,left: 82.2},
                4:{top: 90.5,left: 82.2},
                5:{top: 59.5,left: 88.2},
                6:{top: 70.5,left: 88.2},
                7:{top: 78.5,left: 89.2},
                8:{top: 91.5,left: 88.2},
                9:{top: 45.5,left: 82.2},
                10:{top: 45.5,left: 88.2},
                11:{top: 36.5,left: 82.2},
                12:{top: 37.5,left: 89.2},
                13:{top: 25.5,left: 83.2},
                14:{top: 26.5,left: 89.2},
                15:{top: 17.5,left: 83.2},
                16:{top: 17.5,left: 88.5},
                17:{top: 20.5,left: 77.8},
                18:{top: 28.5,left: 77},
                19:{top: 38.5,left: 76.5},
                20:{top: 20.5,left: 93.8},
                21:{top: 29.5,left: 94.6},
                22:{top: 39.5,left: 95.2},
                23:{top: 20.5,left: 64},
                24:{top: 18.5,left: 45.2},
                25:{top: 54.5,left: 10},
                26:{top: 69.5,left: 10},
                27:{top: 78.5,left: 10},
                28:{top: 55,left: 17.2},
                29:{top: 69.5,left: 17.2},
                30:{top: 79.5,left: 17.2},
                31:{top: 23.5,left: 15.5},
                32:{top: 23.5,left: 10.5},
                33:{top: 19.5,left: 20},
                34:{top: 26.5,left: 21.5},
                35:{top: 19.5,left: 6},
                36:{top: 27.5,left: 5.5},
                37:{top: 9.5,left: 85.8},
                38:{top: 89.5,left: 16.2},
                39:{top: 88.5,left: 10}
            };

            const crackingRegionCoords = {
                1: [{top: 14.9, left: 49.0}],
                2: [{top: 37.6, left: 49.0}],
                3: [{top: 26.9, left: 23.5}, {top: 26.9, left: 75.5}, {top: 59.0, left: 50.0}]
            };

            const minBookingAmount = {{ $minBookingAmount }};
            const urgentBookingFee = {{ $urgentBookingFee }};

            let attendees = [];
            let nextAttendeeIndex = 0;

            const attendeesListEl = document.getElementById('attendees-list');
            const templateHtml = document.getElementById('attendee-template').innerHTML;

            function addAttendee() {
                const index = nextAttendeeIndex++;
                const number = attendeesListEl.children.length + 1;

                // Create attendee object
                const attendee = {
                    index: index,
                    selectedRegions: new Set(),
                    selectedCrackingRegions: new Set(),
                    selectedHijamaRegions: new Set(),
                    duration: 0,
                    price: 0
                };
                attendees.push(attendee);

                // Compile and append template HTML
                let compiledHtml = templateHtml
                    .replaceAll('{index}', index)
                    .replaceAll('{number}', number);

                const wrapper = document.createElement('div');
                wrapper.innerHTML = compiledHtml;
                const cardEl = wrapper.firstElementChild;
                attendeesListEl.appendChild(cardEl);

                // Show close/delete button if not the first attendee
                if (number > 1) {
                    cardEl.querySelector('.btn-remove-attendee').style.display = 'block';
                }

                // Initialize interactive maps and hotspots
                initAttendeeMaps(index);

                // Set up event listeners for inputs and selections
                setupAttendeeEventListeners(index);

                // Smooth scroll accordion top view behavior
                cardEl.querySelectorAll('.accordion-collapse').forEach(collapseEl => {
                    collapseEl.addEventListener('shown.bs.collapse', function () {
                        const accordionItem = this.closest('.accordion-item');
                        if (accordionItem) {
                            accordionItem.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    });
                });

                // Update pricing and UI state
                updateFormInputsAndPricing();
            }

            function removeAttendee(index) {
                // Find and remove attendee object
                attendees = attendees.filter(a => a.index !== index);

                // Remove DOM card
                const cardEl = document.getElementById(`attendee-card-${index}`);
                if (cardEl) {
                    cardEl.remove();
                }

                // Re-sequence numbers of cards
                Array.from(attendeesListEl.children).forEach((card, idx) => {
                    const numSpan = card.querySelector('.attendee-number');
                    if (numSpan) {
                        numSpan.textContent = idx + 1;
                    }
                });

                // Update pricing
                updateFormInputsAndPricing();
            }

            // Register remove click event delegation
            attendeesListEl.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-remove-attendee')) {
                    const idx = parseInt(e.target.dataset.index);
                    removeAttendee(idx);
                }
            });

            // Add Attendee click listener
            document.getElementById('btn-add-attendee').addEventListener('click', addAttendee);

            function initAttendeeMaps(index) {
                const att = attendees.find(a => a.index === index);
                if (!att) return;

                // 1. Massage Map Hotspots
                const massageMapContainer = document.getElementById(`massage-map-container-${index}`);
                for (let i = 1; i <= 39; i++) {
                    const coord = regionCoords[i];
                    if (!coord) continue;

                    const hotspot = document.createElement('div');
                    hotspot.className = 'hotspot available';
                    hotspot.style.top = coord.top + '%';
                    hotspot.style.left = coord.left + '%';
                    hotspot.dataset.region = i;
                    hotspot.innerHTML = i;
                    hotspot.addEventListener('click', function() {
                        const rNum = parseInt(this.dataset.region);
                        const massageNoneRadio = document.getElementById(`massage_none-${index}`);
                        const regionsOnlyRadio = document.getElementById(`package_regions_only-${index}`);
                        
                        if (massageNoneRadio && massageNoneRadio.checked && regionsOnlyRadio) {
                            regionsOnlyRadio.checked = true;
                            // Show maps
                            document.getElementById(`massage_map_col-${index}`).style.display = 'block';
                            document.getElementById(`massage_intensity_container-${index}`).style.display = 'block';
                        }
                        
                        if (att.selectedRegions.has(rNum)) {
                            att.selectedRegions.delete(rNum);
                            this.classList.remove('selected');
                        } else {
                            att.selectedRegions.add(rNum);
                            this.classList.add('selected');
                        }
                        updateFormInputsAndPricing();
                    });
                    massageMapContainer.appendChild(hotspot);
                }

                // 2. Cracking Map Hotspots
                const crackingMapContainer = document.getElementById(`cracking-map-container-${index}`);
                if (crackingMapContainer) {
                    for (let rNum in crackingRegionCoords) {
                        const coords = crackingRegionCoords[rNum];
                        coords.forEach(coord => {
                            const hotspot = document.createElement('div');
                            hotspot.className = 'hotspot available';
                            hotspot.style.top = coord.top + '%';
                            hotspot.style.left = coord.left + '%';
                            hotspot.dataset.region = rNum;
                            hotspot.innerHTML = rNum;
                            hotspot.addEventListener('click', function() {
                                const rVal = parseInt(this.dataset.region);
                                const crackingRegionsRadio = document.getElementById(`cracking_regions_option-${index}`);
                                
                                if (crackingRegionsRadio && !crackingRegionsRadio.checked) {
                                    crackingRegionsRadio.checked = true;
                                    att.selectedCrackingRegions.clear();
                                    document.querySelectorAll(`#cracking-map-container-${index} .hotspot`).forEach(el => el.classList.remove('selected'));
                                }

                                if (att.selectedCrackingRegions.has(rVal)) {
                                    att.selectedCrackingRegions.delete(rVal);
                                    document.querySelectorAll(`#cracking-map-container-${index} .hotspot[data-region="${rVal}"]`).forEach(el => el.classList.remove('selected'));
                                } else {
                                    att.selectedCrackingRegions.add(rVal);
                                    document.querySelectorAll(`#cracking-map-container-${index} .hotspot[data-region="${rVal}"]`).forEach(el => el.classList.add('selected'));
                                }
                                updateFormInputsAndPricing();
                            });
                            crackingMapContainer.appendChild(hotspot);
                        });
                    }
                }

                // 3. Hijama Map Hotspots
                const hijamaMapContainer = document.getElementById(`hijama-map-container-${index}`);
                if (hijamaMapContainer) {
                    for (let i = 1; i <= 39; i++) {
                        const coord = regionCoords[i];
                        if (!coord) continue;

                        const hotspot = document.createElement('div');
                        hotspot.className = 'hotspot available';
                        hotspot.style.top = coord.top + '%';
                        hotspot.style.left = coord.left + '%';
                        hotspot.dataset.region = i;
                        hotspot.innerHTML = i;
                        hotspot.addEventListener('click', function() {
                            const rVal = parseInt(this.dataset.region);
                            const hijamaRegionsRadio = document.getElementById(`hijama_regions_option-${index}`);
                            
                            if (hijamaRegionsRadio && !hijamaRegionsRadio.checked) {
                                hijamaRegionsRadio.checked = true;
                                att.selectedHijamaRegions.clear();
                                document.querySelectorAll(`#hijama-map-container-${index} .hotspot`).forEach(el => el.classList.remove('selected'));
                            }

                            if (att.selectedHijamaRegions.has(rVal)) {
                                att.selectedHijamaRegions.delete(rVal);
                                this.classList.remove('selected');
                            } else {
                                att.selectedHijamaRegions.add(rVal);
                                this.classList.add('selected');
                            }
                            updateFormInputsAndPricing();
                        });
                        hijamaMapContainer.appendChild(hotspot);
                    }
                }
            }

            function setupAttendeeEventListeners(index) {
                const att = attendees.find(a => a.index === index);
                if (!att) return;

                // Massage choices change handler
                const massageChoices = document.querySelectorAll(`input[name="attendees[${index}][massage_package_choice]"]`);
                massageChoices.forEach(radio => {
                    radio.addEventListener('change', function() {
                        const massageMapCol = document.getElementById(`massage_map_col-${index}`);
                        const massageIntensityContainer = document.getElementById(`massage_intensity_container-${index}`);
                        
                        if (this.value === 'none') {
                            att.selectedRegions.clear();
                            document.querySelectorAll(`#massage-map-container-${index} .hotspot`).forEach(el => el.classList.remove('selected'));
                            if (massageMapCol) massageMapCol.style.display = 'none';
                            if (massageIntensityContainer) massageIntensityContainer.style.display = 'none';
                        } else {
                            if (massageMapCol) massageMapCol.style.display = 'block';
                            if (massageIntensityContainer) massageIntensityContainer.style.display = 'block';
                        }
                        updateFormInputsAndPricing();
                    });
                });

                // Massage intensity change handler
                const intensityRadios = document.querySelectorAll(`input[name="attendees[${index}][massage_intensity_radio]"]`);
                intensityRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        const intensityHidden = document.getElementById(`massage_intensity_hidden-${index}`);
                        if (intensityHidden) {
                            intensityHidden.value = this.value;
                        }
                        updateFormInputsAndPricing();
                    });
                });

                // Cracking type change handler
                const crackingRadios = document.querySelectorAll(`input[name="attendees[${index}][cracking_type]"]`);
                crackingRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        if (this.value === 'none' || this.value === 'whole_body') {
                            att.selectedCrackingRegions.clear();
                            document.querySelectorAll(`#cracking-map-container-${index} .hotspot`).forEach(el => el.classList.remove('selected'));
                        }
                        updateFormInputsAndPricing();
                    });
                });

                // Hijama type change handler
                const hijamaRadios = document.querySelectorAll(`input[name="attendees[${index}][hijama_type]"]`);
                hijamaRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        const hijamaStyleSection = document.getElementById(`hijama_style_section-${index}`);
                        const hijamaMapCol = document.getElementById(`hijama_map_col-${index}`);
                        
                        document.querySelectorAll(`#hijama-map-container-${index} .hotspot`).forEach(el => el.classList.remove('selected'));
                        att.selectedHijamaRegions.clear();

                        if (this.value === 'none') {
                            if (hijamaStyleSection) hijamaStyleSection.style.display = 'none';
                            if (hijamaMapCol) hijamaMapCol.style.display = 'none';
                        } else {
                            if (hijamaStyleSection) hijamaStyleSection.style.display = 'block';
                            if (hijamaMapCol) hijamaMapCol.style.display = 'block';

                            const hijamaBackPreset = [1, 3, 5, 7, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 37];
                            const hijamaFrontPreset = [19, 22, 23, 24, 25, 27, 28, 30, 31, 32, 33, 34, 35, 36];

                            if (this.value === 'whole_back') {
                                hijamaBackPreset.forEach(rNum => {
                                    att.selectedHijamaRegions.add(rNum);
                                    const hs = document.querySelector(`#hijama-map-container-${index} .hotspot[data-region="${rNum}"]`);
                                    if (hs) hs.classList.add('selected');
                                });
                            } else if (this.value === 'whole_front') {
                                hijamaFrontPreset.forEach(rNum => {
                                    att.selectedHijamaRegions.add(rNum);
                                    const hs = document.querySelector(`#hijama-map-container-${index} .hotspot[data-region="${rNum}"]`);
                                    if (hs) hs.classList.add('selected');
                                });
                            }
                        }
                        updateFormInputsAndPricing();
                    });
                });

                // Hijama style change handler
                const hijamaStyles = document.querySelectorAll(`input[name="attendees[${index}][hijama_style]"]`);
                hijamaStyles.forEach(radio => {
                    radio.addEventListener('change', updateFormInputsAndPricing);
                });

                // Gender change handler
                const genderSel = document.getElementById(`gender-${index}`);
                if (genderSel) {
                    genderSel.addEventListener('change', function() {
                        const isUrgentChecked = document.getElementById('is_urgent') && document.getElementById('is_urgent').checked;
                        if (isUrgentChecked) {
                            validateTimeSelection();
                        } else {
                            fetchAvailableTimes();
                        }
                    });
                }
            }

            let globalMaxGroupDuration = 0;

            function updateFormInputsAndPricing() {
                let grandTotalSessionsPrice = 0;
                globalMaxGroupDuration = 0;

                attendees.forEach(att => {
                    const index = att.index;
                    
                    // 1. Update Hidden Form Inputs inside cards
                    const hiddenRegionsContainer = document.getElementById(`hidden-regions-inputs-${index}`);
                    if (hiddenRegionsContainer) {
                        hiddenRegionsContainer.innerHTML = '';
                        att.selectedRegions.forEach(rNum => {
                            const inp = document.createElement('input');
                            inp.type = 'hidden'; inp.name = `attendees[${index}][regions][]`; inp.value = rNum;
                            hiddenRegionsContainer.appendChild(inp);
                        });
                    }

                    const hiddenCrackingContainer = document.getElementById(`hidden-cracking-regions-inputs-${index}`);
                    if (hiddenCrackingContainer) {
                        hiddenCrackingContainer.innerHTML = '';
                        att.selectedCrackingRegions.forEach(rNum => {
                            const inp = document.createElement('input');
                            inp.type = 'hidden'; inp.name = `attendees[${index}][cracking_regions][]`; inp.value = rNum;
                            hiddenCrackingContainer.appendChild(inp);
                        });
                    }

                    const hiddenHijamaContainer = document.getElementById(`hidden-hijama-regions-inputs-${index}`);
                    if (hiddenHijamaContainer) {
                        hiddenHijamaContainer.innerHTML = '';
                        att.selectedHijamaRegions.forEach(rNum => {
                            const inp = document.createElement('input');
                            inp.type = 'hidden'; inp.name = `attendees[${index}][hijama_regions][]`; inp.value = rNum;
                            hiddenHijamaContainer.appendChild(inp);
                        });
                    }

                    const hiddenPackagesContainer = document.getElementById(`hidden-packages-inputs-${index}`);
                    const packChoice = document.querySelector(`input[name="attendees[${index}][massage_package_choice]"]:checked`).value;
                    if (hiddenPackagesContainer) {
                        hiddenPackagesContainer.innerHTML = '';
                        if (packChoice === 'intensive' || packChoice === 'economy') {
                            const inp = document.createElement('input');
                            inp.type = 'hidden'; inp.name = `attendees[${index}][packages][]`; inp.value = packChoice;
                            hiddenPackagesContainer.appendChild(inp);
                        }
                    }

                    // 2. Calculate Attendee Pricing & Duration
                    let totalRepetitions = 0;
                    att.selectedRegions.forEach(rNum => {
                        totalRepetitions += regionRepetitions[rNum] || 0;
                    });

                    const isIntensiveChecked = packChoice === 'intensive';
                    const isEconomyChecked = packChoice === 'economy';
                    const isRegionsOnlyChecked = packChoice === 'regions_only';
                    const isHard = document.getElementById(`intensity_hard-${index}`).checked;

                    let massageDuration = 0;
                    let massagePrice = 0;
                    const pricingRows = document.getElementById(`pricing-rows-${index}`);

                    if (pricingRows) {
                        pricingRows.innerHTML = '';
                        if (isIntensiveChecked || isEconomyChecked || att.selectedRegions.size > 0) {
                            if (isIntensiveChecked) {
                                document.getElementById(`treatment_style-${index}`).value = 'intensive';
                                massageDuration = 92.4 + (totalRepetitions * 1.54);
                                massagePrice = isHard ? 1570.8 + (totalRepetitions * 26.18) : 1201.2 + (totalRepetitions * 20.02);
                                pricingRows.innerHTML = `<tr><td><strong>كامل الجسم مكثف (${isHard ? 'هارد' : 'ميديم'})</strong></td><td><span>${massageDuration.toFixed(1)} د</span></td><td><span>${massagePrice.toFixed(2)} ج.م</span></td></tr>`;
                            } else if (isEconomyChecked) {
                                document.getElementById(`treatment_style-${index}`).value = 'economy';
                                massageDuration = 61.6 + (totalRepetitions * 1.54);
                                massagePrice = isHard ? 866.8 + (totalRepetitions * 21.67) : 660.4 + (totalRepetitions * 16.51);
                                pricingRows.innerHTML = `<tr><td><strong>كامل الجسم اقتصادي (${isHard ? 'هارد' : 'ميديم'})</strong></td><td><span>${massageDuration.toFixed(1)} د</span></td><td><span>${massagePrice.toFixed(2)} ج.م</span></td></tr>`;
                            } else {
                                // Regions only
                                const durationVal = totalRepetitions * 1.54;
                                const treatmentStyleVal = document.getElementById(`treatment_style-${index}`).value;
                                
                                const priceInt = totalRepetitions * (isHard ? 26.18 : 20.02);
                                const priceEco = totalRepetitions * (isHard ? 21.67 : 16.51);

                                pricingRows.innerHTML = `
                                    <tr>
                                        <td>
                                            <input type="radio" name="style-${index}" value="intensive" ${treatmentStyleVal === 'intensive' ? 'checked' : ''} id="s1-${index}"> 
                                            <label for="s1-${index}">مكثف (${isHard ? 'هارد' : 'ميديم'})</label>
                                        </td>
                                        <td>${durationVal.toFixed(1)} د</td>
                                        <td>${priceInt.toFixed(2)} ج.م</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="radio" name="style-${index}" value="economy" ${treatmentStyleVal === 'economy' ? 'checked' : ''} id="s2-${index}"> 
                                            <label for="s2-${index}">اقتصادي (${isHard ? 'هارد' : 'ميديم'})</label>
                                        </td>
                                        <td>${durationVal.toFixed(1)} د</td>
                                        <td>${priceEco.toFixed(2)} ج.م</td>
                                    </tr>`;
                                
                                document.getElementById(`s1-${index}`).addEventListener('change', () => { 
                                    document.getElementById(`treatment_style-${index}`).value = 'intensive'; 
                                    updateFormInputsAndPricing(); 
                                });
                                document.getElementById(`s2-${index}`).addEventListener('change', () => { 
                                    document.getElementById(`treatment_style-${index}`).value = 'economy'; 
                                    updateFormInputsAndPricing(); 
                                });

                                if (treatmentStyleVal === 'intensive') {
                                    massageDuration = durationVal;
                                    massagePrice = priceInt;
                                } else {
                                    massageDuration = durationVal;
                                    massagePrice = priceEco;
                                }
                            }
                        }
                    }

                    // Cracking Price & Duration
                    let crackingPrice = 0;
                    let crackingDuration = 0;
                    const crackingChoice = document.querySelector(`input[name="attendees[${index}][cracking_type]"]:checked`).value;

                    if (crackingChoice === 'whole_body') {
                        crackingPrice = 350;
                        crackingDuration = 30;
                        document.getElementById(`cracking_price_desc-${index}`).innerText = 'تقويم الجسم كامل';
                        document.getElementById(`cracking_price_value-${index}`).innerText = '350.00 ج.م';
                    } else if (crackingChoice === 'regions') {
                        const rCount = att.selectedCrackingRegions.size;
                        crackingPrice = rCount * 150;
                        crackingDuration = rCount * 15;
                        document.getElementById(`cracking_price_desc-${index}`).innerText = `تقويم مناطق مخصصة (${rCount})`;
                        document.getElementById(`cracking_price_value-${index}`).innerText = crackingPrice.toFixed(2) + ' ج.م';
                    } else {
                        document.getElementById(`cracking_price_desc-${index}`).innerText = 'بدون تقويم';
                        document.getElementById(`cracking_price_value-${index}`).innerText = '0.00 ج.م';
                    }

                    // Hijama Price & Duration
                    let hijamaPrice = 0;
                    let hijamaDuration = 0;
                    let totalCups = 0;
                    const hijamaChoice = document.querySelector(`input[name="attendees[${index}][hijama_type]"]:checked`).value;

                    if (hijamaChoice !== 'none') {
                        hijamaDuration = 30;
                        const hijamaStyle = document.querySelector(`input[name="attendees[${index}][hijama_style]"]:checked`).value;

                        att.selectedHijamaRegions.forEach(rNum => {
                            let regionCups = 0;
                            const reps = rNum;
                            if (reps === 1) { regionCups = hijamaStyle === 'intensive' ? 3 : 2; }
                            else if (reps === 2) { regionCups = hijamaStyle === 'intensive' ? 1 : 1; }
                            else if (reps === 3) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 4) { regionCups = hijamaStyle === 'intensive' ? 4 : 2; }
                            else if (reps === 5) { regionCups = hijamaStyle === 'intensive' ? 3 : 2; }
                            else if (reps === 6) { regionCups = hijamaStyle === 'intensive' ? 1 : 1; }
                            else if (reps === 7) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 8) { regionCups = hijamaStyle === 'intensive' ? 4 : 2; }
                            else if (reps === 9) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 10) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 11) { regionCups = hijamaStyle === 'intensive' ? 3 : 1; }
                            else if (reps === 12) { regionCups = hijamaStyle === 'intensive' ? 3 : 1; }
                            else if (reps === 13) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 14) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 15) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 16) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 17) { regionCups = hijamaStyle === 'intensive' ? 1 : 1; }
                            else if (reps === 18) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 19) { regionCups = hijamaStyle === 'intensive' ? 3 : 1; }
                            else if (reps === 20) { regionCups = hijamaStyle === 'intensive' ? 1 : 1; }
                            else if (reps === 21) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 22) { regionCups = hijamaStyle === 'intensive' ? 3 : 1; }
                            else if (reps === 23) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 24) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 25) { regionCups = hijamaStyle === 'intensive' ? 3 : 1; }
                            else if (reps === 26) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 27) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 28) { regionCups = hijamaStyle === 'intensive' ? 3 : 1; }
                            else if (reps === 29) { regionCups = hijamaStyle === 'intensive' ? 3 : 1; }
                            else if (reps === 30) { regionCups = hijamaStyle === 'intensive' ? 3 : 1; }
                            else if (reps === 31) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 32) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 33) { regionCups = hijamaStyle === 'intensive' ? 1 : 1; }
                            else if (reps === 34) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 35) { regionCups = hijamaStyle === 'intensive' ? 1 : 1; }
                            else if (reps === 36) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 37) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 38) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            else if (reps === 39) { regionCups = hijamaStyle === 'intensive' ? 2 : 1; }
                            totalCups += regionCups;
                        });

                        let cupPrice = 45;
                        if (totalCups > 20) { cupPrice = 35; }
                        else if (totalCups >= 16) { cupPrice = 37; }
                        else if (totalCups >= 11) { cupPrice = 40; }
                        else { cupPrice = 45; }

                        hijamaPrice = totalCups * cupPrice;

                        let typeText = 'حجامة مناطق';
                        if (hijamaChoice === 'whole_back') typeText = 'حجامة خلفيات كامل';
                        if (hijamaChoice === 'whole_front') typeText = 'حجامة أماميات كامل';

                        document.getElementById(`hijama_price_desc-${index}`).innerText = `${typeText} (${hijamaStyle === 'intensive' ? 'مكثف' : 'اقتصادي'})`;
                        document.getElementById(`hijama_cups_value-${index}`).innerText = `${totalCups} كاس (سعر الكاس ${cupPrice} ج)`;
                        document.getElementById(`hijama_price_value-${index}`).innerText = `${hijamaPrice.toFixed(2)} ج.م`;
                    } else {
                        document.getElementById(`hijama_price_desc-${index}`).innerText = 'بدون حجامة';
                        document.getElementById(`hijama_cups_value-${index}`).innerText = '0 كاس';
                        document.getElementById(`hijama_price_value-${index}`).innerText = '0.00 ج.م';
                    }

                    const attendeeTotalPrice = massagePrice + crackingPrice + hijamaPrice;
                    const attendeeTotalDuration = massageDuration + crackingDuration + hijamaDuration;

                    att.price = attendeeTotalPrice;
                    att.duration = attendeeTotalDuration;

                    // Update attendee summary block values
                    document.getElementById(`attendee_total_price_val-${index}`).innerText = attendeeTotalPrice.toFixed(2);
                    document.getElementById(`attendee_total_duration_val-${index}`).innerText = attendeeTotalDuration.toFixed(1);

                    grandTotalSessionsPrice += attendeeTotalPrice;
                    if (attendeeTotalDuration > globalMaxGroupDuration) {
                        globalMaxGroupDuration = attendeeTotalDuration;
                    }
                });

                // Apply group level urgent fee if selected
                const isUrgentChecked = document.getElementById('is_urgent') && document.getElementById('is_urgent').checked;
                let grandTotal = grandTotalSessionsPrice;
                const urgentFeeRow = document.getElementById('urgent_fee_row');

                if (isUrgentChecked) {
                    grandTotal += urgentBookingFee;
                    if (urgentFeeRow) {
                        document.getElementById('summary_urgent_fee').innerText = urgentBookingFee;
                        urgentFeeRow.style.display = 'flex';
                    }
                } else {
                    if (urgentFeeRow) {
                        urgentFeeRow.style.display = 'none';
                    }
                }

                // 3. Minimum price limit logic (Only for groups)
                const isGroup = attendees.length > 1;
                const groupMinBookingAmount = isGroup ? (attendees.length * (minBookingAmount / 3)) : minBookingAmount;
                const isBelowMinPrice = isGroup && (grandTotalSessionsPrice < groupMinBookingAmount);
                const warningBanner = document.getElementById('min_price_warning_banner');

                if (warningBanner) {
                    if (isBelowMinPrice) {
                        document.getElementById('min_price_current_val').innerText = grandTotalSessionsPrice.toFixed(2);
                        document.getElementById('min_price_setting_val').innerText = Math.round(groupMinBookingAmount);
                        warningBanner.style.display = 'block';
                    } else {
                        warningBanner.style.display = 'none';
                    }
                }

                document.getElementById('summary_total_price').innerText = grandTotal.toFixed(2);
                document.getElementById('summary_total_duration').innerText = globalMaxGroupDuration.toFixed(1);
                
                const durationRow = document.getElementById('duration_row');
                if (durationRow) {
                    if (globalMaxGroupDuration === 0) {
                        durationRow.classList.remove('d-flex');
                        durationRow.classList.add('d-none');
                    } else {
                        durationRow.classList.remove('d-none');
                        durationRow.classList.add('d-flex');
                    }
                }

                // Calculate deposit (40%)
                document.getElementById('deposit_amount').innerText = Math.ceil(grandTotal * 0.40);

                // Control submit button state
                const submitBtn = document.querySelector('button[type="submit"]');
                if (submitBtn) {
                    if (isBelowMinPrice) {
                        submitBtn.disabled = true;
                        submitBtn.innerText = `تأكيد الحجز (غير متاح - أقل من الحد الأدنى للمجموعة ${Math.round(groupMinBookingAmount)} ج.م)`;
                    } else {
                        // Check if urgent validations are passing
                        const feedbackDiv = document.getElementById('time_validation_feedback');
                        if (isUrgentChecked && feedbackDiv && feedbackDiv.innerText.includes('✗')) {
                            submitBtn.disabled = true;
                            submitBtn.innerText = 'تأكيد حجز السيشن (الرجاء اختيار وقت متاح)';
                        } else {
                            submitBtn.disabled = false;
                            submitBtn.innerText = 'تأكيد حجز السيشن';
                        }
                    }
                }

                // Fetch available times or validate based on mode
                if (isUrgentChecked) {
                    validateTimeSelection();
                } else {
                    fetchAvailableTimes();
                }
            }

            function fetchAvailableTimes() {
                const dateVal = document.getElementById('appointment_date').value;
                const timeSelect = document.getElementById('appointment_time_select');
                const dateInput = document.getElementById('appointment_date');
                
                if (!dateVal) return;

                // Build attendees details payload
                const attendeesPayload = attendees.map(att => {
                    const genderVal = document.getElementById(`gender-${att.index}`).value;
                    return {
                        gender: genderVal,
                        duration: Math.ceil(att.duration || 30)
                    };
                });

                const hasEmptyGender = attendeesPayload.some(att => !att.gender);
                if (hasEmptyGender) {
                    timeSelect.innerHTML = '<option value="" disabled selected hidden>يرجى اختيار الجنس لجميع الأفراد أولاً...</option>';
                    return;
                }
                
                timeSelect.innerHTML = '<option>جاري التحميل...</option>';
                const payloadStr = encodeURIComponent(JSON.stringify(attendeesPayload));
                
                fetch(`{{ route('booking.available-times') }}?date=${dateVal}&attendees=${payloadStr}&is_urgent=0`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) {
                            timeSelect.innerHTML = `<option value="" disabled selected hidden>${data.error}</option>`;
                            alert(data.error);
                            dateInput.value = '';
                            return;
                        }
                        timeSelect.innerHTML = '<option value="" disabled selected hidden>اختر الوقت المناسب للمجموعة...</option>';
                        Object.keys(data).forEach(k => { 
                            const o = document.createElement('option'); 
                            o.value = k; 
                            o.textContent = data[k]; 
                            timeSelect.appendChild(o); 
                        });
                        
                        document.getElementById('appointment_time').value = timeSelect.value;
                    });
            }

            function validateTimeSelection() {
                const dateVal = document.getElementById('appointment_date').value;
                const timeVal = document.getElementById('appointment_time_input').value;
                const feedbackDiv = document.getElementById('time_validation_feedback');
                const submitBtn = document.querySelector('button[type="submit"]');

                document.getElementById('appointment_time').value = timeVal;

                if (!dateVal || !timeVal) {
                    if (feedbackDiv) {
                        feedbackDiv.style.display = 'none';
                    }
                    return;
                }

                // Check genders
                const attendeesPayload = attendees.map(att => {
                    const genderVal = document.getElementById(`gender-${att.index}`).value;
                    return {
                        gender: genderVal,
                        duration: Math.ceil(att.duration || 30)
                    };
                });

                const hasEmptyGender = attendeesPayload.some(att => !att.gender);
                if (hasEmptyGender) {
                    if (feedbackDiv) {
                        feedbackDiv.style.display = 'block';
                        feedbackDiv.style.color = '#e74c3c';
                        feedbackDiv.innerText = 'يرجى اختيار الجنس لجميع الأفراد أولاً للتحقق من التوفر.';
                    }
                    return;
                }

                if (feedbackDiv) {
                    feedbackDiv.style.display = 'block';
                    feedbackDiv.style.color = '#e67e22';
                    feedbackDiv.innerText = '⏳ جاري التحقق من توفر الوقت...';
                }

                const payloadStr = encodeURIComponent(JSON.stringify(attendeesPayload));

                fetch(`{{ route('booking.validate-time') }}?date=${dateVal}&time=${timeVal}&attendees=${payloadStr}&is_urgent=1`)
                    .then(res => res.json())
                    .then(data => {
                        if (feedbackDiv) {
                            if (data.available) {
                                feedbackDiv.style.color = '#2ecc71';
                                feedbackDiv.innerText = '✓ ' + data.message;
                                
                                // Only enable if price condition is also met (or if single person)
                                let grandTotalSessionsPrice = attendees.reduce((acc, a) => acc + a.price, 0);
                                if (attendees.length === 1 || grandTotalSessionsPrice >= minBookingAmount) {
                                    if (submitBtn) submitBtn.disabled = false;
                                }
                            } else {
                                feedbackDiv.style.color = '#e74c3c';
                                feedbackDiv.innerText = '✗ ' + data.message;
                                if (submitBtn) submitBtn.disabled = true;
                            }
                        }
                    })
                    .catch(err => {
                        if (feedbackDiv) {
                            feedbackDiv.style.color = '#e74c3c';
                            feedbackDiv.innerText = '⚠️ خطأ في الاتصال بالخادم للتحقق من الموعد.';
                        }
                    });
            }

            function handleUrgentToggle() {
                const isUrgentChecked = document.getElementById('is_urgent') && document.getElementById('is_urgent').checked;
                const regularContainer = document.getElementById('regular_time_container');
                const urgentContainer = document.getElementById('urgent_time_container');
                const timeSelect = document.getElementById('appointment_time_select');
                const timeInput = document.getElementById('appointment_time_input');
                const submitBtn = document.querySelector('button[type="submit"]');

                if (isUrgentChecked) {
                    regularContainer.style.display = 'none';
                    urgentContainer.style.display = 'block';
                    timeSelect.required = false;
                    timeInput.required = true;
                    validateTimeSelection();
                } else {
                    regularContainer.style.display = 'block';
                    urgentContainer.style.display = 'none';
                    timeSelect.required = true;
                    timeInput.required = false;
                    
                    const feedbackDiv = document.getElementById('time_validation_feedback');
                    if (feedbackDiv) feedbackDiv.style.display = 'none';
                    
                    let grandTotalSessionsPrice = attendees.reduce((acc, a) => acc + a.price, 0);
                    if (attendees.length === 1 || grandTotalSessionsPrice >= minBookingAmount) {
                        if (submitBtn) submitBtn.disabled = false;
                    }

                    document.getElementById('appointment_time').value = timeSelect.value;
                    fetchAvailableTimes();
                }
            }

            // Listeners for Regular Select change
            document.getElementById('appointment_time_select').addEventListener('change', function() {
                document.getElementById('appointment_time').value = this.value;
            });

            // Listeners for input changes
            document.getElementById('appointment_date').addEventListener('change', function() {
                const isUrgentChecked = document.getElementById('is_urgent') && document.getElementById('is_urgent').checked;
                if (isUrgentChecked) {
                    validateTimeSelection();
                } else {
                    fetchAvailableTimes();
                }
            });

            document.getElementById('appointment_time_input').addEventListener('input', validateTimeSelection);
            
            if (document.getElementById('is_urgent')) {
                document.getElementById('is_urgent').addEventListener('change', function() {
                    updateFormInputsAndPricing();
                    handleUrgentToggle();
                });
            }

            // Initialize Form with 1st Attendee
            addAttendee();
        });
    </script>
</body>
</html>
