@extends('layouts.app')

@section('title', 'Find the Word - Individual Result')

@section('content')
@php
    // Display-only English labels — the underlying $predikat string stays
    // Indonesian (shared with the admin panel / Leaderboard).
    $predikatLabels = [
        'Sangat Baik' => 'Excellent',
        'Baik' => 'Good',
        'Cukup' => 'Fair',
        'Perlu Bimbingan' => 'Needs Improvement',
    ];
    $predikatLabel = $predikatLabels[$predikat] ?? $predikat;

    $evalStyle = match ($predikat) {
        'Sangat Baik' => ['bg' => 'var(--primary-lime)', 'color' => '#000', 'text' => '🔥 Awesome! Your understanding is top-notch!'],
        'Baik' => ['bg' => 'var(--primary-cyan)', 'color' => '#000', 'text' => '😎 Nice, your result is great!'],
        'Cukup' => ['bg' => 'var(--primary-yellow)', 'color' => '#000', 'text' => '💪 Not bad, try reviewing a bit more!'],
        default => ['bg' => 'var(--primary-pink)', 'color' => '#fff', 'text' => '🌱 Keep learning, don\'t give up!'],
    };
@endphp

<div class="container">
    <div class="quiz-box">

<div class="result-header">
    <span class="welcome-badge" style="background: var(--primary-lime);">🎉 Quiz Complete!</span>
    <h2>Results & Evaluation</h2>
    <div class="group-name-display">{{ $attempt->siswa?->name ?? auth('siswa')->user()->name }}</div>
</div>

<div class="eval-badge" style="background: {{ $evalStyle['bg'] }}; color: {{ $evalStyle['color'] }};">
    {{ $evalStyle['text'] }}
</div>

<div class="score-total-box">
    <label style="font-size:0.8rem; font-weight:800; text-transform:uppercase; color:var(--text-muted); display:block; margin-bottom:8px;">
        Total Score &middot; Rating: {{ $predikatLabel }}
    </label>
    <div class="score-val">{{ number_format($scoreOn100, 1) }} / 100</div>
</div>

<div class="score-cards-grid" style="grid-template-columns: 1fr;">
    @foreach ($answers as $answer)
        <div class="score-card" style="text-align: left; border-top: 6px solid {{ $answer->is_correct ? 'var(--primary-lime)' : 'var(--primary-pink)' }};">
            <label>{{ $answer->soal->narasi }}</label>
            <p style="font-size: 0.9rem; margin-top: 6px; color: var(--text-main);">
                Your answer: <strong>{{ $answer->opsi->opsi_text }}</strong>
                {{ $answer->is_correct ? '✅' : '❌' }}
            </p>
        </div>
    @endforeach
</div>

<p style="color: var(--text-muted); font-size: 0.8rem; text-align: center;">
    This score is for mapping initial understanding and does not count toward your report card.
</p>

    </div>
</div>
@endsection
