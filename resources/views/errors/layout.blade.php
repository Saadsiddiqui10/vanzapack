<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') — {{ config('app.name', 'VanzaPack') }}</title>
    {{-- Static links only: error pages must render even when the database is down --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    @vite('resources/css/app.css')
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-50 p-6 text-slate-700">
    <div class="max-w-md text-center">
        <a href="{{ url('/') }}" class="mb-6 inline-flex items-center">
            <img src="{{ asset('images/logo.png') }}" alt="VanzaPack" class="h-14 w-auto">
        </a>
        <p class="font-display text-6xl font-extrabold text-brand-500">@yield('code')</p>
        <h1 class="mt-2 font-display text-xl font-bold text-brand-800">@yield('title')</h1>
        <p class="mt-2 text-sm text-slate-500">@yield('message')</p>
        <a href="{{ url('/') }}" class="btn-primary mt-6">Back to store</a>
    </div>
</body>
</html>
