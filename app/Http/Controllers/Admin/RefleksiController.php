<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterKelas;
use App\Models\Refleksi;
use Illuminate\Http\Request;

class RefleksiController extends Controller
{
    /**
     * Rekap refleksi peserta: rata-rata bintang per pernyataan + daftar jawaban.
     * Bisa difilter per kelas.
     */
    public function index(Request $request)
    {
        $kelasId = $request->query('kelas_id');

        $base = Refleksi::query()->when($kelasId, fn ($q) => $q->where('kelas_id', $kelasId));

        $summary = [
            'count' => (clone $base)->count(),
            'averages' => [],
            'distribution' => [],
        ];

        foreach (array_keys(Refleksi::STATEMENTS) as $field) {
            $summary['averages'][$field] = round((float) (clone $base)->avg($field), 1);
            // Sebaran 1-5 bintang per pernyataan.
            $counts = (clone $base)->selectRaw("{$field} as stars, count(*) as total")->groupBy($field)->pluck('total', 'stars');
            $summary['distribution'][$field] = collect(range(1, 5))->mapWithKeys(fn ($n) => [$n => (int) ($counts[$n] ?? 0)])->all();
        }

        $items = (clone $base)->with('kelas')->latest()->paginate(20)->withQueryString();

        return view('admin.refleksi.index', [
            'items' => $items,
            'summary' => $summary,
            'statements' => Refleksi::STATEMENTS,
            'kelasList' => MasterKelas::orderBy('name')->get(),
            'kelasId' => $kelasId,
        ]);
    }

    public function destroy(Refleksi $refleksi)
    {
        $refleksi->delete();

        return back()->with('status', 'Refleksi dihapus.');
    }
}
