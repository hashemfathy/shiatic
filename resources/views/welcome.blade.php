<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Primary SEO Metadata -->
    <title>مركز شياتك للكايروبراكتيك وتقويم العمود الفقري | Shiatic Center</title>
    <meta name="description" content="مركز شياتك (Shiatic) هو مركزك المتخصص في الكايروبراكتيك، تقويم العمود الفقري، والمساج العلاجي والرياضي في مصر. احجز جلستك العلاجية الآن للتخلص من آلام الظهر والمفاصل مع أفضل المتخصصين.">
    <meta name="keywords" content="شياتك, Shiatic, كايروبراكتيك مصر, تقويم العمود الفقري, مساج علاجي, علاج عرق النسا, علاج آلام الظهر, علاج آلام المفاصل, علاج طبيعي, دكتور كايروبراكتيك مصر, مساج رياضي">
    <meta name="author" content="Shiatic Center">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="مركز شياتك للكايروبراكتيك وتقويم العمود الفقري | Shiatic Center">
    <meta property="og:description" content="مركز شياتك المتخصص في الكايروبراكتيك، تقويم العمود الفقري، والمساج العلاجي والرياضي في مصر. احجز جلستك العلاجية الآن للتخلص من آلام الظهر والمفاصل.">
    <meta property="og:image" content="{{ asset('images/R.jpg') }}">
    <meta property="og:site_name" content="شياتك Shiatic">
    <meta property="og:locale" content="ar_EG">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url('/') }}">
    <meta name="twitter:title" content="مركز شياتك للكايروبراكتيك وتقويم العمود الفقري | Shiatic Center">
    <meta name="twitter:description" content="مركز شياتك المتخصص في الكايروبراكتيك، تقويم العمود الفقري، والمساج العلاجي والرياضي في مصر. احجز جلستك العلاجية الآن للتخلص من آلام الظهر والمفاصل.">
    <meta name="twitter:image" content="{{ asset('images/R.jpg') }}">

    <!-- Schema.org JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "MedicalClinic",
      "name": "شياتك - Shiatic",
      "alternateName": "Shiatic Chiropractic & Spine Care",
      "description": "مركز شياتك المتخصص في الكايروبراكتيك وتقويم العمود الفقري والمساج العلاجي والرياضي في مصر.",
      "url": "{{ url('/') }}",
      "logo": "{{ asset('images/R.jpg') }}",
      "image": "{{ asset('images/R.jpg') }}",
      "telephone": "01064344092",
      "priceRange": "$$",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Cairo",
        "addressCountry": "EG"
      },
      "openingHoursSpecification": [
        {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": [
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday",
            "Sunday"
          ],
          "opens": "09:00",
          "closes": "23:00"
        }
      ],
      "medicalSpecialty": "Chiropractic",
      "availableService": [
        {
          "@type": "MedicalTherapy",
          "name": "تقويم العمود الفقري - Chiropractic Adjustment"
        },
        {
          "@type": "MedicalTherapy",
          "name": "المساج العلاجي - Therapeutic Massage"
        },
        {
          "@type": "MedicalTherapy",
          "name": "الحجامة - Hijama / Cupping Therapy"
        }
      ]
    }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1e293b;
            --accent: #E67E22;
            --bg-glass: rgba(255, 255, 255, 0.85);
            --border-glass: rgba(255, 255, 255, 0.6);
            --text-muted: #64748b;
        }

        body {
            font-family: 'Cairo', 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            margin: 0;
            overflow-x: hidden;
            min-height: 100vh;
        }

        .hero {
            position: relative;
            background-image: linear-gradient(rgba(248, 250, 252, 0.8), rgba(248, 250, 252, 0.95)), url('{{ asset('images/R.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #0f172a;
            padding: 3rem 1rem;
        }

        .glass-container {
            background: var(--bg-glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-glass);
            border-radius: 32px;
            padding: 3.5rem;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
            max-width: 1100px;
            width: 100%;
        }

        .navbar-brand-custom {
            font-family: 'Outfit', sans-serif;
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(to right, #0f172a, #475569);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 0.5rem;
        }

        .tagline {
            font-size: 1.25rem;
            font-weight: 300;
            color: #e67e22;
            letter-spacing: 1.5px;
            margin-bottom: 2rem;
        }

        .service-tag {
            display: inline-block;
            background: rgba(15, 23, 42, 0.04);
            border: 1px solid rgba(15, 23, 42, 0.08);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
        }

        /* Nav Cards Grid */
        .nav-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .nav-card {
            background: rgba(15, 23, 42, 0.02);
            border: 1px solid rgba(15, 23, 42, 0.05);
            border-radius: 20px;
            padding: 2rem 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: #0f172a !important;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .nav-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 157, 66, 0.05) 0%, rgba(230, 126, 34, 0) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .nav-card:hover {
            transform: translateY(-5px);
            background: rgba(15, 23, 42, 0.04);
            border-color: rgba(255, 157, 66, 0.3);
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
        }

        .nav-card:hover::before {
            opacity: 1;
        }

        .nav-card-primary {
            background: linear-gradient(135deg, #ff9d42 0%, #e67e22 100%);
            border: none;
            box-shadow: 0 6px 20px rgba(230, 126, 34, 0.2);
            color: white !important;
        }

        .nav-card-primary:hover {
            transform: translateY(-5px);
            background: linear-gradient(135deg, #ffa852 0%, #eb892f 100%);
            border-color: transparent;
            box-shadow: 0 10px 25px rgba(230, 126, 34, 0.3);
        }

        .nav-card-icon {
            font-size: 2.2rem;
            margin-bottom: 1rem;
            z-index: 1;
        }

        .nav-card h3 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            z-index: 1;
        }

        .nav-card p {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.4;
            z-index: 1;
        }

        .nav-card-primary p {
            color: rgba(255, 255, 255, 0.9);
        }

        /* QR Code Container/Modal styling */
        .qr-card {
            background: white;
            padding: 8px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.08);
            transition: transform 0.3s ease;
            display: inline-block;
        }

        .qr-card img {
            width: 100%;
            max-width: 180px;
            height: auto;
            display: block;
        }

        .payment-section-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #0f172a;
            margin-top: 2rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }

        .payment-card {
            background: rgba(15, 23, 42, 0.02);
            border: 1px solid rgba(15, 23, 42, 0.05);
            border-radius: 24px;
            padding: 2rem;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .payment-card:hover {
            background: rgba(15, 23, 42, 0.04);
            border-color: rgba(15, 23, 42, 0.1);
            transform: translateY(-3px);
        }

        .payment-badge {
            background: rgba(15, 23, 42, 0.05);
            color: #0f172a;
            border: 1px solid rgba(15, 23, 42, 0.1);
            padding: 6px 16px;
            font-weight: 700;
            border-radius: 50px;
            font-size: 0.95rem;
            letter-spacing: 1px;
        }

        .login-link {
            position: absolute;
            top: 2rem;
            right: 2rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: color 0.3s ease;
            z-index: 100;
        }

        .login-link:hover {
            color: #0f172a;
        }

        /* Responsiveness */
        @media (max-width: 992px) {
            .nav-grid {
                grid-template-columns: 1fr;
                gap: 1.2rem;
            }
            .payment-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            .glass-container {
                padding: 2.5rem 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .navbar-brand-custom {
                font-size: 1.8rem;
            }
            .tagline {
                font-size: 1.05rem;
                margin-bottom: 1.5rem;
            }
            .qr-desktop-only {
                display: none !important; /* Hide QR codes on mobile */
            }
            .login-link {
                top: 1rem;
                right: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <a href="{{ url('admin') }}" class="login-link" id="link-admin-login">تسجيل دخول المشرف</a>
    
    <section class="hero">
        <div class="glass-container text-center">
            <h1 class="m-0"><a href="{{ url('/') }}" class="navbar-brand-custom">Shiatic</a></h1>
            <div class="tagline">Spine tune & Touch the moon</div>
            <div class="service-tag">Chiropractic & Spine Therapy</div>
            
            <div class="nav-grid">
                <!-- Book Session -->
                <a href="{{ route('booking.form') }}" class="nav-card nav-card-primary" id="link-book-session">
                    <div class="nav-card-icon"><i class="fas fa-check-square"></i></div>
                    <h3>حجز سيشن جديدة</h3>
                    <p>احجز موعد جلستك العلاجية أو الوقائية الآن بسهولة</p>
                </a>

                <!-- New Client -->
               <!-- <a href="{{ url('new-client') }}" class="nav-card" id="link-new-client">
                    <div class="nav-card-icon">👤</div>
                    <h3>عميل جديد</h3>
                    <p>قم بتسجيل بياناتك لأول مرة في المركز</p>
                    <div class="qr-desktop-only mt-3 qr-card">
                        <img src="{{ asset('images/qr-code.png') }}" alt="New Client QR">
                    </div>
                </a>-->

                <!-- Rate Us -->
                <a href="#" class="nav-card" id="link-rate-us">
                    <div class="nav-card-icon">⭐</div>
                    <h3>تقييم المركز</h3>
                    <p>شاركنا رأيك وتجربتك لمساعدتنا على التطور</p>
                    <div class="qr-desktop-only mt-3 qr-card">
                        <img src="{{ asset('images/rate-us-qr.png') }}" alt="Rate Us QR">
                    </div>
                </a>
            </div>

            <div class="payment-section-title">مقدم الحجز وطرق الدفع المقبولة</div>
            
            <div class="payment-grid">
                <!-- Instapay -->
                <div class="payment-card gap-3">
                    <img src="{{ asset('images/instapay.png') }}" alt="Instapay" style="height: 40px; object-fit: contain;">
                    <div class="qr-card">
                        <img src="{{ asset('images/instapay-qr.jpeg') }}" class="qr-image" style="width: 100%; max-width: 180px; height: auto;" alt="Instapay QR">
                    </div>
                    <div><span class="payment-badge">0128887676</span></div>
                </div>

                <!-- Vodafone Cash -->
                <div class="payment-card gap-3">
                    <img src="{{ asset('images/vodafone-cash.png') }}" alt="Vodafone Cash" style="height: 40px; object-fit: contain;">
                    <div class="qr-card">
                        <img src="{{ asset('images/vodafone-cash-qr.JPG') }}" class="qr-image" style="width: 100%; max-width: 180px; height: auto;" alt="Vodafone Cash QR">
                    </div>
                    <div><span class="payment-badge">01064344092</span></div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
