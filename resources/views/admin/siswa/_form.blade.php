{{-- Partial form dipakai oleh create.blade.php & edit.blade.php --}}
{{-- Variabel opsional: $siswa (edit mode), wajib: $kelasList --}}

@php $isEdit = isset($siswa); @endphp

<div class="form-group">
    <label>Foto</label>
    @if ($isEdit && $siswa->fotoUrl())
        <img src="{{ $siswa->fotoUrl() }}" class="thumb" style="width:100px; height:100px; margin-bottom:8px; border-radius:14px;">
    @endif
    <input type="file" name="foto" accept="image/*" style="box-shadow:none; padding:8px 0;">
    <p style="font-size: 0.75rem; color: var(--text-muted);">Kosongkan jika tidak ingin mengganti foto.</p>
</div>

<div class="form-grid">
    <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text" name="name" placeholder="Nama siswa" value="{{ old('name', $isEdit ? $siswa->name : '') }}" required>
    </div>
    <div class="form-group">
        <label>NISN</label>
        <input type="text" name="nisn" placeholder="Nomor Induk Siswa Nasional" value="{{ old('nisn', $isEdit ? $siswa->nisn : '') }}" required>
        <p style="font-size: 0.75rem; color: var(--text-muted);">
            {{ $isEdit ? 'Password login siswa ini tidak berubah walau NISN diedit. Kalau siswa lupa password, pakai tombol "Reset Password" di daftar siswa.' : 'Password login otomatis dibuat sama dengan NISN ini. Siswa bisa gantinya sendiri nanti, atau di-reset admin lewat daftar siswa.' }}
        </p>
    </div>
</div>

<div class="form-grid">
    <div class="form-group">
        <label>Jenis Kelamin</label>
        @php $jkOld = old('jenis_kelamin', $isEdit ? ($siswa->jenis_kelamin ? '1' : '0') : ''); @endphp
        <select name="jenis_kelamin" required>
            <option value="">-- Pilih --</option>
            <option value="1" @selected($jkOld === '1')>Laki-Laki</option>
            <option value="0" @selected($jkOld === '0')>Perempuan</option>
        </select>
    </div>
    <div class="form-group">
        <label>Kelas</label>
        @php $kelasOld = old('kelas_id', $isEdit ? $siswa->kelas_id : null); @endphp
        <select name="kelas_id">
            <option value="">-- Pilih Kelas --</option>
            @foreach ($kelasList as $kelasOption)
                <option value="{{ $kelasOption->id }}" @selected((int) $kelasOld === $kelasOption->id)>
                    {{ $kelasOption->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px;">
    <a href="{{ route('admin.siswa.index') }}" class="btn btn-dark">Batal</a>
    <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-floppy-disk"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Siswa' }}
    </button>
</div>
