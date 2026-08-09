<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelompok;
use App\Models\MasterKelas;
use Illuminate\Http\Request;

class KelompokController extends Controller
{
    public function index(Request $request)
    {
        $kelasFilter = $request->query('kelas');

        $kelompokList = Kelompok::with('kelas')
            ->when($kelasFilter, fn ($query) => $query->where('kelas_id', $kelasFilter))
            ->orderBy('name')
            ->get();

        $kelasList = MasterKelas::orderBy('name')->get();

        return view('admin.kelompok.index', compact('kelompokList', 'kelasList', 'kelasFilter'));
    }

    public function create()
    {
        $kelasList = MasterKelas::orderBy('name')->get();

        return view('admin.kelompok.create', compact('kelasList'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request, isCreate: true);

        Kelompok::create($data);

        return redirect()->route('admin.kelompok.index')->with('status', 'Kelompok baru berhasil ditambahkan.');
    }

    public function edit(Kelompok $kelompok)
    {
        $kelasList = MasterKelas::orderBy('name')->get();

        return view('admin.kelompok.edit', compact('kelompok', 'kelasList'));
    }

    public function update(Request $request, Kelompok $kelompok)
    {
        $data = $this->validateRequest($request, $kelompok->id);

        $kelompok->update($data);

        return redirect()->route('admin.kelompok.index')->with('status', 'Data kelompok berhasil diperbarui.');
    }

    public function destroy(Kelompok $kelompok)
    {
        $kelompok->delete();

        return back()->with('status', 'Data kelompok berhasil dihapus.');
    }

    private function validateRequest(Request $request, ?int $ignoreId = null, bool $isCreate = false): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'kelas_id' => ['nullable', 'integer', 'exists:zd_master_kelas,id'],
            'username' => [
                'required', 'string', 'max:60',
                'unique:zd_kelompok,username' . ($ignoreId ? ",{$ignoreId}" : ''),
            ],
            'password' => [$isCreate ? 'required' : 'nullable', 'string', 'min:6'],
        ], [
            'username.unique' => 'Username ini sudah dipakai.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }
}
