@extends('layouts.admin')

@section('title', 'Leaderboard')

@section('page-title')
    <i class="fa-solid fa-trophy" style="color: var(--sun);"></i> Leaderboard
@endsection

@section('content')
<style>
    .lb-rows { display: flex; flex-direction: column; gap: 10px; font-variant-numeric: tabular-nums; }
    .lb-row { display: grid; grid-template-columns: 40px minmax(0, 1fr) auto auto auto; gap: 8px 16px; align-items: center; padding: 12px 18px; border-radius: 20px; background: var(--surface); border: 2px solid var(--line); }
    .lb-row.lead { border-color: var(--sun); box-shadow: 0 4px 0 var(--sun-deep); }
    .lb-row.wait { opacity: .6; border-style: dashed; }
    .lb-rank { font-family: var(--font-display); font-size: 1.7rem; color: var(--brand-ink); text-align: center; }
    .lb-who { display: flex; flex-direction: column; gap: 5px; min-width: 0; }
    .lb-name-line { display: flex; align-items: center; gap: 8px; min-width: 0; font-weight: 900; font-size: 1.15rem; }
    .lb-name { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .lb-crown { color: var(--sun-deep); }
    .lb-tag { flex: none; padding: 1px 10px; border-radius: 999px; background: var(--sun); color: var(--on-sun); font-size: .7rem; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; }
    .lb-sub { font-size: .78rem; font-weight: 800; color: var(--ink-soft); }
    .lb-bar { height: 9px; border-radius: 999px; background: var(--surface-2); overflow: hidden; }
    .lb-bar i { display: block; height: 100%; border-radius: 999px; background: var(--ocean); transition: width .6s ease; }
    .lb-row.lead .lb-bar i { background: var(--sun); }
    .lb-pts { font-family: var(--font-display); font-size: 1.9rem; line-height: 1; text-align: right; min-width: 84px; }
    .lb-pts small { font-size: .8rem; color: var(--ink-soft); }
    .lb-time { font-weight: 800; color: var(--ink-soft); font-size: .92rem; min-width: 70px; text-align: right; }
    .lb-actions { display: flex; gap: 8px; align-items: center; justify-content: flex-end; min-width: 120px; }
    .lb-empty, .lb-more { text-align: center; color: var(--ink-soft); font-weight: 800; padding: 18px; }
    .lb-note { color: var(--ink-soft); font-size: .88rem; font-weight: 700; margin-bottom: 16px; }
    .lb-live { display: flex; align-items: center; gap: 8px; font-size: .8rem; font-weight: 800; color: var(--ink-soft); }
    .lb-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--palm); animation: lbpulse 2s infinite; }
    @keyframes lbpulse { 0%, 100% { opacity: 1; } 50% { opacity: .3; } }
    @media (max-width: 760px) {
        .lb-row { grid-template-columns: 32px minmax(0, 1fr) auto; }
        .lb-time { grid-column: 2 / -1; text-align: left; }
        .lb-actions { grid-column: 2 / -1; justify-content: flex-start; }
    }
    @media (prefers-reduced-motion: reduce) { .lb-dot { animation: none; } .lb-bar i { transition: none; } }
</style>

