@extends('layouts.app')

@section('title', 'Find the Word - Result')

@section('content')
@php
    // Display-only English labels. The underlying $predikat string stays in
    // Indonesian (shared with the admin panel / Leaderboard), so we only
    // translate what's shown on this public page.
    $predikatLabels = [
        'Sangat Baik' => 'Excellent',
        'Baik' => 'Good',
        'Cukup' => 'Fair',
        'Perlu Bimbingan' => 'Needs Improvement',
    ];
    $predikatLabel = $predikatLabels[$predikat] ?? $predikat;

    $evalStyle = match ($predikat) {
        'Sangat Baik' => ['bg' => 'var(--primary-lime)', 'color' => '#000', 'text' => '🔥 GG WP! Your tourism vocabulary is outstanding!'],
        'Baik' => ['bg' => 'var(--primary-cyan)', 'color' => '#000', 'text' => '😎 Nice! Your tourism vocabulary knowledge is pretty solid!'],
        'Cukup' => ['bg' => 'var(--primary-yellow)', 'color' => '#000', 'text' => '💪 Not bad! A bit more practice and you\'ll nail it!'],
        default => ['bg' => 'var(--primary-pink)', 'color' => '#fff', 'text' => '🌱 Nice try! Keep practicing your tourism vocabulary!'],
    };
@endphp

<div class="container">
    <div class="quiz-box">

<div class="result-header">
    <span class="welcome-badge" style="background: var(--primary-lime);">🎉 Quiz Complete!</span>
    <h2>Results & Evaluation</h2>
    <div class="group-name-display">Group: {{ $group->name }}</div>
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

<div class="score-cards-grid">
    @foreach ($answers as $answer)
        <div class="score-card {{ $answer->image->session_type === 'adjectives' ? 'score-card-adj' : 'score-card-verb' }}">
            <label>{{ $answer->image->title }}</label>
            <div class="score-val" style="color: {{ $answer->image->session_type === 'adjectives' ? 'var(--ink-pink)' : 'var(--ink-cyan)' }};">
                {{ $answer->correct_count }}/5
            </div>
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">{{ number_format($answer->score, 1) }} pts</p>
        </div>
    @endforeach
</div>

<p style="color: var(--text-muted); font-size: 0.8rem; text-align: center;">
    This score is for mapping initial understanding and does not count toward your report card.
</p>

    </div>
</div>
@endsection
