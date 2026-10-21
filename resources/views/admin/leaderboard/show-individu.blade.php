@extends('layouts.admin')

@section('title', 'Detail Jawaban - ' . $attempt->displayName())

@section('page-title')
    <i class="fa-solid fa-list-check" style="color: var(--ocean);"></i> Detail Jawaban Individu
@endsection

@section('top-actions')
    <a href="{{ route('admin.leaderboard', ['tipe' => 'individu']) }}" class="btn btn-dark btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
@include('admin.leaderboard._detail-styles')
@php
    $predikat = $attempt->predikat();
    $seconds = $attempt->durationSeconds();
    $time = $seconds === null ? '--:--' : sprintf('%02d:%02d', intdiv($seconds, 60), $seconds % 60);
    $correctTotal = $answers->where('is_correct', true)->count();
@endphp

<div class="panel-card">
    <div class="dt-summary">
        <div><div class="dt-label">Peserta</div><div class="dt-value">{{ $attempt->displayName() }}</div></div>
        <div><div class="dt-label">Kelas</div><div class="dt-value">{{ $attempt->displayKelas() }}</div></div>
        <div><div class="dt-label">Kuis</div><div class="dt-value">{{ $attempt->quiz?->title }}</div></div>
        <div><div class="dt-label">Skor (0-100)</div><div class="dt-value">{{ number_format($attempt->scoreOn100(), 1) }}</div></div>
        <div>
            <div class="dt-label">Predikat</div>
            <span class="badge @if($predikat === 'Sangat Baik') badge-lime @elseif($predikat === 'Baik') badge-cyan @elseif($predikat === 'Cukup') badge-yellow @else badge-pink @endif">{{ $predikat }}</span>
        </div>
        <div><div class="dt-label">Benar</div><div class="dt-value">{{ $correctTotal }}/{{ $answers->count() }}</div></div>
        <div><div class="dt-label">Waktu</div><div class="dt-value">{{ $time }}</div></div>
    </div>
</div>

<div class="panel-card">
    <div class="panel-header">
        <h3>Jawaban per Soal</h3>
        <span class="dt-legend">hijau = benar &middot; merah = pilihan siswa yang salah &middot; garis putus = jawaban yang benar</span>
    </div>

    @forelse ($answers as $i => $answer)
        @php $soal = $answer->soal; @endphp
        <div class="dt-card">
            <div class="dt-head">
                @if ($soal->gambarUrl())
                    <img src="{{ $soal->gambarUrl() }}" alt="Gambar soal" class="q-thumb">
                @endif
                <div>
                    <h4>{{ $i + 1 }}. {{ $soal->narasi }}</h4>
                </div>
                <div class="dt-score">
                    @if ($answer->is_correct)
                        <i class="fa-solid fa-circle-check" style="color: var(--palm);"></i>
                    @else
                        <i class="fa-solid fa-circle-xmark" style="color: var(--coral);"></i>
                    @endif
                </div>
            </div>
            <div class="dt-words" style="grid-template-columns: 1fr;">
                @foreach ($soal->opsi as $opsi)
                    @php
                        $picked = (int) $answer->opsi_id === (int) $opsi->id;
                        $state = $picked ? ($opsi->is_correct ? 'ok' : 'bad') : ($opsi->is_correct ? 'missed' : 'dim');
                    @endphp
                    <div class="dt-word {{ $state }}">
                        <i class="fa-solid {{ $state === 'bad' ? 'fa-xmark' : ($state === 'dim' ? 'fa-minus' : 'fa-check') }}"></i>
                        <span>{{ $opsi->opsi_text }}</span>
                        @if ($picked) <span style="margin-left:auto; font-size:.75rem;">pilihan siswa</span> @endif
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p style="text-align:center; color: var(--ink-soft); font-weight: 800; padding: 20px;">Belum ada jawaban tersimpan.</p>
    @endforelse
</div>
@endsection
