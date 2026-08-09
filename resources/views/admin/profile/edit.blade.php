@extends('layouts.admin')

@section('title', 'Profil Admin')

@section('page-title')
    <i class="fa-solid fa-id-badge" style="color: var(--primary-yellow);"></i> Profil Admin
@endsection

@section('content')
<div class="panel-card">
    <div class="panel-header">
        <h3><i class="fa-solid fa-user-pen" style="color: var(--primary-cyan);"></i> Data Diri</h3>
    </div>

    <form method="POST" action="{{ route('admin.profile.update') }}" style="max-width: 640px;">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" required>
            </div>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>

        <hr style="border: none; border-top: 2px dashed #334155; margin: 24px 0;">

        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 16px;">
            Isi bagian ini kalau mau ganti password. Kosongkan kalau tidak.
        </p>

        <div class="form-group">
            <label>Password Lama</label>
            <input type="password" name="current_password" autocomplete="current-password">
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label>Password Baru</label>
                <input type="password" name="password" autocomplete="new-password">
            </div>
            <div class="form-group">
                <label>Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" autocomplete="new-password">
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top: 10px;">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
        </button>
    </form>
</div>
@endsection
