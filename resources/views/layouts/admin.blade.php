<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Find the Word')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lilita+One&family=Nunito:wght@500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        /* Tema: terang (default) & gelap otomatis mengikuti perangkat — palet sama dengan halaman peserta (Travel Quest). */
        :root {
            --font-display: 'Lilita One', 'Trebuchet MS', system-ui, sans-serif;
            --font-body: 'Nunito', system-ui, -apple-system, 'Segoe UI', sans-serif;

            --bg: #D6F0F3; --bg-2: #BDE5EB; --surface: #FFFFFF; --surface-2: #EBF8FA;
            --ink: #0B2F3B; --ink-soft: #3F6471; --line: #A9D6DD;
            --ocean: #0E7C86; --ocean-deep: #0A5560; --brand-ink: #0A5560;
            --sun: #FFB81F; --sun-deep: #D18A06; --on-sun: #3A2600;
            --palm: #15935F; --palm-deep: #0B6B45; --palm-bg: #DDF5E8;
            --coral: #CC3A40; --coral-bg: #FFE4E2;
            --pink: #D6336C; --pink-deep: #A61E4D; --purple: #7048E8; --purple-deep: #5028C6;
            --on-accent: #FFFFFF; --hl: #FFF0C4;
            --shadow: 0 14px 30px -16px rgba(10, 70, 85, .45);

            /* alias lama supaya style inline di halaman admin tetap jalan */
            --bg-dark: var(--bg); --sidebar-bg: var(--surface); --card-bg: var(--surface);
            --text-main: var(--ink); --text-muted: var(--ink-soft);
            --primary-yellow: var(--sun); --primary-pink: var(--pink); --primary-cyan: var(--ocean);
            --primary-purple: var(--purple); --primary-lime: var(--palm);
            color-scheme: light;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #07212A; --bg-2: #0C2F3A; --surface: #0E3340; --surface-2: #124452;
                --ink: #E7F7F9; --ink-soft: #9FC6CF; --line: #245462;
                --ocean: #25AEBB; --ocean-deep: #117985; --brand-ink: #7BDCE6;
                --sun: #FFC24A; --sun-deep: #C98F1B; --on-sun: #3A2600;
                --palm: #4FD79A; --palm-deep: #2FA673; --palm-bg: #0F3E2E;
                --coral: #FF8A8E; --coral-bg: #4A2127;
                --pink: #FF7AA8; --pink-deep: #D6528A; --purple: #A78BFA; --purple-deep: #7C5CE0;
                --on-accent: #04242B; --hl: #4A3B12;
                --shadow: 0 14px 30px -16px rgba(0, 0, 0, .8);
                color-scheme: dark;
            }
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: var(--font-body); }
        body { background-color: var(--bg); color: var(--ink); min-height: 100vh; display: flex; font-weight: 500; }
        h1, h2, h3, h4 { font-family: var(--font-display); font-weight: 400; }

        .sidebar {
            width: 260px; background: var(--surface); border-right: 2px solid var(--line);
            padding: 24px 20px; display: flex; flex-direction: column; justify-content: space-between;
            position: fixed; height: 100vh; z-index: 100;
        }
        .brand-logo {
            display: flex; align-items: center; gap: 12px; font-size: 1.4rem; font-family: var(--font-display);
            color: var(--ink); text-decoration: none; padding-bottom: 20px; border-bottom: 2px dashed var(--line);
        }
        .brand-icon {
            width: 42px; height: 42px; background: var(--sun); color: var(--on-sun);
            border-radius: 13px; display: flex; align-items: center;
            justify-content: center; font-size: 1.2rem; box-shadow: 0 3px 0 var(--sun-deep); overflow: hidden;
            transform: rotate(-6deg);
        }
        .brand-icon img { width: 100%; height: 100%; object-fit: cover; }
        .nav-menu { margin-top: 24px; display: flex; flex-direction: column; gap: 8px; }
        .nav-item {
            display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: var(--ink-soft);
            text-decoration: none; font-weight: 800; border-radius: 14px; border: 2px solid transparent;
            transition: all 0.15s ease; cursor: pointer;
        }
        .nav-item:hover { background: var(--surface-2); color: var(--ink); }
        .nav-item.active { background: var(--ocean); color: var(--on-accent); border-color: var(--ocean-deep); box-shadow: 0 3px 0 var(--ocean-deep); }
        .user-profile {
            display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--surface-2);
            border: 2px solid var(--line); border-radius: 14px;
        }
        .avatar {
            width: 40px; height: 40px; background: var(--sun); border: 0; color: var(--on-sun);
            border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800;
            cursor: pointer; font-family: inherit; font-size: 1rem; box-shadow: 0 3px 0 var(--sun-deep);
        }
        .logout-btn { background: none; border: none; color: var(--ink-soft); cursor: pointer; font-weight: 800; font-size: 0.85rem; }
        .logout-btn:hover { color: var(--pink); }

        .admin-menu { position: relative; flex-shrink: 0; }
        .admin-menu-dropdown {
            position: absolute; top: calc(100% + 10px); right: 0; background: var(--surface);
            border: 2px solid var(--line); border-radius: 16px; box-shadow: var(--shadow);
            min-width: 190px; padding: 10px; z-index: 500;
        }
        .admin-menu-name {
            padding: 8px 12px; font-weight: 900; font-size: 0.82rem; border-bottom: 2px dashed var(--line);
            margin-bottom: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .admin-menu-item {
            display: flex; align-items: center; gap: 10px; width: 100%; padding: 10px 12px; border-radius: 10px;
            color: var(--ink); text-decoration: none; font-weight: 800; font-size: 0.85rem; cursor: pointer;
            background: none; border: none; font-family: inherit; text-align: left;
        }
        .admin-menu-item:hover { background: var(--surface-2); color: var(--ocean); }
        .admin-menu-item.danger:hover { color: var(--pink); }

        .main-content { margin-left: 260px; flex: 1; padding: 30px 40px; min-width: 0; }
        .top-bar {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px;
            background: var(--surface); border: 2px solid var(--line); border-radius: 22px; padding: 16px 24px;
            box-shadow: var(--shadow); flex-wrap: wrap; gap: 12px;
        }

        .btn {
            --sh: var(--ocean-deep);
            padding: 12px 22px; font-weight: 900; font-size: 0.9rem; border: 0;
            border-radius: 14px; cursor: pointer; box-shadow: 0 4px 0 var(--sh); transition: transform .1s, box-shadow .1s;
            display: inline-flex; align-items: center; gap: 8px; text-decoration: none;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn:active { transform: translateY(3px); box-shadow: 0 1px 0 var(--sh); }
        .btn-sm { padding: 8px 14px; font-size: 0.8rem; border-radius: 12px; }
        .btn-primary { --sh: var(--palm-deep); background: var(--palm); color: var(--on-accent); }
        .btn-cyan { --sh: var(--ocean-deep); background: var(--ocean); color: var(--on-accent); }
        .btn-pink { --sh: var(--pink-deep); background: var(--pink); color: var(--on-accent); }
        .btn-purple { --sh: var(--purple-deep); background: var(--purple); color: var(--on-accent); }
        .btn-sun { --sh: var(--sun-deep); background: var(--sun); color: var(--on-sun); }
        .btn-dark { background: var(--surface); color: var(--ink); border: 2px solid var(--line); box-shadow: none; }
        .btn-dark:active { box-shadow: none; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px; margin-bottom: 28px; }
        .stat-card { background: var(--surface); border: 2px solid var(--line); border-radius: 20px; padding: 18px; box-shadow: var(--shadow); display: flex; align-items: center; gap: 14px; }
        .stat-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: var(--on-accent); }
        .stat-info h4 { font-size: 0.78rem; color: var(--ink-soft); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 4px; font-family: var(--font-body); font-weight: 900; }
        .stat-info span { font-size: 1.7rem; font-family: var(--font-display); }

        .panel-card { background: var(--surface); border: 2px solid var(--line); border-radius: 22px; padding: 24px; box-shadow: var(--shadow); margin-bottom: 24px; }
        .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }

        .quiz-table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        .quiz-table th { text-align: left; padding: 10px 16px; color: var(--ink-soft); font-size: 0.75rem; text-transform: uppercase; letter-spacing: .08em; font-weight: 900; }
        .quiz-table tbody tr { background: var(--surface-2); }
        .quiz-table td { padding: 14px 16px; border-top: 2px solid var(--line); border-bottom: 2px solid var(--line); }
        .quiz-table td:first-child { border-left: 2px solid var(--line); border-top-left-radius: 14px; border-bottom-left-radius: 14px; }
        .quiz-table td:last-child { border-right: 2px solid var(--line); border-top-right-radius: 14px; border-bottom-right-radius: 14px; text-align: right; }

        .badge { padding: 4px 12px; border-radius: 999px; font-size: 0.72rem; font-weight: 900; letter-spacing: .04em; text-transform: uppercase; display: inline-block; color: var(--on-accent); }
        .badge-cyan { background: var(--ocean); }
        .badge-yellow { background: var(--sun); color: var(--on-sun); }
        .badge-pink { background: var(--pink); }
        .badge-lime { background: var(--palm); }

        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 4px; }
        @media (max-width: 640px) { .form-grid { grid-template-columns: 1fr; } }
        .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 18px; }
        .form-group label { font-weight: 800; font-size: 0.9rem; }
        .form-group input, .form-group textarea, .form-group select, .filter-select {
            padding: 12px 16px; background: var(--surface-2); border: 2px solid var(--line); border-radius: 14px;
            color: var(--ink); font-size: 0.95rem; font-weight: 700; outline: none; font-family: inherit;
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus, .filter-select:focus { border-color: var(--ocean); outline: 3px solid var(--ocean); outline-offset: 1px; }
        .filter-select { padding: 10px 16px; border-radius: 12px; }
        .form-group.checkbox-row { flex-direction: row; align-items: center; gap: 10px; }

        .word-bank-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px 20px; background: var(--surface-2); border: 2px solid var(--line); border-radius: 18px; padding: 18px; margin-top: 10px; }
        @media (max-width: 640px) { .word-bank-grid { grid-template-columns: 1fr; } }
        .word-item-row { display: flex; gap: 10px; align-items: center; padding: 8px 10px; background: var(--surface); border: 2px solid var(--line); border-radius: 12px; }
        .word-item-row input[type="text"] { flex: 1; padding: 8px 10px; background: transparent; border: none; color: var(--ink); font-size: 0.9rem; outline: none; box-shadow: none; }
        .word-item-row input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--palm); cursor: pointer; }

        .alert-box { padding: 14px 18px; border-radius: 14px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 800; border: 2px solid var(--line); }
        .alert-success { background: var(--palm-bg); color: var(--palm-deep); border-color: var(--palm); }
        .alert-error { background: var(--coral-bg); color: var(--coral); border-color: var(--coral); }
        .alert-error ul { padding-left: 18px; }
        .alert-box code { background: rgba(0,0,0,0.12); padding: 2px 6px; border-radius: 6px; font-size: 0.85em; }

        img.thumb { width: 56px; height: 56px; object-fit: cover; border-radius: 12px; border: 2px solid var(--line); }

        .q-thumb {
            width: 84px; height: 64px; flex-shrink: 0; object-fit: cover; border-radius: 12px;
            border: 2px solid var(--line);
        }
        .q-thumb-empty {
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px;
            background: var(--surface-2); color: var(--ink-soft); font-size: 0.62rem; font-weight: 800;
            text-align: center; padding: 4px;
        }
        .q-thumb-empty i { font-size: 1.1rem; }

        .modal-overlay {
            position: fixed; inset: 0; background: rgba(7, 33, 42, 0.65); z-index: 2000;
            display: flex; align-items: center; justify-content: center; padding: 20px;
        }
        .modal-box {
            background: var(--surface); border: 2px solid var(--line); border-radius: 22px; box-shadow: var(--shadow);
            width: 100%; max-width: 560px; max-height: 90vh; overflow-y: auto; padding: 26px;
        }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
        .modal-close { background: none; border: none; color: var(--ink-soft); font-size: 1.3rem; cursor: pointer; }
        .modal-close:hover { color: var(--pink); }
        [x-cloak] { display: none !important; }

        .q-card {
            background: var(--surface-2); border: 2px solid var(--line); border-radius: 18px; padding: 18px;
            margin-bottom: 16px; display: flex; justify-content: space-between;
            align-items: center; gap: 16px; flex-wrap: wrap;
        }
        .stat-info h4 { margin: 0; }
        :focus-visible { outline: 3px solid var(--ocean); outline-offset: 2px; }
        @media (max-width: 900px) {
            .sidebar { position: static; width: 100%; height: auto; border-right: 0; border-bottom: 2px solid var(--line); }
            body { flex-direction: column; }
            .main-content { margin-left: 0; padding: 20px 16px; }
        }
    </style>
