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

    <p class="intro">Rate each statement below with stars (&#11088; 1&ndash;5), then share what you learned today.<br>
        <em>Berikan penilaian bintang untuk setiap pernyataan.</em></p>

    <form method="POST" action="{{ route('refleksi.store') }}">
        @csrf

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

        <label class="field" for="learned">What I learned today is
            <span class="hint">Hari ini saya belajar tentang:</span></label>
        <textarea class="input" id="learned" name="learned" maxlength="2000" required>{{ old('learned') }}</textarea>

        <label class="field" for="confusing">What I still find confusing is
            <span class="hint">Hal yang masih membingungkan adalah:</span></label>
        <textarea class="input" id="confusing" name="confusing" maxlength="2000">{{ old('confusing') }}</textarea>

        <label class="field">&#11088; Your rating</label>
        @foreach ($statements as $field => $text)
            <div class="stmt">
                <p>{{ $text }}</p>
                <div class="stars" role="radiogroup" aria-label="{{ $text }}">
                    @for ($n = 5; $n >= 1; $n--)
                        <input type="radio" name="{{ $field }}" id="{{ $field }}_{{ $n }}" value="{{ $n }}" @checked((int) old($field) === $n) required>
                        <label for="{{ $field }}_{{ $n }}" title="{{ $n }} star{{ $n > 1 ? 's' : '' }}">&#9733;</label>
                    @endfor
                </div>
                <div class="stars-scale"><span>1 = lowest</span><span>5 = highest</span></div>
            </div>
        @endforeach

        <button type="submit" class="btn btn-grad" style="margin-top: 10px;">&#128228; Submit Reflection</button>
    </form>
</div>
@endif
@endsection
