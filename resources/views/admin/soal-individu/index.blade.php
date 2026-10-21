@extends('layouts.admin')

@section('title', 'Daftar Soal Individu')

@section('page-title')
    <i class="fa-solid fa-file-circle-question" style="color: var(--primary-yellow);"></i> Daftar Soal Individu
@endsection

@section('top-actions')
    <a href="{{ route('admin.quizzes.individu') }}" class="btn btn-dark"><i class="fa-solid fa-arrow-left"></i> Daftar Kuis Individu</a>
@endsection

@section('content')
<div class="panel-card" style="padding: 16px 24px;">
    <form method="GET" action="{{ route('admin.soal-individu.index') }}"
          style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        <label style="font-weight: 800; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-filter" style="color: var(--primary-pink);"></i> Pilih Kuis Dikelola:
        </label>
        <select name="quiz" onchange="this.form.submit()" class="filter-select" style="width: 300px;">
            @forelse ($quizzes as $quiz)
                <option value="{{ $quiz->id }}" @selected($selectedQuiz && $selectedQuiz->id === $quiz->id)>
                    {{ $quiz->title }} ({{ $quiz->category_label }})
                </option>
            @empty
                <option value="">Belum ada kuis individu</option>
            @endforelse
        </select>
    </form>
</div>

<div class="panel-card">
    <div class="panel-header">
        <h3><i class="fa-solid fa-circle-question" style="color: var(--primary-yellow);"></i> Daftar Soal Terdaftar</h3>
        @if ($selectedQuiz)
            <a href="{{ route('admin.soal-individu.create', ['quiz' => $selectedQuiz->id]) }}" class="btn btn-cyan btn-sm">
                <i class="fa-solid fa-plus"></i> Tambah Soal Baru
            </a>
        @endif
    </div>

    @forelse ($soal as $item)
        <div class="q-card">
            <div style="display: flex; gap: 16px; align-items: center; flex: 1; min-width: 240px;">
                @if ($item->gambarUrl())
                    <img src="{{ $item->gambarUrl() }}" alt="Soal #{{ $item->order_number }}" class="q-thumb">
                @else
                    <div class="q-thumb q-thumb-empty">
                        <i class="fa-solid fa-image-slash"></i>
                        <span>Tanpa gambar</span>
                    </div>
                @endif
                <div>
                    <h4 style="margin-bottom: 6px;">
                        Soal #{{ $item->order_number }}
                        @unless($item->is_active)
                            <span class="badge badge-pink">NONAKTIF</span>
                        @endunless
                    </h4>
                    <p style="color: var(--text-muted); font-size: 0.85rem;">{{ \Illuminate\Support\Str::limit($item->narasi, 120) }}</p>
                    <p style="color: var(--text-muted); font-size: 0.78rem; margin-top: 4px;">{{ $item->opsi_count }} opsi jawaban</p>
                </div>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('admin.soal-individu.edit', $item) }}" class="btn btn-purple btn-sm"><i class="fa-solid fa-pen"></i></a>
                <form method="POST" action="{{ route('admin.soal-individu.destroy', $item) }}"
                      onsubmit="return confirm('Hapus soal ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-pink btn-sm"><i class="fa-solid fa-trash"></i></button>
                </form>
            </div>
        </div>
    @empty
        <div style="text-align: center; color: var(--text-muted); padding: 30px;">
            @if ($selectedQuiz)
                Belum ada soal untuk kuis ini. Klik <strong>"Tambah Soal Baru"</strong>.
            @else
                Belum ada kuis individu. Buat dulu lewat <strong>Daftar Kuis Individu</strong>.
            @endif
        </div>
    @endforelse
</div>
@endsection
