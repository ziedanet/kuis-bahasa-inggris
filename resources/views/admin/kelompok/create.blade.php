@extends('layouts.admin')

@section('title', 'Tambah Kelompok')

@section('page-title')
    <i class="fa-solid fa-people-group" style="color: var(--primary-lime);"></i> Tambah Kelompok
@endsection

@section('top-actions')
    <a href="{{ route('admin.kelompok.index') }}" class="btn btn-dark"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="panel-card">
    <form method="POST" action="{{ route('admin.kelompok.store') }}">
        @csrf
        @include('admin.kelompok._form')
    </form>
</div>
@endsection
