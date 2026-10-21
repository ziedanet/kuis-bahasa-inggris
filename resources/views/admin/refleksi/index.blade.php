@extends('layouts.admin')

@section('title', 'Refleksi')

@section('page-title')
    <i class="fa-solid fa-star" style="color: var(--sun);"></i> Refleksi Peserta Didik
@endsection

@section('content')
<style>
    .rf-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 14px; margin-bottom: 4px; }
    .rf-stat { background: var(--surface-2); border: 2px solid var(--line); border-radius: 18px; padding: 14px 16px; }
    .rf-stat small { display: block; font-weight: 800; color: var(--ink-soft); font-size: .78rem; margin-bottom: 4px; }
    .rf-avg { font-family: var(--font-display); font-size: 2rem; line-height: 1; }
    .rf-avg span { font-size: .9rem; color: var(--ink-soft); }
    .rf-stars { color: #f59e0b; letter-spacing: 2px; font-size: 1.05rem; }
    .rf-stars .off { color: var(--line); }
    .rf-dist { margin-top: 8px; display: grid; gap: 3px; }
    .rf-dist div { display: grid; grid-template-columns: 16px 1fr 26px; align-items: center; gap: 6px; font-size: .72rem; font-weight: 800; color: var(--ink-soft); }
    .rf-dist i { display: block; height: 7px; border-radius: 999px; background: #f59e0b; }
    .rf-item { border: 2px solid var(--line); border-radius: 18px; padding: 14px 18px; margin-bottom: 12px; background: var(--surface); }
    .rf-head { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 8px; align-items: flex-start; margin-bottom: 10px; }
    .rf-head strong { font-size: 1.05rem; }
    .rf-head small { color: var(--ink-soft); font-weight: 700; }
    .rf-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 10px; }
    .rf-box { background: var(--surface-2); border-radius: 12px; padding: 10px 12px; }
    .rf-box h5 { font-size: .72rem; letter-spacing: .06em; text-transform: uppercase; color: var(--ink-soft); margin-bottom: 4px; }
    .rf-box p { white-space: pre-line; font-size: .92rem; overflow-wrap: anywhere; }
    .rf-rates { display: flex; flex-wrap: wrap; gap: 8px 18px; font-size: .82rem; font-weight: 700; }
    @media (max-width: 700px) { .rf-cols { grid-template-columns: 1fr; } }
</style>

@php
    $star = fn (int $n) => '<span class="rf-stars">'.str_repeat('★', $n).'<span class="off">'.str_repeat('★', 5 - $n).'</span></span>';
    $short = [
        'rating_understand' => 'Memahami materi',
        'rating_teamwork' => 'Kerja sama kelompok',
        'rating_fun' => 'Pembelajaran menyenangkan',
    ];
@endphp

<div class="panel-card" style="padding: 16px 24px;">
    <form method="GET" action="{{ route('admin.refleksi.index') }}" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        <label style="font-weight: 800; display: flex; align-items: center; gap: 8px; font-size: 0.85rem;">
            <i class="fa-solid fa-filter" style="color: var(--pink);"></i> Kelas:
        </label>
        <select name="kelas_id" onchange="this.form.submit()" class="filter-select" style="width: 240px;">
            <option value="">Semua Kelas</option>
            @foreach ($kelasList as $kelas)
                <option value="{{ $kelas->id }}" @selected((string) $kelasId === (string) $kelas->id)>{{ $kelas->name }}</option>
            @endforeach
        </select>
        @if ($kelasId)
            <a href="{{ route('admin.refleksi.index') }}" class="btn btn-dark btn-sm">Reset</a>
        @endif
        <span style="margin-left: auto; font-weight: 800; color: var(--ink-soft);">{{ $summary['count'] }} refleksi masuk</span>
    </form>
</div>

<div class="panel-card">
    <div class="panel-header"><h3><i class="fa-solid fa-chart-simple" style="color: var(--ocean);"></i> Rekap Penilaian Bintang</h3></div>
    <div class="rf-grid">
        @foreach ($statements as $field => $text)
            <div class="rf-stat">
                <small>{{ $text }}</small>
                <div class="rf-avg">{{ $summary['count'] ? number_format($summary['averages'][$field], 1) : '-' }}<span> / 5</span></div>
                <div class="rf-dist">
                    @foreach ([5, 4, 3, 2, 1] as $n)
                        @php $c = $summary['distribution'][$field][$n]; @endphp
                        <div><span>{{ $n }}★</span><i style="width: {{ $summary['count'] ? ($c / $summary['count']) * 100 : 0 }}%;"></i><span>{{ $c }}</span></div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="panel-card">
    <div class="panel-header">
        <h3><i class="fa-solid fa-comments" style="color: var(--ocean);"></i> Jawaban Peserta</h3>
        <span style="color: var(--ink-soft); font-size: .85rem; font-weight: 700;">Link form untuk siswa: <a href="{{ route('refleksi.create') }}" target="_blank" rel="noopener">{{ route('refleksi.create') }}</a></span>
    </div>

    @forelse ($items as $item)
        <div class="rf-item">
            <div class="rf-head">
                <div>
                    <strong>{{ $item->participant_name }}</strong><br>
                    <small>{{ $item->kelas?->name ?? '-' }} &middot; {{ $item->created_at->translatedFormat('d M Y, H:i') }} &middot; rata-rata {{ number_format($item->averageRating(), 1) }}/5</small>
                </div>
                <form method="POST" action="{{ route('admin.refleksi.destroy', $item) }}"
                      onsubmit="return confirm('Hapus refleksi {{ e($item->participant_name) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-pink btn-sm" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                </form>
            </div>
            <div class="rf-cols">
                <div class="rf-box"><h5>Hari ini saya belajar tentang</h5><p>{{ $item->learned }}</p></div>
                <div class="rf-box"><h5>Hal yang masih membingungkan</h5><p>{{ $item->confusing ?: '-' }}</p></div>
            </div>
            <div class="rf-rates">
                @foreach ($short as $field => $label)
                    <span>{{ $label }}: {!! $star((int) $item->$field) !!}</span>
                @endforeach
            </div>
        </div>
    @empty
        <p style="text-align:center; color: var(--ink-soft); font-weight: 800; padding: 24px;">Belum ada refleksi masuk.</p>
    @endforelse

    @if ($items->hasPages())
        <div style="display:flex; gap:10px; justify-content:center; align-items:center; margin-top: 12px;">
            @if ($items->previousPageUrl())<a href="{{ $items->previousPageUrl() }}" class="btn btn-dark btn-sm">&laquo; Sebelumnya</a>@endif
            <span style="font-weight:800;">Halaman {{ $items->currentPage() }} / {{ $items->lastPage() }}</span>
            @if ($items->nextPageUrl())<a href="{{ $items->nextPageUrl() }}" class="btn btn-dark btn-sm">Berikutnya &raquo;</a>@endif
        </div>
    @endif
</div>
@endsection
