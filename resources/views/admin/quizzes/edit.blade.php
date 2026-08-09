@extends('layouts.admin')

@section('title', 'Edit Kuis')

@section('page-title')
    <i class="fa-solid fa-pen-to-square" style="color: var(--primary-cyan);"></i> Edit Kuis
@endsection

@section('top-actions')
    <a href="{{ route($quiz->is_kelompok ? 'admin.quizzes.index' : 'admin.quizzes.individu') }}" class="btn btn-dark"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="panel-card">
    <form method="POST" action="{{ route('admin.quizzes.update', $quiz) }}">
        @csrf
        @method('PUT')
        @include('admin.quizzes._form')
    </form>
</div>
@endsection
