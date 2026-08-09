@extends('layouts.admin')

@section('title', 'Tambah Siswa')

@section('page-title')
    <i class="fa-solid fa-user-graduate" style="color: var(--primary-lime);"></i> Tambah Siswa
@endsection

@section('top-actions')
    <a href="{{ route('admin.siswa.index') }}" class="btn btn-dark"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="panel-card" style="max-width: 640px;">
    <form method="POST" action="{{ route('admin.siswa.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.siswa._form')
    </form>
</div>
@endsection
