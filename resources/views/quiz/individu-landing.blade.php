@extends('layouts.assessment')

@section('title', $quiz->title . ' - Individual Assessment')
@section('subtitle', $quiz->title)

@section('content')
<div class="card">
    @if ($errors->any())
        <div class="err">{{ $errors->first() }}</div>
    @endif

    @if ($quiz->narration)
        <p style="color: var(--muted); font-size: .92rem; margin-bottom: 18px;">{{ $quiz->narration }}</p>
    @endif

    <form method="POST" action="{{ route('quiz.individu.start', $quiz) }}">
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

        <button type="submit" class="btn btn-grad">&#128640; Start Assessment</button>
    </form>
</div>

<p class="note">{{ $totalSoal }} multiple-choice {{ \Illuminate\Support\Str::plural('question', $totalSoal) }} &middot; your score appears instantly after submitting</p>
@endsection
