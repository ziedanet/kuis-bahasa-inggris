@extends('layouts.travel-quest')

@section('title', $quiz->title . ' - Start Quiz')

@section('route')
    @include('travel-quest._route', ['step' => 0])
@endsection

@section('content')
@php
    $titleWords = preg_split('/\s+/', trim($quiz->title));
    $lastWord = array_pop($titleWords);
    $firstWords = implode(' ', $titleWords);
@endphp
<section class="screen in welcome">
    <div class="welcome-copy">
        <p class="eyebrow">English class · Team game</p>
        <h1>{{ $firstWords }} <span>{{ $lastWord }}</span></h1>
        <p class="lead">{{ $quiz->narration ?? $quiz->description }}</p>

        <form class="checkin" method="POST" action="{{ route('quiz.start', $quiz) }}">
            @csrf
            <label for="team-name">Team name</label>
            <input type="text" id="team-name" value="{{ $kelompokAccount->name }}" readonly>
            @if ($kelompokAccount->kelas)
                <p class="sync">Class: {{ $kelompokAccount->kelas->name }}</p>
            @endif
            @if ($errors->any())
                <p class="err">{{ $errors->first() }}</p>
            @endif
            <button type="submit" class="btn btn-sun">
                Start the journey <svg class="ico" aria-hidden="true"><use href="#i-arrow"/></svg>
            </button>
        </form>

        <div class="gates">
            <h2 class="gates-title">Your flight plan</h2>
            <ul>
                <li>
                    <span class="gate-id">Session 1</span>
                    <strong>Adjectives</strong>
                    <span class="gate-note">{{ $adjCount }} {{ \Illuminate\Support\Str::plural('photo', $adjCount) }}</span>
                </li>
                <li>
                    <span class="gate-id">Session 2</span>
                    <strong>Verbs</strong>
                    <span class="gate-note">{{ $verbCount }} {{ \Illuminate\Support\Str::plural('photo', $verbCount) }}</span>
                </li>
            </ul>
        </div>

        <p class="rules">For each photo, pick exactly 5 of the 10 words. Your final score is out of 100, and it does not count toward your report card.</p>
    </div>

    <div class="welcome-art">
        @include('travel-quest._art', ['artId' => 'heroArt'])
    </div>
</section>
@endsection
