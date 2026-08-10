<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلب حجز جديد</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            color: #333333;
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: right;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #e1e8ed;
        }
        .header {
            background-color: #e27c1d;
            padding: 25px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            padding: 30px;
        }
        .intro-text {
            font-size: 16px;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table th, .details-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 15px;
        }
        .details-table th {
            width: 35%;
            background-color: #fafafa;
            color: #555555;
            text-align: right;
            font-weight: 600;
        }
        .details-table td {
            color: #111111;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            background-color: #f1f1f1;
            color: #555555;
        }
        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .btn-wrapper {
            text-align: center;
            margin-top: 30px;
        }
        .btn {
            display: inline-block;
            background-color: #e27c1d;
            color: #ffffff !important;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(226, 124, 29, 0.2);
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #ca6b15;
        }
        .footer {
            background-color: #fafafa;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #888888;
            border-top: 1px solid #eeeeee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>طلب حجز جديد</h1>
        </div>
        <div class="content">
            @php
                $hasChildren = $bookingRequest->children && $bookingRequest->children->count() > 0;
                $totalGroupPrice = $bookingRequest->total_price;
                $totalGroupDeposit = $bookingRequest->deposit;
                if ($hasChildren) {
                    $totalGroupPrice += $bookingRequest->children->sum('total_price');
                    $totalGroupDeposit += $bookingRequest->children->sum('deposit');
                }
            @endphp

            @if($hasChildren)
                <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 15px; border-radius: 8px; margin-bottom: 25px; direction: rtl; text-align: right;">
                    <p style="margin: 0; font-weight: bold; color: #b45309; font-size: 16px;">🔥 طلب حجز جماعي جديد (عدد الأفراد: {{ $bookingRequest->children->count() + 1 }})</p>
                    <p style="margin: 5px 0 0 0; color: #78350f; font-size: 14px;">
                        السعر الإجمالي للمجموعة: <strong>{{ $totalGroupPrice }} جنيه</strong> |
                        المقدم الإجمالي المطلوب (40%): <strong>{{ $totalGroupDeposit }} جنيه</strong>
                    </p>
                </div>
                <p class="intro-text">مرحباً، لقد تم استلام طلب حجز جماعي جديد عبر الموقع الإلكتروني. إليك تفاصيل المجموعة:</p>
            @else
                <p class="intro-text">مرحباً، لقد تم استلام طلب حجز جديد عبر الموقع الإلكتروني. إليك تفاصيل الطلب:</p>
            @endif
            
            <h3 style="font-size: 18px; border-bottom: 2px solid #e27c1d; padding-bottom: 5px; margin-top: 10px; color: #e27c1d;">
                {{ $hasChildren ? 'الشخص 1 (الأساسي)' : 'تفاصيل الجلسة' }}
            </h3>
            <table class="details-table">
                <tr>
                    <th>الاسم</th>
                    <td>{{ $bookingRequest->name }}</td>
                </tr>
                <tr>
                    <th>رقم الجوال</th>
                    <td><a href="tel:{{ $bookingRequest->phone }}">{{ $bookingRequest->phone }}</a></td>
                </tr>
                <tr>
                    <th>النوع (الجنس)</th>
                    <td>{{ $bookingRequest->gender == 'male' ? 'ذكر' : 'أنثى' }}</td>
                </tr>
                <tr>
                    <th>نوع الحجز</th>
                    <td>
                        <span class="badge badge-info">{{ $bookingRequest->booking_type }}</span>
                    </td>
                </tr>
                <tr>
                    <th>نوع الخدمة</th>
                    <td>{{ $bookingRequest->service_type ?: 'غير محدد' }}</td>
                </tr>
                @if($bookingRequest->packages && is_array($bookingRequest->packages))
                    <tr>
                        <th>الباقة</th>
                        <td>{{ implode(', ', array_map(fn($p) => $p === 'intensive' ? 'مكثف (Intensive)' : 'اقتصادي (Economy)', $bookingRequest->packages)) }}</td>
                    </tr>
                @endif
                <tr>
                    <th>التاريخ والوقت</th>
                    <td>{{ $bookingRequest->date }} - {{ $bookingRequest->time ?: 'غير محدد' }}</td>
                </tr>
                <tr>
                    <th>المبلغ</th>
                    <td style="font-weight: bold; color: #1e40af;">{{ $bookingRequest->total_price }} جنيه</td>
                </tr>
                @if($bookingRequest->description)
                    <tr>
                        <th>التفاصيل الإضافية</th>
                        <td style="font-size: 14px; color: #555; white-space: pre-line;">{{ $bookingRequest->description }}</td>
                    </tr>
                @endif
            </table>

            @if($hasChildren)
                @foreach($bookingRequest->children as $index => $child)
                    <h3 style="font-size: 18px; border-bottom: 2px solid #e27c1d; padding-bottom: 5px; margin-top: 25px; color: #e27c1d;">الشخص {{ $index + 2 }}</h3>
                    <table class="details-table">
                        <tr>
                            <th>الاسم</th>
                            <td>{{ $child->name }}</td>
                        </tr>
                        <tr>
                            <th>رقم الجوال</th>
                            <td><a href="tel:{{ $child->phone }}">{{ $child->phone }}</a></td>
                        </tr>
                        <tr>
                            <th>النوع (الجنس)</th>
                            <td>{{ $child->gender == 'male' ? 'ذكر' : 'أنثى' }}</td>
                        </tr>
                        <tr>
                            <th>نوع الحجز</th>
                            <td>
                                <span class="badge badge-info">{{ $child->booking_type }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>نوع الخدمة</th>
                            <td>{{ $child->service_type ?: 'غير محدد' }}</td>
                        </tr>
                        @if($child->packages && is_array($child->packages))
                            <tr>
                                <th>الباقة</th>
                                <td>{{ implode(', ', array_map(fn($p) => $p === 'intensive' ? 'مكثف (Intensive)' : 'اقتصادي (Economy)', $child->packages)) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>المبلغ</th>
                            <td style="font-weight: bold; color: #1e40af;">{{ $child->total_price }} جنيه</td>
                        </tr>
                        @if($child->description)
                            <tr>
                                <th>التفاصيل الإضافية</th>
                                <td style="font-size: 14px; color: #555; white-space: pre-line;">{{ $child->description }}</td>
                            </tr>
                        @endif
                    </table>
                @endforeach
            @endif

            <div class="btn-wrapper">
                <a href="{{ config('app.url') }}/admin/requests/{{ $bookingRequest->id }}/edit" class="btn">عرض الطلب في لوحة التحكم</a>
            </div>
        </div>
        <div class="footer">
            <p>هذا البريد مرسل تلقائياً من نظام حجز {{ config('app.name') }}.</p>
        </div>
    </div>
</body>
</html>
