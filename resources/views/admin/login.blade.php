@extends('layouts.admin')

@section('title', 'Login Admin')

@section('content')
<div style="min-height: 100vh; width: 100%; display: flex; align-items: center; justify-content: center; padding: 20px;">
    <div class="panel-card" style="max-width: 380px; width: 100%; box-shadow: 8px 8px 0px #000;">
        <div style="text-align: center; margin-bottom: 24px;">
            <div class="brand-icon" style="margin: 0 auto 12px;"><img src="{{ asset('images/logo-kuis.png') }}" alt="Logo Find the Word"></div>
            <h1 style="font-size: 1.3rem;">Admin &middot; Find the Word</h1>
        </div>

        @if ($errors->any())
            <div class="alert-box alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <div class="form-group">
                <label for="username"><i class="fa-solid fa-user"></i> Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus placeholder="admin" autocapitalize="off">
            </div>
            <div class="form-group">
                <label for="password"><i class="fa-solid fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>
            <div class="form-group checkbox-row">
                <input type="checkbox" name="remember" value="1" style="width:18px; height:18px; accent-color: var(--primary-lime);">
                <label style="margin:0;">Ingat saya</label>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                Masuk <i class="fa-solid fa-right-to-bracket"></i>
            </button>
        </form>
    </div>
</div>
@endsection
