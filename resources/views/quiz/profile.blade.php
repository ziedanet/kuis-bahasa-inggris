@extends('layouts.app')

@section('title', 'Profil Saya - Find the Word')

@section('content')
<div class="container">
    <div class="quiz-box" style="max-width: 480px; margin: 0 auto;">
        <span class="welcome-badge">👤 Profil Saya</span>
        <h2>{{ $account->name }}</h2>
        <p style="color: var(--text-muted); margin-bottom: 24px;">
            {{ $guard === 'siswa' ? 'NISN: ' . $account->nisn : 'Username: ' . $account->username }}
            @if ($account->kelas) &middot; Kelas: {{ $account->kelas->name }} @endif
        </p>

        @if (session('status'))
            <div class="alert-error" style="background: rgba(163, 230, 53, 0.15); border-color: var(--primary-lime); color: var(--ink-lime);">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('user.profile.update') }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="current_password">Password Lama</label>
                <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
            </div>
            <div class="form-group">
                <label for="password">Password Baru</label>
                <input type="password" id="password" name="password" required autocomplete="new-password" minlength="4">
            </div>
            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password Baru</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" minlength="4">
            </div>

            <button type="submit" class="btn btn-start" style="width: 100%; justify-content: center;">
                <i class="fa-solid fa-key"></i> Simpan Password Baru
            </button>
        </form>

        <div style="margin-top: 20px; text-align: center;">
            <a href="{{ route('quiz.home') }}" style="color: var(--text-muted); font-size: 0.85rem; text-decoration: none;">&larr; Kembali ke beranda</a>
        </div>
    </div>
</div>
@endsection
