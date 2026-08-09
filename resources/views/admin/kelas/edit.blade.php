@extends('layouts.admin')

@section('title', 'Edit Kelas')

@section('page-title')
    <i class="fa-solid fa-pen-to-square" style="color: var(--primary-cyan);"></i> Edit Kelas
@endsection

@section('top-actions')
    <a href="{{ route('admin.kelas.index') }}" class="btn btn-dark"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="panel-card" style="max-width: 480px;">
    <form method="POST" action="{{ route('admin.kelas.update', $kelas) }}">
        @csrf
        @method('PUT')
        @include('admin.kelas._form')
    </form>
</div>
@endsection
