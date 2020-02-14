<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Code95-cms</title>
    <!-- Favicon icon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link href="{{ asset('backend/css/backend-template.css')}}" media="all" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/file-upload-with-preview@4.0.2/dist/file-upload-with-preview.min.css">
    <link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href='//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700' rel='stylesheet'>
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css" /> --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('styles')
    <link href="{{ asset('backend/assets/css/style.css')}}" media="all" rel="stylesheet" type="text/css" />
    {!! SEO::generate(true) !!}
</head>

<body>
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->

    <!-- [ navigation menu ] start -->
    <nav class="pcoded-navbar navbar-dark">
        <div class="navbar-wrapper">
            <div class="navbar-brand header-logo">
                <a href="{{ route('admin.web.index') }}" class="b-brand">
                    <div class="b-bg">
                        <i class="feather icon-trending-up"></i>
                    </div>
                    <span class="b-title">Code95</span>
                </a>
                <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
            </div>
            <div class="navbar-content scroll-div" style="overflow-y: auto">
                @include('backend.pages.layouts.sidebar')
            </div>
        </div>
    </nav>
    <!-- [ navigation menu ] end -->

    <!-- [ Header ] start -->
    <header class="navbar pcoded-header navbar-expand-lg navbar-light">
        @include('backend.pages.layouts.quick-actions')
    </header>
    <!-- [ Header ] end -->
    <!-- [ Main Content ] start -->
    <div class="pcoded-main-container">
        @include('backend.pages.layouts.errors')
        <div id="app">
            @yield('content')
        </div>
    </div>

    @routes
    <!-- dashboard-custom js -->
    <script type="text/javascript" src="{{asset('js/backend-template.js')}}"></script>
    <script type="text/javascript" src="{{asset('backend/js/app.js')}}"></script>
    <script src="https://unpkg.com/file-upload-with-preview@4.0.2/dist/file-upload-with-preview.min.js"></script>
    <script type="text/javascript" src="{{asset('backend/assets/js/custom/custom.js')}}"></script>
    @yield('scripts')
</body>

</html>