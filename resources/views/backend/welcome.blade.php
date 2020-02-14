<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- favicon -->
    <link rel="icon" href="{{ URL::asset('/images/shiatic-favicon.png') }}" type="image/x-icon" />

    <!-- Scripts -->
    <script src="{{ asset('js/backend/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    @yield('styles')
    <link href="{{ asset('css/backend/app.css') }}" rel="stylesheet">
    {!! SEO::generate(true) !!}
</head>

<body>
    <div id="app">
        @yield('content')
    </div>
    @yield('scripts')
</body>

</html>