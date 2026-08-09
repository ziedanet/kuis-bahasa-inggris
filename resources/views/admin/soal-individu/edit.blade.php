@extends('layouts.admin')

@section('title', 'Edit Soal Individu')

@section('page-title')
    <i class="fa-solid fa-pen-to-square" style="color: var(--primary-cyan);"></i> Edit Soal Individu
@endsection

@section('top-actions')
    <a href="{{ route('admin.soal-individu.index', ['quiz' => $soal->quiz_id]) }}" class="btn btn-dark"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="panel-card" style="max-width: 720px;">
    <form method="POST" action="{{ route('admin.soal-individu.update', $soal) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.soal-individu._form')
    </form>
</div>
@endsection
