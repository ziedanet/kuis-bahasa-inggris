@extends('layouts.assessment')

@section('title', 'Student Reflection')
@section('heading', 'Student Reflection')
@section('subtitle', 'Refleksi Peserta Didik')

@section('styles')
        .intro { color: var(--muted); font-size: .92rem; margin-bottom: 18px; line-height: 1.55; }
        textarea.input { min-height: 96px; resize: vertical; line-height: 1.5; }
        .hint { display: block; font-weight: 600; color: var(--muted); font-size: .8rem; margin-top: 2px; }
        .stmt { border: 1px solid var(--line); border-radius: 16px; padding: 14px 16px; margin-bottom: 12px; background: var(--soft); }
        .stmt p { font-weight: 700; font-size: .95rem; margin-bottom: 8px; }
        .stars { display: inline-flex; flex-direction: row-reverse; gap: 4px; }
        .stars input { position: absolute; opacity: 0; pointer-events: none; }
        .stars label { font-size: 2rem; line-height: 1; cursor: pointer; color: #d4cfe6; transition: transform .1s, color .1s; }
        .stars label:hover, .stars label:hover ~ label, .stars input:checked ~ label { color: #f59e0b; }
        .stars label:hover { transform: scale(1.12); }
        .stars input:focus-visible + label { outline: 3px solid var(--violet); outline-offset: 2px; border-radius: 6px; }
        .stars-scale { display: flex; justify-content: space-between; font-size: .72rem; color: var(--muted); margin-top: 4px; max-width: 220px; }
        .wiz-step { display: none; }
        .wiz-step.active { display: block; animation: wizin .2s ease; }
        .js-off .wiz-step { display: block; }
        @keyframes wizin { from { opacity: 0; transform: translateX(10px); } to { opacity: 1; transform: none; } }
        .wiz-top { display: flex; justify-content: space-between; font-size: .82rem; font-weight: 700; color: var(--muted); }
        .wiz-bar { height: 8px; border-radius: 999px; background: var(--line); overflow: hidden; margin: 4px 0 18px; }
        .wiz-bar i { display: block; height: 100%; width: 0; border-radius: 999px; background: linear-gradient(90deg, var(--violet), var(--rose)); transition: width .25s; }
        .wiz-q { font-size: 1.15rem; font-weight: 800; line-height: 1.4; margin-bottom: 14px; }
        .stars.big label { font-size: 3rem; }
        .stars-scale.big { max-width: 300px; font-size: .8rem; margin-bottom: 6px; }
        .wiz-nav { display: flex; gap: 10px; margin-top: 8px; }
        .wiz-nav .btn { flex: 1; }
        .wiz-nav .btn-soft { flex: 0 0 auto; }
        .wiz-err { color: #991b1b; font-weight: 700; font-size: .88rem; margin: 0 0 10px; min-height: 1.2em; }
        .thanks { text-align: center; padding: 10px 0; }
        .thanks .big { font-size: 3rem; }
        .thanks h2 { margin: 6px 0; }
@endsection

@section('content')
@if (session('submitted'))
    <div class="card thanks">
        <div class="big">&#127881;</div>
        <h2>Thank you!</h2>
        <p class="note">Your reflection has been submitted. Terima kasih sudah mengisi refleksi.</p>
        <a href="{{ route('refleksi.create') }}" class="btn btn-soft" style="margin-top: 8px;">Fill in another reflection</a>
        <a href="{{ route('quiz.home') }}" class="btn btn-grad" style="margin-top: 10px;">&#8962; Back to Home</a>
    </div>
@else
<div class="card">
    @if ($errors->any())
        <div class="err">{{ $errors->first() }}</div>
    @endif

    <div class="wiz-top"><span id="wiz-label">Step 1 of 6</span><span id="wiz-pct">0%</span></div>
    <div class="wiz-bar"><i id="wiz-fill"></i></div>

    <form method="POST" action="{{ route('refleksi.store') }}" id="wiz-form" novalidate>
        @csrf

        {{-- Step 1: data diri --}}
        <div class="wiz-step" data-fields="participant_name,kelas_id">
            <p class="intro">Fill in your name and class first.<br><em>Isi nama dan kelas terlebih dahulu.</em></p>

            <label class="field" for="participant_name">&#128587; Full Name</label>
            <input type="text" class="input" id="participant_name" name="participant_name"
                   value="{{ old('participant_name') }}" placeholder="e.g. Muhammad Rizky" maxlength="150" required autocomplete="name">

            <label class="field" for="kelas_id">&#127979; Class</label>
            <select class="input" id="kelas_id" name="kelas_id" required>
                <option value="">Choose your class</option>
                @foreach ($kelasList as $kelas)
                    <option value="{{ $kelas->id }}" @selected((string) old('kelas_id') === (string) $kelas->id)>{{ $kelas->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Step 2-4: tiap pernyataan satu langkah --}}
        @foreach ($statements as $field => $text)
            <div class="wiz-step" data-fields="{{ $field }}">
                <p class="intro">Rate this statement with stars (&#11088; 1&ndash;5).<br><em>Berikan penilaian bintang.</em></p>
                <p class="wiz-q">{{ $text }}</p>
                <div class="stars big" role="radiogroup" aria-label="{{ $text }}">
                    @for ($n = 5; $n >= 1; $n--)
                        <input type="radio" name="{{ $field }}" id="{{ $field }}_{{ $n }}" value="{{ $n }}" @checked((int) old($field) === $n) required>
                        <label for="{{ $field }}_{{ $n }}" title="{{ $n }} star{{ $n > 1 ? 's' : '' }}">&#9733;</label>
                    @endfor
                </div>
                <div class="stars-scale big"><span>1 = lowest</span><span>5 = highest</span></div>
            </div>
        @endforeach

        {{-- Step 5: belajar --}}
        <div class="wiz-step" data-fields="learned">
            <label class="field" for="learned" style="font-size:1.05rem;">What I learned today is
                <span class="hint">Hari ini saya belajar tentang:</span></label>
            <textarea class="input" id="learned" name="learned" maxlength="2000" required>{{ old('learned') }}</textarea>
        </div>

        {{-- Step 6: membingungkan --}}
        <div class="wiz-step" data-fields="confusing">
            <label class="field" for="confusing" style="font-size:1.05rem;">What I still find confusing is
                <span class="hint">Hal yang masih membingungkan adalah: (optional / boleh kosong)</span></label>
            <textarea class="input" id="confusing" name="confusing" maxlength="2000">{{ old('confusing') }}</textarea>
        </div>

        <p class="wiz-err" id="wiz-err" role="alert"></p>

        <div class="wiz-nav">
            <button type="button" class="btn btn-soft" id="wiz-back" hidden>&larr; Back</button>
            <button type="button" class="btn btn-grad" id="wiz-next">Next &rarr;</button>
            <button type="submit" class="btn btn-grad" id="wiz-submit" hidden>&#128228; Submit Reflection</button>
        </div>
    </form>

    <script>
    (function () {
        const form = document.getElementById('wiz-form');
        const steps = Array.from(form.querySelectorAll('.wiz-step'));
        const back = document.getElementById('wiz-back');
        const next = document.getElementById('wiz-next');
        const submit = document.getElementById('wiz-submit');
        const err = document.getElementById('wiz-err');
        const label = document.getElementById('wiz-label');
        const pct = document.getElementById('wiz-pct');
        const fill = document.getElementById('wiz-fill');
        const messages = {
            participant_name: 'Please enter your full name.',
            kelas_id: 'Please choose your class.',
            learned: 'Please tell us what you learned today.',
        };
        const serverErrorField = @json($errors->any() ? array_key_first($errors->messages()) : null);
        let current = 0;

        function valid(step) {
            const fields = step.dataset.fields.split(',');
            for (const name of fields) {
                const els = form.querySelectorAll('[name="' + name + '"]');
                const first = els[0];
                if (!first || !first.required) continue;
                const ok = first.type === 'radio'
                    ? Array.from(els).some((el) => el.checked)
                    : first.value.trim() !== '';
                if (!ok) return messages[name] || 'Please choose a rating (1-5 stars).';
            }
            return '';
        }

        function show(i) {
            current = Math.max(0, Math.min(steps.length - 1, i));
            steps.forEach((el, idx) => el.classList.toggle('active', idx === current));
            const last = current === steps.length - 1;
            back.hidden = current === 0;
            next.hidden = last;
            submit.hidden = !last;
            err.textContent = '';
            label.textContent = 'Step ' + (current + 1) + ' of ' + steps.length;
            const p = Math.round(current / steps.length * 100);
            pct.textContent = p + '%';
            fill.style.width = p + '%';
            const focusable = steps[current].querySelector('input:not([type=radio]), select, textarea');
            if (focusable) focusable.focus({ preventScroll: true });
        }

        next.addEventListener('click', () => {
            const msg = valid(steps[current]);
            if (msg) { err.textContent = msg; return; }
            show(current + 1);
        });
        back.addEventListener('click', () => show(current - 1));

        // Enter di kolom teks satu baris = lanjut, bukan kirim form.
        form.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && e.target.tagName === 'INPUT' && e.target.type !== 'radio') {
                e.preventDefault();
                if (current < steps.length - 1) next.click(); else submit.click();
            }
        });

        // Cegah kirim kalau ada langkah yang belum valid: lompat ke langkah itu.
        form.addEventListener('submit', (e) => {
            for (let i = 0; i < steps.length; i++) {
                const msg = valid(steps[i]);
                if (msg) { e.preventDefault(); show(i); err.textContent = msg; return; }
            }
        });

        let start = 0;
        if (serverErrorField) {
            const idx = steps.findIndex((el) => el.dataset.fields.split(',').includes(serverErrorField));
            if (idx >= 0) start = idx;
        }
        show(start);
    })();
    </script>
</div>
@endif
@endsection
