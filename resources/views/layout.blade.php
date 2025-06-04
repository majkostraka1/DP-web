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
        <div class="container-fluid">
            <nav class="navbar-collapse navbar-nav justify-content-between">
                <button id="openMenu" class="btn">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div class="d-flex align-items-center">
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
                </div>
            </nav>


            <div id="menu" class="overlay">
                <button id="closeMenu" class="close-btn">×</button>
                <ul class="overlay-links">
                    <li><a href="{{ route('home') }}">Meranie dát</a></li>
                    <li><a href="{{ route('prediction') }}">Predikcia aktivity</a></li>


{{--                    <li><a href="{{ route('lstm') }}">LSTM predikcia</a></li>--}}
{{--                    <li><a href="{{ route('gru') }}">GRU predikcia</a></li>--}}
                </ul>
            </div>
        </div>
    </header>




    @yield('content')

        <script>
            const openMenuBtn = document.getElementById('openMenu');
            const closeMenuBtn = document.getElementById('closeMenu');
            const menu = document.getElementById('menu');

            openMenuBtn.addEventListener('click', () => {
                menu.classList.add('show');
            });

            closeMenuBtn.addEventListener('click', () => {
                menu.classList.remove('show');
            });
        </script>

        @stack('scripts')

        @livewireScriptConfig
    </body>
</html>