</head>
<body>
    @auth
    <div class="sidebar">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="brand-logo">
                <div class="brand-icon"><img src="{{ asset('images/logo-kuis.png') }}" alt="Logo Find the Word"></div>
                <span>Find the Word</span>
            </a>
            <div class="nav-menu">
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <a href="{{ route('admin.quizzes.index') }}" class="nav-item {{ request()->routeIs('admin.quizzes.index') || request()->routeIs('admin.soal.*') || request()->routeIs('admin.images.*') || (request()->routeIs('admin.quizzes.edit') && optional(request()->route('quiz'))->is_kelompok) ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group"></i> Daftar Kuis Kelompok
                </a>
                <a href="{{ route('admin.quizzes.individu') }}" class="nav-item {{ request()->routeIs('admin.quizzes.individu') || request()->routeIs('admin.soal-individu.*') || (request()->routeIs('admin.quizzes.edit') && ! optional(request()->route('quiz'))->is_kelompok) ? 'active' : '' }}">
                    <i class="fa-solid fa-user-pen"></i> Daftar Kuis Individu
                </a>
                <a href="{{ route('admin.leaderboard') }}" class="nav-item {{ request()->routeIs('admin.leaderboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-trophy"></i> Leaderboard
                </a>
                <a href="{{ route('admin.kelas.index') }}" class="nav-item {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chalkboard-user"></i> Master Kelas
                </a>
                <a href="{{ route('admin.siswa.index') }}" class="nav-item {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-graduate"></i> Daftar User
                </a>
                <a href="{{ route('admin.kelompok.index') }}" class="nav-item {{ request()->routeIs('admin.kelompok.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-people-group"></i> Daftar Kelompok
                </a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h1 style="font-size:1.4rem;">@yield('page-title')</h1>
            <div style="display:flex; align-items:center; gap:14px;">
                <div>@yield('top-actions')</div>
                <div class="admin-menu" x-data="{ open: false }">
                    <button type="button" class="avatar" x-on:click="open = !open" title="{{ auth()->user()->name }}">
                        <i class="fa-solid fa-user-shield"></i>
                    </button>
                    <div class="admin-menu-dropdown" x-show="open" x-on:click.outside="open = false" x-cloak>
                        <div class="admin-menu-name">{{ auth()->user()->name }}</div>
                        <a href="{{ route('admin.profile.edit') }}" class="admin-menu-item">
                            <i class="fa-solid fa-user-pen"></i> Profil
                        </a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="admin-menu-item danger">
                                <i class="fa-solid fa-right-from-bracket"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if (! is_link(public_path('storage')))
            <div class="alert-box alert-error">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Folder <code>public/storage</code> belum ter-link ke <code>storage/app/public</code> &mdash; gambar yang diupload akan tersimpan tapi tidak muncul.
                Jalankan <code>php artisan storage:link</code> di terminal project, lalu refresh halaman ini.
            </div>
        @endif

        @if (session('status'))
            <div class="alert-box alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert-box alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
    @else
        @yield('content')
    @endauth

    @stack('scripts')
</body>
</html>
