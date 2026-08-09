@extends('layouts.admin')

@section('title', 'Tambah Kelas')

@section('page-title')
    <i class="fa-solid fa-chalkboard-user" style="color: var(--primary-lime);"></i> Tambah Kelas
@endsection

@section('top-actions')
    <a href="{{ route('admin.kelas.index') }}" class="btn btn-dark"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="panel-card" style="max-width: 480px;">
    <form method="POST" action="{{ route('admin.kelas.store') }}">
        @csrf
        @include('admin.kelas._form')
    </form>
</div>
@endsection
