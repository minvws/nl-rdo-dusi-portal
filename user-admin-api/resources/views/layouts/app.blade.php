<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex,nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @hasSection('page-title')
        <title>@yield('page-title') - {{ config('app.name', '') }}</title>
        @else
        <title>{{ config('app.name', '') }}</title>
        @endif
        <link rel="preload" href="{{ asset('images/logo/ro-logo.svg') }}" as="image">
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <link href="{{ asset('images/favicon/favicon.ico') }}" rel="shortcut icon">
        <script defer src="{{ asset('js/manon.min.js') }}"></script>
        <script defer src="{{ url('js/app.js') }}"></script>
    </head>
    <body>
{{--    TODO: Check the old-browser-error --}}
{{--        <x-old-browser-error />--}}
        <x-header>
            <x-navigation />
        </x-header>

        <main class="{{ ($withSidemenu ?? false) ? 'sidemenu' : ''}}" id="main-content" tabindex="-1">
            <x-flash element="p" />

            @yield('content')
        </main>
        <x-footer />
    </body>
</html>
