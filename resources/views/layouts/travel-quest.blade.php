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
    <style>
        /* Menu logout di pil nama tim (memakai token tema dari travel-quest.css). */
        .team-menu { position: relative; }
        .team-dropdown { position: absolute; right: 0; top: calc(100% + 8px); min-width: 170px; background: var(--surface); border: 2px solid var(--line); border-radius: 16px; box-shadow: var(--shadow); padding: 6px; z-index: 30; }
        .team-dropdown-item { display: flex; align-items: center; gap: 8px; width: 100%; padding: 10px 12px; border: 0; border-radius: 10px; background: none; color: var(--ink); font-weight: 900; cursor: pointer; text-align: left; }
        .team-dropdown-item:hover { background: var(--surface-2); color: var(--coral-ink); }
    </style>
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
                    <div class="team-menu" id="team-menu">
                        <button type="button" class="team-pill" id="team-menu-btn" aria-haspopup="true" aria-expanded="false" style="cursor: pointer; font: inherit; color: inherit;">
                            <svg class="ico" aria-hidden="true"><use href="#i-pin"/></svg>
                            <span>{{ auth('kelompok')->user()->name }}</span>
                            <svg class="ico" aria-hidden="true" style="width: 14px; height: 14px; transform: rotate(90deg);"><use href="#i-arrow"/></svg>
                        </button>
                        <div class="team-dropdown" id="team-dropdown" hidden>
                            <form method="POST" action="{{ route('user.logout') }}">
                                @csrf
                                <input type="hidden" name="return_to" value="{{ request()->getRequestUri() }}">
                                <button type="submit" class="team-dropdown-item">
                                    <svg class="ico" aria-hidden="true"><use href="#i-lock"/></svg> Log out
                                </button>
                            </form>
                        </div>
                    </div>
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

        (function () {
            const btn = document.getElementById('team-menu-btn');
            const menu = document.getElementById('team-dropdown');
            if (!btn || !menu) return;
            const close = () => { menu.hidden = true; btn.setAttribute('aria-expanded', 'false'); };
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                menu.hidden = !menu.hidden;
                btn.setAttribute('aria-expanded', menu.hidden ? 'false' : 'true');
            });
            document.addEventListener('click', (e) => { if (!menu.contains(e.target)) close(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
        })();
    </script>
    @stack('scripts')
</body>
</html>
