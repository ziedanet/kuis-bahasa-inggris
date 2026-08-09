@extends('layouts.admin')

@section('title', 'Tambah Soal Individu')

@section('page-title')
    <i class="fa-solid fa-plus-circle" style="color: var(--primary-lime);"></i> Tambah Soal Individu
@endsection

@section('top-actions')
    <a href="{{ route('admin.soal-individu.index', ['quiz' => $selectedQuizId]) }}" class="btn btn-dark"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="panel-card" style="max-width: 720px;">
    <form method="POST" action="{{ route('admin.soal-individu.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.soal-individu._form')
    </form>
</div>
@endsection
