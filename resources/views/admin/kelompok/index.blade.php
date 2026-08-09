@extends('layouts.admin')

@section('title', 'Daftar Kelompok')

@section('page-title')
    <i class="fa-solid fa-people-group" style="color: var(--primary-yellow);"></i> Daftar Kelompok
@endsection

@section('top-actions')
    <a href="{{ route('admin.kelompok.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Kelompok
    </a>
@endsection

@section('content')
<div class="panel-card" style="padding: 16px 24px;">
    <form method="GET" action="{{ route('admin.kelompok.index') }}"
          style="display: flex; align-items: center; flex-wrap: wrap; gap: 12px;">
        <label style="font-weight: 800; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-filter" style="color: var(--primary-pink);"></i> Filter:
        </label>
        <select name="kelas" onchange="this.form.submit()" style="padding: 10px 16px; background: #0f172a; color: #fff; border: 2px solid #000; border-radius: 10px; font-weight: 700; width: 220px;">
            <option value="">Semua Kelas</option>
            @foreach ($kelasList as $kelasOption)
                <option value="{{ $kelasOption->id }}" @selected((string) $kelasFilter === (string) $kelasOption->id)>
                    {{ $kelasOption->name }}
                </option>
            @endforeach
        </select>
        @if ($kelasFilter)
            <a href="{{ route('admin.kelompok.index') }}" class="btn btn-dark btn-sm">Reset</a>
        @endif
    </form>
</div>

<div class="panel-card">
    <div class="panel-header">
        <h3><i class="fa-solid fa-list" style="color: var(--primary-cyan);"></i> Kelompok Terdaftar</h3>
    </div>

    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: -8px; margin-bottom: 16px;">
        Akun kelompok ini bisa dipakai buat login (username + password) lewat halaman utama.
    </p>

    <table class="quiz-table">
        <thead>
            <tr>
                <th>Nama Kelompok</th>
                <th>Kelas</th>
                <th>Username</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kelompokList as $kelompok)
                <tr>
                    <td><strong>{{ $kelompok->name }}</strong></td>
                    <td style="color: var(--text-muted);">{{ $kelompok->kelas?->name ?? '-' }}</td>
                    <td style="color: var(--text-muted);">{{ $kelompok->username }}</td>
                    <td>
                        <a href="{{ route('admin.kelompok.edit', $kelompok) }}" class="btn btn-cyan btn-sm"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('admin.kelompok.destroy', $kelompok) }}" style="display:inline;"
                              onsubmit="return confirm('Hapus kelompok ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-pink btn-sm"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center; color:var(--text-muted); padding:24px;">Belum ada kelompok. Klik "Tambah Kelompok".</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
