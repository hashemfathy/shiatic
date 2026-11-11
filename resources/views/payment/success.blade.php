<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم تأكيد الحجز والسداد بنجاح - Shiatic Clinic</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-navy: #0f172a;
            --accent-orange: #ff9d42;
            --accent-green: #10b981;
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

        .receipt-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.08), 0 0 0 1px rgba(15, 23, 42, 0.04);
            max-width: 650px;
            width: 100%;
            overflow: hidden;
            position: relative;
        }

        .receipt-header {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #ffffff;
            padding: 3rem 2rem 2.5rem;
            text-align: center;
            position: relative;
        }

        .check-circle {
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
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.3);
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            0% { transform: scale(0.6); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .receipt-body {
            padding: 2.5rem 2rem;
        }

        .txn-badge-box {
            background: #f1f5f9;
            border: 1px dashed #cbd5e1;
            border-radius: 14px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        .txn-label {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 600;
        }

        .txn-value {
            font-family: monospace;
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
            direction: ltr;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #64748b;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-val {
            font-weight: 700;
            color: #0f172a;
            font-size: 1rem;
        }

        .paid-amount-pill {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            padding: 0.35rem 0.9rem;
            border-radius: 50px;
            font-size: 1.15rem;
            font-weight: 800;
        }

        .btn-home {
            background: #0f172a;
            color: #ffffff;
            font-weight: 700;
            border-radius: 12px;
            padding: 0.85rem 2rem;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-home:hover {
            background: #1e293b;
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.15);
        }

        .btn-whatsapp {
            background: #25D366;
            color: #ffffff;
            font-weight: 700;
            border-radius: 12px;
            padding: 0.85rem 2rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-whatsapp:hover {
            background: #20bd5a;
            color: #ffffff;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="receipt-card">
        <div class="receipt-header">
            <div class="check-circle">
                <i class="bi bi-check-lg"></i>
            </div>
            <h2 class="fw-bold mb-2">تم تأكيد حجزك وسداد المقدم بنجاح!</h2>
            <p class="mb-0 text-white-50 fs-6">شكراً لك، تم استلام المبلغ وتأكيد الموعد رسمياً في جدول العيادة.</p>
        </div>

        <div class="receipt-body">
            <!-- Transaction ID Box -->
            <div class="txn-badge-box">
                <div>
                    <div class="txn-label">رقم العملية المرجعي (Transaction ID)</div>
                    <div class="txn-value" id="txnValue">{{ $transactionId }}</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="copyTxn()">
                    <i class="bi bi-clipboard me-1"></i> نسخ
                </button>
            </div>

            <!-- Booking details -->
            <div class="mb-4">
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-person"></i> الاسم:</span>
                    <span class="detail-val">{{ $booking->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-telephone"></i> رقم الهاتف:</span>
                    <span class="detail-val" dir="ltr">{{ $booking->phone }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-calendar-event"></i> تاريخ الموعد:</span>
                    <span class="detail-val">{{ $booking->date }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-clock"></i> وقت الموعد:</span>
                    <span class="detail-val">{{ $booking->time }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-activity"></i> نوع الحجز:</span>
                    <span class="detail-val">{{ $booking->booking_type }}</span>
                </div>
                @if($booking->children()->exists())
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-people"></i> عدد الأفراد في الحجز:</span>
                    <span class="detail-val">{{ $booking->children()->count() + 1 }} أفراد</span>
                </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-credit-card-2-front"></i> وسيلة الدفع:</span>
                    <span class="detail-val">كاشير (Kashier) أونلاين</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-cash-stack"></i> المبلغ المسدد (مقدم الحجز):</span>
                    <span class="paid-amount-pill">{{ number_format($amountPaid, 2) }} ج.م</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-shield-check"></i> حالة الحجز:</span>
                    <span class="badge bg-success fs-6 px-3 py-2">مؤكد ومسدد (Confirmed & Paid)</span>
                </div>
            </div>

            <div class="alert alert-light border rounded-3 p-3 text-muted small mb-4">
                <i class="bi bi-info-circle-fill text-primary me-1"></i>
                تم إرسال تفاصيل الحجز إلى نظام العيادة. يرجى الحضور قبل الموعد بـ 10 دقائق على الأقل. للاستفسار أو التعديل، يمكنك التواصل معنا في أي وقت.
            </div>

            <!-- Actions -->
            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                <a href="{{ url('/') }}" class="btn-home text-center">
                    <i class="bi bi-house me-1"></i> العودة للرئيسية
                </a>
                <a href="https://wa.me/201064344092?text={{ urlencode('مرحباً، قمت بسداد مقدم الحجز أونلاين برقم عملية: ' . $transactionId . ' للاسم: ' . $booking->name) }}" target="_blank" class="btn-whatsapp text-center justify-content-center">
                    <i class="bi bi-whatsapp"></i> تواصل معنا عبر واتساب
                </a>
            </div>
        </div>
    </div>

    <script>
        function copyTxn() {
            const text = document.getElementById('txnValue').innerText;
            navigator.clipboard.writeText(text).then(() => {
                alert('تم نسخ رقم العملية بنجاح: ' + text);
            });
        }
    </script>
</body>
</html>
