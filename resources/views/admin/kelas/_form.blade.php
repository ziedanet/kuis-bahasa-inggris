{{-- Partial form dipakai oleh create.blade.php & edit.blade.php --}}
{{-- Variabel opsional: $kelas (edit mode) --}}

@php $isEdit = isset($kelas); @endphp

<div class="form-group">
    <label>Nama Kelas</label>
    <input type="text" name="name" placeholder="Misal: X-1, XI IPA 2, dst"
           value="{{ old('name', $isEdit ? $kelas->name : '') }}" required autofocus>
</div>

<div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px;">
    <a href="{{ route('admin.kelas.index') }}" class="btn btn-dark">Batal</a>
    <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-floppy-disk"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Kelas' }}
    </button>
</div>
