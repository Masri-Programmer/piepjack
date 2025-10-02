<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Piepjack Clothing') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('img/favicon/site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('img/favicon/favicon.ico') }}">
    <meta name="msapplication-TileImage" content="{{ asset('img/favicon/android-chrome-192x192.png') }}">
    <meta name="msapplication-TileImage" content="{{ asset('img/favicon/android-chrome-512x512.png') }}">

    {{--
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="theme-color" content="#ffffff"> -->

    <meta name="description" content="@yield('description', 'High-quality, modern clothing for every occasion.')">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="robots" content="index, follow">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Piepjack Clothing')">
    <meta property="og:description" content="@yield('description', 'High-quality, modern clothing for every occasion.')">
    <meta property="og:image" content="{{ asset('img/social-preview.jpg') }}">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', 'Piepjack Clothing')">
    <meta property="twitter:description" content="@yield('description', 'High-quality, modern clothing for every occasion.')">
    <meta property="twitter:image" content="{{ asset('img/social-preview.jpg') }}">

    --}}

    @vite(['resources/js/app.js'])
</head>

<body>
    <div id="app"></div>
</body>

</html>