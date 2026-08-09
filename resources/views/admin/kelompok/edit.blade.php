@extends('layouts.admin')

@section('title', 'Edit Kelompok')

@section('page-title')
    <i class="fa-solid fa-pen-to-square" style="color: var(--primary-cyan);"></i> Edit Kelompok
@endsection

@section('top-actions')
    <a href="{{ route('admin.kelompok.index') }}" class="btn btn-dark"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="panel-card">
    <form method="POST" action="{{ route('admin.kelompok.update', $kelompok) }}">
        @csrf
        @method('PUT')
        @include('admin.kelompok._form')
    </form>
</div>
@endsection
