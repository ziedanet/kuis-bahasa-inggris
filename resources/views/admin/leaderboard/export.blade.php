<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Leaderboard {{ $tipe === 'kelompok' ? 'Kelompok' : 'Individu' }} - {{ now()->format('Y-m-d') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Lilita+One&family=Nunito:wght@500;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Nunito', system-ui, sans-serif; color: #0B2F3B; background: #fff; padding: 28px 32px; font-size: 14px; }
        h1 { font-family: 'Lilita One', 'Trebuchet MS', sans-serif; font-weight: 400; font-size: 30px; }
        .meta { color: #3F6471; font-weight: 700; margin: 4px 0 18px; }
        .toolbar { display: flex; gap: 10px; align-items: center; margin-bottom: 18px; padding: 12px 14px; background: #EBF8FA; border: 2px solid #A9D6DD; border-radius: 12px; font-weight: 700; }
        .toolbar button { padding: 8px 16px; border: 0; border-radius: 10px; background: #FFB81F; color: #3A2600; font-weight: 900; cursor: pointer; font-family: inherit; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; letter-spacing: .08em; text-transform: uppercase; color: #3F6471; padding: 8px 10px; border-bottom: 2px solid #0B2F3B; }
        td { padding: 9px 10px; border-bottom: 1px solid #A9D6DD; }
        tr { page-break-inside: avoid; }
        .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
        .rank { font-family: 'Lilita One', sans-serif; font-size: 18px; width: 48px; }
        .top td { background: #FFF0C4; }
        .empty { text-align: center; padding: 30px; color: #3F6471; font-weight: 800; }
        .foot { margin-top: 18px; color: #3F6471; font-size: 12px; font-weight: 700; }
        @media print {
            .toolbar { display: none; }
            body { padding: 0; }
            .top td { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        @page { margin: 14mm; }
    </style>
</head>
<body>
@php
    $fmt = fn (?int $s) => $s === null ? '--:--' : sprintf('%02d:%02d', intdiv($s, 60), $s % 60);
    $isKelompok = $tipe === 'kelompok';
@endphp

<div class="toolbar">
    <button type="button" onclick="window.print()">Cetak / Simpan sebagai PDF</button>
    <span>Di dialog cetak, pilih tujuan <strong>Save as PDF</strong> (Simpan sebagai PDF).</span>
</div>

<h1>Leaderboard {{ $isKelompok ? 'Kelompok' : 'Individu' }}</h1>
<p class="meta">
    {{ $quizTitle ?? 'Semua Kuis' }} &middot; Dicetak {{ now()->translatedFormat('d F Y, H:i') }} &middot; {{ $rows->count() }} peserta
</p>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>{{ $isKelompok ? 'Kelompok' : 'Siswa' }}</th>
            <th>Kelas</th>
            <th>Kuis</th>
            <th class="num">Skor (0-100)</th>
            <th class="num">Benar</th>
            <th class="num">Salah</th>
            <th class="num">Dijawab</th>
            <th class="num">Belum</th>
            <th>Status</th>
            <th>Predikat</th>
            <th class="num">Waktu</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($rows as $i => $item)
            @php
                $name = $isKelompok ? $item->name : $item->displayName();
                $kelas = $isKelompok ? $item->kelas?->name : $item->displayKelas();
            @endphp
            <tr class="{{ $i === 0 ? 'top' : '' }}">
                <td class="rank">{{ $i + 1 }}</td>
                <td><strong>{{ $name }}</strong></td>
                <td>{{ $kelas ?? '-' }}</td>
                <td>{{ $item->quiz?->title }}</td>
                <td class="num"><strong>{{ number_format($item->scoreOn100(), 1) }}</strong></td>
                <td class="num">{{ $item->progress['correct'] }}</td>
                <td class="num">{{ $item->progress['wrong'] }}</td>
                <td class="num">{{ $item->progress['answered'] }}/{{ $item->progress['total'] }}</td>
                <td class="num">{{ $item->progress['remaining'] }}</td>
                <td>{{ $item->progress['finished'] ? 'Selesai' : 'Mengerjakan' }}</td>
                <td>{{ $item->predikat() }}</td>
                <td class="num">{{ $fmt($item->durationSeconds()) }}</td>
            </tr>
        @empty
            <tr><td colspan="12" class="empty">Belum ada peserta yang mulai mengerjakan kuis ini.</td></tr>
        @endforelse
    </tbody>
</table>

<p class="foot">Kelompok: benar/salah dihitung per kata (5 kata per foto). Individu: per soal. Urutan: skor tertinggi dulu; jika sama, waktu tersingkat.</p>

<script>
    // Buka dialog cetak otomatis setelah font termuat.
    window.addEventListener('load', () => {
        (document.fonts ? document.fonts.ready : Promise.resolve()).then(() => setTimeout(() => window.print(), 300));
    });
</script>
</body>
</html>
