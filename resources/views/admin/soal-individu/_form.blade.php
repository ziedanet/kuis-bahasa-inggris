{{-- Partial form dipakai oleh create.blade.php & edit.blade.php --}}
{{-- Variabel opsional: $soal (edit mode), wajib: $quizzes --}}

@php
    $isEdit = isset($soal);
    $opsiOld = old('opsi', $isEdit ? $soal->opsi->pluck('opsi_text')->all() : array_fill(0, 4, ''));
    $correctOld = old('correct_index', $isEdit ? $soal->opsi->values()->search(fn ($o) => $o->is_correct) : null);
    $currentQuizId = old('quiz_id', $isEdit ? $soal->quiz_id : ($selectedQuizId ?? null));
@endphp

<div class="form-group">
    <label>Kuis Induk</label>
    <select name="quiz_id" required>
        <option value="">-- Pilih Kuis Individu --</option>
        @foreach ($quizzes as $quizOption)
            <option value="{{ $quizOption->id }}" @selected((int) $currentQuizId === $quizOption->id)>
                {{ $quizOption->title }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-grid">
    <div class="form-group">
        <label>Urutan Tampil</label>
        <input type="number" name="order_number" min="1" value="{{ old('order_number', $isEdit ? $soal->order_number : 1) }}">
    </div>
    <div class="form-group checkbox-row" style="align-self: end; margin-bottom: 18px;">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $isEdit ? $soal->is_active : true)) style="width:18px; height:18px; accent-color: var(--primary-lime);">
        <label style="margin:0;">Aktifkan soal ini</label>
    </div>
</div>

<div class="form-group">
    <label>Narasi Soal</label>
    <textarea name="narasi" rows="3" placeholder="Tulis pertanyaan/narasi soal di sini...">{{ old('narasi', $isEdit ? $soal->narasi : '') }}</textarea>
</div>

<div class="form-group">
    <label>Gambar (opsional)</label>
    @if ($isEdit && $soal->gambarUrl())
        <img src="{{ $soal->gambarUrl() }}" class="thumb" style="width:120px; height:80px; margin-bottom:8px;">
    @endif
    <input type="file" name="gambar" accept="image/*" style="box-shadow:none; padding:8px 0;">
    <p style="font-size: 0.75rem; color: var(--text-muted);">Kosongkan kalau soal ini tidak pakai gambar / tidak ingin mengganti gambar.</p>
</div>

<div class="form-group">
    <label style="color: var(--primary-yellow);"><i class="fa-solid fa-key"></i> 4 Pilihan Jawaban (A-D) &mdash; pilih 1 sebagai kunci jawaban benar</label>

    <div class="word-bank-grid" style="grid-template-columns: 1fr;">
        @for ($i = 0; $i < 4; $i++)
            <div class="word-item-row">
                <input type="radio" name="correct_index" value="{{ $i }}" @checked((string) $correctOld === (string) $i) required
                       style="width:18px; height:18px; accent-color: var(--primary-lime); flex-shrink: 0;">
                <input type="text" name="opsi[]" required placeholder="Opsi {{ chr(65 + $i) }}" value="{{ $opsiOld[$i] ?? '' }}">
            </div>
        @endfor
    </div>
</div>

<div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px;">
    <a href="{{ route('admin.soal-individu.index', ['quiz' => $currentQuizId]) }}" class="btn btn-dark">Batal</a>
    <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-floppy-disk"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Soal' }}
    </button>
</div>
