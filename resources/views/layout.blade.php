<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>@yield('title')</title>

        @csrf

        @livewireStyles
        @vite(['resources/js/app.js', 'resources/scss/app.scss'])
        @stack('styles')
    </head>

    <body>
        @yield('content')

        @stack('scripts')

        @livewireScriptConfig
    </body>
</html>
