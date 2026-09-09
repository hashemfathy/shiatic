<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Primary SEO Metadata -->
    <title>حجز سيشن جديدة - تقويم العمود الفقري | Shiatic Clinic Booking</title>
    <meta name="description" content="قم بحجز موعد جلستك العلاجية أو الوقائية أو الرياضية الآن في مركز شياتك (Shiatic). أدخل بياناتك الشخصية واختر الباقة المناسبة والمنطقة المستهدفة لعلاج آلام الظهر والفقرات.">
    <meta name="keywords" content="حجز موعد كيروبراكتيك, نموذج حجز شياتك, عرق النسا, علاج الفقرات, مساج علاجي">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ route('booking.form') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('booking.form') }}">
    <meta property="og:title" content="حجز سيشن جديدة - تقويم العمود الفقري | Shiatic Clinic Booking">
    <meta property="og:description" content="قم بحجز موعد جلستك العلاجية أو الوقائية أو الرياضية الآن في مركز شياتك (Shiatic).">
    <meta property="og:image" content="{{ asset('images/R.jpg') }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ route('booking.form') }}">
    <meta name="twitter:title" content="حجز سيشن جديدة - تقويم العمود الفقري | Shiatic Clinic Booking">
    <meta name="twitter:description" content="قم بحجز موعد جلستك العلاجية أو الوقائية أو الرياضية الآن في مركز شياتك (Shiatic).">
    <meta name="twitter:image" content="{{ asset('images/R.jpg') }}">

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
            display: block;
            width: 100%;
            max-width: 600px;
            aspect-ratio: 438 / 264;
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
        .booking-tabs-nav .btn-tab {
            padding: 0.85rem 2.2rem;
            font-size: 1.25rem;
            font-weight: 700;
            border-radius: 50rem;
            border: 2px solid #cbd5e1;
            background-color: #ffffff;
            color: #475569;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .booking-tabs-nav .btn-tab:hover {
            border-color: #ff9d42;
            color: #e67e22;
        }
        .booking-tabs-nav .btn-tab.active {
            background: linear-gradient(135deg, #e67e22, #ff9d42);
            border-color: #e67e22;
            color: #ffffff;
            box-shadow: 0 6px 20px rgba(230, 126, 34, 0.3);
        }

        .th-hotspot-severe:hover {
            background: rgba(239, 68, 68, 0.35);
            color: #991b1b;
            border-color: #ef4444;
            transform: translate(-50%, -50%) scale(1.15);
        }
        .th-hotspot-severe.selected {
            background: #ef4444 !important;
            color: #ffffff !important;
            border-color: #ffffff !important;
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.7) !important;
        }

        .th-hotspot-moderate:hover {
            background: rgba(245, 158, 11, 0.35);
            color: #92400e;
            border-color: #f59e0b;
            transform: translate(-50%, -50%) scale(1.15);
        }
        .th-hotspot-moderate.selected {
            background: #f59e0b !important;
            color: #ffffff !important;
            border-color: #ffffff !important;
            box-shadow: 0 0 10px rgba(245, 158, 11, 0.7) !important;
        }

        .protocol-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent !important;
        }
        .protocol-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        }
        .protocol-card.selected-protocol {
            border-color: #ff9d42 !important;
            background: rgba(255, 157, 66, 0.06) !important;
        }
    </style>
