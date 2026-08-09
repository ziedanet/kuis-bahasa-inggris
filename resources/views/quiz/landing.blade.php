@extends('layouts.app')

@section('title', $quiz->title . ' - Start Quiz')

@section('content')
<div class="container">
    <div class="quiz-box">
        <span class="welcome-badge">🔎 Diagnostic Assessment</span>
        <h2>{{ $quiz->title }}</h2>

        <div class="narration-box">
            {{ $quiz->narration ?? $quiz->description }}
        </div>

        @if ($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <div class="form-group">
            <label><i class="fa-solid fa-users"></i> Group</label>
            <div class="input-cream" style="cursor: default;">{{ $kelompokAccount->name }}</div>
        </div>
        @if ($kelompokAccount->kelas)
            <div class="form-group">
                <label><i class="fa-solid fa-chalkboard"></i> Class</label>
                <div class="input-cream" style="cursor: default;">{{ $kelompokAccount->kelas->name }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('quiz.start', $quiz) }}">
            @csrf
            <button type="submit" class="btn btn-start" style="width: 100%; justify-content: center; font-size: 1.1rem;">
                Start the Adventure <i class="fa-solid fa-arrow-right"></i>
            </button>
        </form>

        <p style="color: var(--text-muted); font-size: 0.8rem; margin-top: 18px; text-align: center;">
            The score is for mapping initial understanding and does not count toward your report card.
        </p>
    </div>
</div>
@endsection