<div class="panel-card" style="padding: 16px 24px;">
    <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 12px; justify-content: space-between;">
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.leaderboard', ['tipe' => 'kelompok']) }}"
               class="btn btn-sm {{ $tipe === 'kelompok' ? 'btn-primary' : 'btn-dark' }}">
                <i class="fa-solid fa-people-group"></i> Kuis Kelompok
            </a>
            <a href="{{ route('admin.leaderboard', ['tipe' => 'individu']) }}"
               class="btn btn-sm {{ $tipe === 'individu' ? 'btn-primary' : 'btn-dark' }}">
                <i class="fa-solid fa-user-pen"></i> Kuis Individu
            </a>
        </div>

        <form method="GET" action="{{ route('admin.leaderboard') }}" style="display: flex; align-items: center; gap: 10px;">
            <input type="hidden" name="tipe" value="{{ $tipe }}">
            <label style="font-weight: 800; display: flex; align-items: center; gap: 8px; font-size: 0.85rem;">
                <i class="fa-solid fa-filter" style="color: var(--pink);"></i> Kuis:
            </label>
            <select name="quiz_id" onchange="this.form.submit()" class="filter-select" style="width: 240px;">
                <option value="">Semua Kuis</option>
                @foreach ($quizList as $quizOption)
                    <option value="{{ $quizOption->id }}" @selected((string) $quizId === (string) $quizOption->id)>
                        {{ $quizOption->title }}
                    </option>
                @endforeach
            </select>
            @if ($quizId)
                <a href="{{ route('admin.leaderboard', ['tipe' => $tipe]) }}" class="btn btn-dark btn-sm">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="panel-card">
    <div class="panel-header">
        <h3>
            <i class="fa-solid fa-ranking-star" style="color: var(--ocean);"></i>
            Peringkat {{ $tipe === 'kelompok' ? 'Kelompok' : 'Individu' }}
        </h3>
        <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 10px;">
            <span class="lb-live" aria-live="off"><span class="lb-dot" id="lb-dot"></span> <span id="lb-state">Auto-refresh 10 detik</span> &middot; <span id="lb-updated">sekarang</span></span>
            <button type="button" class="btn btn-sun btn-sm" id="lb-toggle"><i class="fa-solid fa-pause" id="lb-toggle-icon"></i> <span id="lb-toggle-text">Stop refresh</span></button>
            <a href="{{ route('admin.leaderboard.export', request()->query()) }}" target="_blank" rel="noopener" class="btn btn-cyan btn-sm">
                <i class="fa-solid fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <p class="lb-note">
        Hanya yang sudah menyelesaikan seluruh soal masuk peringkat. Urutan: skor tertinggi dulu, kalau skor sama yang waktu pengerjaannya paling singkat menang.
    </p>

    <div id="lb-container">
        @include('admin.leaderboard._rows')
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const INTERVAL = 10000;
    const container = document.getElementById('lb-container');
    const stamp = document.getElementById('lb-updated');
    const stateEl = document.getElementById('lb-state');
    const dot = document.getElementById('lb-dot');
    const toggle = document.getElementById('lb-toggle');
    const toggleIcon = document.getElementById('lb-toggle-icon');
    const toggleText = document.getElementById('lb-toggle-text');
    const params = new URLSearchParams(window.location.search);
    params.set('partial', '1');
    const url = window.location.pathname + '?' + params.toString();
    const pad = (n) => String(n).padStart(2, '0');
    let timer = null;
    let running = true;

    function mark() {
        const d = new Date();
        stamp.textContent = 'diperbarui ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
    }

    async function refresh() {
        // Jangan timpa halaman kalau tab sedang tidak terlihat.
        if (document.hidden) return;
        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin', cache: 'no-store' });
            if (res.redirected || !res.ok) { window.location.reload(); return; }
            container.innerHTML = await res.text();
            mark();
        } catch (e) { /* koneksi putus sebentar: coba lagi di putaran berikutnya */ }
    }

    function render() {
        stateEl.textContent = running ? 'Auto-refresh 10 detik' : 'Refresh dihentikan';
        dot.style.animationPlayState = running ? 'running' : 'paused';
        dot.style.background = running ? '' : 'var(--ink-soft)';
        toggleIcon.className = running ? 'fa-solid fa-pause' : 'fa-solid fa-play';
        toggleText.textContent = running ? 'Stop refresh' : 'Lanjutkan refresh';
    }

    function start() { running = true; clearInterval(timer); timer = setInterval(refresh, INTERVAL); render(); }
    function stop() { running = false; clearInterval(timer); render(); }

    toggle.addEventListener('click', () => {
        if (running) { stop(); } else { refresh(); start(); }
    });
    document.addEventListener('visibilitychange', () => { if (!document.hidden && running) refresh(); });

    mark();
    start();
})();
</script>
@endpush
