<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Find the Word - Kuis Kosakata Wisata')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Space+Grotesk:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        :root {
            --bg-page: #f5f3fc;
            --primary-yellow: #facc15;
            --primary-pink: #ec4899;
            --primary-cyan: #06b6d4;
            --primary-purple: #8b5cf6;
            --primary-lime: #a3e635;
            --ink-yellow: #b45309;
            --ink-pink: #be185d;
            --ink-cyan: #0e7490;
            --ink-purple: #6d28d9;
            --ink-lime: #4d7c0f;
            --card-bg: #ffffff;
            --surface-soft: #f1edfb;
            --surface-soft-hover: #e6ddf9;
            --text-main: #211c3d;
            --text-muted: #675f8c;
            --shadow-brutal: 5px 5px 0px #000000;
            --shadow-brutal-hover: 8px 8px 0px #000000;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; scroll-behavior: smooth; }

        body {
            background-color: var(--bg-page);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
            background-image:
                radial-gradient(at 0% 0%, rgba(236, 72, 153, 0.22) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.22) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(6, 182, 212, 0.22) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(250, 204, 21, 0.25) 0px, transparent 50%);
            background-attachment: fixed;
        }

        h1, h2, h3, .brand-font { font-family: 'Space Grotesk', sans-serif; }

        /* ===== Navbar ===== */
        .navbar {
            position: sticky; top: 20px; z-index: 1000; width: 90%; max-width: 1100px; margin: 20px auto 0;
            background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border: 3px solid #000;
            box-shadow: var(--shadow-brutal); border-radius: 20px; padding: 12px 24px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .logo { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text-main); font-weight: 800; font-size: 1.15rem; }
        .logo-icon {
            width: 38px; height: 38px; background: var(--primary-yellow); border: 2px solid #000;
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
            color: #000; box-shadow: 2px 2px 0px #000; overflow: hidden;
        }
        .logo-icon img { width: 100%; height: 100%; object-fit: cover; }
        .nav-links { display: flex; gap: 8px; list-style: none; align-items: center; }
        .nav-links a {
            text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 0.9rem;
            padding: 8px 14px; border-radius: 12px; transition: all 0.2s ease;
        }
        .nav-links a:hover { background: var(--primary-purple); color: #fff; transform: translateY(-2px); }
        .btn-nav-login { background: var(--primary-yellow) !important; color: #000 !important; border: 2px solid #000; box-shadow: 3px 3px 0px #000; }
        .btn-nav-login:hover { transform: translate(-2px, -2px) !important; box-shadow: 5px 5px 0px #000 !important; background: var(--primary-lime) !important; }
        .group-badge {
            background: var(--primary-cyan); color: #000; padding: 7px 14px; border-radius: 50px;
            font-weight: 800; border: 2px solid #000; box-shadow: 2px 2px 0px #000;
            display: flex; align-items: center; gap: 6px; font-size: 0.82rem;
        }
        .menu-toggle { display: none; font-size: 1.4rem; cursor: pointer; color: var(--text-main); background: none; border: none; }

        @media (max-width: 768px) {
            .navbar { padding: 12px 18px; }
            .menu-toggle { display: block; }
            .nav-links {
                position: absolute; top: 64px; left: 0; width: 100%; background: var(--card-bg);
                border: 3px solid #000; box-shadow: var(--shadow-brutal); border-radius: 16px;
                flex-direction: column; align-items: stretch; padding: 14px; display: none; gap: 6px;
            }
            .nav-links.active { display: flex; }
        }

        /* ===== Generic form-flow container (registrasi / soal / hasil) ===== */
        .container { width: 90%; max-width: 900px; margin: 30px auto; }
        .quiz-box { background: var(--card-bg); border: 3px solid #000; box-shadow: 8px 8px 0px #000; border-radius: 24px; padding: 32px; position: relative; }

        .welcome-badge {
            display: inline-block; background: var(--primary-cyan); color: #000; font-weight: 800;
            padding: 6px 14px; border-radius: 20px; border: 2px solid #000; box-shadow: 2px 2px 0px #000;
            margin-bottom: 16px; font-size: 0.85rem; text-transform: uppercase;
        }
        .quiz-box h2 { font-size: 2.1rem; margin-bottom: 12px; }

        .narration-box {
            background: var(--surface-soft); border: 2px solid #000; border-left: 6px solid var(--primary-yellow);
            padding: 16px 20px; border-radius: 14px; margin: 20px 0 28px; font-style: italic;
            color: var(--text-main); line-height: 1.6; font-size: 0.95rem;
        }

        .form-group { margin-bottom: 24px; }
        .form-group label { display: block; font-weight: 700; margin-bottom: 8px; font-size: 1rem; }
        .form-group input, .form-group select {
            width: 100%; padding: 14px 18px; background: var(--surface-soft); border: 2px solid #000;
            border-radius: 14px; color: var(--text-main); font-size: 1.05rem; outline: none;
            box-shadow: 3px 3px 0px #000; transition: all 0.2s; font-family: inherit;
        }
        .form-group input:focus, .form-group select:focus { border-color: var(--primary-purple); box-shadow: 5px 5px 0px var(--primary-purple); }

        .btn {
            padding: 14px 28px; font-weight: 800; font-size: 1rem; border: 2px solid #000;
            border-radius: 14px; cursor: pointer; box-shadow: 4px 4px 0px #000; transition: all 0.2s ease;
            display: inline-flex; align-items: center; gap: 10px; text-decoration: none;
        }
        .btn:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0px #000; }
        .btn:disabled { opacity: 0.4; cursor: not-allowed; transform: none !important; box-shadow: 4px 4px 0px #000 !important; }

        .btn-primary { background: var(--primary-purple); color: #fff; }
        .btn-primary:hover { background: var(--primary-pink); }
        .btn-start { background: var(--primary-lime); color: #000; }
        .btn-start:hover { background: var(--primary-yellow); }
        .btn-next { background: var(--primary-cyan); color: #000; }
        .btn-prev { background: var(--surface-soft); color: var(--text-main); }
        .btn-submit { background: var(--primary-pink); color: #fff; }
        .btn-dark { background: var(--surface-soft); color: var(--text-main); padding: 12px 22px; font-size: 0.9rem; }
        .btn-dark:hover { background: var(--surface-soft-hover); }

        .alert-error {
            background: rgba(236, 72, 153, 0.12); border: 2px solid var(--primary-pink); color: var(--ink-pink);
            padding: 14px 18px; border-radius: 14px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600;
        }

        .q-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
        .q-tag { padding: 6px 14px; border-radius: 20px; border: 2px solid #000; font-weight: 800; font-size: 0.85rem; box-shadow: 2px 2px 0px #000; color: #000; text-transform: uppercase; }
        .q-counter { font-weight: 800; font-size: 1rem; color: var(--ink-yellow); }

        .q-content { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
        @media (max-width: 768px) { .q-content { grid-template-columns: 1fr; } }

        .img-wrapper { border: 3px solid #000; border-radius: 16px; overflow: hidden; box-shadow: 4px 4px 0px #000; height: 220px; background: var(--surface-soft); display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 0.85rem; }
        .img-wrapper img { width: 100%; height: 100%; object-fit: cover; }

        .q-info { display: flex; flex-direction: column; justify-content: center; }
        .q-info h3 { font-size: 1.4rem; margin-bottom: 10px; color: var(--text-main); }
        .q-info p { color: var(--text-muted); font-size: 0.92rem; line-height: 1.5; }

        .instruction-badge {
            background: var(--surface-soft); border: 2px solid #000; padding: 10px 16px; border-radius: 12px;
            margin-bottom: 16px; font-weight: 700; font-size: 0.9rem; display: flex;
            align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;
        }

        .word-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 12px; margin-bottom: 28px; }
        .word-chip {
            background: var(--surface-soft); border: 2px solid #000; border-radius: 12px; padding: 12px 10px;
            text-align: center; font-weight: 700; cursor: pointer; box-shadow: 3px 3px 0px #000;
            transition: all 0.2s ease; font-size: 0.95rem; user-select: none; color: var(--text-main);
        }
        .word-chip:hover { transform: translate(-2px, -2px); box-shadow: 5px 5px 0px #000; background: var(--surface-soft-hover); }
        .word-chip.selected { background: var(--primary-yellow); color: #000; border-color: #000; box-shadow: 4px 4px 0px #000; }

        .nav-controls { display: flex; justify-content: flex-end; align-items: center; border-top: 2px dashed #d9d3f0; padding-top: 20px; }

        .result-header { text-align: center; margin-bottom: 30px; }
        .group-name-display { font-size: 1.3rem; color: var(--ink-cyan); font-weight: 800; margin-bottom: 8px; }

        .score-cards-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 24px; }
        @media (max-width: 600px) { .score-cards-grid { grid-template-columns: 1fr; } }

        .score-card { background: var(--surface-soft); border: 3px solid #000; border-radius: 18px; padding: 18px 16px; text-align: center; box-shadow: 4px 4px 0px #000; }
        .score-card-adj { border-top: 6px solid var(--primary-pink); }
        .score-card-verb { border-top: 6px solid var(--primary-cyan); }
        .score-card label { font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 8px; }
        .score-val { font-size: 1.8rem; font-weight: 800; font-family: 'Space Grotesk', sans-serif; color: var(--text-main); }

        .score-total-box {
            max-width: 320px; margin: 0 auto 24px; background: var(--surface-soft); border: 3px solid #000;
            border-top: 6px solid var(--primary-yellow); border-radius: 18px; padding: 20px; text-align: center;
            box-shadow: 4px 4px 0px #000;
        }
        .score-total-box .score-val { font-size: 2.4rem; color: var(--ink-yellow); }

        .eval-badge {
            text-align: center; padding: 16px; border: 2px solid #000; border-radius: 16px;
            font-weight: 800; font-size: 1.05rem; box-shadow: 4px 4px 0px #000; margin-bottom: 24px;
        }

        /* ===== Homepage: hero ===== */
        .hero { max-width: 900px; margin: 56px auto 40px; padding: 0 20px; text-align: center; }
        .badge-hype {
            display: inline-flex; align-items: center; gap: 8px; background: var(--primary-pink); color: #fff;
            padding: 8px 20px; border-radius: 50px; font-weight: 800; font-size: 0.85rem; border: 2px solid #000;
            box-shadow: 3px 3px 0px #000; margin-bottom: 24px; animation: pulse 2s infinite;
        }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
        .hero h1 { font-size: 2.6rem; line-height: 1.18; margin-bottom: 20px; font-weight: 800; }
        .highlight-yellow { background: var(--primary-yellow); color: #000; padding: 2px 12px; border-radius: 8px; border: 2px solid #000; display: inline-block; box-shadow: 3px 3px 0px #000; }
        .highlight-cyan { background: var(--primary-cyan); color: #000; padding: 2px 12px; border-radius: 8px; border: 2px solid #000; display: inline-block; box-shadow: 3px 3px 0px #000; }
        .hero p { font-size: 1.05rem; color: var(--text-muted); max-width: 600px; margin: 0 auto 32px; line-height: 1.6; }
        .hero-btns { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }

        /* ===== Homepage: section ===== */
        .section { max-width: 1000px; margin: 70px auto; padding: 0 20px; scroll-margin-top: 110px; }
        .section-title { text-align: center; margin-bottom: 40px; }
        .section-title h2 { font-size: 2rem; font-weight: 800; margin-bottom: 10px; }
        .section-title p { color: var(--text-muted); font-size: 1rem; }

        .steps-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        @media (max-width: 700px) { .steps-grid { grid-template-columns: 1fr; } }
        .step-card { background: var(--card-bg); border: 3px solid #000; border-radius: 18px; padding: 22px 18px; box-shadow: var(--shadow-brutal); }
        .step-num {
            width: 34px; height: 34px; border-radius: 10px; border: 2px solid #000; background: var(--primary-cyan);
            color: #000; display: flex; align-items: center; justify-content: center; font-weight: 800;
            margin-bottom: 14px; box-shadow: 2px 2px 0px #000;
        }
        .step-card h4 { font-size: 1.05rem; margin-bottom: 8px; }
        .step-card p { color: var(--text-muted); font-size: 0.88rem; line-height: 1.5; margin: 0; }

        /* Kuis showcase grid di homepage */
        .quiz-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; }

        .quiz-card {
            background: var(--card-bg); border: 3px solid #000; border-radius: 20px; box-shadow: var(--shadow-brutal);
            padding: 24px; display: flex; flex-direction: column; justify-content: space-between;
            transition: all 0.25s ease;
        }
        .quiz-card:hover { transform: translate(-4px, -4px); box-shadow: var(--shadow-brutal-hover); }

        .quiz-card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
        .quiz-logo {
            width: 54px; height: 54px; border-radius: 16px; border: 2px solid #000;
            display: flex; align-items: center; justify-content: center; font-size: 1.6rem; box-shadow: 3px 3px 0px #000; color: #000;
        }
        .quiz-category {
            font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 20px; border: 2px solid #000;
            text-transform: uppercase; box-shadow: 2px 2px 0px #000; color: #000;
        }
        .quiz-card h3 { font-size: 1.2rem; margin-bottom: 8px; line-height: 1.3; }
        .quiz-card p { color: var(--text-muted); font-size: 0.87rem; margin-bottom: 18px; line-height: 1.5; }

        .quiz-meta {
            display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px;
            padding-top: 14px; border-top: 2px dashed #d9d3f0; margin-bottom: 16px;
            font-size: 0.78rem; color: var(--text-muted); font-weight: 600;
        }
        .quiz-meta span { display: flex; align-items: center; gap: 5px; }

        .btn-play {
            width: 100%; padding: 12px; background: var(--primary-lime); color: #000; font-weight: 800;
            border: 2px solid #000; border-radius: 12px; box-shadow: 3px 3px 0px #000; cursor: pointer;
            transition: all 0.2s ease; text-align: center; text-decoration: none; display: block;
        }
        .btn-play:hover { background: var(--primary-yellow); transform: translate(-2px, -2px); box-shadow: 5px 5px 0px #000; }
        .btn-play.disabled {
            background: var(--surface-soft); color: var(--text-muted); cursor: not-allowed; box-shadow: 3px 3px 0px #000;
            pointer-events: none;
        }

        /* ===== Login admin (homepage & halaman admin) ===== */
        .login-wrapper {
            max-width: 440px; margin: 0 auto; background: var(--card-bg); border: 3px solid #000;
            box-shadow: 8px 8px 0px #000; border-radius: 24px; padding: 32px; position: relative;
        }
        .form-toggle { display: flex; background: var(--surface-soft); padding: 6px; border-radius: 14px; border: 2px solid #000; margin-bottom: 24px; }
        .form-toggle button {
            flex: 1; padding: 10px; border: none; border-radius: 10px; font-weight: 800; cursor: pointer;
            background: transparent; color: var(--text-muted); font-family: inherit; font-size: 0.85rem;
        }
        .form-toggle button.active { background: var(--primary-purple); color: #fff; border: 2px solid #000; box-shadow: 2px 2px 0px #000; }
        [x-cloak] { display: none !important; }

        .input-cream {
            width: 100%; padding: 12px 16px; background: #fef9c3; border: 2px solid #000; border-radius: 12px;
            color: #000; font-size: 0.95rem; outline: none; box-shadow: 2px 2px 0px #000; font-family: inherit;
        }
        .input-cream:focus { border-color: var(--primary-purple); box-shadow: 4px 4px 0px var(--primary-purple); }

        .form-options { display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; margin-bottom: 22px; }
        .form-options label { display: flex; align-items: center; gap: 6px; cursor: pointer; }

        .btn-login-submit {
            width: 100%; padding: 14px; background: var(--primary-pink); color: #fff; font-weight: 800;
            font-size: 1rem; border: 2px solid #000; border-radius: 12px; box-shadow: 4px 4px 0px #000;
            cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-login-submit:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0px #000; background: var(--primary-purple); }

        /* ===== Footer ===== */
        footer { background: var(--surface-soft); border-top: 3px solid #000; padding: 44px 20px 24px; margin-top: 80px; }
        .footer-container { max-width: 1000px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 32px; margin-bottom: 28px; }
        .footer-brand p { color: var(--text-muted); font-size: 0.88rem; margin-top: 10px; line-height: 1.6; }
        .footer-links h4 { font-size: 1rem; margin-bottom: 12px; color: var(--ink-purple); }
        .footer-links ul { list-style: none; }
        .footer-links li { margin-bottom: 8px; }
        .footer-links a { color: var(--text-muted); text-decoration: none; font-size: 0.88rem; transition: color 0.2s; }
        .footer-links a:hover { color: var(--ink-cyan); }
        .footer-bottom { max-width: 1000px; margin: 0 auto; padding-top: 20px; border-top: 1px solid #ddd6f3; text-align: center; color: var(--text-muted); font-size: 0.82rem; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="{{ route('quiz.home') }}" class="logo">
            <div class="logo-icon"><img src="{{ asset('images/logo-kuis.png') }}" alt="Logo Find the Word"></div>
            <span>Find the Word</span>
        </a>

        <button class="menu-toggle" id="mobile-menu-btn" type="button" aria-label="Menu">
            <i class="fa-solid fa-bars"></i>
        </button>

        <ul class="nav-links" id="nav-links">
            <li><a href="{{ route('quiz.home') }}">Home</a></li>
            <li><a href="{{ route('quiz.home') }}#kuis">Daftar Kuis</a></li>
            @isset($group)
                <li><div class="group-badge"><i class="fa-solid fa-users"></i> {{ $group->name }}</div></li>
            @endisset
            @if (auth('siswa')->check())
                <li><a href="{{ route('user.profile.edit') }}" class="group-badge" style="text-decoration: none;"><i class="fa-solid fa-user-graduate"></i> {{ auth('siswa')->user()->name }}</a></li>
            @elseif (auth('kelompok')->check())
                <li><a href="{{ route('user.profile.edit') }}" class="group-badge" style="text-decoration: none;"><i class="fa-solid fa-people-group"></i> {{ auth('kelompok')->user()->name }}</a></li>
            @else
                <li><a href="{{ route('quiz.home') }}#user-login">Masuk</a></li>
            @endif
            <li><a href="{{ route('admin.login') }}" class="btn-nav-login"><i class="fa-solid fa-user-shield"></i> Login Admin</a></li>
        </ul>
    </nav>

    @yield('content')

    <footer>
        <div class="footer-container">
            <div class="footer-brand">
                <a href="{{ route('quiz.home') }}" class="logo">
                    <div class="logo-icon"><img src="{{ asset('images/logo-kuis.png') }}" alt="Logo Find the Word"></div>
                    <span>Find the Word</span>
                </a>
                <p>Kuis kelompok buat asesmen diagnostik kosakata (adjectives &amp; verbs) seputar objek wisata, sebelum masuk ke materi.</p>
            </div>
            <div class="footer-links">
                <h4>Navigasi</h4>
                <ul>
                    <li><a href="{{ route('quiz.home') }}">Home</a></li>
                    <li><a href="{{ route('quiz.home') }}#kuis">Daftar Kuis</a></li>
                    <li><a href="{{ route('quiz.home') }}#user-login">Login Siswa/Kelompok</a></li>
                    <li><a href="{{ route('admin.login') }}">Login Admin</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Tentang</h4>
                <ul>
                    <li>4 gambar objek wisata</li>
                    <li>Sesi Adjectives &amp; Verbs</li>
                    <li>Skor untuk pemetaan awal</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; {{ date('Y') }} Find the Word &mdash; Asesmen Diagnostik Kosakata Wisata.
        </div>
    </footer>

    <script>
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const navLinks = document.getElementById('nav-links');
        mobileMenuBtn?.addEventListener('click', () => navLinks.classList.toggle('active'));
    </script>
</body>
</html>
