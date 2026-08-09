@extends('layouts.app')

@section('title', 'Find the Word - Kuis Kosakata Wisata')

@section('content')
{{-- HERO --}}
<section class="hero">
    <div class="badge-hype">
        <i class="fa-solid fa-fire"></i> Asesmen Diagnostik
    </div>
    <h1>Uji Kosakata Wisata Kalian,<br>
        <span class="highlight-yellow">Raih Predikat Terbaik</span> &amp;
        <span class="highlight-cyan">Ajak Kelompokmu!</span> 🚀
    </h1>
    <p>Kuis kelompok buat ngecek pemahaman awal kosakata (adjectives &amp; verbs) seputar objek wisata, sebelum masuk ke materi. Santai aja, skornya cuma buat pemetaan &mdash; gak masuk nilai rapor.</p>
    <div class="hero-btns">
        <a href="#kuis" class="btn btn-primary">
            <i class="fa-solid fa-gamepad"></i> Main Kuis Sekarang
        </a>
    </div>
</section>

{{-- KUIS YANG TERSEDIA --}}
<section class="section" id="kuis">
    <div class="section-title">
        <h2>Kuis <span style="color: var(--primary-pink);">Yang Tersedia</span> 🔥</h2>
        <p>Satu kelompok, satu perangkat, langsung mulai.</p>
    </div>

    <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 28px; flex-wrap: wrap;">
        <a href="{{ route('quiz.home') }}#kuis" class="btn {{ ! $tipe ? 'btn-primary' : 'btn-dark' }}">Semua</a>
        <a href="{{ route('quiz.home', ['tipe' => 'kelompok']) }}#kuis" class="btn {{ $tipe === 'kelompok' ? 'btn-primary' : 'btn-dark' }}">
            <i class="fa-solid fa-people-group"></i> Kelompok
        </a>
        <a href="{{ route('quiz.home', ['tipe' => 'individu']) }}#kuis" class="btn {{ $tipe === 'individu' ? 'btn-primary' : 'btn-dark' }}">
            <i class="fa-solid fa-user"></i> Individu
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
                                {{ $quiz->is_kelompok ? 'KELOMPOK' : 'INDIVIDU' }}
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
                            <span><i class="fa-solid fa-image"></i> {{ $quiz->images_count }} Gambar</span>
                        @else
                            <span><i class="fa-solid fa-list-check"></i> {{ $quiz->soal_individu_count }} Soal</span>
                        @endif
                        @if ($quiz->meta_secondary)<span><i class="fa-solid fa-clock"></i> {{ $quiz->meta_secondary }}</span>@endif
                        @if ($quiz->meta_tertiary)<span><i class="fa-solid fa-users"></i> {{ $quiz->meta_tertiary }}</span>@endif
                    </div>
                    @if ($quiz->playUrl())
                        <a href="{{ $quiz->playUrl() }}" class="btn-play">Mulai Kuis 🚀</a>
                    @else
                        <span class="btn-play disabled">{{ $quiz->is_kelompok ? 'Segera Hadir' : 'Segera Hadir (Individu)' }}</span>
                    @endif
                </div>
            </div>
        @empty
            <p style="text-align:center; color: var(--text-muted); grid-column: 1 / -1;">Belum ada kuis untuk filter ini.</p>
        @endforelse
    </div>
</section>

{{-- LOGIN USER --}}
<section class="section" id="user-login">
    <div class="section-title">
        <h2>Masuk ke <span style="color: var(--ink-lime);">Find the Word</span> ⚡</h2>
        <p>Buat siswa & kelompok yang sudah didaftarkan guru/admin.</p>
    </div>

    @if (auth('siswa')->check())
        <div class="login-wrapper" style="text-align:center;">
            <p style="font-weight: 800; margin-bottom: 6px;">Sudah login sebagai siswa</p>
            <p style="color: var(--text-muted); margin-bottom: 20px;">{{ auth('siswa')->user()->name }}</p>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('user.profile.edit') }}" class="btn-login-submit" style="background: var(--primary-cyan); color: #000;">
                    <i class="fa-solid fa-user-gear"></i> Profil
                </a>
                <form method="POST" action="{{ route('user.logout') }}" style="flex: 1;">
                    @csrf
                    <button type="submit" class="btn-login-submit" style="background: var(--primary-pink); width: 100%;">Keluar</button>
                </form>
            </div>
        </div>
    @elseif (auth('kelompok')->check())
        <div class="login-wrapper" style="text-align:center;">
            <p style="font-weight: 800; margin-bottom: 6px;">Sudah login sebagai kelompok</p>
            <p style="color: var(--text-muted); margin-bottom: 20px;">{{ auth('kelompok')->user()->name }}</p>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('user.profile.edit') }}" class="btn-login-submit" style="background: var(--primary-cyan); color: #000;">
                    <i class="fa-solid fa-user-gear"></i> Profil
                </a>
                <form method="POST" action="{{ route('user.logout') }}" style="flex: 1;">
                    @csrf
                    <button type="submit" class="btn-login-submit" style="background: var(--primary-pink); width: 100%;">Keluar</button>
                </form>
            </div>
        </div>
    @else
        @include('quiz._user_login_form')
    @endif
</section>
@endsection
