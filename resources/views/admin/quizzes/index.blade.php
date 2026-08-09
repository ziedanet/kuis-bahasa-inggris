@extends('layouts.admin')

@section('title', 'Daftar Kuis Kelompok')

@section('page-title')
    <i class="fa-solid fa-layer-group" style="color: var(--primary-yellow);"></i> Daftar Kuis Kelompok
@endsection

@section('top-actions')
    <button type="button" class="btn btn-primary" x-data x-on:click="$dispatch('open-create-quiz')">
        <i class="fa-solid fa-plus"></i> Buat Kuis Baru
    </button>
@endsection

@section('content')
<div class="panel-card">
    <div class="panel-header">
        <h3><i class="fa-solid fa-list" style="color: var(--primary-cyan);"></i> Manajemen Kuis</h3>
    </div>

    <table class="quiz-table">
        <thead>
            <tr>
                <th>Judul Kuis</th>
                <th>Kategori / Tema</th>
                <th>Durasi</th>
                <th>Jumlah Soal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($quizzes as $quiz)
                <tr>
                    <td><strong>{{ $quiz->title }}</strong></td>
                    <td><span class="badge badge-cyan">{{ $quiz->category_label }}</span></td>
                    <td><i class="fa-regular fa-clock"></i> {{ $quiz->duration_minutes }} Menit</td>
                    <td><strong>{{ $quiz->images_count }}</strong> Soal</td>
                    <td><span class="badge {{ $quiz->is_active ? 'badge-yellow' : 'badge-pink' }}">{{ $quiz->is_active ? 'Aktif' : 'Draft' }}</span></td>
                    <td>
                        <a href="{{ route('admin.soal.index', ['quiz' => $quiz->id]) }}" class="btn btn-purple btn-sm"><i class="fa-solid fa-gear"></i> Soal</a>
                        <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-cyan btn-sm"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz) }}" style="display:inline;"
                              onsubmit="return confirm('Hapus kuis ini beserta semua soal & jawaban kelompok di dalamnya?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-pink btn-sm"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding:24px;">Belum ada kuis. Klik "Buat Kuis Baru".</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Buat Kuis Baru --}}
<div
    x-data="{ show: {{ $errors->any() ? 'true' : 'false' }} }"
    x-on:open-create-quiz.window="show = true"
    x-show="show"
    x-cloak
    class="modal-overlay"
    x-on:keydown.escape.window="show = false"
    style="display: none;"
>
    <div class="modal-box" x-on:click.outside="show = false">
        <div class="modal-header">
            <h3 style="margin:0;"><i class="fa-solid fa-plus-circle" style="color: var(--primary-lime);"></i> Buat Kuis Baru</h3>
            <button type="button" class="modal-close" x-on:click="show = false"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form method="POST" action="{{ route('admin.quizzes.store') }}">
            @csrf
            <input type="hidden" name="is_kelompok" value="1">

            <div class="form-grid">
                <div class="form-group">
                    <label>Judul Kuis</label>
                    <input type="text" name="title" placeholder="Misal: Find The Right Word" value="{{ old('title') }}" required>
                </div>
                <div class="form-group">
                    <label>Tema / Kategori</label>
                    <input type="text" name="category_label" placeholder="Misal: Vocabulary" value="{{ old('category_label') }}" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Durasi Pengerjaan (Menit)</label>
                    <input type="number" name="duration_minutes" min="1" value="{{ old('duration_minutes', 10) }}" required>
                </div>
                <div class="form-group">
                    <label>Status Kuis</label>
                    <select name="status">
                        <option value="aktif" @selected(old('status', 'aktif') === 'aktif')>Aktif / Published</option>
                        <option value="draft" @selected(old('status') === 'draft')>Draft / Hidden</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Narasi Pengantar</label>
                <textarea name="narration" rows="3" placeholder="Narasi instruksi singkat untuk peserta sebelum mulai...">{{ old('narration') }}</textarea>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-dark" x-on:click="show = false">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Kuis
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
