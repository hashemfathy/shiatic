<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إشعار بموعد جلسة جديد</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0b1329;
            color: #334155;
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: right;
        }
        .wrapper {
            width: 100%;
            background-color: #0b1329;
            padding: 30px 10px;
        }
        .container {
            max-width: 620px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #e27c1d 0%, #b45309 100%);
            padding: 30px 25px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0 0 8px 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 0;
            font-size: 14px;
            opacity: 0.92;
        }
        .content {
            padding: 28px 25px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 12px;
        }
        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #e27c1d;
            margin: 22px 0 10px 0;
            display: flex;
            align-items: center;
        }
        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 18px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 14px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #64748b;
            font-weight: 600;
        }
        .info-value {
            color: #0f172a;
            font-weight: 700;
        }
        .session-badge {
            display: inline-block;
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13.5px;
            margin: 4px 4px 4px 0;
        }
        .notes-box {
            background: #fffbeb;
            border-right: 4px solid #f59e0b;
            padding: 12px 14px;
            border-radius: 6px;
            font-size: 13.5px;
            line-height: 1.6;
            color: #78350f;
            white-space: pre-line;
            margin-top: 10px;
        }
        .btn-wrapper {
            text-align: center;
            margin-top: 25px;
        }
        .btn {
            display: inline-block;
            background: #e27c1d;
            color: #ffffff !important;
            padding: 12px 28px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 15px;
            box-shadow: 0 4px 12px rgba(226, 124, 29, 0.3);
        }
        .footer {
            background-color: #f1f5f9;
            padding: 18px;
            text-align: center;
            font-size: 12.5px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>🏥 مركز شياتيك - Shiatic</h1>
                <p>إشعار إسناد موعد جلسة جديد للمختص</p>
            </div>

            <div class="content">
                <div class="greeting">
                    مرحباً دكتور / {{ $employee->name }}،
                </div>
                <p style="font-size: 14.5px; line-height: 1.6; margin: 0 0 18px 0; color: #475569;">
                    تم قبول حجز جديد وإسناد جلسة إلى جدول مواعيدك. إليك تفاصيل الموعد والعميل:
                </p>

                <!-- تفاصيل الموعد والعميل -->
                <div class="section-title">📅 بيانات الموعد والعميل</div>
                <div class="info-card">
                    <div class="info-row">
                        <span class="info-label">اسم العميل:</span>
                        <span class="info-value">{{ $visit->client?->name ?? 'غير محدد' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">النوع:</span>
                        <span class="info-value">{{ ($visit->client?->gender == 'female') ? 'أنثى' : 'ذكر' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">تاريخ الجلسة:</span>
                        <span class="info-value" style="color: #e27c1d;">{{ $visit->date }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">وقت الموعد:</span>
                        <span class="info-value">
                            @php
                                $hourVal = $visit->hour;
                                $h = floor($hourVal);
                                $m = round(($hourVal - $h) * 60);
                                $amPm = ($h >= 12 && $h < 24) ? 'مساءً' : 'صباحاً';
                                $displayH = $h % 12;
                                if ($displayH == 0) $displayH = 12;
                                $timeFormatted = sprintf('%02d:%02d %s', $displayH, $m, $amPm);
                            @endphp
                            {{ $timeFormatted }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">نوع الزيارة:</span>
                        <span class="info-value">{{ $visit->type ?? 'وقائية' }}</span>
                    </div>
                </div>

                <!-- الجلسات المسندة -->
                <div class="section-title">💆‍♂️ الجلسات المسندة إليك</div>
                <div style="margin-bottom: 18px;">
                    @forelse($sessions as $sess)
                        <div class="session-badge">
                            ✨ {{ $sess['type'] ?? 'جلسة' }}
                        </div>
                    @empty
                        <div class="session-badge">
                            ✨ جلسة علاجية / استشارة
                        </div>
                    @endforelse
                </div>

                <!-- شكوى العميل وتفاصيل الحالة -->
                @if(!empty($visit->complaint))
                    <div class="section-title">📝 تفاصيل الشكوى والبروتوكول</div>
                    <div class="notes-box">
                        {{ $visit->complaint }}
                    </div>
                @endif

                <div class="btn-wrapper">
                    <a href="{{ config('app.url') }}/admin/visits/{{ $visit->id }}/edit" class="btn">عرض تفاصيل الزيارة في النظام</a>
                </div>
            </div>

            <div class="footer">
                <p style="margin: 0 0 5px 0;">مركز شياتيك - علاج آلام العمود الفقري والعظام والمفاصل</p>
                <p style="margin: 0;">مرسل عبر: <a href="mailto:hashem@shiatic.com" style="color: #64748b; text-decoration: none;">hashem@shiatic.com</a></p>
            </div>
        </div>
    </div>
</body>
</html>
