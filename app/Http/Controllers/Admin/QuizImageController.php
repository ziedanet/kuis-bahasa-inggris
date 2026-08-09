<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuizImageController extends Controller
{
    public function create(Request $request)
    {
        $quizzes = Quiz::orderBy('title')->get();
        $selectedQuizId = (int) $request->query('quiz');

        return view('admin.images.create', compact('quizzes', 'selectedQuizId'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $image = QuizImage::create([
            'quiz_id' => $data['quiz_id'],
            'session_type' => $data['session_type'],
            'title' => $data['title'],
            'instruction' => $data['instruction'],
            'order_number' => $data['order_number'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('quiz', 'public');
            $image->update(['image_path' => $path]);
        }

        $this->syncWords($image, $data['words'], $data['correct_words'] ?? []);

        return redirect()->route('admin.soal.index', ['quiz' => $data['quiz_id']])
            ->with('status', 'Soal berhasil ditambahkan.');
    }

    public function edit(QuizImage $image)
    {
        $image->load('words');
        $quizzes = Quiz::orderBy('title')->get();

        return view('admin.images.edit', compact('image', 'quizzes'));
    }

    public function update(Request $request, QuizImage $image)
    {
        $data = $this->validateRequest($request);

        $image->update([
            'quiz_id' => $data['quiz_id'],
            'session_type' => $data['session_type'],
            'title' => $data['title'],
            'instruction' => $data['instruction'],
            'order_number' => $data['order_number'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->hasFile('image')) {
            if ($image->image_path) {
                Storage::disk('public')->delete($image->image_path);
            }
            $path = $request->file('image')->store('quiz', 'public');
            $image->update(['image_path' => $path]);
        }

        $this->syncWords($image, $data['words'], $data['correct_words'] ?? [], resetAnswers: true);

        return redirect()->route('admin.soal.index', ['quiz' => $data['quiz_id']])
            ->with('status', 'Soal berhasil diperbarui.');
    }

    public function destroy(QuizImage $image)
    {
        $quizId = $image->quiz_id;

        if ($image->image_path) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return redirect()->route('admin.soal.index', ['quiz' => $quizId])
            ->with('status', 'Soal berhasil dihapus.');
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'quiz_id' => ['required', 'integer', 'exists:zd_quizzes,id'],
            'session_type' => ['required', 'in:adjectives,verbs'],
            'title' => ['required', 'string', 'max:150'],
            'instruction' => ['required', 'string'],
            'order_number' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:4096'],
            'words' => ['required', 'array', 'size:10'],
            'words.*' => ['required', 'string', 'max:60'],
            'correct_words' => ['nullable', 'array', 'size:5'],
            'correct_words.*' => ['integer', 'min:0', 'max:9'],
        ], [
            'quiz_id.required' => 'Pilih kuis induk buat soal ini.',
            'words.size' => 'Harus diisi tepat 10 pilihan kata.',
            'correct_words.size' => 'Pilih tepat 5 kata sebagai kunci jawaban benar.',
        ]);
    }

    /**
     * @param  array<int, string>  $words  index 0-9
     * @param  array<int, int>  $correctIndexes  index (0-9) kata yang benar
     */
    private function syncWords(QuizImage $image, array $words, array $correctIndexes, bool $resetAnswers = false): void
    {
        $image->words()->delete();

        foreach ($words as $index => $word) {
            $image->words()->create([
                'word' => $word,
                'is_correct' => in_array((int) $index, array_map('intval', $correctIndexes), true),
            ]);
        }

        if ($resetAnswers) {
            // Jawaban lama tidak valid lagi karena kata/kunci berubah.
            $image->answers()->delete();
        }
    }
}
