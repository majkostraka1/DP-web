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

    <header class="navbar navbar-expand">
        <nav class="navbar-collapse navbar-nav justify-content-end" id="navbarNavDropdown">
            <a
                    class="nav-link dropdown-toggle"
                    href="#"
                    id="navbarDropdownMenuLink"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
            >
                {{ app()->getLocale() }}
            </a>

            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                <li><a class="dropdown-item" href="{{ route('lang.switch', 'cz') }}">cz</a></li>
                <li><a class="dropdown-item" href="{{ route('lang.switch', 'sk') }}">sk</a></li>
                <li><a class="dropdown-item" href="{{ route('lang.switch', 'en') }}">en</a></li>
            </ul>
        </nav>
    </header>



@yield('content')

        @stack('scripts')

        @livewireScriptConfig
    </body>
</html>
