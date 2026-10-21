@extends('layouts.assessment')

@section('title', $quiz->title . ' - Question ' . $currentStep)
@section('subtitle', $quiz->title)

@section('styles')
        .who { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px; }
        .who div { background: var(--soft); border-radius: 14px; padding: 8px 14px; }
        .who small { display: block; color: var(--muted); font-size: .72rem; }
        .who b { font-size: .92rem; }
        .progress { height: 8px; border-radius: 999px; background: var(--line); overflow: hidden; margin: 4px 0 16px; }
        .progress i { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--violet), var(--rose)); }
        .step { display: flex; justify-content: space-between; font-size: .82rem; font-weight: 700; color: var(--muted); }
        .passage { background: #f1e9fd; border: 1px solid #e0d2fa; border-radius: 22px; padding: 20px 22px; margin-bottom: 18px; }
        .passage .tag { font-size: .7rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: var(--violet); }
        .passage h2 { font-size: 1.15rem; color: var(--violet); margin: 4px 0 8px; }
        .passage p { font-size: .9rem; line-height: 1.7; white-space: pre-line; }
        .q-head { display: flex; gap: 12px; align-items: flex-start; margin-bottom: 16px; }
        .q-num { flex: none; min-width: 30px; height: 30px; border-radius: 9px; background: linear-gradient(135deg, var(--violet), var(--rose)); color: #fff; display: grid; place-items: center; font-weight: 800; font-size: .85rem; }
        .q-text { font-weight: 800; line-height: 1.45; }
        .q-img { width: 100%; max-height: 260px; object-fit: cover; border-radius: 16px; margin-bottom: 16px; border: 1px solid var(--line); }
        .opt { display: flex; align-items: center; gap: 12px; padding: 13px 14px; border: 1px solid var(--line); border-radius: 14px; margin-bottom: 10px; cursor: pointer; background: #fff; transition: border-color .12s, background .12s; }
        .opt:hover { border-color: var(--violet); }
        .opt input { position: absolute; opacity: 0; pointer-events: none; }
        .opt .letter { flex: none; width: 28px; height: 28px; border-radius: 50%; border: 1px solid #c8b8f0; color: var(--violet); display: grid; place-items: center; font-size: .78rem; font-weight: 800; }
        .opt:has(input:checked) { border-color: var(--violet); background: #f6f0ff; box-shadow: 0 0 0 2px rgba(124, 58, 237, .25); }
        .opt:has(input:checked) .letter { background: var(--violet); color: #fff; border-color: var(--violet); }
        .opt:has(input:focus-visible) { outline: 3px solid var(--violet); outline-offset: 2px; }
        .opt .txt { font-size: .95rem; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 10px; }
        .stats div { background: var(--soft); border-radius: 14px; padding: 8px 6px; text-align: center; }
        .stats b { display: block; font-size: 1.15rem; }
        .stats small { color: var(--muted); font-size: .7rem; font-weight: 700; }
        .stats .s-ok b { color: #15803d; } .stats .s-bad b { color: #dc2626; }
        .nav { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 16px; }
        .nav span { width: 30px; height: 30px; border-radius: 9px; display: grid; place-items: center; font-size: .78rem; font-weight: 800; border: 1px solid var(--line); color: var(--muted); background: #fff; }
        .nav .ok { background: #dcfce7; border-color: #16a34a; color: #15803d; }
        .nav .bad { background: #fee2e2; border-color: #dc2626; color: #dc2626; }
        .nav .now { border-color: var(--violet); color: var(--violet); box-shadow: 0 0 0 2px rgba(124, 58, 237, .25); }
        .legend { font-size: .72rem; color: var(--muted); margin: -8px 0 14px; }
        .opt.locked { cursor: default; }
        .opt.locked:hover { border-color: var(--line); }
        .opt.ok { border-color: #16a34a; background: #f0fdf4; }
        .opt.ok .letter { background: #16a34a; color: #fff; border-color: #16a34a; }
        .opt.bad { border-color: #dc2626; background: #fef2f2; }
        .opt.bad .letter { background: #dc2626; color: #fff; border-color: #dc2626; }
        .opt.missed { border: 2px dashed #16a34a; }
        .verdict { padding: 12px 14px; border-radius: 14px; font-weight: 800; margin-bottom: 14px; }
        .verdict.ok { background: #dcfce7; color: #15803d; }
        .verdict.bad { background: #fee2e2; color: #b91c1c; }
@endsection

@section('content')
<div class="who">
    <div><small>Name</small><b>{{ $attempt->displayName() }}</b></div>
    <div><small>Class</small><b>{{ $attempt->displayKelas() }}</b></div>
</div>

<div class="step"><span>Question {{ $currentStep }} of {{ $totalSoal }}</span><span>{{ $progress['answered'] }} answered &middot; {{ $progress['remaining'] }} left</span></div>
<div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="{{ $totalSoal }}" aria-valuenow="{{ $progress['answered'] }}">
    <i style="width: {{ $progress['answered'] / max(1, $totalSoal) * 100 }}%;"></i>
</div>

<div class="stats">
    <div class="s-ok"><b>{{ $progress['correct'] }}</b><small>Correct</small></div>
    <div class="s-bad"><b>{{ $progress['wrong'] }}</b><small>Wrong</small></div>
    <div><b>{{ $progress['remaining'] }}</b><small>Unanswered</small></div>
    <div><b>{{ rtrim(rtrim(number_format($progress['score'], 1), '0'), '.') }}</b><small>Score so far</small></div>
</div>
<div class="nav" aria-label="Question status">
    @foreach ($progress['items'] as $item)
        <span class="{{ $item['state'] }}" title="Question {{ $item['n'] }}: {{ ['ok' => 'correct', 'bad' => 'wrong', 'now' => 'current', 'todo' => 'unanswered'][$item['state']] }}">{{ $item['n'] }}</span>
    @endforeach
</div>
<div class="legend">Green = correct &middot; Red = wrong &middot; Outlined = current &middot; Grey = unanswered</div>

@if ($quiz->bacaan_teks)
    <div class="passage">
        <div class="tag">&#128214; Reading passage</div>
        @if ($quiz->bacaan_judul)<h2>{{ $quiz->bacaan_judul }}</h2>@endif
        <p>{{ $quiz->bacaan_teks }}</p>
    </div>
@endif

<form method="POST" action="{{ route('quiz.individu.answer', $quiz) }}" class="card">
    @csrf
    <input type="hidden" name="soal_id" value="{{ $soal->id }}">

    @if ($errors->any())
        <div class="err">{{ $errors->first() }}</div>
    @endif

    <div class="q-head">
        <span class="q-num">{{ $currentStep }}</span>
        <span class="q-text">{{ $soal->narasi }}</span>
    </div>

    @if ($soal->gambarUrl())
        <img src="{{ $soal->gambarUrl() }}" alt="Question image" class="q-img">
    @endif

    @if ($mode === 'review')
        <div class="verdict {{ $answer->is_correct ? 'ok' : 'bad' }}">
            {{ $answer->is_correct ? '✅ Correct! +1 point' : '❌ Wrong. The correct answer is marked in green.' }}
        </div>
        @foreach ($opsiList as $i => $opsi)
            @php
                $picked = (int) $answer->opsi_id === (int) $opsi->id;
                $cls = $picked ? ($opsi->is_correct ? 'ok' : 'bad') : ($opsi->is_correct ? 'ok missed' : '');
            @endphp
            <div class="opt locked {{ $cls }}">
                <span class="letter">{{ chr(65 + $i) }}</span>
                <span class="txt">{{ $opsi->opsi_text }}</span>
                @if ($picked)<span style="margin-left:auto; font-size:.75rem; font-weight:800;">Your answer</span>@endif
            </div>
        @endforeach

        <a href="{{ route('quiz.individu.play', $quiz) }}" class="btn btn-grad" style="margin-top: 8px; text-align:center; display:block;">
            {{ $isLast ? '🏁 See my results' : 'Next question →' }}
        </a>
    @else
        @foreach ($opsiList as $i => $opsi)
            <label class="opt">
                <input type="radio" name="opsi_id" value="{{ $opsi->id }}" required>
                <span class="letter">{{ chr(65 + $i) }}</span>
                <span class="txt">{{ $opsi->opsi_text }}</span>
            </label>
        @endforeach

        <button type="submit" class="btn btn-grad" style="margin-top: 8px;">Check answer</button>
    @endif
</form>
@endsection
