<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Travel Quest')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lilita+One&family=Nunito:wght@500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/travel-quest.css') }}">
</head>
<body>
    @include('travel-quest._sprite')

    <div class="wrap">
        <header class="top">
            <a href="{{ route('quiz.home') }}" class="brand" style="color: inherit; text-decoration: none;">
                <span class="logo" aria-hidden="true"><svg class="ico"><use href="#i-plane"/></svg></span>
                <span>Travel Quest</span>
            </a>
            <div class="top-right">
                <a href="{{ route('quiz.home') }}" class="btn btn-ghost btn-sm">
                    <svg class="ico" aria-hidden="true"><use href="#i-home"/></svg> Home
                </a>
                @if (auth('kelompok')->check())
                    <span class="team-pill">
                        <svg class="ico" aria-hidden="true"><use href="#i-pin"/></svg>
                        <span>{{ auth('kelompok')->user()->name }}</span>
                    </span>
                @endif
            </div>
        </header>

        @yield('route')

        <main>
            @yield('content')
        </main>
    </div>

    <div class="toast" id="toast" role="status" aria-live="polite"></div>

    <script>
        function tqToast(message) {
            const el = document.getElementById('toast');
            el.textContent = message;
            el.classList.add('on');
            clearTimeout(el._t);
            el._t = setTimeout(() => el.classList.remove('on'), 3000);
        }
    </script>
    @stack('scripts')
</body>
</html>
