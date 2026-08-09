<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterKelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $kelasFilter = $request->query('kelas');
        $jkFilter = $request->query('jenis_kelamin');

        $siswaList = Siswa::with('kelas')
            ->when($kelasFilter, fn ($query) => $query->where('kelas_id', $kelasFilter))
            ->when($jkFilter !== null && $jkFilter !== '', fn ($query) => $query->where('jenis_kelamin', $jkFilter))
            ->orderBy('name')
            ->get();

        $kelasList = MasterKelas::orderBy('name')->get();

        return view('admin.siswa.index', compact('siswaList', 'kelasList', 'kelasFilter', 'jkFilter'));
    }

    public function create()
    {
        $kelasList = MasterKelas::orderBy('name')->get();

        return view('admin.siswa.create', compact('kelasList'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('siswa', 'public');
        }

        // Password login default = NISN. Siswa/admin bisa reset lewat tombol "Reset Password".
        $data['password'] = $data['nisn'];

        Siswa::create($data);

        return redirect()->route('admin.siswa.index')->with('status', 'Siswa baru berhasil ditambahkan. Password login default = NISN.');
    }

    public function edit(Siswa $siswa)
    {
        $kelasList = MasterKelas::orderBy('name')->get();

        return view('admin.siswa.edit', compact('siswa', 'kelasList'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $data = $this->validateRequest($request, $siswa->id);

        if ($request->hasFile('foto')) {
            if ($siswa->foto) {
                Storage::disk('public')->delete($siswa->foto);
            }
            $data['foto'] = $request->file('foto')->store('siswa', 'public');
        }

        $siswa->update($data);

        return redirect()->route('admin.siswa.index')->with('status', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        if ($siswa->foto) {
            Storage::disk('public')->delete($siswa->foto);
        }

        $siswa->delete();

        return back()->with('status', 'Data siswa berhasil dihapus.');
    }

    /**
     * Reset password login siswa balik ke NISN-nya, buat kasus lupa password.
     */
    public function resetPassword(Siswa $siswa)
    {
        $siswa->update(['password' => $siswa->nisn]);

        return back()->with('status', "Password {$siswa->name} berhasil direset ke NISN ({$siswa->nisn}).");
    }

    private function validateRequest(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'nisn' => [
                'required', 'string', 'max:30',
                'unique:zd_siswa,nisn' . ($ignoreId ? ",{$ignoreId}" : ''),
            ],
            'jenis_kelamin' => ['required', 'in:0,1'],
            'kelas_id' => ['nullable', 'integer', 'exists:zd_master_kelas,id'],
            'foto' => ['nullable', 'image', 'max:4096'],
        ], [
            'nisn.unique' => 'NISN ini sudah terdaftar.',
        ]);

        unset($data['foto']);

        return $data;
    }
}
