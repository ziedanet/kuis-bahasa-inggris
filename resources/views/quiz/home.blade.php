@extends('layouts.app')

@section('title', 'Find the Word - Tourism Vocabulary Quiz')

@section('content')
{{-- HERO --}}
<section class="hero">
    <div class="badge-hype">
        <i class="fa-solid fa-fire"></i> Diagnostic Assessment
    </div>
    <h1>Test Your Tourism Vocabulary,<br>
        <span class="highlight-yellow">Earn the Best Rating</span> &amp;
        <span class="highlight-cyan">Bring Your Group!</span> 🚀
    </h1>
    <p>A group quiz to check your initial understanding of vocabulary (adjectives &amp; verbs) about tourist destinations, before diving into the material. Relax &mdash; the score is only for mapping, it doesn't count toward your report card.</p>
    <div class="hero-btns">
        <a href="#kuis" class="btn btn-primary">
            <i class="fa-solid fa-gamepad"></i> Play the Quiz Now
        </a>
        <a href="{{ route('refleksi.create') }}" class="btn btn-dark">
            <i class="fa-solid fa-star"></i> Reflection
        </a>
    </div>
</section>

{{-- AVAILABLE QUIZZES --}}
<section class="section" id="kuis">
    <div class="section-title">
        <h2>Available <span style="color: var(--primary-pink);">Quizzes</span> 🔥</h2>
        <p>One group, one device, get started right away.</p>
    </div>

    <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 28px; flex-wrap: wrap;">
        <a href="{{ route('quiz.home') }}#kuis" class="btn {{ ! $tipe ? 'btn-primary' : 'btn-dark' }}">All</a>
        <a href="{{ route('quiz.home', ['tipe' => 'kelompok']) }}#kuis" class="btn {{ $tipe === 'kelompok' ? 'btn-primary' : 'btn-dark' }}">
            <i class="fa-solid fa-people-group"></i> Group
        </a>
        <a href="{{ route('quiz.home', ['tipe' => 'individu']) }}#kuis" class="btn {{ $tipe === 'individu' ? 'btn-primary' : 'btn-dark' }}">
            <i class="fa-solid fa-user"></i> Individual
        </a>
    </div>

    <div class="quiz-grid">
        @forelse ($quizzes as $quiz)
            <div class="quiz-card">
                <div>
                    <div class="quiz-card-header">
                        <div class="quiz-logo" style="background: var({{ $quiz->icon_color }});">
                            <i class="{{ $quiz->icon }}"></i>
                        </div>
                        <div style="display: flex; gap: 6px; flex-wrap: wrap; justify-content: flex-end;">
                            <span class="quiz-category" style="background: {{ $quiz->is_kelompok ? 'var(--primary-purple)' : 'var(--primary-lime)' }}; {{ $quiz->is_kelompok ? '' : 'color:#000;' }}">
                                {{ $quiz->is_kelompok ? 'GROUP' : 'INDIVIDUAL' }}
                            </span>
                            @if ($quiz->category_label)
                                <span class="quiz-category" style="background: var({{ $quiz->category_color }});">{{ $quiz->category_label }}</span>
                            @endif
                        </div>
                    </div>
                    <h3>{{ $quiz->title }}</h3>
                    <p>{{ $quiz->description }}</p>
                </div>
                <div>
                    <div class="quiz-meta">
                        @if ($quiz->is_kelompok)
                            <span><i class="fa-solid fa-image"></i> {{ $quiz->images_count }} Images</span>
                        @else
                            <span><i class="fa-solid fa-list-check"></i> {{ $quiz->soal_individu_count }} Questions</span>
                        @endif
                        @if ($quiz->meta_secondary)<span><i class="fa-solid fa-clock"></i> {{ $quiz->meta_secondary }}</span>@endif
                        @if ($quiz->meta_tertiary)<span><i class="fa-solid fa-users"></i> {{ $quiz->meta_tertiary }}</span>@endif
                    </div>
                    @if ($quiz->playUrl())
                        <a href="{{ $quiz->playUrl() }}" class="btn-play">Start Quiz 🚀</a>
                    @else
                        <span class="btn-play disabled">{{ $quiz->is_kelompok ? 'Coming Soon' : 'Coming Soon (Individual)' }}</span>
                    @endif
                </div>
            </div>
        @empty
            <p style="text-align:center; color: var(--text-muted); width: 100%;">No quizzes for this filter yet.</p>
        @endforelse
    </div>
</section>

{{-- USER LOGIN --}}
<section class="section" id="user-login">
    <div class="section-title">
        <h2>Sign In to <span style="color: var(--ink-lime);">Find the Word</span> ⚡</h2>
        <p>For students & groups already registered by a teacher/admin.</p>
    </div>

    @if (auth('siswa')->check())
        <div class="login-wrapper" style="text-align:center;">
            <p style="font-weight: 800; margin-bottom: 6px;">Signed in as student</p>
            <p style="color: var(--text-muted); margin-bottom: 20px;">{{ auth('siswa')->user()->name }}</p>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('user.profile.edit') }}" class="btn-login-submit" style="background: var(--primary-cyan); color: #000;">
                    <i class="fa-solid fa-user-gear"></i> Profile
                </a>
                <form method="POST" action="{{ route('user.logout') }}" style="flex: 1;">
                    @csrf
                    <button type="submit" class="btn-login-submit" style="background: var(--primary-pink); width: 100%;">Sign Out</button>
                </form>
            </div>
        </div>
    @elseif (auth('kelompok')->check())
        <div class="login-wrapper" style="text-align:center;">
            <p style="font-weight: 800; margin-bottom: 6px;">Signed in as group</p>
            <p style="color: var(--text-muted); margin-bottom: 20px;">{{ auth('kelompok')->user()->name }}</p>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('user.profile.edit') }}" class="btn-login-submit" style="background: var(--primary-cyan); color: #000;">
                    <i class="fa-solid fa-user-gear"></i> Profile
                </a>
                <form method="POST" action="{{ route('user.logout') }}" style="flex: 1;">
                    @csrf
                    <button type="submit" class="btn-login-submit" style="background: var(--primary-pink); width: 100%;">Sign Out</button>
                </form>
            </div>
        </div>
    @else
        @include('quiz._user_login_form')
    @endif
</section>
@endsection
