<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Individual Assessment')</title>
    <style>
        :root {
            --violet: #7c3aed; --magenta: #c026d3; --rose: #db2777;
            --ink: #1f1b2e; --muted: #6b6683; --line: #e6e2f0; --soft: #f5f3fa; --card: #ffffff;
            --ok: #16a34a; --bad: #dc2626;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: var(--ink); background: #faf9fc; min-height: 100vh; padding: 18px 16px 48px;
            background-image:
                radial-gradient(900px 420px at 85% -5%, rgba(124, 58, 237, .10), transparent 60%),
                radial-gradient(800px 380px at 0% 0%, rgba(219, 39, 119, .08), transparent 60%);
        }
        .wrap { max-width: 640px; margin: 0 auto; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
        .home-link {
            display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 999px;
            background: var(--card); border: 1px solid var(--line); color: var(--violet); font-weight: 700;
            font-size: .85rem; text-decoration: none;
        }
        .home-link:hover { background: var(--soft); }
        header { text-align: center; margin: 8px 0 22px; }
        .dots { display: flex; gap: 8px; justify-content: center; margin-bottom: 10px; }
        .dots i { width: 8px; height: 8px; border-radius: 50%; display: block; }
        .dots i:nth-child(1) { background: #7c3aed; } .dots i:nth-child(2) { background: #2563eb; }
        .dots i:nth-child(3) { background: #0891b2; } .dots i:nth-child(4) { background: #ea580c; }
        .dots i:nth-child(5) { background: #db2777; }
        h1 {
            font-size: 2rem; font-weight: 800; letter-spacing: -.01em;
            background: linear-gradient(90deg, var(--violet), var(--rose)); -webkit-background-clip: text;
            background-clip: text; color: transparent;
        }
        .sub { color: var(--muted); font-size: .95rem; margin-top: 4px; }
        .card {
            background: var(--card); border: 1px solid var(--line); border-radius: 24px; padding: 24px;
            box-shadow: 0 18px 40px -22px rgba(76, 29, 149, .35); margin-bottom: 18px;
        }
        label.field { display: block; font-weight: 700; color: var(--violet); font-size: .9rem; margin-bottom: 8px; }
        .input {
            width: 100%; padding: 14px 16px; border-radius: 14px; border: 1px solid var(--line); background: var(--soft);
            font-size: 1rem; color: var(--ink); font-family: inherit; margin-bottom: 18px; outline: none;
        }
        .input:focus { border-color: var(--violet); box-shadow: 0 0 0 3px rgba(124, 58, 237, .18); background: #fff; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: 0; cursor: pointer;
            padding: 15px 22px; border-radius: 14px; font-weight: 800; font-size: 1rem; font-family: inherit;
            text-decoration: none; transition: transform .1s, box-shadow .15s;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-grad { width: 100%; color: #fff; background: linear-gradient(90deg, var(--violet), var(--rose)); box-shadow: 0 12px 24px -12px rgba(219, 39, 119, .6); }
        .btn-soft { background: var(--soft); color: var(--ink); border: 1px solid var(--line); }
        .note { text-align: center; color: var(--muted); font-size: .9rem; margin: 6px 0 16px; }
        .err { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 12px; padding: 10px 14px; font-weight: 600; font-size: .9rem; margin-bottom: 16px; }
        .foot { text-align: center; color: #a29dbb; font-size: .78rem; margin-top: 18px; }
        :focus-visible { outline: 3px solid var(--violet); outline-offset: 2px; }
        @media print {
            body { background: #fff; padding: 0; }
            .no-print, .topbar { display: none !important; }
            .card { box-shadow: none; }
        }
        @yield('styles')
    </style>
</head>
<body>
    <div class="wrap">
        <div class="topbar no-print">
            <a href="{{ route('quiz.home') }}" class="home-link">&#8962; Home</a>
        </div>

        <header>
            <div class="dots" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></div>
            <h1>Individual Assessment</h1>
            <p class="sub">@yield('subtitle')</p>
        </header>

        @yield('content')

        <p class="foot no-print">@yield('subtitle')</p>
    </div>
</body>
</html>
