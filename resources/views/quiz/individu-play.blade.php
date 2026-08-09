@extends('layouts.app')

@section('title', $quiz->title . ' - Find the Word')

@section('content')
<div class="container">
    <div class="quiz-box">

    <div class="q-header">
        <span class="q-tag" style="background: var(--primary-lime);">SOAL INDIVIDU 📝</span>
        <span class="q-counter">Soal {{ $currentStep }} dari {{ $totalSoal }}</span>
    </div>

    <div class="q-content" style="{{ $soal->gambarUrl() ? '' : 'grid-template-columns: 1fr;' }}">
        @if ($soal->gambarUrl())
            <div class="img-wrapper">
                <img src="{{ $soal->gambarUrl() }}" alt="Gambar soal">
            </div>
        @endif
        <div class="q-info" style="justify-content: center;">
            <h3>{{ $soal->narasi }}</h3>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('quiz.individu.answer', $quiz) }}">
        @csrf
        <input type="hidden" name="soal_id" value="{{ $soal->id }}">

        <div class="instruction-badge">
            <span><i class="fa-solid fa-circle-check"></i> Pilih <strong style="color: var(--ink-yellow);">1 JAWABAN</strong> yang paling tepat:</span>
        </div>

        <div class="word-grid" style="grid-template-columns: 1fr;">
            @foreach ($opsiList as $opsi)
                <label class="word-chip" style="text-align: left; display: flex; align-items: center; gap: 12px; cursor: pointer;">
                    <input type="radio" name="opsi_id" value="{{ $opsi->id }}" required style="width: 18px; height: 18px; accent-color: var(--primary-lime); flex-shrink: 0;">
                    {{ $opsi->opsi_text }}
                </label>
            @endforeach
        </div>

        <div class="nav-controls">
            <button type="submit" class="btn btn-next">
                Selanjutnya <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </form>
    </div>
</div>
@endsection
