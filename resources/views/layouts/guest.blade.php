<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Feane') }}</title>
    @include('template.frontend.css')
    <style>
        .auth-shell { min-height: 100vh; display: grid; place-items: center; padding: 32px 16px; }
        .auth-shell > div { width: min(440px, 100%); }
        .auth-shell label { display: block; margin-top: 14px; }
        .auth-shell button { margin-top: 16px; }
        .auth-shell a { color: var(--ink); }
    </style>
</head>
<body>
    <main class="auth-shell"><div>{{ $slot }}</div></main>
</body>
</html>
