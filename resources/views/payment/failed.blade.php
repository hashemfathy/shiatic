<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعذر إتمام الدفع - Shiatic Clinic</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-navy: #0f172a;
            --accent-orange: #ff9d42;
            --bg-light: #f8fafc;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--bg-light);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .error-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.08), 0 0 0 1px rgba(15, 23, 42, 0.04);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
            text-align: center;
        }

        .error-header {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: #ffffff;
            padding: 3rem 2rem 2.5rem;
        }

        .error-icon {
            width: 84px;
            height: 84px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            border: 3px solid rgba(255, 255, 255, 0.85);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.8rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 10px 25px rgba(220, 38, 38, 0.3);
        }

        .error-body {
            padding: 2.5rem 2rem;
        }

        .btn-retry {
            background: linear-gradient(135deg, #ff9d42 0%, #f97316 100%);
            color: #ffffff;
            font-weight: 700;
            border-radius: 12px;
            padding: 0.85rem 2rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
        }

        .btn-retry:hover {
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(249, 115, 22, 0.4);
        }

        .btn-cancel {
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            border-radius: 12px;
            padding: 0.85rem 2rem;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s ease;
        }

        .btn-cancel:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
    </style>
</head>
<body>

    <div class="error-card">
        <div class="error-header">
            <div class="error-icon">
                <i class="bi bi-x-lg"></i>
            </div>
            <h2 class="fw-bold mb-2">تعذر استكمال عملية الدفع</h2>
            <p class="mb-0 text-white-50 fs-6">لم يتم خصم أي مبالغ، وتم إلغاء المعاملة أو عدم إتمامها.</p>
        </div>

        <div class="error-body">
            @if(!empty($errorMessage))
            <div class="alert alert-danger border-0 rounded-3 mb-4 text-start">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ $errorMessage }}
            </div>
            @endif

            @if(!empty($booking))
            <div class="card bg-light border-0 rounded-3 p-3 mb-4 text-start">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">الاسم:</span>
                    <span class="fw-bold">{{ $booking->name }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">موعد الحجز:</span>
                    <span class="fw-bold">{{ $booking->date }} في تمام {{ $booking->time }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">المبلغ المطلوب سداده (مقدم الحجز):</span>
                    <span class="fw-bold text-danger">{{ number_format($booking->deposit, 2) }} ج.م</span>
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                <a href="{{ route('payment.kashier.pay', ['id' => $booking->id]) }}" class="btn-retry justify-content-center">
                    <i class="bi bi-arrow-clockwise"></i> إعادة محاولة الدفع عبر كاشير
                </a>
                <a href="{{ route('booking.form') }}" class="btn-cancel">
                    تعديل بيانات الحجز
                </a>
            </div>
            @else
            <div class="mt-4">
                <a href="{{ route('booking.form') }}" class="btn-retry">
                    العودة لصفحة الحجز
                </a>
            </div>
            @endif
        </div>
    </div>

</body>
</html>
