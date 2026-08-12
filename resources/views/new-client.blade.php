<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    
    <!-- Primary SEO Metadata -->
    <title>تسجيل عميل جديد | Shiatic New Client Intake</title>
    <meta name="description" content="استمارة قبول مريض جديد لمركز شياتك (Shiatic) لتقويم العمود الفقري (الكايروبراكتيك). يرجى ملء البيانات الشخصية وتفاصيل الإصابة للتسجيل.">
    <meta name="keywords" content="استمارة مريض جديد, شياتك, كيروبراكتيك مصر, عرق النسا, علاج الفقرات, مساج علاجي">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/new-client') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/new-client') }}">
    <meta property="og:title" content="تسجيل عميل جديد | Shiatic New Client Intake">
    <meta property="og:description" content="استمارة قبول مريض جديد لمركز شياتك (Shiatic) لتقويم العمود الفقري (الكايروبراكتيك).">
    <meta property="og:image" content="{{ asset('images/R.jpg') }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url('/new-client') }}">
    <meta name="twitter:title" content="تسجيل عميل جديد | Shiatic New Client Intake">
    <meta name="twitter:description" content="استمارة قبول مريض جديد لمركز شياتك (Shiatic) لتقويم العمود الفقري (الكايروبراكتيك).">
    <meta name="twitter:image" content="{{ asset('images/R.jpg') }}">

    <link rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">

</head>

<body class="bg-gray-100">

    <div class="max-w-2xl mx-auto mt-10 bg-white p-10 rounded-2xl shadow-lg">

        <h1 class="text-2xl font-semibold mb-6 text-gray-800" id="new-client-title">
            استمارة قبول مريض جديد
        </h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('public.client.store') }}">
            @csrf

            <!-- Helper: Form Field -->
            @php
                function field($label, $name, $type = "text") {
                    return '
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm mb-2 font-medium" for="input-'.$name.'">'.$label.' *</label>
                            <input type="'.$type.'" id="input-'.$name.'" name="'.$name.'" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg 
                                focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                    ';
                }
            @endphp

            {!! field("الاسم", "name") !!}
            {!! field("رقم الهاتف", "phone") !!}
            {!! field("السن", "age", "number") !!}
             <div class="mb-6">
                <label class="block text-gray-700 text-sm mb-2 font-medium" for="input-date_of_birth">تاريخ الميلاد *</label>
                <input 
                    type="date" 
                    id="input-date_of_birth"
                    name="date_of_birth" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm mb-2 font-medium" for="select-gender">الجنس*</label>
                <select name="gender" id="select-gender" required 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="" disabled selected>Select</option>
                    <option value="male">ذكر</option>
                    <option value="female">أنثى</option>
                </select>
            </div>
            {!! field("الوزن", "weight", "number") !!}
            {!! field("طبيعة العمل", "work") !!}
            {!! field("المحافظة (المدينة)", "governorate") !!}
            {!! field("وصف الاصابة", "injury") !!}
            <!-- {!! field("تشخبص الطبيب", "doctor_report") !!} -->
            <!-- {!! field("اسم الطبيب", "doctor_name") !!} -->
            <!-- {!! field("متى شعرت بالالم و الاصابة", "injury_first_date") !!} -->
            <!-- {!! field("اكثر وضع للجسم تشعر بالالم", "most_paineful_position") !!} -->
            <!-- {!! field("اكثر وضع للجسم مريح", "most_restful_position") !!} -->
            <!-- {!! field("تقدر تحضر كام سيشن بالاسبوع كحد اقصى", "num_sessions_available") !!} -->
            <!-- {!! field("أفضل مواعيد تقدر تلتزم فيها بالجلسات ", "best_dates_for_sessions") !!} -->
            {!! field("سمعت عننا ازاي ", "suggested_by") !!}

            <!-- Boolean Fields -->
           
           <!-- <div class="mb-6">
                <label class="block text-gray-700 text-sm mb-2 font-medium">سبب الاصابة</label>
                <select name="injury_reason" required 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="" disabled selected>Select</option>
                    <option value="مع الوقت">مع الوقت</option>
                    <option value="حركة مفاجئة">حركة مفاجئة</option>
                    <option value="حادث">حادث</option>
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm mb-2 font-medium">نوع الاشعة</label>
                <select name="scan_type" required 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="" disabled selected>Select</option>
                    <option value="مقطعية">مقطعية</option>
                    <option value="رنين">رنين</option>
                    <option value="اكس راي">اكس راي</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm mb-2 font-medium">يوجد تنميل او خدل او ألم بالاطراف (القدم او الذراع) ؟ *</label>
                <select name="numbness_in_limbs[]" multiple required 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 h-40">
                    <option value="" disabled selected>Select</option>
                    <option value="نعم بالقدم اليسرى">نعم بالقدم اليسرى</option>
                    <option value="نعم بالقدم اليمنى">نعم بالقدم اليمنى</option>
                    <option value="نعم بالقدمين معا">نعم بالقدمين معا</option>
                    <option value="نعم بالذراع الايسر">نعم بالذراع الايسر</option>
                    <option value="نعم بالذراع الايمن">نعم بالذراع الايمن</option>
                    <option value="نعم بالذراعين معا">نعم بالذراعين معا</option>
                    <option value="لا يوجد">لا يوجد</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Hold CTRL (or CMD on Mac) to select multiple.</p>
            </div>-->

            <div class="mb-6">
                <label class="block text-gray-700 text-sm mb-2 font-medium" for="select-is_previous_surgery">توجد اي عمليات سابقة ؟ (خاصة بالاصابة او غيرها) *</label>
                <select name="is_previous_surgery" id="select-is_previous_surgery" required 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="" disabled selected>Select</option>
                    <option value="نعم">نعم</option>
                    <option value="لا">لا</option>
                </select>
            </div> 
   
            <!-- Submit -->
            <button 
                type="submit" 
                id="btn-submit-client-form"
                class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                Submit
            </button>

        </form>

    </div>

</body>
</html>
