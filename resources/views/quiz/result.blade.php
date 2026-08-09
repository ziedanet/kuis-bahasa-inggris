@extends('layouts.app')

@section('title', 'Find the Word - Hasil')

@section('content')
@php
    $evalStyle = match ($predikat) {
        'Sangat Baik' => ['bg' => 'var(--primary-lime)', 'color' => '#000', 'text' => '🔥 GG WP! Vocabulary wisata kalian luar biasa mantap!'],
        'Baik' => ['bg' => 'var(--primary-cyan)', 'color' => '#000', 'text' => '😎 Mantap! Pengetahuan kosakata wisata kalian cukup oke!'],
        'Cukup' => ['bg' => 'var(--primary-yellow)', 'color' => '#000', 'text' => '💪 Lumayan! Sedikit pengulangan lagi biar makin jago!'],
        default => ['bg' => 'var(--primary-pink)', 'color' => '#fff', 'text' => '🌱 Nice try! Yuk semangat belajar kosakata wisata lagi!'],
    };
@endphp

<div class="container">
    <div class="quiz-box">

<div class="result-header">
    <span class="welcome-badge" style="background: var(--primary-lime);">🎉 Kuis Selesai!</span>
    <h2>Hasil & Evaluasi</h2>
    <div class="group-name-display">Kelompok: {{ $group->name }}</div>
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

<div class="score-cards-grid">
    @foreach ($answers as $answer)
        <div class="score-card {{ $answer->image->session_type === 'adjectives' ? 'score-card-adj' : 'score-card-verb' }}">
            <label>{{ $answer->image->title }}</label>
            <div class="score-val" style="color: {{ $answer->image->session_type === 'adjectives' ? 'var(--ink-pink)' : 'var(--ink-cyan)' }};">
                {{ $answer->correct_count }}/5
            </div>
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">{{ number_format($answer->score, 1) }} poin</p>
        </div>
    @endforeach
</div>

<p style="color: var(--text-muted); font-size: 0.8rem; text-align: center;">
    Skor ini untuk pemetaan pemahaman awal, tidak dimasukkan ke nilai rapor.
</p>

    </div>
</div>
@endsection
