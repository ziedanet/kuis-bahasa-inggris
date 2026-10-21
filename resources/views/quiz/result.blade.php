@extends('layouts.travel-quest')

@section('title', 'Travel Quest - Boarding Pass')

@section('route')
    @include('travel-quest._route', ['step' => 3])
@endsection

@section('content')
@php
    // Display-only English labels. The underlying $predikat string stays in
    // Indonesian (shared with the admin panel / Leaderboard).
    $predikatLabels = [
        'Sangat Baik' => 'Excellent',
        'Baik' => 'Good',
        'Cukup' => 'Fair',
        'Perlu Bimbingan' => 'Needs Improvement',
    ];
    $predikatLabel = $predikatLabels[$predikat] ?? $predikat;

    $message = match ($predikat) {
        'Sangat Baik' => 'Outstanding! Your tourism vocabulary is top-notch.',
        'Baik' => 'Great job! Your tourism vocabulary is pretty solid.',
        'Cukup' => 'Not bad! A bit more practice and you will nail it.',
        default => 'Nice try! Keep practicing your tourism vocabulary.',
    };

    $fmt = fn (?int $s) => $s === null ? '--:--' : sprintf('%02d:%02d', intdiv($s, 60), $s % 60);
    $leg = fn (string $type) => $sessionStats[$type]['correct'] . '/' . $sessionStats[$type]['max'];
@endphp
<section class="screen in final-wrap">
    <article class="pass" id="boarding-pass" tabindex="-1">
        <div class="pass-main">
            <p class="eyebrow">Boarding pass · Travel Quest</p>
            <h2>{{ $group->name }}</h2>
            <p class="pass-date">{{ ($group->finished_at ?? now())->format('F j, Y') }}</p>

            <dl class="legs">
                <div>
                    <dt>Session 1 · Adjectives</dt>
                    <dd>{{ $leg('adjectives') }}</dd>
                </div>
                <div>
                    <dt>Session 2 · Verbs</dt>
                    <dd>{{ $leg('verbs') }}</dd>
                </div>
                <div>
                    <dt>Total time</dt>
                    <dd>{{ $fmt($durationSeconds) }}</dd>
                </div>
            </dl>

            <p class="pass-msg">{{ $message }}</p>
            <p class="pass-place">Your teacher will announce the class ranking.</p>
        </div>

        <div class="pass-stub">
            <div>
                <div class="total">{{ rtrim(rtrim(number_format($scoreOn100, 1), '0'), '.') }}<small>/100</small></div>
                <div class="total-lbl">Total score</div>
            </div>
            <div class="rank-stamp">{{ $predikatLabel }}</div>
        </div>
    </article>

    <div class="final-actions">
        <a href="{{ route('quiz.home') }}" class="btn btn-sun">
            Back to home <svg class="ico" aria-hidden="true"><use href="#i-arrow"/></svg>
        </a>
    </div>
</section>
@endsection
