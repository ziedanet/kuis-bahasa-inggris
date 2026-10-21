@extends('layouts.admin')

@section('title', 'Detail Jawaban - ' . $group->name)

@section('page-title')
    <i class="fa-solid fa-list-check" style="color: var(--ocean);"></i> Detail Jawaban Kelompok
@endsection

@section('top-actions')
    <a href="{{ route('admin.leaderboard', ['tipe' => 'kelompok']) }}" class="btn btn-dark btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
@include('admin.leaderboard._detail-styles')
@php
    $predikat = $group->predikat();
    $seconds = $group->durationSeconds();
    $time = $seconds === null ? '--:--' : sprintf('%02d:%02d', intdiv($seconds, 60), $seconds % 60);
@endphp

<div class="panel-card">
    <div class="dt-summary">
        <div><div class="dt-label">Kelompok</div><div class="dt-value">{{ $group->name }}</div></div>
        <div><div class="dt-label">Kelas</div><div class="dt-value">{{ $group->kelas?->name ?? '-' }}</div></div>
        <div><div class="dt-label">Kuis</div><div class="dt-value">{{ $group->quiz?->title }}</div></div>
        <div><div class="dt-label">Skor (0-100)</div><div class="dt-value">{{ number_format($group->scoreOn100(), 1) }}</div></div>
        <div>
            <div class="dt-label">Predikat</div>
            <span class="badge @if($predikat === 'Sangat Baik') badge-lime @elseif($predikat === 'Baik') badge-cyan @elseif($predikat === 'Cukup') badge-yellow @else badge-pink @endif">{{ $predikat }}</span>
        </div>
        <div><div class="dt-label">Waktu</div><div class="dt-value">{{ $time }}</div></div>
    </div>
</div>

<div class="panel-card">
    <div class="panel-header">
        <h3>Jawaban per Gambar</h3>
        <span class="dt-legend">
            <i class="fa-solid fa-check" style="color: var(--palm);"></i> hijau = benar &nbsp;
            <i class="fa-solid fa-xmark" style="color: var(--coral);"></i> merah = salah &nbsp;
            <i class="fa-solid fa-check" style="color: var(--palm);"></i> garis putus = kata benar yang terlewat
        </span>
    </div>

    @forelse ($answers as $answer)
        @php
            $image = $answer->image;
            $selected = array_map('intval', $answer->selected_word_ids ?? []);
        @endphp
        <div class="dt-card">
            <div class="dt-head">
                @if ($image->imageUrl())
                    <img src="{{ $image->imageUrl() }}" alt="{{ $image->title }}" class="q-thumb">
                @else
                    <div class="q-thumb q-thumb-empty"><i class="fa-regular fa-image"></i><span>Belum ada gambar</span></div>
                @endif
                <div>
                    <h4>{{ $image->title }}</h4>
                    <div class="dt-meta">
                        <span class="badge {{ $image->session_type === 'adjectives' ? 'badge-pink' : 'badge-cyan' }}">{{ strtoupper($image->session_type) }}</span>
                        @if ($image->lokasi) &nbsp;<i class="fa-solid fa-location-dot"></i> {{ $image->lokasi }} @endif
                    </div>
                </div>
                <div class="dt-score">{{ $answer->correct_count }}/5</div>
            </div>
            <div class="dt-words">
                @foreach ($image->words as $word)
                    @php
                        $picked = in_array($word->id, $selected, true);
                        $state = $picked ? ($word->is_correct ? 'ok' : 'bad') : ($word->is_correct ? 'missed' : 'dim');
                    @endphp
                    <div class="dt-word {{ $state }}">
                        <i class="fa-solid {{ $state === 'bad' ? 'fa-xmark' : ($state === 'dim' ? 'fa-minus' : 'fa-check') }}"></i>
                        <span>{{ $word->word }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p style="text-align:center; color: var(--ink-soft); font-weight: 800; padding: 20px;">Belum ada jawaban tersimpan.</p>
    @endforelse
</div>
@endsection
