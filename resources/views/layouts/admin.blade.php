<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Find the Word')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Space+Grotesk:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        :root {
            --bg-dark: #0f172a; --sidebar-bg: #1e293b;
            --primary-yellow: #facc15; --primary-pink: #ec4899; --primary-cyan: #06b6d4;
            --primary-purple: #8b5cf6; --primary-lime: #a3e635; --card-bg: #1e293b;
            --text-main: #f8fafc; --text-muted: #94a3b8;
            --shadow-brutal: 4px 4px 0px #000000; --shadow-brutal-hover: 6px 6px 0px #000000;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background-color: var(--bg-dark); color: var(--text-main); min-height: 100vh; display: flex; }
        h1, h2, h3, h4 { font-family: 'Space Grotesk', sans-serif; }

        .sidebar {
            width: 260px; background: var(--sidebar-bg); border-right: 3px solid #000;
            padding: 24px 20px; display: flex; flex-direction: column; justify-content: space-between;
            position: fixed; height: 100vh; z-index: 100;
        }
        .brand-logo {
            display: flex; align-items: center; gap: 12px; font-size: 1.3rem; font-weight: 800;
            color: #fff; text-decoration: none; padding-bottom: 20px; border-bottom: 2px dashed #334155;
        }
        .brand-icon {
            width: 42px; height: 42px; background: var(--primary-yellow); color: #000;
            border: 2px solid #000; border-radius: 12px; display: flex; align-items: center;
            justify-content: center; font-size: 1.2rem; box-shadow: 3px 3px 0px #000; overflow: hidden;
        }
        .brand-icon img { width: 100%; height: 100%; object-fit: cover; }
        .nav-menu { margin-top: 24px; display: flex; flex-direction: column; gap: 10px; }
        .nav-item {
            display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: var(--text-muted);
            text-decoration: none; font-weight: 700; border-radius: 12px; border: 2px solid transparent;
            transition: all 0.2s ease; cursor: pointer;
        }
        .nav-item:hover, .nav-item.active {
            background: var(--bg-dark); color: var(--primary-yellow); border-color: #000; box-shadow: var(--shadow-brutal);
        }
        .user-profile {
            display: flex; align-items: center; gap: 12px; padding: 12px; background: #0f172a;
            border: 2px solid #000; border-radius: 14px; box-shadow: 3px 3px 0px #000;
        }
        .avatar {
            width: 38px; height: 38px; background: var(--primary-pink); border: 2px solid #000;
            border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #fff;
            cursor: pointer; font-family: inherit; font-size: 1rem;
        }
        .logout-btn { background: none; border: none; color: var(--text-muted); cursor: pointer; font-weight: 700; font-size: 0.85rem; }
        .logout-btn:hover { color: var(--primary-pink); }

        .admin-menu { position: relative; flex-shrink: 0; }
        .admin-menu-dropdown {
            position: absolute; top: calc(100% + 10px); right: 0; background: var(--card-bg);
            border: 2px solid #000; border-radius: 14px; box-shadow: 4px 4px 0px #000;
            min-width: 190px; padding: 10px; z-index: 500;
        }
        .admin-menu-name {
            padding: 8px 12px; font-weight: 800; font-size: 0.82rem; border-bottom: 2px dashed #334155;
            margin-bottom: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .admin-menu-item {
            display: flex; align-items: center; gap: 10px; width: 100%; padding: 10px 12px; border-radius: 10px;
            color: var(--text-main); text-decoration: none; font-weight: 700; font-size: 0.85rem; cursor: pointer;
            background: none; border: none; font-family: inherit; text-align: left;
        }
        .admin-menu-item:hover { background: var(--bg-dark); color: var(--primary-yellow); }
        .admin-menu-item.danger:hover { color: var(--primary-pink); }

        .main-content { margin-left: 260px; flex: 1; padding: 30px 40px; }
        .top-bar {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px;
            background: var(--card-bg); border: 3px solid #000; border-radius: 20px; padding: 16px 24px;
            box-shadow: var(--shadow-brutal); flex-wrap: wrap; gap: 12px;
        }

        .btn {
            padding: 12px 22px; font-weight: 800; font-size: 0.9rem; border: 2px solid #000;
            border-radius: 12px; cursor: pointer; box-shadow: var(--shadow-brutal); transition: all 0.2s ease;
            display: inline-flex; align-items: center; gap: 8px; text-decoration: none;
        }
        .btn:hover { transform: translate(-2px, -2px); box-shadow: var(--shadow-brutal-hover); }
        .btn-sm { padding: 8px 14px; font-size: 0.8rem; }
        .btn-primary { background: var(--primary-lime); color: #000; }
        .btn-cyan { background: var(--primary-cyan); color: #000; }
        .btn-pink { background: var(--primary-pink); color: #fff; }
        .btn-purple { background: var(--primary-purple); color: #fff; }
        .btn-dark { background: #334155; color: #fff; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px; margin-bottom: 28px; }
        .stat-card { background: var(--card-bg); border: 3px solid #000; border-radius: 18px; padding: 18px; box-shadow: var(--shadow-brutal); display: flex; align-items: center; gap: 14px; }
        .stat-icon { width: 46px; height: 46px; border: 2px solid #000; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; box-shadow: 2px 2px 0px #000; color: #000; }
        .stat-info h4 { font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px; }
        .stat-info span { font-size: 1.4rem; font-weight: 800; }

        .panel-card { background: var(--card-bg); border: 3px solid #000; border-radius: 20px; padding: 24px; box-shadow: var(--shadow-brutal); margin-bottom: 24px; }
        .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }

        .quiz-table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        .quiz-table th { text-align: left; padding: 10px 16px; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; font-weight: 800; }
        .quiz-table tbody tr { background: #0f172a; }
        .quiz-table td { padding: 14px 16px; border-top: 2px solid #000; border-bottom: 2px solid #000; }
        .quiz-table td:first-child { border-left: 2px solid #000; border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
        .quiz-table td:last-child { border-right: 2px solid #000; border-top-right-radius: 12px; border-bottom-right-radius: 12px; text-align: right; }

        .badge { padding: 5px 12px; border-radius: 20px; border: 2px solid #000; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; box-shadow: 2px 2px 0px #000; display: inline-block; color: #000; }
        .badge-cyan { background: var(--primary-cyan); }
        .badge-yellow { background: var(--primary-yellow); }
        .badge-pink { background: var(--primary-pink); color: #fff; }
        .badge-lime { background: var(--primary-lime); }

        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 4px; }
        @media (max-width: 640px) { .form-grid { grid-template-columns: 1fr; } }
        .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 18px; }
        .form-group label { font-weight: 700; font-size: 0.9rem; }
        .form-group input, .form-group textarea, .form-group select {
            padding: 12px 16px; background: #0f172a; border: 2px solid #000; border-radius: 12px;
            color: #fff; font-size: 0.95rem; outline: none; box-shadow: 3px 3px 0px #000; font-family: inherit;
        }
        .form-group.checkbox-row { flex-direction: row; align-items: center; gap: 10px; }

        .word-bank-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px 20px; background: #0f172a; border: 2px solid #000; border-radius: 16px; padding: 18px; margin-top: 10px; }
        @media (max-width: 640px) { .word-bank-grid { grid-template-columns: 1fr; } }
        .word-item-row { display: flex; gap: 10px; align-items: center; padding: 8px 10px; background: #1e293b; border: 2px solid #000; border-radius: 10px; }
        .word-item-row input[type="text"] { flex: 1; padding: 8px 10px; background: transparent; border: none; color: #fff; font-size: 0.9rem; outline: none; box-shadow: none; }
        .word-item-row input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--primary-lime); cursor: pointer; }

        .alert-box { padding: 14px 18px; border-radius: 14px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600; border: 2px solid #000; }
        .alert-success { background: rgba(163, 230, 53, 0.15); color: var(--primary-lime); }
        .alert-error { background: rgba(236, 72, 153, 0.15); color: #fecdd3; }
        .alert-error ul { padding-left: 18px; }
        .alert-box code { background: rgba(0,0,0,0.35); padding: 2px 6px; border-radius: 6px; font-size: 0.85em; }

        img.thumb { width: 56px; height: 56px; object-fit: cover; border-radius: 10px; border: 2px solid #000; }

        .q-thumb {
            width: 84px; height: 64px; flex-shrink: 0; object-fit: cover; border-radius: 12px;
            border: 2px solid #000; box-shadow: 2px 2px 0px #000;
        }
        .q-thumb-empty {
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px;
            background: #1e293b; color: var(--text-muted); font-size: 0.62rem; font-weight: 700;
            text-align: center; padding: 4px;
        }
        .q-thumb-empty i { font-size: 1.1rem; }

        .modal-overlay {
            position: fixed; inset: 0; background: rgba(15, 23, 42, 0.85); z-index: 2000;
            display: flex; align-items: center; justify-content: center; padding: 20px;
        }
        .modal-box {
            background: var(--card-bg); border: 3px solid #000; border-radius: 20px; box-shadow: 8px 8px 0px #000;
            width: 100%; max-width: 560px; max-height: 90vh; overflow-y: auto; padding: 26px;
        }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
        .modal-close { background: none; border: none; color: var(--text-muted); font-size: 1.3rem; cursor: pointer; }
        .modal-close:hover { color: var(--primary-pink); }
        [x-cloak] { display: none !important; }

        .q-card {
            background: #0f172a; border: 2px solid #000; border-radius: 16px; padding: 18px;
            margin-bottom: 16px; box-shadow: 3px 3px 0px #000; display: flex; justify-content: space-between;
            align-items: center; gap: 16px; flex-wrap: wrap;
        }
        .stat-info h4 { margin: 0; }
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
