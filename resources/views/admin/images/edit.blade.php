@extends('layouts.admin')

@section('title', 'Edit Soal')

@section('page-title')
    <i class="fa-solid fa-pen-to-square" style="color: var(--primary-cyan);"></i> Edit Soal
@endsection

@section('top-actions')
    <a href="{{ route('admin.soal.index', ['quiz' => $image->quiz_id]) }}" class="btn btn-dark"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="alert-box alert-error" style="background: rgba(250, 204, 21, 0.1); color: var(--primary-yellow); border-color: var(--primary-yellow);">
    <i class="fa-solid fa-triangle-exclamation"></i> Mengubah kata/kunci jawaban akan menghapus jawaban kelompok yang sudah mengerjakan soal ini.
</div>

<div class="panel-card" style="max-width: 720px;">
    <form method="POST" action="{{ route('admin.images.update', $image) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.images._form')
    </form>
</div>
@endsection
