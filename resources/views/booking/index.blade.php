<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Primary SEO Metadata -->
    <title>الخدمات وحجوزات الجلسات | Shiatic Booking & Services</title>
    <meta name="description" content="تعرف على الخدمات العلاجية والوقائية والرياضية التي يقدمها مركز شياتك (Shiatic) لتقويم العمود الفقري (الكايروبراكتيك). احجز موعد جلستك العلاجية الآن بسهولة.">
    <meta name="keywords" content="حجز جلسة كيروبراكتيك, خدمات شياتك, مساج علاجي مصر, تقويم العمود الفقري, عرق النسا, علاج الظهر">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ route('booking.index') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('booking.index') }}">
    <meta property="og:title" content="الخدمات وحجوزات الجلسات | Shiatic Booking & Services">
    <meta property="og:description" content="تعرف على الخدمات العلاجية والوقائية والرياضية التي يقدمها مركز شياتك (Shiatic) لتقويم العمود الفقري (الكايروبراكتيك). احجز موعد جلستك العلاجية الآن بسهولة.">
    <meta property="og:image" content="{{ asset('images/R.jpg') }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ route('booking.index') }}">
    <meta name="twitter:title" content="الخدمات وحجوزات الجلسات | Shiatic Booking & Services">
    <meta name="twitter:description" content="تعرف على الخدمات العلاجية والوقائية والرياضية التي يقدمها مركز شياتك (Shiatic) لتقويم العمود الفقري (الكايروبراكتيك). احجز موعد جلستك العلاجية الآن بسهولة.">
    <meta name="twitter:image" content="{{ asset('images/R.jpg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Cairo:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2C3E50;
            --accent: #E67E22;
            --bg-glass: rgba(255, 255, 255, 0.05);
            --border-glass: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Cairo', 'Outfit', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem 1rem;
        }

        .glass-container {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-glass);
            border-radius: 24px;
            padding: 3rem;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            max-width: 800px;
            width: 100%;
            text-align: center;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 2rem;
            background: linear-gradient(to left, #fff, #94a3b8);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .section-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 1.5rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: block;
            color: inherit;
        }

        .section-card:hover {
            background: rgba(255, 255, 255, 0.07);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -10px rgba(255, 255, 255, 0.1);
        }

        .section-card h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #f8fafc;
            margin-bottom: 0.8rem;
            transition: color 0.3s ease;
        }

        .section-card:hover h2 {
            color: #ff9d42;
        }

        .section-card p {
            font-size: 1.1rem;
            color: #94a3b8;
            margin: 0;
            font-weight: 300;
            line-height: 1.6;
        }

        .icon-box {
            font-size: 2.5rem;
            color: #ff9d42;
            margin-bottom: 1rem;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            text-decoration: none;
            font-size: 1rem;
            margin-top: 1.5rem;
            transition: color 0.3s ease;
        }

        .btn-back:hover {
            color: white;
        }

        @media (max-width: 768px) {
            .glass-container {
                padding: 2rem 1.2rem;
            }
            h1 {
                font-size: 1.8rem;
                margin-bottom: 1.5rem;
            }
            .section-card {
                padding: 1.8rem 1.2rem;
                margin-bottom: 1rem;
            }
            .section-card h2 {
                font-size: 1.35rem;
            }
            .section-card p {
                font-size: 0.95rem;
            }
            .icon-box {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="glass-container">
        <h1 id="booking-services-title">الخدمات والحجوزات وعلاج آلام العمود الفقري</h1>
        
        <!-- Section 1 -->
        <div class="section-card" id="card-booking-info" onclick="alert('مركز شياتك هو مركز متخصص في العلاج اليدوي وتقويم العمود الفقري (الكايروبراكتيك) والمساج العلاجي والرياضي والوقائي.')">
            <div class="icon-box">ℹ️</div>
            <h2>عن المركز وجلساتنا</h2>
            <p>تعرف على رؤيتنا، الخدمات التي نقدمها، والتقنيات الحديثة المستخدمة لراحتكم وعلاجكم.</p>
        </div>

        <!-- Section 2 -->
        <a href="{{ route('booking.form') }}" class="section-card" id="card-booking-form">
            <div class="icon-box">🗹</div>
            <h2>حجز سيشن</h2>
            <p>ابدأ بحجز سيشن الآن. أدخل بياناتك الشخصية واختر الباقة والتقنيات المناسبة لك.</p>
        </a>

        <a href="/" class="btn-back" id="link-back-home">← العودة للرئيسية</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
