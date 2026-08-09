@extends('layouts.admin')

@section('title', 'Input Kuis Baru')

@section('page-title')
    <i class="fa-solid fa-pen-to-square" style="color: var(--primary-lime);"></i> Form Tambah Kuis Baru
@endsection

@section('top-actions')
    <a href="{{ route('admin.quizzes.index') }}" class="btn btn-dark"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="panel-card">
    <form method="POST" action="{{ route('admin.quizzes.store') }}">
        @csrf
        @include('admin.quizzes._form')
    </form>
</div>

<p style="color: var(--text-muted); font-size: 0.85rem;">
    Setelah kuis dibuat, lanjut tambahkan soal (gambar + pilihan kata) lewat tombol <strong>Soal</strong> di daftar kuis.
    Kuis baru otomatis muncul di homepage sebagai "Segera Hadir" sampai ada minimal 1 soal.
</p>
@endsection
