<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\SoalIndividu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SoalIndividuController extends Controller
{
    public function index(Request $request)
    {
        $quizzes = Quiz::where('is_kelompok', false)->orderBy('title')->get();

        $selectedQuiz = $request->filled('quiz')
            ? $quizzes->firstWhere('id', (int) $request->query('quiz'))
            : $quizzes->first();

        $soal = $selectedQuiz
            ? $selectedQuiz->soalIndividu()->withCount('opsi')->orderBy('order_number')->get()
            : collect();

        return view('admin.soal-individu.index', [
            'quizzes' => $quizzes,
            'selectedQuiz' => $selectedQuiz,
            'soal' => $soal,
        ]);
    }

    public function create(Request $request)
    {
        $quizzes = Quiz::where('is_kelompok', false)->orderBy('title')->get();
        $selectedQuizId = (int) $request->query('quiz');

        return view('admin.soal-individu.create', compact('quizzes', 'selectedQuizId'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $soal = SoalIndividu::create([
            'quiz_id' => $data['quiz_id'],
            'narasi' => $data['narasi'],
            'order_number' => $data['order_number'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->hasFile('gambar')) {
            $soal->update(['gambar' => $request->file('gambar')->store('soal-individu', 'public')]);
        }

        $this->syncOpsi($soal, $data['opsi'], (int) $data['correct_index']);

        return redirect()->route('admin.soal-individu.index', ['quiz' => $data['quiz_id']])
            ->with('status', 'Soal berhasil ditambahkan.');
    }

    public function edit(SoalIndividu $soal)
    {
        $soal->load('opsi');
        $quizzes = Quiz::where('is_kelompok', false)->orderBy('title')->get();

        return view('admin.soal-individu.edit', compact('soal', 'quizzes'));
    }

    public function update(Request $request, SoalIndividu $soal)
    {
        $data = $this->validateRequest($request);

        $soal->update([
            'quiz_id' => $data['quiz_id'],
            'narasi' => $data['narasi'],
            'order_number' => $data['order_number'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->hasFile('gambar')) {
            if ($soal->gambar) {
                Storage::disk('public')->delete($soal->gambar);
            }
            $soal->update(['gambar' => $request->file('gambar')->store('soal-individu', 'public')]);
        }

        $this->syncOpsi($soal, $data['opsi'], (int) $data['correct_index']);

        return redirect()->route('admin.soal-individu.index', ['quiz' => $data['quiz_id']])
            ->with('status', 'Soal berhasil diperbarui.');
    }

    public function destroy(SoalIndividu $soal)
    {
        $quizId = $soal->quiz_id;

        if ($soal->gambar) {
            Storage::disk('public')->delete($soal->gambar);
        }

        $soal->delete();

        return redirect()->route('admin.soal-individu.index', ['quiz' => $quizId])
            ->with('status', 'Soal berhasil dihapus.');
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'quiz_id' => ['required', 'integer', 'exists:zd_quizzes,id'],
            'narasi' => ['required', 'string'],
            'gambar' => ['nullable', 'image', 'max:4096'],
            'order_number' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'opsi' => ['required', 'array', 'size:5'],
            'opsi.*' => ['required', 'string', 'max:150'],
            'correct_index' => ['required', 'integer', 'min:0', 'max:4'],
        ], [
            'quiz_id.required' => 'Pilih kuis induk buat soal ini.',
            'opsi.size' => 'Harus diisi tepat 5 pilihan jawaban.',
            'correct_index.required' => 'Pilih 1 jawaban yang benar.',
        ]);
    }

    /**
     * @param  array<int, string>  $opsiTexts  index 0-4
     */
    private function syncOpsi(SoalIndividu $soal, array $opsiTexts, int $correctIndex): void
    {
        $soal->opsi()->delete();

        foreach ($opsiTexts as $index => $text) {
            $soal->opsi()->create([
                'opsi_text' => $text,
                'is_correct' => (int) $index === $correctIndex,
            ]);
        }
    }
}
