{{-- Partial form dipakai oleh create.blade.php & edit.blade.php --}}
{{-- Variabel opsional: $image (edit mode) --}}

@php
    $isEdit = isset($image);
    $wordsOld = old('words', $isEdit ? $image->words->pluck('word')->all() : array_fill(0, 10, ''));
    $correctOld = array_map('intval', old('correct_words', $isEdit ? $image->words->values()->filter(fn($w) => $w->is_correct)->keys()->all() : []));
@endphp

@php
    $currentQuizId = old('quiz_id', $isEdit ? $image->quiz_id : ($selectedQuizId ?? null));
@endphp

<div x-data="wordForm(@js($correctOld))">
    <div class="form-group">
        <label>Kuis Induk</label>
        <select name="quiz_id" required>
            <option value="">-- Pilih Kuis --</option>
            @foreach ($quizzes as $quizOption)
                <option value="{{ $quizOption->id }}" @selected((int) $currentQuizId === $quizOption->id)>
                    {{ $quizOption->title }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-grid">
        <div class="form-group">
            <label>Sesi</label>
            <select name="session_type">
                <option value="adjectives" @selected(old('session_type', $isEdit ? $image->session_type : '') === 'adjectives')>Sesi 1 - Adjectives</option>
                <option value="verbs" @selected(old('session_type', $isEdit ? $image->session_type : '') === 'verbs')>Sesi 2 - Verbs/Activities</option>
            </select>
        </div>
        <div class="form-group">
            <label>Urutan Tampil</label>
            <input type="number" name="order_number" min="1" value="{{ old('order_number', $isEdit ? $image->order_number : 1) }}">
        </div>
    </div>

    <div class="form-group">
        <label>Judul Gambar</label>
        <input type="text" name="title" placeholder="Contoh: Pantai Rupat" value="{{ old('title', $isEdit ? $image->title : '') }}">
    </div>

    <div class="form-group">
        <label>Instruksi</label>
        <textarea name="instruction" rows="2" placeholder="Pilihlah 5 dari 10 kata sifat berikut yang paling tepat menggambarkan...">{{ old('instruction', $isEdit ? $image->instruction : '') }}</textarea>
    </div>

    <div class="form-group">
        <label>Gambar</label>
        @if ($isEdit && $image->imageUrl())
            <img src="{{ $image->imageUrl() }}" class="thumb" style="width:120px; height:80px; margin-bottom:8px;">
        @endif
        <input type="file" name="image" accept="image/*" style="box-shadow:none; padding:8px 0;">
        <p style="font-size: 0.75rem; color: var(--text-muted);">Kosongkan jika tidak ingin mengganti gambar.</p>
    </div>

    <div class="form-group checkbox-row">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $isEdit ? $image->is_active : true)) style="width:18px; height:18px; accent-color: var(--primary-lime);">
        <label style="margin:0;">Aktifkan gambar ini di kuis</label>
    </div>

    <div class="form-group">
        <label style="color: var(--primary-yellow);"><i class="fa-solid fa-key"></i> 10 Pilihan Kata &mdash; centang tepat 5 sebagai kunci jawaban benar</label>
        <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 8px;">Terpilih sebagai kunci: <span x-text="correct.length" style="color: var(--primary-lime); font-weight:800;"></span> / 5</p>

        <div class="word-bank-grid">
            @for ($i = 0; $i < 10; $i++)
                <div class="word-item-row">
                    <input type="checkbox" :value="{{ $i }}"
                           x-on:change="toggle({{ $i }}, $event)"
                           :checked="correct.includes({{ $i }})">
                    <input type="hidden" name="correct_words[]" value="{{ $i }}" :disabled="!correct.includes({{ $i }})">
                    <input type="text" name="words[]" required placeholder="Kata {{ $i + 1 }}" value="{{ $wordsOld[$i] ?? '' }}">
                </div>
            @endfor
        </div>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 20px;">
        <i class="fa-solid fa-floppy-disk"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Gambar & Kuis' }}
    </button>
</div>

<script>
    function wordForm(initialCorrect) {
        return {
            correct: initialCorrect || [],
            toggle(index, event) {
                if (event.target.checked) {
                    if (this.correct.length >= 5) {
                        event.target.checked = false;
                        return;
                    }
                    this.correct.push(index);
                } else {
                    this.correct = this.correct.filter(i => i !== index);
                }
            },
        };
    }
</script>
