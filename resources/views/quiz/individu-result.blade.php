@extends('layouts.assessment')

@section('title', $quiz->title . ' - Result')
@section('subtitle', $quiz->title)

@section('styles')
        .ring-card { text-align: center; }
        .ring { position: relative; width: 168px; height: 168px; margin: 0 auto 14px; }
        .ring svg { transform: rotate(-90deg); }
        .ring .num { position: absolute; inset: 0; display: grid; place-content: center; text-align: center; }
        .ring .num b { font-size: 2.6rem; line-height: 1; }
        .ring .num span { font-size: .78rem; color: var(--muted); }
        .pill { display: inline-block; padding: 10px 18px; border-radius: 999px; font-weight: 800; font-size: .95rem; }
        .pill.ok { background: #dcfce7; color: #166534; }
        .pill.bad { background: #fee2e2; color: var(--bad); }
        .who-line { color: var(--muted); font-size: .9rem; margin-top: 12px; }
        .who-line b { color: var(--ink); }
        .review h2 { color: var(--violet); font-size: 1.05rem; margin-bottom: 14px; }
        .rv { display: flex; gap: 12px; padding: 14px 0; border-top: 1px solid var(--line); }
        .rv:first-of-type { border-top: 0; }
        .rv .mark { flex: none; width: 24px; font-size: 1.15rem; }
        .rv .q { font-weight: 800; font-size: .95rem; margin-bottom: 4px; }
        .rv .a { font-size: .86rem; }
        .rv .a b.ok { color: var(--ok); } .rv .a b.bad { color: var(--bad); }
        .actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px; }
        .actions form { display: contents; }
        .actions .btn { width: 100%; }
        @media (max-width: 520px) { .actions { grid-template-columns: 1fr; } }
@endsection

@section('content')
@php
    $circumference = 2 * M_PI * 76;
    $dash = $circumference * max(0, min(100, $scoreOn100)) / 100;
    $color = $passed ? 'var(--ok)' : 'var(--bad)';
    $scoreLabel = rtrim(rtrim(number_format($scoreOn100, 1), '0'), '.');
    $letter = fn ($soal, $opsiId) => chr(65 + max(0, $soal->opsi->values()->search(fn ($o) => (int) $o->id === (int) $opsiId)));
@endphp

<div class="card ring-card">
    <div class="ring">
        <svg width="168" height="168" viewBox="0 0 168 168" aria-hidden="true">
            <circle cx="84" cy="84" r="76" fill="none" stroke="#efeaf8" stroke-width="12"/>
            <circle cx="84" cy="84" r="76" fill="none" stroke="{{ $color }}" stroke-width="12" stroke-linecap="round"
                    stroke-dasharray="{{ $dash }} {{ $circumference }}"/>
        </svg>
        <div class="num"><b>{{ $scoreLabel }}</b><span>out of 100</span></div>
    </div>

    <span class="pill {{ $passed ? 'ok' : 'bad' }}">
        {{ $passed ? '🎉 Passed — great work!' : '📘 Not passed yet — needs review' }}
    </span>
    <p class="who-line">Name: <b>{{ $attempt->displayName() }}</b> &nbsp;&middot;&nbsp; Class: <b>{{ $attempt->displayKelas() }}</b></p>
</div>

<div class="card review">
    <h2>&#128203; Answer Review</h2>
    @forelse ($answers as $i => $answer)
        @php $soal = $answer->soal; $correct = $soal->opsi->firstWhere('is_correct', true); @endphp
        <div class="rv">
            <span class="mark" aria-hidden="true">{{ $answer->is_correct ? '✅' : '❌' }}</span>
            <div>
                <div class="q">{{ $i + 1 }}. {{ $soal->narasi }}</div>
                <div class="a">
                    Your answer:
                    @if ($answer->opsi)
                        <b class="{{ $answer->is_correct ? 'ok' : 'bad' }}">{{ $letter($soal, $answer->opsi_id) }}. {{ $answer->opsi->opsi_text }}</b>
                    @else
                        <b class="bad">-</b>
                    @endif
                </div>
                @unless ($answer->is_correct)
                    @if ($correct)
                        <div class="a">Correct answer: <b class="ok">{{ $letter($soal, $correct->id) }}. {{ $correct->opsi_text }}</b></div>
                    @endif
                @endunless
            </div>
        </div>
    @empty
        <p class="note">No answers recorded.</p>
    @endforelse
</div>

<div class="actions no-print">
    <form method="POST" action="{{ route('quiz.individu.retry', $quiz) }}">
        @csrf
        <button type="submit" class="btn btn-soft">&#8634; Try Again</button>
    </form>
    <button type="button" class="btn btn-grad" onclick="window.print()">&#128424; Print / Save Result</button>
</div>
<form method="POST" action="{{ route('quiz.individu.finish', $quiz) }}" class="no-print">
    @csrf
    <button type="submit" class="btn btn-soft" style="width: 100%;">&#10003; Finish</button>
</form>
@endsection