</head>
<body>
    <div class="glass-container">
        <h1>حجز سيشن جديدة</h1>

        <!-- قسم اختيار نوع السيشن (وقائية / علاجية) -->
        <div class="booking-tabs-nav d-flex justify-content-center gap-3 mb-4">
            <button type="button" class="btn btn-tab active" id="tab-btn-preventative" onclick="switchBookingTab('preventative')">
                🛡️ سيشن وقائية
            </button>
            <button type="button" class="btn btn-tab" id="tab-btn-therapeutic" onclick="switchBookingTab('therapeutic')">
                🩺 سيشن علاجية
            </button>
        </div>

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
            <!-- النموذج الأول: حجز السيشن الوقائية -->
            <form action="{{ route('booking.store') }}" method="POST" id="bookingFormPreventative">
            @csrf

            <!-- Hidden field for active tab -->
            <input type="hidden" name="active_tab" id="active_tab" value="وقائية">
            <input type="hidden" name="booking_type" value="وقائية">

            <!-- Section 1: الجلسات الوقائية -->
            <div id="preventative-section">
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

                        <!-- Coupon Input Section -->
                        <div class="mt-4 p-3 rounded-4 mb-4" style="background: rgba(15, 23, 42, 0.02); border: 1px solid rgba(15, 23, 42, 0.05); border-radius: 16px; text-align: right;">
                            <label for="coupon_input" class="form-label fw-bold text-dark mb-2">🎟️ هل لديك كوبون خصم؟</label>
                            <div class="input-group">
                                <input type="text" id="coupon_input" class="form-control" placeholder="أدخل كود الكوبون هنا" style="text-transform: uppercase; border-radius: 0 12px 12px 0;">
                                <button type="button" id="btn-apply-coupon" class="btn btn-warning fw-bold text-white px-4" style="border-radius: 12px 0 0 12px;">تطبيق</button>
                            </div>
                            <input type="hidden" name="coupon_code" id="submitted_coupon_code">
                            <div id="coupon_feedback" class="mt-2 fw-bold" style="display: none; font-size: 0.95rem;"></div>
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
                            <div id="coupon_discount_row" class="justify-content-between mb-2 text-success" style="display: none;">
                                <span>خصم الكوبون:</span>
                                <span class="fw-bold"><span id="summary_coupon_discount">0</span> ج.م</span>
                            </div>
                            <div class="d-flex justify-content-between" id="duration_row">
                                <span>المدة الأقصى المتوقعة (لسيشن متزامنة):</span>
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
            </div>
            </form>

            <!-- Section 2: الجلسات العلاجية (قسم منفصل تماماً) -->
            <div id="therapeutic-section" style="display: none;">
                <!-- النموذج الثاني: حجز السيشن العلاجية -->
                <form action="{{ route('booking.store') }}" method="POST" id="bookingFormTherapeutic">
                @csrf
                <input type="hidden" name="active_tab" value="علاجية">
                <input type="hidden" name="booking_type" value="علاجية">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-dark mb-2">🩺 حجز سيشن علاجية مخصصة</h3>
                    <p class="text-muted">يرجى استكمال البيانات التالية وتحديد مناطق الألم لمساعدتنا في تقديم أفضل خدمة علاجية لك.</p>
                </div>

                <!-- نموذج البيانات الشخصية والصحية -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: rgba(15, 23, 42, 0.02); border: 1px solid rgba(15, 23, 42, 0.06) !important;">
                    <h5 class="fw-bold mb-3 text-warning">📋 البيانات الشخصية والصحية</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="th_name" class="form-label">الاسم *</label>
                            <input type="text" class="form-control" id="th_name" name="therapeutic_name" placeholder="أدخل اسمك الكامل">
                        </div>
                        <div class="col-md-6">
                            <label for="th_phone" class="form-label">التليفون *</label>
                            <input type="tel" class="form-control" id="th_phone" name="therapeutic_phone" placeholder="أدخل رقم التليفون">
                        </div>
                        <div class="col-md-4">
                            <label for="th_gender" class="form-label">الجنس *</label>
                            <select class="form-select" id="th_gender" name="therapeutic_gender">
                                <option value="" disabled selected>اختر الجنس</option>
                                <option value="male">ذكر</option>
                                <option value="female">أنثى</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="th_age" class="form-label">السن *</label>
                            <input type="number" class="form-control" id="th_age" name="therapeutic_age" min="1" max="120" placeholder="مثال: 35">
                        </div>
                        <div class="col-md-4">
                            <label for="th_weight" class="form-label">الوزن (كجم) *</label>
                            <input type="number" class="form-control" id="th_weight" name="therapeutic_weight" min="1" max="300" placeholder="مثال: 75">
                        </div>
                        <div class="col-md-6">
                            <label for="th_blood_type" class="form-label">فصيلة الدم *</label>
                            <select class="form-select" id="th_blood_type" name="therapeutic_blood_type">
                                <option value="" disabled selected>اختر فصيلة الدم</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                                <option value="O">O</option>
                                <option value="dont_know">لا اعرف</option>
                            </select>
                            <input type="hidden" id="th_effective_blood_type" name="therapeutic_effective_blood_type" value="O">
                        </div>
                    </div>
                </div>

                <!-- ملحوظة الأشعة -->
                <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-dark p-3 rounded-4 mb-4 text-center" style="font-size: 1.15rem; font-weight: 700; border-right: 5px solid #ff9d42 !important;">
                    📄 <strong>ملحوظة هامّة:</strong> يرجى احضار الاشعة المتعلقة بالاصابه مع السيشن
                </div>

                <!-- صور تحديد مناطق الألم (شديدة ومتوسطة) -->
                <div class="row g-4 my-4">
                    <!-- الصورة الأولى: شديدة الألم -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card h-100 border-0 shadow-sm rounded-4 p-3 text-center" style="background: rgba(239, 68, 68, 0.04); border: 1px solid rgba(239, 68, 68, 0.2) !important;">
                            <h6 class="fw-bold text-danger mb-3" style="font-size: 1.05rem;">🔥 يرجى اختيار مناطق شديدة الالم</h6>
                            <div class="body-map-container th-map-container" id="th-map-severe" style="margin: 0 auto; max-width: 600px;">
                                <img src="{{ asset('images/body.jpg') }}" alt="مناطق شديدة الألم" class="body-map-img">
                            </div>
                            <div class="mt-3 text-muted" style="font-size: 0.9rem;">
                                المناطق المحددة: <span id="severe-count-badge" class="badge bg-danger fs-6">0</span>
                            </div>
                        </div>
                    </div>

                    <!-- الصورة الثانية: متوسطة الألم -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card h-100 border-0 shadow-sm rounded-4 p-3 text-center" style="background: rgba(245, 158, 11, 0.04); border: 1px solid rgba(245, 158, 11, 0.2) !important;">
                            <h6 class="fw-bold text-warning mb-3" style="font-size: 1.05rem;">⚡ يرجى اختيار مناطق متوسطة الالم</h6>
                            <div class="body-map-container th-map-container" id="th-map-moderate" style="margin: 0 auto; max-width: 600px;">
                                <img src="{{ asset('images/body.jpg') }}" alt="مناطق متوسطة الألم" class="body-map-img">
                            </div>
                            <div class="mt-3 text-muted" style="font-size: 0.9rem;">
                                المناطق المحددة: <span id="moderate-count-badge" class="badge bg-warning text-dark fs-6">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تنبيه التحقق من المدخلات الحيوية قبل التأكيد -->
                <div id="therapeutic_validation_feedback" class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger p-3 rounded-4 my-3 text-center" style="display: none; font-size: 1.05rem; font-weight: 700;">
                    ⚠️ يرجى التأكد من استكمال كافة البيانات الأساسية (الاسم، التليفون، الجنس، السن، الوزن، وفصيلة الدم) وتحديد منطقة ألم واحدة على الأقل قبل تأكيد وتوليد البروتوكول العلاجي.
                </div>

                <!-- زر التأكيد -->
                <div class="text-center my-4">
                    <button type="button" class="btn btn-warning text-white fw-bold px-5 py-3 rounded-4 shadow fs-5" id="btn-confirm-therapeutic" onclick="confirmTherapeuticSelection()">
                        ✅ التأكيد وإظهار البروتوكول العلاجي
                    </button>
                </div>

                <!-- قسم البروتوكول العلاجي (يظهر بعد التأكيد) -->
                <div id="therapeutic-protocol-section" class="mt-5 p-4 rounded-4" style="display: none; background: rgba(15, 23, 42, 0.03); border: 2px dashed #ff9d42;">
                    <h4 class="text-center fw-bold text-dark mb-4">🩺 البروتوكول العلاجي المقترح</h4>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center protocol-card" id="protocol-intensive-card" onclick="selectProtocol('intensive')">
                                <div class="badge bg-danger text-white mb-2 py-2 px-3 align-self-center rounded-pill fs-6">الخيار الأول</div>
                                <h4 class="fw-bold text-dark">🔥 البروتوكول المكثف</h4>
                                <p class="text-muted">علاج شامل ومكثف لمناطق الألم المحددة لتحقيق أقصى استجابة علاجية وسرعة التعافي.</p>
                                
                                <!-- المساج العلاجي في المكثف -->
                                <div id="proto-massage-intensive-block" class="my-3 p-3 rounded-3 text-start" style="background: rgba(239, 68, 68, 0.06); border-right: 4px solid #ef4444;">
                                    <div class="fw-bold text-danger mb-1">💆‍♂️ المساج العلاجي (مكثف):</div>
                                    <div style="font-size: 0.9rem;" id="proto-massage-intensive-desc">سيشن مساج علاجي مكثف مخصصة لمناطق الألم المحددة.</div>
                                </div>

                                <!-- الكيروبراكتيك العلاجي في المكثف -->
                                <div id="proto-chiro-intensive-block" class="my-3 p-3 rounded-3 text-start" style="background: rgba(239, 68, 68, 0.06); border-right: 4px solid #ef4444;">
                                    <div class="fw-bold text-danger mb-1">🦴 الكيروبراكتيك العلاجي (مكثف):</div>
                                    <div style="font-size: 0.9rem;" id="proto-chiro-intensive-desc">سيتم الحساب بناءً على تكنيكات مناطق الألم المحددة.</div>
                                </div>

                                <!-- الحجامة في المكثف -->
                                <div id="proto-hijama-intensive-block" class="my-3 p-3 rounded-3 text-start" style="background: rgba(239, 68, 68, 0.06); border-right: 4px solid #ef4444;">
                                    <div class="fw-bold text-danger mb-1">🏺 الحجامة (مكثف):</div>
                                    <div style="font-size: 0.9rem;" id="proto-hijama-intensive-desc">سيتم تحديد تضمين الحجامة بناءً على اختيار مناطق شديدة الألم.</div>
                                </div>

                                <!-- التأهيل في المكثف -->
                                <div id="proto-rehab-intensive-block" class="my-3 p-3 rounded-3 text-start" style="background: rgba(239, 68, 68, 0.06); border-right: 4px solid #ef4444;">
                                    <div class="fw-bold text-danger mb-1">🏋️ التأهيل (مكثف):</div>
                                    <div style="font-size: 0.9rem;" id="proto-rehab-intensive-desc">سيتم تحديد مدة التأهيل بناءً على مناطق الألم المحددة.</div>
                                </div>

                                <!-- المدة الكاملة والسعر النهائي للمكثف -->
                                <div class="p-3 my-3 rounded-3 text-center" style="background: rgba(239, 68, 68, 0.08); border: 2px solid rgba(239, 68, 68, 0.25);">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-6 border-end border-danger border-opacity-25">
                                            <span class="text-muted d-block small fw-bold">⏱️ المدة الكاملة</span>
                                            <strong class="text-dark fs-5" id="proto-total-duration-intensive">0 دقيقة</strong>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted d-block small fw-bold">💰 السعر النهائي</span>
                                            <strong class="text-danger fs-4" id="proto-total-price-intensive">0.00 ج.م</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-auto pt-2">
                                    <input type="radio" name="therapeutic_protocol" value="intensive" id="proto_intensive" class="btn-check">
                                    <label for="proto_intensive" class="btn btn-outline-danger w-100 fw-bold rounded-3">اختيار البروتوكول المكثف</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center protocol-card" id="protocol-economy-card" onclick="selectProtocol('economy')">
                                <div class="badge bg-success text-white mb-2 py-2 px-3 align-self-center rounded-pill fs-6">الخيار الثاني</div>
                                <h4 class="fw-bold text-dark">🌱 البروتوكول الاقتصادي</h4>
                                <p class="text-muted">جلسات مركزة على المناطق الرئيسية بتكلفة مناسبة تناسب الاحتياجات العلاجية الأساسية.</p>

                                <!-- المساج العلاجي في الاقتصادي -->
                                <div id="proto-massage-economy-block" class="my-3 p-3 rounded-3 text-start" style="background: rgba(16, 185, 129, 0.06); border-right: 4px solid #10b981;">
                                    <div class="fw-bold text-success mb-1">💆‍♂️ المساج العلاجي (اقتصادي):</div>
                                    <div style="font-size: 0.9rem;" id="proto-massage-economy-desc">سيشن مساج علاجي مركزة لمناطق الألم المحددة.</div>
                                </div>

                                <!-- الكيروبراكتيك العلاجي في الاقتصادي -->
                                <div id="proto-chiro-economy-block" class="my-3 p-3 rounded-3 text-start" style="background: rgba(16, 185, 129, 0.06); border-right: 4px solid #10b981;">
                                    <div class="fw-bold text-success mb-1">🦴 الكيروبراكتيك العلاجي (اقتصادي):</div>
                                    <div style="font-size: 0.9rem;" id="proto-chiro-economy-desc">سيتم الحساب بناءً على تكنيكات مناطق الألم المحددة.</div>
                                </div>

                                <!-- الحجامة في الاقتصادي -->
                                <div id="proto-hijama-economy-block" class="my-3 p-3 rounded-3 text-start" style="background: rgba(16, 185, 129, 0.06); border-right: 4px solid #10b981;">
                                    <div class="fw-bold text-success mb-1">🏺 الحجامة (اقتصادي):</div>
                                    <div style="font-size: 0.9rem;" id="proto-hijama-economy-desc">سيتم تحديد تضمين الحجامة بناءً على اختيار مناطق شديدة الألم.</div>
                                </div>

                                <!-- التأهيل في الاقتصادي -->
                                <div id="proto-rehab-economy-block" class="my-3 p-3 rounded-3 text-start" style="background: rgba(16, 185, 129, 0.06); border-right: 4px solid #10b981;">
                                    <div class="fw-bold text-success mb-1">🏋️ التأهيل (اقتصادي):</div>
                                    <div style="font-size: 0.9rem;" id="proto-rehab-economy-desc">سيتم تحديد مدة التأهيل بناءً على مناطق الألم المحددة.</div>
                                </div>

                                <!-- المدة الكاملة والسعر النهائي للاقتصادي -->
                                <div class="p-3 my-3 rounded-3 text-center" style="background: rgba(16, 185, 129, 0.08); border: 2px solid rgba(16, 185, 129, 0.25);">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-6 border-end border-success border-opacity-25">
                                            <span class="text-muted d-block small fw-bold">⏱️ المدة الكاملة</span>
                                            <strong class="text-dark fs-5" id="proto-total-duration-economy">0 دقيقة</strong>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted d-block small fw-bold">💰 السعر النهائي</span>
                                            <strong class="text-success fs-4" id="proto-total-price-economy">0.00 ج.م</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-auto pt-2">
                                    <input type="radio" name="therapeutic_protocol" value="economy" id="proto_economy" class="btn-check">
                                    <label for="proto_economy" class="btn btn-outline-success w-100 fw-bold rounded-3">اختيار البروتوكول الاقتصادي</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden inputs for therapeutic booking data -->
                <input type="hidden" name="therapeutic_severe_regions" id="th_submitted_severe_regions">
                <input type="hidden" name="therapeutic_moderate_regions" id="th_submitted_moderate_regions">
                <input type="hidden" name="therapeutic_total_price" id="th_submitted_total_price">
                <input type="hidden" name="therapeutic_total_duration" id="th_submitted_total_duration">

                <!-- قسم تحديد موعد السيشن العلاجية -->
                <div id="therapeutic-appointment-section" class="card mt-5 mb-4" style="display: none; background: rgba(15, 23, 42, 0.02); border: 1px solid rgba(15, 23, 42, 0.05); border-radius: 16px;">
                    <div class="card-body p-4">
                        <h4 class="mb-4 text-center" style="font-weight: 700; color: #ff9d42;">تحديد موعد السيشن</h4>
                        
                        <!-- Urgent Booking Toggle -->
                        <div class="mt-4 p-3 rounded-4 mb-4" style="background: rgba(230, 126, 34, 0.05); border: 1px dashed rgba(230, 126, 34, 0.3); border-radius: 16px; text-align: right;">
                            <div class="form-check form-switch d-flex align-items-center">
                                <input class="form-check-input" type="checkbox" id="th_is_urgent" name="therapeutic_is_urgent" value="1" style="width: 2.5rem; height: 1.25rem; accent-color: #e67e22; margin-left: 1rem; cursor: pointer;">
                                <label class="form-check-label text-dark fw-bold" for="th_is_urgent" style="cursor: pointer; font-size: 1.1rem; flex-grow: 1;">
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
                                <label for="th_appointment_date" class="form-label">اختر التاريخ *</label>
                                <input type="date" class="form-control" id="th_appointment_date" name="therapeutic_date" min="{{ date('Y-m-d') }}">
                            </div>
                            <!-- Regular Booking Time Select -->
                            <div class="col-md-6" id="th_regular_time_container">
                                <label for="th_appointment_time_select" class="form-label">اختر الوقت المتاح للمجموعة *</label>
                                <select class="form-select" id="th_appointment_time_select">
                                    <option value="" disabled selected>يرجى اختيار تاريخ أولاً</option>
                                </select>
                            </div>

                            <!-- Urgent Booking Time Input -->
                            <div class="col-md-6" id="th_urgent_time_container" style="display: none;">
                                <label for="th_appointment_time_input" class="form-label">اختر الوقت المطلوب *</label>
                                <input type="time" class="form-control" id="th_appointment_time_input">
                                <div id="th_time_validation_feedback" class="mt-2 fw-bold" style="display: none; font-size: 0.9rem;"></div>
                            </div>

                            <!-- Hidden submitted time input -->
                            <input type="hidden" id="th_appointment_time" name="therapeutic_time">
                        </div>

                        <!-- Coupon Input Section -->
                        <div class="mt-4 p-3 rounded-4 mb-4" style="background: rgba(15, 23, 42, 0.02); border: 1px solid rgba(15, 23, 42, 0.05); border-radius: 16px; text-align: right;">
                            <label for="th_coupon_input" class="form-label fw-bold text-dark mb-2">🎟️ هل لديك كوبون خصم؟</label>
                            <div class="input-group">
                                <input type="text" id="th_coupon_input" class="form-control" placeholder="أدخل كود الكوبون هنا" style="text-transform: uppercase; border-radius: 0 12px 12px 0;">
                                <button type="button" id="th_btn-apply-coupon" class="btn btn-warning fw-bold text-white px-4" style="border-radius: 12px 0 0 12px;">تطبيق</button>
                            </div>
                            <input type="hidden" name="therapeutic_coupon_code" id="th_submitted_coupon_code">
                            <div id="th_coupon_feedback" class="mt-2 fw-bold" style="display: none; font-size: 0.95rem;"></div>
                        </div>

                        <!-- Services Summary -->
                        <div class="mt-4 p-3 rounded-3" style="background: rgba(15, 23, 42, 0.03); border-right: 4px solid #38bdf8;">
                            <h5 class="mb-3 text-dark" style="font-weight: 700;">💰 تفاصيل السعر والمدة الإجمالية :</h5>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>السعر الإجمالي لجميع جلسات :</span>
                                <span class="fw-bold text-dark"><span id="th_summary_total_price" class="text-warning fs-5">0.00</span> ج.م</span>
                            </div>
                            <div id="th_urgent_fee_row" class="justify-content-between mb-2" style="display: none;">
                                <span>رسوم الحجز المستعجل الإضافية:</span>
                                <span class="fw-bold text-warning"><span id="th_summary_urgent_fee">0</span> ج.م</span>
                            </div>
                            <div id="th_coupon_discount_row" class="justify-content-between mb-2 text-success" style="display: none;">
                                <span>خصم الكوبون:</span>
                                <span class="fw-bold"><span id="th_summary_coupon_discount">0</span> ج.م</span>
                            </div>
                            <div class="d-flex justify-content-between" id="th_duration_row">
                                <span>المدة المتوقعة للسيشن:</span>
                                <span class="fw-bold text-dark"><span id="th_summary_total_duration" class="text-info">0</span> دقيقة</span>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="mt-4 p-3 rounded-3" style="background: rgba(15, 23, 42, 0.02); border-right: 4px solid #ff9d42;">
                            <h6 class="fw-bold mb-3 text-dark" style="font-size: 1.05rem; border-bottom: 1px solid rgba(15, 23, 42, 0.08); padding-bottom: 0.5rem;">⚖️ الأحكام والشروط:</h6>
                            <ul class="mb-0 text-dark" style="list-style-type: none; padding-right: 0.5rem; font-size: 0.95rem; line-height: 1.8;">
                                <li>
                                    📌 يرجى ارسال مقدم حجز بقيمة 40% من السعر الإجمالي = <strong class="text-warning" id="th_deposit_amount">0</strong> جنيه 
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
                                    <input class="form-check-input" type="radio" name="therapeutic_user_agreement" id="th_agree_yes" value="موافق" style="accent-color: #2ecc71; width: 1.3rem; height: 1.3rem; margin-left: 0.5rem;">
                                    <label class="form-check-label text-success fw-bold" for="th_agree_yes" style="cursor: pointer; font-size: 1.1rem;">موافق</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="therapeutic_user_agreement" id="th_agree_no" value="الغاء الحجز" onclick="window.location.href='{{ url('/') }}'" style="accent-color: #e67e22; width: 1.3rem; height: 1.3rem; margin-left: 0.5rem;">
                                    <label class="form-check-label text-danger fw-bold" for="th_agree_no" style="cursor: pointer; font-size: 1.1rem;">الغاء الحجز</label>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-submit mt-4" id="btn-submit-therapeutic">تأكيد حجز السيشن</button>
                    </div>
                </div>
                </form>
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('booking.index') }}" class="btn-back">← العودة للخيارات</a>
            </div>
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
                                <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info p-2 rounded-3 mb-3 text-center female-chiro-note" id="female-chiro-note-{index}" style="display: none; font-size: 0.95rem; font-weight: 700;">
                                    يقوم بتنفيذه ك. احمد عادل
                                </div>
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
                                                <div class="fw-normal text-warning" style="font-size: 0.85rem; margin-top: 0.25rem;">مكثف (600 ج.م) أو اقتصادي (450 ج.م)</div>
                                            </label>
                                        </div>
                                        
                                        <div class="package-checkbox-card">
                                            <input type="radio" id="cracking_regions_option-{index}" name="attendees[{index}][cracking_type]" value="regions">
                                            <label for="cracking_regions_option-{index}" class="ms-2">
                                                اختيار مناطق من الصورة
                                            </label>
                                        </div>
                                        
                                        <!-- Hidden inputs for cracking regions -->
                                        <div id="hidden-cracking-regions-inputs-{index}"></div>

                                        <!-- Cracking Style Section -->
                                        <div id="cracking_style_section-{index}" class="cracking-style-section-el mt-4 p-3 rounded-4" style="background: rgba(15, 23, 42, 0.02); border: 1px solid rgba(15, 23, 42, 0.05); display: none;">
                                            <label class="form-label d-block mb-3" style="font-weight: 600;">اختر باقة تقويم العمود الفقري *</label>
                                            <div class="d-flex flex-column gap-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="attendees[{index}][cracking_style]" id="cracking_style_intensive-{index}" value="intensive" checked>
                                                    <label class="form-check-label text-dark" for="cracking_style_intensive-{index}" style="font-weight: 600;">
                                                        مكثف (سيشن شاملة ومكثفة)
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="attendees[{index}][cracking_style]" id="cracking_style_economy-{index}" value="economy">
                                                    <label class="form-check-label text-dark" for="cracking_style_economy-{index}" style="font-weight: 600;">
                                                        اقتصادي (سيشن أساسية)
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
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
                                            <p style="color: #16a34a; margin-bottom: 0; font-weight: 600;">اضغط على الأرقام لاختيار وتحديد مناطق (1، 2، 3، 4، 5)</p>
                                        </div>
                                        <div class="body-map-container cracking-map-container-el" id="cracking-map-container-{index}" style="max-width: 380px; aspect-ratio: 200 / 420;">
                                            <img src="{{ asset('images/cracking.png') }}" alt="Cracking Spine Chart" class="body-map-img" style="max-width: 380px;">
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
            const regionRepetitionsIntensive = {
                1: 2, 3: 2, 4: 3, 5: 2, 7: 2, 8: 3, 9: 1, 10: 1,
                11: 4, 12: 4, 13: 1, 14: 1, 15: 1, 16: 1, 17: 2, 18: 4,
                19: 2, 20: 2, 25: 2, 27: 2, 28: 2, 30: 2, 31: 1,
                32: 1, 33: 1, 34: 1, 35: 1, 36: 1, 37: 1
            };

            const regionRepetitionsEconomy = {
                1: 2, 3: 2, 5: 2, 7: 2, 9: 1, 10: 1, 11: 2, 12: 2,
                13: 1, 14: 1, 15: 1, 16: 1, 17: 1, 18: 2, 19: 2,
                20: 1, 21: 1, 22: 1, 23: 1, 24: 1, 25: 2, 27: 2,
                28: 2, 30: 2, 31: 1, 32: 1, 33: 1, 34: 1, 35: 1,
                36: 1, 37: 1
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
                1: [{top: 17.5, left: 50.0}],
                2: [{top: 27.8, left: 29.0}, {top: 27.8, left: 71.0}],
                3: [{top: 26.0, left: 50.0}],
                4: [{top: 36.5, left: 50.0}],
                5: [{top: 63.8, left: 50.0}]
            };

            const minBookingAmount = {{ $minBookingAmount }};
            const urgentBookingFee = {{ $urgentBookingFee }};

            let attendees = [];
            let nextAttendeeIndex = 0;
            let couponCode = '';
            let couponType = '';
            let couponValue = 0;
            let couponDiscount = 0;

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
                        const crackingStyleSec = document.getElementById(`cracking_style_section-${index}`);
                        if (crackingStyleSec) {
                            if (this.value === 'none') {
                                crackingStyleSec.style.display = 'none';
                            } else {
                                crackingStyleSec.style.display = 'block';
                            }
                        }
                        if (this.value === 'none' || this.value === 'whole_body') {
                            att.selectedCrackingRegions.clear();
                            document.querySelectorAll(`#cracking-map-container-${index} .hotspot`).forEach(el => el.classList.remove('selected'));
                        }
                        updateFormInputsAndPricing();
                    });
                });

                // Cracking style change handler
                const crackingStyles = document.querySelectorAll(`input[name="attendees[${index}][cracking_style]"]`);
                crackingStyles.forEach(radio => {
                    radio.addEventListener('change', function() {
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
                        const noteEl = document.getElementById(`female-chiro-note-${index}`);
                        if (noteEl) {
                            if (this.value === 'female') {
                                noteEl.style.display = 'block';
                            } else {
                                noteEl.style.display = 'none';
                            }
                        }

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
                    let totalRepetitionsInt = 0;
                    let totalRepetitionsEco = 0;
                    att.selectedRegions.forEach(rNum => {
                        totalRepetitionsInt += regionRepetitionsIntensive[rNum] || 0;
                        totalRepetitionsEco += regionRepetitionsEconomy[rNum] || 0;
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
                                massageDuration = 95.4 + (totalRepetitionsInt * 1.8);
                                massagePrice = isHard ? 1600.00 + (totalRepetitionsInt * 17) : 1200.00 + (totalRepetitionsInt * 13);
                                pricingRows.innerHTML = `<tr><td><strong>كامل الجسم مكثف (${isHard ? 'هارد' : 'ميديم'})</strong></td><td><span>${massageDuration.toFixed(1)} د</span></td><td><span>${massagePrice.toFixed(2)} ج.م</span></td></tr>`;
                            } else if (isEconomyChecked) {
                                document.getElementById(`treatment_style-${index}`).value = 'economy';
                                massageDuration = 57.4 + (totalRepetitionsEco * 1.4);
                                massagePrice = isHard ? 950.00 + (totalRepetitionsEco * 17) : 725.00 + (totalRepetitionsEco * 13);
                                pricingRows.innerHTML = `<tr><td><strong>كامل الجسم اقتصادي (${isHard ? 'هارد' : 'ميديم'})</strong></td><td><span>${massageDuration.toFixed(1)} د</span></td><td><span>${massagePrice.toFixed(2)} ج.م</span></td></tr>`;
                            } else {
                                // Regions only
                                const durationValInt = totalRepetitionsInt * 1.8;
                                const priceInt = totalRepetitionsInt * (isHard ? 17 : 13);

                                const durationValEco = totalRepetitionsEco * 1.4;
                                const priceEco = totalRepetitionsEco * (isHard ? 17 : 13);

                                const treatmentStyleVal = document.getElementById(`treatment_style-${index}`).value || 'intensive';

                                pricingRows.innerHTML = `
                                    <tr>
                                        <td>
                                            <input type="radio" name="style-${index}" value="intensive" ${treatmentStyleVal === 'intensive' ? 'checked' : ''} id="s1-${index}"> 
                                            <label for="s1-${index}">مكثف (${isHard ? 'هارد' : 'ميديم'})</label>
                                        </td>
                                        <td>${durationValInt.toFixed(1)} د</td>
                                        <td>${priceInt.toFixed(2)} ج.م</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="radio" name="style-${index}" value="economy" ${treatmentStyleVal === 'economy' ? 'checked' : ''} id="s2-${index}"> 
                                            <label for="s2-${index}">اقتصادي (${isHard ? 'هارد' : 'ميديم'})</label>
                                        </td>
                                        <td>${durationValEco.toFixed(1)} د</td>
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
                                    massageDuration = durationValInt;
                                    massagePrice = priceInt;
                                } else {
                                    massageDuration = durationValEco;
                                    massagePrice = priceEco;
                                }
                            }
                        }
                    }

                    // Cracking Price & Duration
                    let crackingPrice = 0;
                    let crackingDuration = 0;
                    const crackingChoice = document.querySelector(`input[name="attendees[${index}][cracking_type]"]:checked`).value;
                    const crackingStyleEl = document.querySelector(`input[name="attendees[${index}][cracking_style]"]:checked`);
                    const crackingStyle = crackingStyleEl ? crackingStyleEl.value : 'intensive';

                    // Display or hide cracking style section
                    const crackingStyleSec = document.getElementById(`cracking_style_section-${index}`);
                    if (crackingStyleSec) {
                        if (crackingChoice === 'none') {
                            crackingStyleSec.style.display = 'none';
                        } else {
                            crackingStyleSec.style.display = 'block';
                        }
                    }

                    // Mappings for cracking
                    const crackingRepsIntensive = { 1: 22, 2: 39, 3: 40, 4: 28, 5: 32 };
                    const crackingRepsEconomy = { 1: 18, 2: 33, 3: 31, 4: 20, 5: 28 };
                    const crackingCountIntensive = { 1: 11, 2: 13, 3: 9, 4: 11, 5: 16 };
                    const crackingCountEconomy = { 1: 9, 2: 11, 3: 7, 4: 9, 5: 14 };

                    const repsMap = (crackingStyle === 'intensive') ? crackingRepsIntensive : crackingRepsEconomy;
                    const countMap = (crackingStyle === 'intensive') ? crackingCountIntensive : crackingCountEconomy;

                    if (crackingChoice === 'whole_body') {
                        if (crackingStyle === 'intensive') {
                            crackingPrice = 600;
                            crackingDuration = 16.1;
                        } else {
                            crackingPrice = 450;
                            crackingDuration = 13.0;
                        }
                        const labelStyle = crackingStyle === 'intensive' ? 'مكثف' : 'اقتصادي';
                        document.getElementById(`cracking_price_desc-${index}`).innerText = `تقويم الجسم كامل (${labelStyle})`;
                        document.getElementById(`cracking_price_value-${index}`).innerText = crackingPrice.toFixed(2) + ' ج.م';
                    } else if (crackingChoice === 'regions') {
                        let totalCrackingReps = 0;
                        let totalCrackingCount = 0;
                        att.selectedCrackingRegions.forEach(rNum => {
                            if (repsMap[rNum]) totalCrackingReps += repsMap[rNum];
                            if (countMap[rNum]) totalCrackingCount += countMap[rNum];
                        });

                        crackingPrice = totalCrackingCount * 12.00;
                        crackingDuration = totalCrackingReps * 0.1;
                        const labelStyle = crackingStyle === 'intensive' ? 'مكثف' : 'اقتصادي';
                        document.getElementById(`cracking_price_desc-${index}`).innerText = `تقويم مناطق مخصصة (${att.selectedCrackingRegions.size}) - ${labelStyle}`;
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

                        hijamaDuration = 10 + totalCups;

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
                    const attendeeVisibleDuration = massageDuration + hijamaDuration;

                    att.price = attendeeTotalPrice;
                    att.duration = attendeeTotalDuration;

                    // Update attendee summary block values
                    document.getElementById(`attendee_total_price_val-${index}`).innerText = attendeeTotalPrice.toFixed(2);
                    document.getElementById(`attendee_total_duration_val-${index}`).innerText = attendeeVisibleDuration.toFixed(1);

                    grandTotalSessionsPrice += attendeeTotalPrice;
                    if (attendeeVisibleDuration > globalMaxGroupDuration) {
                        globalMaxGroupDuration = attendeeVisibleDuration;
                    }
                });

                // Calculate dynamic coupon discount
                if (couponCode) {
                    if (couponType === 'percentage') {
                        couponDiscount = Math.round(grandTotalSessionsPrice * (couponValue / 100) * 100) / 100;
                    } else {
                        couponDiscount = Math.min(couponValue, grandTotalSessionsPrice);
                    }
                    
                    const couponDiscountRow = document.getElementById('coupon_discount_row');
                    if (couponDiscountRow) {
                        document.getElementById('summary_coupon_discount').innerText = couponDiscount.toFixed(2);
                        couponDiscountRow.style.display = 'flex';
                    }
                } else {
                    couponDiscount = 0;
                    const couponDiscountRow = document.getElementById('coupon_discount_row');
                    if (couponDiscountRow) {
                        couponDiscountRow.style.display = 'none';
                    }
                }

                // Apply group level urgent fee if selected
                const isUrgentChecked = document.getElementById('is_urgent') && document.getElementById('is_urgent').checked;
                let grandTotal = grandTotalSessionsPrice - couponDiscount;
                if (grandTotal < 0) grandTotal = 0;
                
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

                document.getElementById('appointment_time').value = timeVal ;

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

            // Coupon Apply click listener
            document.getElementById('btn-apply-coupon').addEventListener('click', function() {
                const codeInput = document.getElementById('coupon_input').value.trim();
                const feedbackEl = document.getElementById('coupon_feedback');
                const dateVal = document.getElementById('appointment_date').value;

                if (!codeInput) {
                    feedbackEl.style.display = 'block';
                    feedbackEl.style.color = '#e74c3c';
                    feedbackEl.innerText = 'يرجى كتابة كود الكوبون أولاً.';
                    return;
                }

                if (!dateVal) {
                    feedbackEl.style.display = 'block';
                    feedbackEl.style.color = '#e74c3c';
                    feedbackEl.innerText = 'يرجى اختيار تاريخ الزيارة أولاً للتحقق من صلاحية الكوبون.';
                    return;
                }

                // Calculate current sessions total price (before urgent fee)
                let totalSessionsPrice = attendees.reduce((acc, a) => acc + a.price, 0);

                feedbackEl.style.display = 'block';
                feedbackEl.style.color = '#e67e22';
                feedbackEl.innerText = '⏳ جاري التحقق من الكوبون...';

                fetch(`{{ route("booking.validate-coupon") }}?code=${encodeURIComponent(codeInput)}&total_price=${totalSessionsPrice}&date=${encodeURIComponent(dateVal)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.valid) {
                        couponCode = data.code;
                        couponType = data.type;
                        couponValue = data.value;
                        couponDiscount = data.discount;
                        document.getElementById('submitted_coupon_code').value = couponCode;
                        
                        feedbackEl.style.color = '#2ecc71';
                        feedbackEl.innerText = '✓ ' + data.message;
                        
                        // Update UI pricing
                        updateFormInputsAndPricing();
                    } else {
                        // Reset
                        couponCode = '';
                        couponType = '';
                        couponValue = 0;
                        couponDiscount = 0;
                        document.getElementById('submitted_coupon_code').value = '';
                        
                        feedbackEl.style.color = '#e74c3c';
                        feedbackEl.innerText = '✗ ' + data.message;
                        
                        // Update UI pricing
                        updateFormInputsAndPricing();
                    }
                })
                .catch(err => {
                    feedbackEl.style.color = '#e74c3c';
                    feedbackEl.innerText = '⚠️ خطأ في الاتصال بالخادم للتحقق من الكوبون.';
                });
            });

            // Initialize Form with 1st Attendee
            addAttendee();
        });

        // Tab Switching Logic (وقائية / علاجية)
        function switchBookingTab(mode) {
            const btnPreventative = document.getElementById('tab-btn-preventative');
            const btnTherapeutic = document.getElementById('tab-btn-therapeutic');
            const preventativeSec = document.getElementById('preventative-section');
            const therapeuticSec = document.getElementById('therapeutic-section');
            const activeTabInput = document.getElementById('active_tab');

            if (mode === 'therapeutic') {
                btnPreventative.classList.remove('active');
                btnTherapeutic.classList.add('active');
                preventativeSec.style.display = 'none';
                therapeuticSec.style.display = 'block';
                if (activeTabInput) activeTabInput.value = 'علاجية';

                // Initialize therapeutic body maps
                initTherapeuticMaps();
            } else {
                btnTherapeutic.classList.remove('active');
                btnPreventative.classList.add('active');
                therapeuticSec.style.display = 'none';
                preventativeSec.style.display = 'block';
                if (activeTabInput) activeTabInput.value = 'وقائية';
            }
        }

        // Therapeutic Body Maps State & Logic
        let thSevereRegions = new Set();
        let thModerateRegions = new Set();
        let therapeuticMapsInitialized = false;

        function initTherapeuticMaps() {
            if (therapeuticMapsInitialized) return;
            therapeuticMapsInitialized = true;

            const regionCoords = {
                1: {top: 59, left: 82.8}, 2: {top: 69.8, left: 82.2}, 3: {top: 77.5, left: 82.2},
                4: {top: 90.5, left: 82.2}, 5: {top: 59.5, left: 88.2}, 6: {top: 70.5, left: 88.2},
                7: {top: 78.5, left: 89.2}, 8: {top: 91.5, left: 88.2}, 9: {top: 45.5, left: 82.2},
                10: {top: 45.5, left: 88.2}, 11: {top: 36.5, left: 82.2}, 12: {top: 37.5, left: 89.2},
                13: {top: 25.5, left: 83.2}, 14: {top: 26.5, left: 89.2}, 15: {top: 17.5, left: 83.2},
                16: {top: 17.5, left: 88.5}, 17: {top: 20.5, left: 77.8}, 18: {top: 28.5, left: 77},
                19: {top: 38.5, left: 76.5}, 20: {top: 20.5, left: 93.8}, 21: {top: 29.5, left: 94.6},
                22: {top: 39.5, left: 95.2}, 23: {top: 20.5, left: 64}, 24: {top: 18.5, left: 45.2},
                25: {top: 54.5, left: 10}, 26: {top: 69.5, left: 10}, 27: {top: 78.5, left: 10},
                28: {top: 55, left: 17.2}, 29: {top: 69.5, left: 17.2}, 30: {top: 79.5, left: 17.2},
                31: {top: 23.5, left: 15.5}, 32: {top: 23.5, left: 10.5}, 33: {top: 19.5, left: 20},
                34: {top: 26.5, left: 21.5}, 35: {top: 19.5, left: 6}, 36: {top: 27.5, left: 5.5},
                37: {top: 9.5, left: 85.8}, 38: {top: 89.5, left: 16.2}, 39: {top: 88.5, left: 10}
            };

            // Map 1: Severe Pain
            const severeMapContainer = document.getElementById('th-map-severe');
            if (severeMapContainer) {
                for (let i = 1; i <= 39; i++) {
                    const coord = regionCoords[i];
                    if (!coord) continue;
                    const hotspot = document.createElement('div');
                    hotspot.className = 'hotspot available th-hotspot-severe';
                    hotspot.style.top = coord.top + '%';
                    hotspot.style.left = coord.left + '%';
                    hotspot.dataset.region = i;
                    hotspot.innerText = i;
                    hotspot.addEventListener('click', function() {
                        const rVal = parseInt(this.dataset.region);
                        if (thSevereRegions.has(rVal)) {
                            thSevereRegions.delete(rVal);
                            this.classList.remove('selected');
                        } else {
                            thSevereRegions.add(rVal);
                            this.classList.add('selected');
                        }
                        document.getElementById('severe-count-badge').innerText = thSevereRegions.size;
                    });
                    severeMapContainer.appendChild(hotspot);
                }
            }

            // Map 2: Moderate Pain
            const moderateMapContainer = document.getElementById('th-map-moderate');
            if (moderateMapContainer) {
                for (let i = 1; i <= 39; i++) {
                    const coord = regionCoords[i];
                    if (!coord) continue;
                    const hotspot = document.createElement('div');
                    hotspot.className = 'hotspot available th-hotspot-moderate';
                    hotspot.style.top = coord.top + '%';
                    hotspot.style.left = coord.left + '%';
                    hotspot.dataset.region = i;
                    hotspot.innerText = i;
                    hotspot.addEventListener('click', function() {
                        const rVal = parseInt(this.dataset.region);
                        if (thModerateRegions.has(rVal)) {
                            thModerateRegions.delete(rVal);
                            this.classList.remove('selected');
                        } else {
                            thModerateRegions.add(rVal);
                            this.classList.add('selected');
                        }
                        document.getElementById('moderate-count-badge').innerText = thModerateRegions.size;
                    });
                    moderateMapContainer.appendChild(hotspot);
                }
            }

            // Listen to blood type changes
            const bloodTypeSelect = document.getElementById('th_blood_type');
            if (bloodTypeSelect) {
                bloodTypeSelect.addEventListener('change', function() {
                    const effectiveInput = document.getElementById('th_effective_blood_type');
                    if (this.value === 'dont_know') {
                        if (effectiveInput) effectiveInput.value = 'O';
                    } else {
                        if (effectiveInput) effectiveInput.value = this.value;
                    }
                });
            }
        }

        // Confirm Therapeutic Selection & Show Protocols
        function confirmTherapeuticSelection() {
            const nameVal = document.getElementById('th_name').value.trim();
            const phoneVal = document.getElementById('th_phone').value.trim();
            const genderVal = document.getElementById('th_gender').value;
            const ageVal = document.getElementById('th_age').value;
            const weightVal = parseFloat(document.getElementById('th_weight').value);
            const bloodTypeVal = document.getElementById('th_blood_type').value;
            const effectiveBloodType = document.getElementById('th_effective_blood_type').value || 'O';

            const severeCount = thSevereRegions.size;
            const moderateCount = thModerateRegions.size;
            const totalPainCount = severeCount + moderateCount;

            const validationFeedback = document.getElementById('therapeutic_validation_feedback');

            // Validation: Ensure all basic patient fields and at least 1 pain area are selected
            if (!nameVal || !phoneVal || !genderVal || !ageVal || isNaN(weightVal) || !bloodTypeVal || totalPainCount === 0) {
                if (validationFeedback) {
                    validationFeedback.style.display = 'block';
                    validationFeedback.scrollIntoView({ behavior: 'smooth' });
                }
                return;
            }

            if (validationFeedback) {
                validationFeedback.style.display = 'none';
            }

            const protocolSec = document.getElementById('therapeutic-protocol-section');
            if (protocolSec) {
                protocolSec.style.display = 'block';

                // 1. Calculate Therapeutic Massage (المساج العلاجي) component
                const bloodTypeTechniques = {
                    'A': 'مسحي علاجي واسترخائي عميق (وتري زلالي / انبساطي تجمعي)',
                    'B': 'ضغط نقطي وعضلي علاجي (ليمفاوي عصبي / وتري زلالي)',
                    'AB': 'تقويم عضلي ومسحي مركب (عصبي زلالي / عقدي حمضي)',
                    'O': 'علاجي هارد عميق وتفكيك التصلبات (وتري مفصلي / حمضي عصبي)'
                };
                const baseTechnique = bloodTypeTechniques[effectiveBloodType] || bloodTypeTechniques['O'];

                // Weight brackets: 30-55 kg, 55-100 kg, 100-300 kg
                let bracket = '30_55';
                if (weightVal >= 100) {
                    bracket = '100_300';
                } else if (weightVal >= 55) {
                    bracket = '55_100';
                }

                const severeParams = {
                    '30_55':   { intensive: { duration: 1.5, price: 21 }, economy: { duration: 1.0, price: 12 } },
                    '55_100':  { intensive: { duration: 2.0, price: 32 }, economy: { duration: 1.5, price: 21 } },
                    '100_300': { intensive: { duration: 2.5, price: 45 }, economy: { duration: 2.0, price: 24 } }
                };

                const moderateParams = {
                    '30_55':   { intensive: { duration: 1.5, price: 21 }, economy: { duration: 1.0, price: 12 } },
                    '55_100':  { intensive: { duration: 2.0, price: 32 }, economy: { duration: 1.5, price: 21 } },
                    '100_300': { intensive: { duration: 2.5, price: 45 }, economy: { duration: 2.0, price: 24 } }
                };

                const severeTechniqueMaps = {
                    'A': {
                        '30_55': {
                            1: 3, 2: 1, 3: 2, 4: 3, 5: 3, 6: 1, 7: 2, 8: 3, 9: 2, 10: 2,
                            11: 6, 12: 6, 13: 5, 14: 5, 15: 2, 16: 2, 17: 1, 18: 2, 19: 1, 20: 1,
                            21: 2, 22: 1, 23: 1, 24: 1, 25: 5, 26: 3, 27: 2, 28: 3, 29: 3, 30: 2,
                            31: 1, 32: 1, 33: 1, 34: 1, 35: 1, 36: 1, 37: 2, 38: 2, 39: 2
                        },
                        '55_100': {
                            1: 3, 2: 1, 3: 2, 4: 3, 5: 3, 6: 1, 7: 2, 8: 3, 9: 2, 10: 2,
                            11: 6, 12: 6, 13: 5, 14: 5, 15: 2, 16: 2, 17: 1, 18: 2, 19: 1, 20: 1,
                            21: 2, 22: 1, 23: 1, 24: 1, 25: 5, 26: 3, 27: 2, 28: 3, 29: 3, 30: 2,
                            31: 1, 32: 1, 33: 1, 34: 1, 35: 1, 36: 1, 37: 2, 38: 2, 39: 2
                        },
                        '100_300': {
                            1: 3, 2: 2, 3: 2, 4: 3, 5: 3, 6: 1, 7: 2, 8: 3, 9: 2, 10: 2,
                            11: 6, 12: 6, 13: 5, 14: 5, 15: 2, 16: 2, 17: 1, 18: 2, 19: 1, 20: 1,
                            21: 2, 22: 1, 23: 1, 24: 1, 25: 5, 26: 3, 27: 2, 28: 2, 29: 3, 30: 2,
                            31: 1, 32: 1, 33: 1, 34: 1, 35: 1, 36: 1, 37: 2, 38: 2, 39: 2
                        }
                    },
                    'AB': {
                        '30_55': {
                            1: 3, 2: 2, 3: 2, 4: 3, 5: 3, 6: 1, 7: 2, 8: 3, 9: 2, 10: 2,
                            11: 6, 12: 6, 13: 5, 14: 5, 15: 2, 16: 2, 17: 1, 18: 2, 19: 1, 20: 1,
                            21: 2, 22: 1, 23: 1, 24: 1, 25: 5, 26: 3, 27: 2, 28: 2, 29: 3, 30: 2,
                            31: 1, 32: 1, 33: 1, 34: 1, 35: 1, 36: 1, 37: 2, 38: 2, 39: 2
                        },
                        '55_100': {
                            1: 3, 2: 2, 3: 2, 4: 3, 5: 3, 6: 1, 7: 2, 8: 3, 9: 2, 10: 2,
                            11: 6, 12: 6, 13: 5, 14: 5, 15: 2, 16: 2, 17: 1, 18: 2, 19: 1, 20: 1,
                            21: 2, 22: 1, 23: 1, 24: 1, 25: 5, 26: 3, 27: 2, 28: 2, 29: 3, 30: 2,
                            31: 1, 32: 1, 33: 1, 34: 1, 35: 1, 36: 1, 37: 2, 38: 2, 39: 2
                        },
                        '100_300': {
                            1: 3, 2: 2, 3: 2, 4: 3, 5: 3, 6: 1, 7: 2, 8: 3, 9: 2, 10: 2,
                            11: 6, 12: 6, 13: 5, 14: 5, 15: 2, 16: 2, 17: 1, 18: 2, 19: 1, 20: 1,
                            21: 2, 22: 1, 23: 1, 24: 1, 25: 5, 26: 3, 27: 2, 28: 2, 29: 3, 30: 2,
                            31: 1, 32: 1, 33: 1, 34: 1, 35: 1, 36: 1, 37: 2, 38: 2, 39: 2
                        }
                    },
                    'B': {
                        '30_55': {
                            1: 3, 2: 1, 3: 2, 4: 3, 5: 3, 6: 1, 7: 2, 8: 3, 9: 2, 10: 2,
                            11: 6, 12: 6, 13: 5, 14: 5, 15: 2, 16: 2, 17: 1, 18: 2, 19: 1, 20: 1,
                            21: 2, 22: 1, 23: 1, 24: 1, 25: 5, 26: 3, 27: 2, 28: 3, 29: 3, 30: 2,
                            31: 1, 32: 1, 33: 1, 34: 1, 35: 1, 36: 1, 37: 2, 38: 2, 39: 2
                        },
                        '55_100': {
                            1: 3, 2: 1, 3: 2, 4: 3, 5: 3, 6: 1, 7: 2, 8: 3, 9: 2, 10: 2,
                            11: 6, 12: 6, 13: 5, 14: 5, 15: 2, 16: 2, 17: 1, 18: 2, 19: 1, 20: 1,
                            21: 2, 22: 1, 23: 1, 24: 1, 25: 5, 26: 3, 27: 2, 28: 3, 29: 3, 30: 2,
                            31: 1, 32: 1, 33: 1, 34: 1, 35: 1, 36: 1, 37: 2, 38: 2, 39: 2
                        },
                        '100_300': {
                            1: 3, 2: 2, 3: 2, 4: 3, 5: 3, 6: 1, 7: 2, 8: 3, 9: 2, 10: 2,
                            11: 6, 12: 6, 13: 5, 14: 5, 15: 2, 16: 2, 17: 1, 18: 2, 19: 1, 20: 1,
                            21: 2, 22: 1, 23: 1, 24: 1, 25: 5, 26: 3, 27: 2, 28: 2, 29: 3, 30: 2,
                            31: 1, 32: 1, 33: 1, 34: 1, 35: 1, 36: 1, 37: 2, 38: 2, 39: 2
                        }
                    },
                    'O': {
                        '30_55': {
                            1: 3, 2: 2, 3: 2, 4: 3, 5: 3, 6: 1, 7: 2, 8: 3, 9: 2, 10: 2,
                            11: 6, 12: 6, 13: 5, 14: 5, 15: 2, 16: 2, 17: 1, 18: 2, 19: 1, 20: 1,
                            21: 2, 22: 1, 23: 1, 24: 1, 25: 5, 26: 3, 27: 2, 28: 2, 29: 3, 30: 2,
                            31: 1, 32: 1, 33: 1, 34: 1, 35: 1, 36: 1, 37: 2, 38: 2, 39: 2
                        },
                        '55_100': {
                            1: 3, 2: 1, 3: 2, 4: 3, 5: 3, 6: 1, 7: 2, 8: 3, 9: 2, 10: 2,
                            11: 6, 12: 6, 13: 5, 14: 5, 15: 2, 16: 2, 17: 1, 18: 2, 19: 1, 20: 1,
                            21: 2, 22: 1, 23: 1, 24: 1, 25: 5, 26: 3, 27: 2, 28: 3, 29: 3, 30: 2,
                            31: 1, 32: 1, 33: 1, 34: 1, 35: 1, 36: 1, 37: 2, 38: 2, 39: 2
                        },
                        '100_300': {
                            1: 3, 2: 2, 3: 2, 4: 3, 5: 3, 6: 1, 7: 2, 8: 3, 9: 2, 10: 2,
                            11: 6, 12: 6, 13: 5, 14: 5, 15: 2, 16: 2, 17: 1, 18: 2, 19: 1, 20: 1,
                            21: 2, 22: 1, 23: 1, 24: 1, 25: 5, 26: 3, 27: 2, 28: 2, 29: 3, 30: 2,
                            31: 1, 32: 1, 33: 1, 34: 1, 35: 1, 36: 1, 37: 2, 38: 2, 39: 2
                        }
                    }
                };

                const moderateTechniqueMap = {
                    1: 3, 2: 1, 3: 2, 4: 3, 5: 3, 6: 1, 7: 2, 8: 3, 9: 1, 10: 1,
                    11: 3, 12: 3, 13: 2, 14: 2, 15: 1, 16: 1, 17: 1, 18: 1, 19: 1, 20: 1,
                    21: 1, 22: 1, 23: 1, 24: 1, 25: 4, 26: 1, 27: 2, 28: 4, 29: 1, 30: 2,
                    31: 1, 32: 1, 33: 1, 34: 1, 35: 1, 36: 1, 37: 2, 38: 1, 39: 1
                };

                const bloodMap = severeTechniqueMaps[effectiveBloodType] || severeTechniqueMaps['O'];
                const sevTechniqueMap = bloodMap[bracket] || bloodMap['55_100'];

                let severeTechniqueCount = 0;
                thSevereRegions.forEach(rNum => {
                    severeTechniqueCount += sevTechniqueMap[rNum] || 1;
                });

                let moderateTechniqueCount = 0;
                thModerateRegions.forEach(rNum => {
                    moderateTechniqueCount += moderateTechniqueMap[rNum] || 1;
                });

                const sevInt = severeParams[bracket].intensive;
                const modInt = moderateParams[bracket].intensive;

                const sevEco = severeParams[bracket].economy;
                const modEco = moderateParams[bracket].economy;

                const massageIntDuration = (severeTechniqueCount * sevInt.duration) + (moderateTechniqueCount * modInt.duration);
                const massageIntPrice = (severeTechniqueCount * sevInt.price) + (moderateTechniqueCount * modInt.price);

                const massageEcoDuration = (severeTechniqueCount * sevEco.duration) + (moderateTechniqueCount * modEco.duration);
                const massageEcoPrice = (severeTechniqueCount * sevEco.price) + (moderateTechniqueCount * modEco.price);

                const massageIntensDesc = document.getElementById('proto-massage-intensive-desc');
                const massageEconDesc = document.getElementById('proto-massage-economy-desc');

                if (massageIntensDesc) {
                    massageIntensDesc.innerHTML = `سيشن مساج علاجي مكثف مخصصة لمناطق الألم المحددة.`;
                }

                if (massageEconDesc) {
                    massageEconDesc.innerHTML = `سيشن مساج علاجي مركزة لمناطق الألم المحددة.`;
                }

                // 2. Calculate Therapeutic Chiropractic (الكيروبراكتيك العلاجي) component based on 5 Region Groups
                const chiroGroups = {
                    1: { name: 'منطقة 1 العنقية', regions: [15, 16, 37] },
                    2: { name: 'منطقة 2 الأكتاف والذراعين', regions: [17, 18, 19, 20, 21, 22, 23, 24, 33, 35] },
                    3: { name: 'منطقة 3 الصدرية', regions: [13, 14] },
                    4: { name: 'منطقة 4 القطنية', regions: [9, 10, 11, 12] },
                    5: { name: 'منطقة 5 القدمين والطرف السفلي', regions: [1, 2, 3, 4, 5, 6, 7, 8, 25, 26, 27, 28, 29, 30, 31, 32, 34, 36, 38, 39] }
                };

                const chiroGroupTechs = {
                    intensive: { 1: 13, 2: 17, 3: 13, 4: 14, 5: 20 },
                    economy:   { 1: 8, 2: 13, 3: 9, 4: 10, 5: 14 }
                };

                const getChiroGroup = (rNum) => {
                    for (let gId in chiroGroups) {
                        if (chiroGroups[gId].regions.includes(parseInt(rNum))) {
                            return parseInt(gId);
                        }
                    }
                    return 5;
                };

                const allPainRegions = new Set([...thSevereRegions, ...thModerateRegions]);
                const activeChiroGroups = new Set();
                allPainRegions.forEach(rNum => {
                    activeChiroGroups.add(getChiroGroup(rNum));
                });

                let chiroIntTechs = 0;
                let chiroEcoTechs = 0;
                activeChiroGroups.forEach(gId => {
                    chiroIntTechs += chiroGroupTechs.intensive[gId] || 0;
                    chiroEcoTechs += chiroGroupTechs.economy[gId] || 0;
                });

                const chiroPricePerTech = 16.23;
                const chiroDurPerTech = 0.25; // 15 sec

                const chiroIntPrice = chiroIntTechs * chiroPricePerTech;
                const chiroIntDuration = chiroIntTechs * chiroDurPerTech;

                const chiroEcoPrice = chiroEcoTechs * chiroPricePerTech;
                const chiroEcoDuration = chiroEcoTechs * chiroDurPerTech;

                const chiroIntensDesc = document.getElementById('proto-chiro-intensive-desc');
                const chiroEconDesc = document.getElementById('proto-chiro-economy-desc');

                if (chiroIntensDesc) {
                    chiroIntensDesc.innerHTML = `تقويم علاجي شامل يشمل <strong>${chiroIntTechs} تكنيكاً</strong> لكافة المناطق المصابة.`;
                }

                if (chiroEconDesc) {
                    chiroEconDesc.innerHTML = `تقويم علاجي مركز يشمل <strong>${chiroEcoTechs} تكنيكاً</strong> للمناطق المصابة.`;
                }

                // 3. Calculate Hijama component if Severe Pain regions are selected
                const intensiveDesc = document.getElementById('proto-hijama-intensive-desc');
                const economyDesc = document.getElementById('proto-hijama-economy-desc');

                let intCups = 0;
                let ecoCups = 0;
                let intCupPrice = 0;
                let ecoCupPrice = 0;
                let intTotalPrice = 0;
                let ecoTotalPrice = 0;
                let intDuration = 0;
                let ecoDuration = 0;

                if (severeCount > 0) {
                    const intensiveCupsMap = {
                        1: 3, 2: 1, 3: 2, 4: 4, 5: 3, 6: 1, 7: 2, 8: 4, 9: 2, 10: 2,
                        11: 3, 12: 3, 13: 2, 14: 2, 15: 2, 16: 2, 17: 1, 18: 2, 19: 3, 20: 1,
                        21: 2, 22: 3, 23: 2, 24: 2, 25: 3, 26: 2, 27: 2, 28: 3, 29: 3, 30: 3,
                        31: 2, 32: 2, 33: 1, 34: 2, 35: 1, 36: 2, 37: 2, 38: 2, 39: 2
                    };
                    const economyCupsMap = {
                        1: 2, 2: 1, 3: 1, 4: 2, 5: 2, 6: 1, 7: 1, 8: 2, 9: 2, 10: 1,
                        11: 1, 12: 1, 13: 1, 14: 1, 15: 1, 16: 1, 17: 1, 18: 1, 19: 1, 20: 1,
                        21: 1, 22: 1, 23: 1, 24: 1, 25: 1, 26: 1, 27: 1, 28: 1, 29: 1, 30: 1,
                        31: 1, 32: 1, 33: 1, 34: 1, 35: 1, 36: 1, 37: 1, 38: 1, 39: 1
                    };

                    thSevereRegions.forEach(rNum => {
                        intCups += intensiveCupsMap[rNum] || 1;
                        ecoCups += economyCupsMap[rNum] || 1;
                    });

                    function getCupPrice(totalCups) {
                        if (totalCups > 20) return 35;
                        if (totalCups >= 16) return 37;
                        if (totalCups >= 11) return 40;
                        return 45;
                    }

                    intCupPrice = getCupPrice(intCups);
                    intTotalPrice = intCups * intCupPrice;
                    intDuration = 10 + intCups;

                    ecoCupPrice = getCupPrice(ecoCups);
                    ecoTotalPrice = ecoCups * ecoCupPrice;
                    ecoDuration = 10 + ecoCups;

                    if (intensiveDesc) {
                        intensiveDesc.innerHTML = `تطبيق كاسات الحجامة المكثفة (<strong>${intCups} كاسات</strong>) على المناطق شديدة الألم.`;
                    }
                    if (economyDesc) {
                        economyDesc.innerHTML = `تطبيق كاسات الحجامة (<strong>${ecoCups} كاسات</strong>) على المناطق شديدة الألم.`;
                    }
                } else {
                    if (intensiveDesc) {
                        intensiveDesc.innerHTML = `<span class="text-muted">لا تتضمن السيشن حجامة لعدم وجود مناطق شديدة الألم.</span>`;
                    }
                    if (economyDesc) {
                        economyDesc.innerHTML = `<span class="text-muted">لا تتضمن السيشن حجامة لعدم وجود مناطق شديدة الألم.</span>`;
                    }
                }

                // 4. Calculate Rehabilitation (التأهيل) component
                const hasAnyPain = (severeCount > 0 || moderateCount > 0);

                const rehabIntensDesc = document.getElementById('proto-rehab-intensive-desc');
                const rehabEconDesc = document.getElementById('proto-rehab-economy-desc');

                let intRehabDuration = 0;
                let intRehabPrice = 0;
                let ecoRehabDuration = 0;
                let ecoRehabPrice = 0;

                // Intensive Rehabilitation
                if (hasAnyPain) {
                    intRehabDuration = 10;
                    intRehabPrice = intRehabDuration * 12;

                    if (rehabIntensDesc) {
                        rehabIntensDesc.innerHTML = `برنامج تمارين تأهيلية متقدمة لتسريع التعافي ومنع تجدد الإصابة.`;
                    }
                } else {
                    if (rehabIntensDesc) {
                        rehabIntensDesc.innerHTML = `<span class="text-muted">لا يتضمن التأهيل تمارين لعدم اختيار مناطق ألم.</span>`;
                    }
                }

                // Economy Rehabilitation (Always 5 minutes when pain is present)
                if (hasAnyPain) {
                    ecoRehabDuration = 5;
                    ecoRehabPrice = ecoRehabDuration * 12;

                    if (rehabEconDesc) {
                        rehabEconDesc.innerHTML = `تمارين استطالة وتأهيل حركي أساسية لتخفيف الإجهاد والألم.`;
                    }
                } else {
                    if (rehabEconDesc) {
                        rehabEconDesc.innerHTML = `<span class="text-muted">لا يتضمن التأهيل تمارين لعدم اختيار مناطق ألم.</span>`;
                    }
                }

                // 5. Calculate Full Duration and Final Price for Intensive and Economy
                const totalIntDuration = massageIntDuration + chiroIntDuration + intDuration + intRehabDuration;
                const totalIntPrice = massageIntPrice + chiroIntPrice + intTotalPrice + intRehabPrice;

                const totalEcoDuration = massageEcoDuration + chiroEcoDuration + ecoDuration + ecoRehabDuration;
                const totalEcoPrice = massageEcoPrice + chiroEcoPrice + ecoTotalPrice + ecoRehabPrice;

                const intDurEl = document.getElementById('proto-total-duration-intensive');
                const intPriceEl = document.getElementById('proto-total-price-intensive');
                const ecoDurEl = document.getElementById('proto-total-duration-economy');
                const ecoPriceEl = document.getElementById('proto-total-price-economy');

                if (intDurEl) intDurEl.textContent = `${Math.round(totalIntDuration)} دقيقة`;
                if (intPriceEl) intPriceEl.textContent = `${totalIntPrice.toFixed(2)} ج.م`;

                if (ecoDurEl) ecoDurEl.textContent = `${Math.round(totalEcoDuration)} دقيقة`;
                if (ecoPriceEl) ecoPriceEl.textContent = `${totalEcoPrice.toFixed(2)} ج.م`;

                // Store calculated values
                thCalculated.intensive = {
                    price: totalIntPrice,
                    duration: Math.round(totalIntDuration)
                };
                thCalculated.economy = {
                    price: totalEcoPrice,
                    duration: Math.round(totalEcoDuration)
                };

                const sevHidden = document.getElementById('th_submitted_severe_regions');
                const modHidden = document.getElementById('th_submitted_moderate_regions');
                if (sevHidden) sevHidden.value = Array.from(thSevereRegions).join(',');
                if (modHidden) modHidden.value = Array.from(thModerateRegions).join(',');

                // Automatically select intensive protocol by default and reveal appointment section
                selectProtocol(currentThProtocol || 'intensive');
            }
        }

        let thCalculated = {
            intensive: { price: 0, duration: 0 },
            economy: { price: 0, duration: 0 }
        };
        let currentThProtocol = 'intensive';
        let thCouponDiscount = 0;

        function selectProtocol(type) {
            currentThProtocol = type;
            const cardIntensive = document.getElementById('protocol-intensive-card');
            const cardEconomy = document.getElementById('protocol-economy-card');
            const radioIntensive = document.getElementById('proto_intensive');
            const radioEconomy = document.getElementById('proto_economy');

            if (type === 'intensive') {
                if (cardIntensive) cardIntensive.classList.add('selected-protocol');
                if (cardEconomy) cardEconomy.classList.remove('selected-protocol');
                if (radioIntensive) radioIntensive.checked = true;
            } else {
                if (cardEconomy) cardEconomy.classList.add('selected-protocol');
                if (cardIntensive) cardIntensive.classList.remove('selected-protocol');
                if (radioEconomy) radioEconomy.checked = true;
            }

            const apptSec = document.getElementById('therapeutic-appointment-section');
            if (apptSec) {
                apptSec.style.display = 'block';
            }

            updateTherapeuticSummaryAndPricing();

            const isUrgentChecked = document.getElementById('th_is_urgent') && document.getElementById('th_is_urgent').checked;
            if (isUrgentChecked) {
                thValidateTimeSelection();
            } else {
                thFetchAvailableTimes();
            }

            if (apptSec) {
                apptSec.scrollIntoView({ behavior: 'smooth' });
            }
        }

        function updateTherapeuticSummaryAndPricing() {
            const protoData = thCalculated[currentThProtocol] || { price: 0, duration: 0 };
            const basePrice = protoData.price;
            const duration = protoData.duration;

            const isUrgent = document.getElementById('th_is_urgent')?.checked || false;
            const urgentFee = isUrgent ? {{ $urgentBookingFee }} : 0;

            const urgentRow = document.getElementById('th_urgent_fee_row');
            const urgentFeeSpan = document.getElementById('th_summary_urgent_fee');
            if (urgentRow && urgentFeeSpan) {
                if (isUrgent) {
                    urgentRow.style.display = 'flex';
                    urgentFeeSpan.textContent = urgentFee;
                } else {
                    urgentRow.style.display = 'none';
                }
            }

            const couponRow = document.getElementById('th_coupon_discount_row');
            const couponSpan = document.getElementById('th_summary_coupon_discount');
            if (couponRow && couponSpan) {
                if (thCouponDiscount > 0) {
                    couponRow.style.display = 'flex';
                    couponSpan.textContent = thCouponDiscount.toFixed(2);
                } else {
                    couponRow.style.display = 'none';
                }
            }

            const finalTotalPrice = Math.max(0, basePrice + urgentFee - thCouponDiscount);
            const depositAmount = Math.ceil(finalTotalPrice * 0.40);

            const priceEl = document.getElementById('th_summary_total_price');
            const durEl = document.getElementById('th_summary_total_duration');
            const depositEl = document.getElementById('th_deposit_amount');

            if (priceEl) priceEl.textContent = finalTotalPrice.toFixed(2);
            if (durEl) durEl.textContent = duration;
            if (depositEl) depositEl.textContent = depositAmount;

            const hiddenPrice = document.getElementById('th_submitted_total_price');
            const hiddenDuration = document.getElementById('th_submitted_total_duration');
            if (hiddenPrice) hiddenPrice.value = finalTotalPrice;
            if (hiddenDuration) hiddenDuration.value = duration;
        }

        function thFetchAvailableTimes() {
            const dateVal = document.getElementById('th_appointment_date')?.value;
            const timeSelect = document.getElementById('th_appointment_time_select');
            const genderVal = document.getElementById('th_gender')?.value;
            const protoData = thCalculated[currentThProtocol] || { price: 0, duration: 30 };

            if (!dateVal || !timeSelect) return;
            if (!genderVal) {
                timeSelect.innerHTML = '<option value="" disabled selected>يرجى اختيار الجنس من الأعلى أولاً...</option>';
                return;
            }

            timeSelect.innerHTML = '<option value="" disabled selected>جارِ البحث عن المواعيد المتاحة...</option>';

            const attendeesPayload = [{
                gender: genderVal,
                duration: Math.ceil(protoData.duration || 30)
            }];

            const isUrgent = document.getElementById('th_is_urgent')?.checked ? 1 : 0;
            const url = `{{ route('booking.available-times') }}?date=${encodeURIComponent(dateVal)}&is_urgent=${isUrgent}&attendees=${encodeURIComponent(JSON.stringify(attendeesPayload))}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    timeSelect.innerHTML = '';
                    if (data.error) {
                        timeSelect.innerHTML = `<option value="" disabled selected>${data.error}</option>`;
                        return;
                    }

                    const timeKeys = Object.keys(data);
                    if (timeKeys.length === 0) {
                        timeSelect.innerHTML = '<option value="" disabled selected>عذراً، لا توجد أوقات شاغرة في هذا اليوم</option>';
                        return;
                    }

                    const defaultOpt = document.createElement('option');
                    defaultOpt.value = '';
                    defaultOpt.disabled = true;
                    defaultOpt.selected = true;
                    defaultOpt.textContent = 'اختر الوقت المناسب...';
                    timeSelect.appendChild(defaultOpt);

                    timeKeys.forEach(timeKey => {
                        const opt = document.createElement('option');
                        opt.value = timeKey;
                        opt.textContent = data[timeKey];
                        timeSelect.appendChild(opt);
                    });
                })
                .catch(err => {
                    timeSelect.innerHTML = '<option value="" disabled selected>حدث خطأ في تحميل الأوقات</option>';
                });
        }

        function thValidateTimeSelection() {
            const dateVal = document.getElementById('th_appointment_date')?.value;
            const timeVal = document.getElementById('th_appointment_time_input')?.value;
            const feedbackEl = document.getElementById('th_time_validation_feedback');
            const genderVal = document.getElementById('th_gender')?.value;
            const protoData = thCalculated[currentThProtocol] || { price: 0, duration: 30 };

            if (!feedbackEl) return;
            if (!dateVal || !timeVal) {
                feedbackEl.style.display = 'none';
                return;
            }

            const attendeesPayload = [{
                gender: genderVal || 'male',
                duration: Math.ceil(protoData.duration || 30)
            }];

            const isUrgent = document.getElementById('th_is_urgent')?.checked ? 1 : 0;
            const url = `{{ route('booking.validate-time') }}?date=${encodeURIComponent(dateVal)}&time=${encodeURIComponent(timeVal)}&is_urgent=${isUrgent}&attendees=${encodeURIComponent(JSON.stringify(attendeesPayload))}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    feedbackEl.style.display = 'block';
                    if (data.available) {
                        feedbackEl.style.color = '#27ae60';
                        feedbackEl.textContent = '✓ ' + data.message;
                    } else {
                        feedbackEl.style.color = '#e74c3c';
                        feedbackEl.textContent = '✗ ' + data.message;
                    }
                })
                .catch(() => {
                    feedbackEl.style.display = 'none';
                });
        }

        // Initialize Therapeutic Listeners
        document.addEventListener('DOMContentLoaded', function() {
            const thDate = document.getElementById('th_appointment_date');
            const thTimeSelect = document.getElementById('th_appointment_time_select');
            const thTimeInput = document.getElementById('th_appointment_time_input');
            const thUrgentToggle = document.getElementById('th_is_urgent');
            const thGender = document.getElementById('th_gender');
            const thBtnCoupon = document.getElementById('th_btn-apply-coupon');

            if (thDate) {
                thDate.addEventListener('change', function() {
                    if (thUrgentToggle && thUrgentToggle.checked) {
                        thValidateTimeSelection();
                    } else {
                        thFetchAvailableTimes();
                    }
                });
            }

            if (thGender) {
                thGender.addEventListener('change', function() {
                    if (thDate && thDate.value) {
                        if (thUrgentToggle && thUrgentToggle.checked) {
                            thValidateTimeSelection();
                        } else {
                            thFetchAvailableTimes();
                        }
                    }
                });
            }

            if (thTimeSelect) {
                thTimeSelect.addEventListener('change', function() {
                    const hiddenTime = document.getElementById('th_appointment_time');
                    if (hiddenTime) hiddenTime.value = this.value;
                });
            }

            if (thTimeInput) {
                thTimeInput.addEventListener('input', function() {
                    const hiddenTime = document.getElementById('th_appointment_time');
                    if (hiddenTime) hiddenTime.value = this.value;
                    thValidateTimeSelection();
                });
            }

            if (thUrgentToggle) {
                thUrgentToggle.addEventListener('change', function() {
                    const regContainer = document.getElementById('th_regular_time_container');
                    const urgContainer = document.getElementById('th_urgent_time_container');
                    const hiddenTime = document.getElementById('th_appointment_time');

                    if (this.checked) {
                        if (regContainer) regContainer.style.display = 'none';
                        if (urgContainer) urgContainer.style.display = 'block';
                        if (hiddenTime) hiddenTime.value = thTimeInput ? thTimeInput.value : '';
                        thValidateTimeSelection();
                    } else {
                        if (regContainer) regContainer.style.display = 'block';
                        if (urgContainer) urgContainer.style.display = 'none';
                        if (hiddenTime) hiddenTime.value = thTimeSelect ? thTimeSelect.value : '';
                        thFetchAvailableTimes();
                    }

                    updateTherapeuticSummaryAndPricing();
                });
            }

            if (thBtnCoupon) {
                thBtnCoupon.addEventListener('click', function() {
                    const code = document.getElementById('th_coupon_input')?.value.trim();
                    const feedbackEl = document.getElementById('th_coupon_feedback');
                    const dateVal = thDate ? thDate.value : '';
                    const protoData = thCalculated[currentThProtocol] || { price: 0, duration: 0 };

                    if (!code) {
                        if (feedbackEl) {
                            feedbackEl.style.display = 'block';
                            feedbackEl.style.color = '#e74c3c';
                            feedbackEl.innerText = 'يرجى إدخال كود الكوبون أولاً.';
                        }
                        return;
                    }

                    const url = `{{ route('booking.validate-coupon') }}?code=${encodeURIComponent(code)}&date=${encodeURIComponent(dateVal)}&total_price=${encodeURIComponent(protoData.price)}`;

                    fetch(url)
                        .then(res => res.json())
                        .then(data => {
                            if (!feedbackEl) return;
                            feedbackEl.style.display = 'block';
                            if (data.valid) {
                                thCouponDiscount = parseFloat(data.discount_amount) || 0;
                                document.getElementById('th_submitted_coupon_code').value = data.code;
                                feedbackEl.style.color = '#27ae60';
                                feedbackEl.innerText = `✓ ${data.message} (تم خصم ${thCouponDiscount.toFixed(2)} ج.م)`;
                            } else {
                                thCouponDiscount = 0;
                                document.getElementById('th_submitted_coupon_code').value = '';
                                feedbackEl.style.color = '#e74c3c';
                                feedbackEl.innerText = '✗ ' + data.message;
                            }
                            updateTherapeuticSummaryAndPricing();
                        })
                        .catch(() => {
                            if (feedbackEl) {
                                feedbackEl.style.display = 'block';
                                feedbackEl.style.color = '#e74c3c';
                                feedbackEl.innerText = '⚠️ خطأ في الاتصال بالخادم للتحقق من الكوبون.';
                            }
                        });
                });
            }

            // Form Submit Listener for Therapeutic Form
            const thForm = document.getElementById('bookingFormTherapeutic');
            if (thForm) {
                thForm.addEventListener('submit', function(e) {
                    const thName = document.getElementById('th_name')?.value.trim();
                    const thPhone = document.getElementById('th_phone')?.value.trim();
                    const thGender = document.getElementById('th_gender')?.value;
                    const thWeight = document.getElementById('th_weight')?.value;
                    const thDate = document.getElementById('th_appointment_date')?.value;
                    const thTime = document.getElementById('th_appointment_time')?.value;
                    const thAgreeYes = document.getElementById('th_agree_yes')?.checked;

                    if (!thName || !thPhone || !thGender || !thWeight) {
                        e.preventDefault();
                        alert('يرجى استكمال البيانات الشخصية والصحية (الاسم، الهاتف، الجنس، والوزن).');
                        return;
                    }

                    if (!thDate) {
                        e.preventDefault();
                        alert('يرجى اختيار تاريخ موعد السيشن.');
                        return;
                    }

                    if (!thTime) {
                        e.preventDefault();
                        alert('يرجى اختيار وقت السيشن المتاح.');
                        return;
                    }

                    if (!thAgreeYes) {
                        e.preventDefault();
                        alert('يجب الموافقة على شروط الحجز والمقدم المالي لتأكيد الحجز.');
                        return;
                    }

                    // Set regions in hidden inputs
                    const severeInput = document.getElementById('th_submitted_severe_regions');
                    if (severeInput) severeInput.value = Array.from(thSevereRegions).join(',');

                    const moderateInput = document.getElementById('th_submitted_moderate_regions');
                    if (moderateInput) moderateInput.value = Array.from(thModerateRegions).join(',');
                });
            }
        });
    </script>
</body>
</html>
