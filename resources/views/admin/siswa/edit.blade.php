@extends('layouts.admin')

@section('title', 'Edit Siswa')

@section('page-title')
    <i class="fa-solid fa-pen-to-square" style="color: var(--primary-cyan);"></i> Edit Siswa
@endsection

@section('top-actions')
    <a href="{{ route('admin.siswa.index') }}" class="btn btn-dark"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="panel-card" style="max-width: 640px;">
    <form method="POST" action="{{ route('admin.siswa.update', $siswa) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.siswa._form')
    </form>
</div>
@endsection
