@extends('layouts.travel-quest')

@section('title', 'Travel Quest - ' . $image->title)

@section('hide-teacher', '1')

@section('route')
    @include('travel-quest._route', ['step' => $sessionNo])
@endsection

@section('content')
@php
    $isReview = $mode === 'review';
    $isAdj = $image->session_type === 'adjectives';
    $correctCount = $answer?->correct_count ?? 0;
    $headline = match (true) {
        $correctCount === 5 => 'Perfect landing!',
        $correctCount >= 3 => 'Nice flying!',
        $correctCount >= 1 => 'Keep going!',
        default => 'Turbulence ahead!',
    };
    $nextLabel = ! $hasNext
        ? 'See your boarding pass'
        : ($nextNewSession ? 'Start Session 2' : 'Next photo');
@endphp
<section class="screen in play-grid">
    <figure class="postcard">
        <div class="photo">
            @if ($image->imageUrl())
                <img src="{{ $image->imageUrl() }}" alt="{{ $image->title }}">
            @else
                <div class="ph">
                    @include('travel-quest._art', ['artId' => 'phArt'])
                    <span class="ph-tag">Photo coming soon</span>
                </div>
            @endif
        </div>
        <div class="postmark" aria-hidden="true"><small>Session</small><b>{{ $sessionNo }}</b></div>
        <figcaption>
            <p class="place">{{ $image->title }}</p>
            @if ($image->lokasi)
                <p class="loc">{{ $image->lokasi }}</p>
            @endif
        </figcaption>
    </figure>

    <div class="panel">
        <p class="eyebrow">Session {{ $sessionNo }} · {{ $isAdj ? 'Adjectives' : 'Verbs' }}</p>
        <h2>{{ $isAdj ? 'Pick 5 words that describe this place' : 'Pick 5 things you can do here' }}</h2>
        <p class="instr">{{ $image->instruction }}</p>
        <p class="tip">Photo {{ $currentStep }} of {{ $totalImages }}. Look closely before you choose.</p>

        @if ($errors->any())
            <p class="err" style="margin-top: 12px;">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="{{ route('quiz.answer', $quiz) }}" id="answer-form">
            @csrf
            <input type="hidden" name="quiz_image_id" value="{{ $image->id }}">
            <div id="picked-inputs"></div>

            <div class="meta">
                <span class="timer"><svg class="ico" aria-hidden="true"><use href="#i-clock"/></svg><span id="timer">00:00</span></span>
                <span class="pips" aria-hidden="true">
                    @for ($i = 0; $i < 5; $i++)
                        <span class="pip {{ $isReview && $i < count($selectedIds) ? 'on' : '' }}"></span>
                    @endfor
                </span>
            </div>

            <div class="words" id="words">
                @foreach ($words as $word)
                    @if ($isReview)
                        @php
                            $picked = in_array($word->id, $selectedIds, true);
                            $state = $picked ? ($word->is_correct ? 'ok' : 'bad') : ($word->is_correct ? 'missed' : 'dim');
                        @endphp
                        <button type="button" class="word {{ $state }}" disabled aria-pressed="{{ $picked ? 'true' : 'false' }}">
                            <span class="mark" aria-hidden="true"><svg class="ico"><use href="{{ $state === 'bad' ? '#i-x' : '#i-check' }}"/></svg></span>
                            <span class="txt">{{ $word->word }}</span>
                            @if ($state === 'ok') <span class="sr">(Correct)</span>
                            @elseif ($state === 'bad') <span class="sr">(Incorrect)</span>
                            @elseif ($state === 'missed') <span class="sr">(Correct answer you missed)</span>
                            @endif
                        </button>
                    @else
                        <button type="button" class="word" data-id="{{ $word->id }}" aria-pressed="false">
                            <span class="mark" aria-hidden="true"><svg class="ico"><use href="#i-check"/></svg></span>
                            <span class="txt">{{ $word->word }}</span>
                        </button>
                    @endif
                @endforeach
            </div>

            @unless ($isReview)
                <div class="actions">
                    <span class="count" id="count">Choose 5 more words</span>
                    <button type="submit" class="btn btn-sun" id="check-btn" disabled>
                        Check answers <svg class="ico" aria-hidden="true"><use href="#i-check"/></svg>
                    </button>
                </div>
            @endunless
        </form>

        @if ($isReview)
            <div class="result" id="result" tabindex="-1">
                <div class="stamp"><span class="n">{{ $correctCount }}</span><span class="of">OUT OF 5</span></div>
                <div>
                    <h3>{{ $headline }}</h3>
                    <p>You found {{ $correctCount }} of 5 correct {{ $isAdj ? 'adjectives' : 'verbs' }} for this photo.</p>
                    <p class="legend">Green = correct · Red = not quite · Dashed = missed</p>
                </div>
                <a href="{{ route('quiz.play', $quiz) }}" class="btn btn-sun next">
                    {{ $nextLabel }} <svg class="ico" aria-hidden="true"><use href="#i-arrow"/></svg>
                </a>
            </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
    const timerEl = document.getElementById('timer');
    const fmt = (s) => String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0');

    @if ($isReview)
        // Timer sudah berhenti: tampilkan waktu saat jawaban dicek.
        let saved = 0;
        try { saved = parseInt(sessionStorage.getItem('tq_time') || '0', 10) || 0; } catch (e) {}
        timerEl.textContent = fmt(saved);
        const result = document.getElementById('result');
        if (result) { result.focus({ preventScroll: true }); result.scrollIntoView({ block: 'nearest', behavior: 'smooth' }); }
    @else
        const MAX = 5;
        const form = document.getElementById('answer-form');
        const holder = document.getElementById('picked-inputs');
        const checkBtn = document.getElementById('check-btn');
        const countEl = document.getElementById('count');
        const pips = document.querySelectorAll('.pip');
        const buttons = document.querySelectorAll('#words .word');
        let picked = [];
        let seconds = 0;
        const tick = setInterval(() => { seconds++; timerEl.textContent = fmt(seconds); }, 1000);

        function render() {
            buttons.forEach(b => b.setAttribute('aria-pressed', picked.includes(b.dataset.id) ? 'true' : 'false'));
            pips.forEach((p, i) => p.classList.toggle('on', i < picked.length));
            checkBtn.disabled = picked.length !== MAX;
            const left = MAX - picked.length;
            countEl.textContent = left === 0 ? 'Ready! Check your answers.' : 'Choose ' + left + ' more word' + (left === 1 ? '' : 's');
            holder.innerHTML = '';
            picked.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden'; input.name = 'word_ids[]'; input.value = id;
                holder.appendChild(input);
            });
        }

        buttons.forEach(btn => btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            if (picked.includes(id)) {
                picked = picked.filter(x => x !== id);
            } else if (picked.length >= MAX) {
                btn.classList.remove('shake'); void btn.offsetWidth; btn.classList.add('shake');
                tqToast('You already chose 5 words. Tap a chosen word to swap it.');
                return;
            } else {
                picked.push(id);
            }
            render();
        }));

        form.addEventListener('submit', (e) => {
            if (picked.length !== MAX) { e.preventDefault(); return; }
            clearInterval(tick);
            try { sessionStorage.setItem('tq_time', String(seconds)); } catch (err) {}
        });

        render();
    @endif
})();
</script>
@endpush
