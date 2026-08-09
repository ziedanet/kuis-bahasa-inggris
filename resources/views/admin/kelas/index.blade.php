@extends('layouts.admin')

@section('title', 'Master Kelas')

@section('page-title')
    <i class="fa-solid fa-chalkboard-user" style="color: var(--primary-yellow);"></i> Master Kelas
@endsection

@section('top-actions')
    <a href="{{ route('admin.kelas.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Kelas
    </a>
@endsection

@section('content')
<div class="panel-card">
    <div class="panel-header">
        <h3><i class="fa-solid fa-list" style="color: var(--primary-cyan);"></i> Daftar Kelas</h3>
    </div>

    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: -8px; margin-bottom: 16px;">
        Daftar kelas ini yang akan muncul sebagai pilihan dropdown saat kelompok mengisi form sebelum mulai kuis &mdash; tidak lagi diketik manual.
    </p>

    <table class="quiz-table">
        <thead>
            <tr>
                <th>Nama Kelas</th>
                <th>Jumlah Kelompok Terdaftar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kelasList as $kelas)
                <tr>
                    <td><strong>{{ $kelas->name }}</strong></td>
                    <td>{{ $kelas->groups_count }}</td>
                    <td>
                        <a href="{{ route('admin.kelas.edit', $kelas) }}" class="btn btn-cyan btn-sm"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('admin.kelas.destroy', $kelas) }}" style="display:inline;"
                              onsubmit="return confirm('Hapus kelas ini? Kelompok yang sudah pakai kelas ini akan tetap ada, hanya kelasnya jadi kosong.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-pink btn-sm"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" style="text-align:center; color:var(--text-muted); padding:24px;">Belum ada kelas. Klik "Tambah Kelas".</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
