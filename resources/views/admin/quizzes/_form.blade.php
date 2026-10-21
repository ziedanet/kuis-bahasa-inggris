{{-- Partial form dipakai oleh create.blade.php & edit.blade.php --}}
{{-- Variabel opsional: $quiz (edit mode) --}}

@php $isEdit = isset($quiz); @endphp

<div class="form-grid">
    <div class="form-group">
        <label>Judul Kuis</label>
        <input type="text" name="title" placeholder="Misal: Find The Right Word"
               value="{{ old('title', $isEdit ? $quiz->title : '') }}" required>
    </div>
    <div class="form-group">
        <label>Tema / Kategori</label>
        <input type="text" name="category_label" placeholder="Misal: Vocabulary"
               value="{{ old('category_label', $isEdit ? $quiz->category_label : '') }}" required>
    </div>
</div>

<div class="form-grid">
    <div class="form-group">
        <label>Durasi Pengerjaan (Menit)</label>
        <input type="number" name="duration_minutes" min="1"
               value="{{ old('duration_minutes', $isEdit ? $quiz->duration_minutes : 10) }}" required>
    </div>
    <div class="form-group">
        <label>Status Kuis</label>
        <select name="status">
            <option value="aktif" @selected(old('status', $isEdit && $quiz->is_active ? 'aktif' : 'draft') === 'aktif')>Aktif / Published</option>
            <option value="draft" @selected(old('status', $isEdit && $quiz->is_active ? 'aktif' : 'draft') === 'draft')>Draft / Hidden</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label>Narasi Pengantar</label>
    <textarea name="narration" rows="3" placeholder="Narasi instruksi singkat untuk peserta sebelum mulai...">{{ old('narration', $isEdit ? $quiz->narration : '') }}</textarea>
</div>

@if (! $isEdit || ! $quiz->is_kelompok)
    <div class="form-group">
        <label>Judul Teks Bacaan <span style="font-weight:400; color: var(--text-muted);">(opsional, kuis individu)</span></label>
        <input type="text" name="bacaan_judul" maxlength="150" placeholder="Contoh: Pekanbaru Zoo"
               value="{{ old('bacaan_judul', $isEdit ? $quiz->bacaan_judul : '') }}">
    </div>
    <div class="form-group">
        <label>Teks Bacaan <span style="font-weight:400; color: var(--text-muted);">(opsional) &mdash; tampil di atas setiap soal, cukup diisi sekali</span></label>
        <textarea name="bacaan_teks" rows="6" placeholder="Tempel teks bacaan yang dipakai semua soal kuis ini...">{{ old('bacaan_teks', $isEdit ? $quiz->bacaan_teks : '') }}</textarea>
    </div>
@endif

<div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px;">
    <a href="{{ route('admin.quizzes.index') }}" class="btn btn-dark">Batal</a>
    <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-floppy-disk"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Kuis' }}
    </button>
</div>
