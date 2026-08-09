@extends('layouts.app')

@section('title', 'Find the Word - Hasil Individu')

@section('content')
@php
    $evalStyle = match ($predikat) {
        'Sangat Baik' => ['bg' => 'var(--primary-lime)', 'color' => '#000', 'text' => '🔥 Keren! Pemahaman kamu top banget!'],
        'Baik' => ['bg' => 'var(--primary-cyan)', 'color' => '#000', 'text' => '😎 Mantap, hasil kamu bagus!'],
        'Cukup' => ['bg' => 'var(--primary-yellow)', 'color' => '#000', 'text' => '💪 Lumayan, coba pelajari lagi ya!'],
        default => ['bg' => 'var(--primary-pink)', 'color' => '#fff', 'text' => '🌱 Yuk belajar lagi, jangan menyerah!'],
    };
@endphp

<div class="container">
    <div class="quiz-box">

<div class="result-header">
    <span class="welcome-badge" style="background: var(--primary-lime);">🎉 Kuis Selesai!</span>
    <h2>Hasil & Evaluasi</h2>
    <div class="group-name-display">{{ $attempt->siswa?->name ?? auth('siswa')->user()->name }}</div>
</div>

<div class="eval-badge" style="background: {{ $evalStyle['bg'] }}; color: {{ $evalStyle['color'] }};">
    {{ $evalStyle['text'] }}
</div>

<div class="score-total-box">
    <label style="font-size:0.8rem; font-weight:800; text-transform:uppercase; color:var(--text-muted); display:block; margin-bottom:8px;">
        Total Skor &middot; Predikat: {{ $predikat }}
    </label>
    <div class="score-val">{{ number_format($scoreOn100, 1) }} / 100</div>
</div>

<div class="score-cards-grid" style="grid-template-columns: 1fr;">
    @foreach ($answers as $answer)
        <div class="score-card" style="text-align: left; border-top: 6px solid {{ $answer->is_correct ? 'var(--primary-lime)' : 'var(--primary-pink)' }};">
            <label>{{ $answer->soal->narasi }}</label>
            <p style="font-size: 0.9rem; margin-top: 6px; color: var(--text-main);">
                Jawaban kamu: <strong>{{ $answer->opsi->opsi_text }}</strong>
                {{ $answer->is_correct ? '✅' : '❌' }}
            </p>
        </div>
    @endforeach
</div>

<p style="color: var(--text-muted); font-size: 0.8rem; text-align: center;">
    Skor ini untuk pemetaan pemahaman awal, tidak dimasukkan ke nilai rapor.
</p>

    </div>
</div>
@endsection
