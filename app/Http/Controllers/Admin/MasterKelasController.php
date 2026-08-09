<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterKelas;
use Illuminate\Http\Request;

class MasterKelasController extends Controller
{
    public function index()
    {
        $kelasList = MasterKelas::withCount('groups')->orderBy('name')->get();

        return view('admin.kelas.index', compact('kelasList'));
    }

    public function create()
    {
        return view('admin.kelas.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        MasterKelas::create($data);

        return redirect()->route('admin.kelas.index')->with('status', 'Kelas baru berhasil ditambahkan.');
    }

    public function edit(MasterKelas $kelas)
    {
        return view('admin.kelas.edit', compact('kelas'));
    }

    public function update(Request $request, MasterKelas $kelas)
    {
        $data = $this->validateRequest($request, $kelas->id);

        $kelas->update($data);

        return redirect()->route('admin.kelas.index')->with('status', 'Kelas berhasil diperbarui.');
    }

    public function destroy(MasterKelas $kelas)
    {
        $kelas->delete();

        return back()->with('status', 'Kelas berhasil dihapus.');
    }

    private function validateRequest(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => [
                'required', 'string', 'max:60',
                'unique:zd_master_kelas,name' . ($ignoreId ? ",{$ignoreId}" : ''),
            ],
        ], [
            'name.unique' => 'Nama kelas ini sudah ada.',
        ]);
    }
}
