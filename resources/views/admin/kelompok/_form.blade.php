{{-- Partial form dipakai oleh create.blade.php & edit.blade.php --}}
{{-- Variabel opsional: $kelompok (edit mode), wajib: $kelasList --}}

@php $isEdit = isset($kelompok); @endphp

<div class="form-grid">
    <div class="form-group">
        <label>Nama Kelompok</label>
        <input type="text" name="name" placeholder="Misal: Kelompok 1" value="{{ old('name', $isEdit ? $kelompok->name : '') }}" required>
    </div>
    <div class="form-group">
        <label>Kelas</label>
        @php $kelasOld = old('kelas_id', $isEdit ? $kelompok->kelas_id : null); @endphp
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

<div class="form-grid">
    <div class="form-group">
        <label>Username Login</label>
        <input type="text" name="username" placeholder="Username buat login kelompok" value="{{ old('username', $isEdit ? $kelompok->username : '') }}" required>
    </div>
    <div class="form-group">
        <label>Password Login</label>
        <input type="password" name="password" placeholder="{{ $isEdit ? 'Kosongkan jika tidak ingin mengganti password' : 'Minimal 6 karakter' }}" autocomplete="new-password">
    </div>
</div>

<div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px;">
    <a href="{{ route('admin.kelompok.index') }}" class="btn btn-dark">Batal</a>
    <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-floppy-disk"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Kelompok' }}
    </button>
</div>
