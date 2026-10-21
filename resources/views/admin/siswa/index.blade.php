@extends('layouts.admin')

@section('title', 'Daftar User')

@section('page-title')
    <i class="fa-solid fa-user-graduate" style="color: var(--primary-yellow);"></i> Daftar User
@endsection

@section('top-actions')
    <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Siswa
    </a>
@endsection

@section('content')
<div class="panel-card" style="padding: 16px 24px;">
    <form method="GET" action="{{ route('admin.siswa.index') }}"
          style="display: flex; align-items: center; flex-wrap: wrap; gap: 12px;">
        <label style="font-weight: 800; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-filter" style="color: var(--primary-pink);"></i> Filter:
        </label>
        <select name="kelas" onchange="this.form.submit()" class="filter-select" style="width: 220px;">
            <option value="">Semua Kelas</option>
            @foreach ($kelasList as $kelasOption)
                <option value="{{ $kelasOption->id }}" @selected((string) $kelasFilter === (string) $kelasOption->id)>
                    {{ $kelasOption->name }}
                </option>
            @endforeach
        </select>
        <select name="jenis_kelamin" onchange="this.form.submit()" class="filter-select" style="width: 200px;">
            <option value="">Semua Jenis Kelamin</option>
            <option value="1" @selected($jkFilter === '1')>Laki-Laki</option>
            <option value="0" @selected($jkFilter === '0')>Perempuan</option>
        </select>
        @if ($kelasFilter || $jkFilter !== null)
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-dark btn-sm">Reset</a>
        @endif
    </form>
</div>

<div class="panel-card">
    <div class="panel-header">
        <h3><i class="fa-solid fa-list" style="color: var(--primary-cyan);"></i> Siswa Terdaftar</h3>
    </div>

    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: -8px; margin-bottom: 16px;">
        Siswa login pakai NISN + password (default = NISN). Lupa password? Klik tombol <i class="fa-solid fa-key"></i> buat reset balik ke NISN.
    </p>

    <table class="quiz-table">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nama</th>
                <th>NISN</th>
                <th>Jenis Kelamin</th>
                <th>Kelas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($siswaList as $siswa)
                <tr>
                    <td>
                        @if ($siswa->fotoUrl())
                            <img src="{{ $siswa->fotoUrl() }}" class="thumb">
                        @else
                            <div class="thumb" style="display:flex; align-items:center; justify-content:center; background:var(--surface-2); color:var(--text-muted);">
                                <i class="fa-solid fa-user"></i>
                            </div>
                        @endif
                    </td>
                    <td><strong>{{ $siswa->name }}</strong></td>
                    <td style="color: var(--text-muted);">{{ $siswa->nisn }}</td>
                    <td>
                        <span class="badge {{ $siswa->jenis_kelamin ? 'badge-cyan' : 'badge-pink' }}">
                            {{ $siswa->jenisKelaminLabel() }}
                        </span>
                    </td>
                    <td style="color: var(--text-muted);">{{ $siswa->kelas?->name ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.siswa.edit', $siswa) }}" class="btn btn-cyan btn-sm"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('admin.siswa.reset-password', $siswa) }}" style="display:inline;"
                              onsubmit="return confirm('Reset password {{ $siswa->name }} balik ke NISN ({{ $siswa->nisn }})?')">
                            @csrf
                            <button type="submit" class="btn btn-purple btn-sm" title="Reset Password ke NISN"><i class="fa-solid fa-key"></i></button>
                        </form>
                        <form method="POST" action="{{ route('admin.siswa.destroy', $siswa) }}" style="display:inline;"
                              onsubmit="return confirm('Hapus data siswa ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-pink btn-sm"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding:24px;">Belum ada siswa. Klik "Tambah Siswa".</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
