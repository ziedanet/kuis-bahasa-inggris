@extends('layouts.app')

@section('title', 'Find the Word - ' . $image->title)

@section('content')
<div class="container">
    <div class="quiz-box" x-data="wordPicker()">

    <div class="q-header">
        <span class="q-tag" style="background: {{ $image->session_type === 'adjectives' ? 'var(--primary-pink)' : 'var(--primary-cyan)' }};">
            {{ $image->session_type === 'adjectives' ? 'ADJECTIVE 🏷️' : 'VERB ⚡' }}
        </span>
        <span class="q-counter">Gambar {{ $currentStep }} dari {{ $totalImages }}</span>
    </div>

    <div class="q-content">
        <div class="img-wrapper">
            @if ($image->imageUrl())
                <img src="{{ $image->imageUrl() }}" alt="{{ $image->title }}">
            @else
                Gambar belum diunggah
            @endif
        </div>
        <div class="q-info">
            <h3>{{ $image->title }}</h3>
            <p>{{ $image->instruction }}</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('quiz.answer', $quiz) }}">
        @csrf
        <input type="hidden" name="quiz_image_id" value="{{ $image->id }}">

        <div class="instruction-badge">
            <span><i class="fa-solid fa-check-double"></i> Pilih tepat <strong style="color: var(--ink-yellow);">5 KATA</strong> yang sesuai:</span>
            <span style="color: var(--ink-pink);"><span x-text="selected.length"></span> / 5</span>
        </div>

        <div class="word-grid">
            @foreach ($words as $word)
                <label class="word-chip" :class="selected.includes({{ $word->id }}) && 'selected'">
                    <input type="checkbox" name="word_ids[]" value="{{ $word->id }}"
                           x-on:change="toggle({{ $word->id }}, $event)"
                           :checked="selected.includes({{ $word->id }})"
                           style="display:none;">
                    {{ $word->word }}
                </label>
            @endforeach
        </div>

        <div class="nav-controls">
            <button type="submit" class="btn btn-next" :disabled="selected.length !== 5">
                Selanjutnya <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </form>
    </div>
</div>

<script>
    function wordPicker() {
        return {
            selected: [],
            toggle(id, event) {
                if (event.target.checked) {
                    if (this.selected.length >= 5) {
                        event.target.checked = false;
                        return;
                    }
                    this.selected.push(id);
                } else {
                    this.selected = this.selected.filter(w => w !== id);
                }
            },
        };
    }
</script>
@endsection
