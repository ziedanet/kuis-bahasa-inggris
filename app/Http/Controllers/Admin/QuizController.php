<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Warna & ikon default dipakai bergantian tiap kuis baru dibuat,
     * biar kartu di homepage tetap ada variasi warna tanpa admin harus pilih manual.
     */
    private const PRESETS = [
        ['icon' => 'fa-solid fa-magnifying-glass', 'icon_color' => '--primary-yellow', 'category_color' => '--primary-cyan'],
        ['icon' => 'fa-solid fa-gamepad', 'icon_color' => '--primary-lime', 'category_color' => '--primary-pink'],
        ['icon' => 'fa-solid fa-masks-theater', 'icon_color' => '--primary-purple', 'category_color' => '--primary-yellow'],
        ['icon' => 'fa-solid fa-globe', 'icon_color' => '--primary-cyan', 'category_color' => '--primary-lime'],
    ];

    public function index()
    {
        $quizzes = Quiz::where('is_kelompok', true)
            ->withCount('images')
            ->orderBy('order_number')
            ->orderBy('title')
            ->get();

        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function individu()
    {
        $quizzes = Quiz::where('is_kelompok', false)
            ->withCount('soalIndividu')
            ->orderBy('order_number')
            ->orderBy('title')
            ->get();

        return view('admin.quizzes.individu', compact('quizzes'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request, isCreate: true);
        $isKelompok = $data['is_kelompok'];
        unset($data['is_kelompok']);

        $preset = self::PRESETS[Quiz::count() % count(self::PRESETS)];

        Quiz::create([
            ...$data,
            ...$preset,
            'is_kelompok' => $isKelompok,
            'meta_primary' => $isKelompok ? '0 Gambar' : '0 Soal',
            'meta_secondary' => $data['duration_minutes'] ? '~' . $data['duration_minutes'] . ' Menit' : null,
            'meta_tertiary' => $isKelompok ? 'Per Kelompok' : 'Individu',
            'order_number' => Quiz::max('order_number') + 1,
        ]);

        $redirectRoute = $isKelompok ? 'admin.quizzes.index' : 'admin.quizzes.individu';
        $soalMenu = $isKelompok ? 'Soal' : 'Daftar Soal';

        return redirect()->route($redirectRoute)->with('status', "Kuis baru berhasil dibuat. Lanjut tambahkan soal lewat tombol \"{$soalMenu}\" di daftar kuis.");
    }

    public function edit(Quiz $quiz)
    {
        return view('admin.quizzes.edit', compact('quiz'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $data = $this->validateRequest($request);

        $quiz->update([
            ...$data,
            'meta_secondary' => $data['duration_minutes'] ? '~' . $data['duration_minutes'] . ' Menit' : null,
        ]);

        $redirectRoute = $quiz->is_kelompok ? 'admin.quizzes.index' : 'admin.quizzes.individu';

        return redirect()->route($redirectRoute)->with('status', 'Kuis berhasil diperbarui.');
    }

    public function destroy(Quiz $quiz)
    {
        $isKelompok = $quiz->is_kelompok;

        $quiz->delete();

        return redirect()->route($isKelompok ? 'admin.quizzes.index' : 'admin.quizzes.individu')
            ->with('status', 'Kuis berhasil dihapus.');
    }

    private function validateRequest(Request $request, bool $isCreate = false): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:150'],
            'category_label' => ['required', 'string', 'max:60'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:aktif,draft'],
            'narration' => ['required', 'string'],
            'bacaan_judul' => ['nullable', 'string', 'max:150'],
            'bacaan_teks' => ['nullable', 'string'],
        ];

        if ($isCreate) {
            $rules['is_kelompok'] = ['required', 'in:0,1'];
        }

        $data = $request->validate($rules);

        $result = [
            'title' => $data['title'],
            'description' => $data['narration'],
            'narration' => $data['narration'],
            'bacaan_judul' => $data['bacaan_judul'] ?? null,
            'bacaan_teks' => $data['bacaan_teks'] ?? null,
            'category_label' => $data['category_label'],
            'duration_minutes' => $data['duration_minutes'],
            'is_active' => $data['status'] === 'aktif',
        ];

        if ($isCreate) {
            $result['is_kelompok'] = (bool) $data['is_kelompok'];
        }

        return $result;
    }
}
