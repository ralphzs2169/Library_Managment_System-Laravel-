<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' | SMART Library Web System' : 'SMART Library Web System' }}</title>

    <link rel="icon" href="{{ asset('build/assets/images/logo-no-bg.png') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('build/assets/images/logo-no-bg.png') }}">

    @vite('resources/css/app.css')
</head>
<body>

    @include('components/header')

    @auth
    @can('view-admin-sidebar')
    @include('components/admin-sidebar')
    @endcan
    @endauth


    <div class="bg-background">
        {{ $slot }}
    </div>


    @can('view-footer')
    @include('components/footer')
    @endcan
</body>
</html>
