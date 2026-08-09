<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizGroup;
use App\Models\QuizGroupAnswer;
use App\Models\QuizImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class QuizController extends Controller
{
    /**
     * Landing page marketing/perkenalan kuis (katalog semua kuis aktif).
     */
    public function home(Request $request)
    {
        $tipe = $request->query('tipe'); // null = semua, 'kelompok', atau 'individu'

        $quizzes = Quiz::where('is_active', true)
            ->when($tipe === 'kelompok', fn ($query) => $query->where('is_kelompok', true))
            ->when($tipe === 'individu', fn ($query) => $query->where('is_kelompok', false))
            ->withCount(['images', 'soalIndividu'])
            ->orderBy('order_number')
            ->get();

        return view('quiz.home', compact('quizzes', 'tipe'));
    }

    /**
     * Halaman konfirmasi sebelum mulai kuis kelompok. Wajib sudah login sebagai
     * kelompok — identitas kelompok (nama & kelas) diambil dari akun yang login,
     * tidak lagi diisi manual lewat form.
     */
    public function landing(Quiz $quiz)
    {
        if (! $quiz->is_kelompok) {
            abort(404);
        }

        if (! Auth::guard('kelompok')->check()) {
            return redirect()->route('quiz.home')->withFragment('user-login')
                ->withErrors(['login' => 'Login sebagai kelompok dulu buat mulai kuis "' . $quiz->title . '".']);
        }

        $kelompokAccount = Auth::guard('kelompok')->user();

        return view('quiz.landing', compact('quiz', 'kelompokAccount'));
    }

    /**
     * Buat (atau lanjutkan) sesi main kuis kelompok ini buat akun kelompok yang login.
     */
    public function start(Request $request, Quiz $quiz)
    {
        if (! $quiz->is_kelompok) {
            abort(404);
        }

        if (! Auth::guard('kelompok')->check()) {
            return redirect()->route('quiz.home')->withFragment('user-login');
        }

        $kelompokAccount = Auth::guard('kelompok')->user();

        QuizGroup::firstOrCreate(
            ['quiz_id' => $quiz->id, 'kelompok_id' => $kelompokAccount->id],
            ['name' => $kelompokAccount->name, 'kelas_id' => $kelompokAccount->kelas_id]
        );

        return redirect()->route('quiz.play', $quiz);
    }

    /**
     * Tampilkan soal berikutnya yang belum dijawab kelompok ini, untuk kuis ini.
     */
    public function play(Quiz $quiz)
    {
        $group = $this->currentGroup($quiz);

        if (! $group) {
            return redirect()->route('quiz.landing', $quiz);
        }

        $answeredImageIds = $group->answers()->pluck('quiz_image_id');

        $nextImage = QuizImage::where('quiz_id', $quiz->id)
            ->where('is_active', true)
            ->whereNotIn('id', $answeredImageIds)
            ->orderByRaw("CASE WHEN session_type = 'adjectives' THEN 0 ELSE 1 END")
            ->orderBy('order_number')
            ->with('words')
            ->first();

        if (! $nextImage) {
            // Soal terakhir baru saja dijawab (atau kuis ini memang tidak punya soal) —
            // catat waktu selesai sekali saja, dipakai buat urutan leaderboard.
            if (! $group->finished_at) {
                $group->update(['finished_at' => now()]);
            }

            return redirect()->route('quiz.result', $quiz);
        }

        $totalImages = QuizImage::where('quiz_id', $quiz->id)->where('is_active', true)->count();
        $currentStep = $answeredImageIds->count() + 1;

        // Acak urutan kata supaya posisi jawaban benar tidak selalu sama.
        $words = $nextImage->words->shuffle()->values();

        return view('quiz.play', [
            'quiz' => $quiz,
            'group' => $group,
            'image' => $nextImage,
            'words' => $words,
            'currentStep' => $currentStep,
            'totalImages' => $totalImages,
        ]);
    }

    /**
     * Simpan jawaban kelompok untuk satu soal.
     */
    public function answer(Request $request, Quiz $quiz)
    {
        $group = $this->currentGroup($quiz);

        if (! $group) {
            return redirect()->route('quiz.landing', $quiz);
        }

        $validated = $request->validate([
            'quiz_image_id' => [
                'required', 'integer',
                Rule::exists('zd_quiz_images', 'id')->where('quiz_id', $quiz->id),
            ],
            'word_ids' => ['required', 'array', 'size:5'],
            'word_ids.*' => ['integer', Rule::exists('zd_quiz_words', 'id')],
        ], [
            'word_ids.size' => 'Pilih tepat 5 kata sebelum lanjut.',
        ]);

        $image = QuizImage::where('quiz_id', $quiz->id)->with('words')->findOrFail($validated['quiz_image_id']);

        // Pastikan semua kata yang dipilih benar-benar milik gambar ini.
        $validWordIds = $image->words->pluck('id')->all();
        $selectedIds = array_values(array_intersect($validated['word_ids'], $validWordIds));

        if (count($selectedIds) !== 5) {
            return back()->withErrors(['word_ids' => 'Pilihan kata tidak valid, coba lagi.']);
        }

        $correctCount = $image->words->whereIn('id', $selectedIds)->where('is_correct', true)->count();
        $score = $correctCount * 2.5;

        QuizGroupAnswer::updateOrCreate(
            ['quiz_group_id' => $group->id, 'quiz_image_id' => $image->id],
            [
                'selected_word_ids' => $selectedIds,
                'correct_count' => $correctCount,
                'score' => $score,
            ]
        );

        return redirect()->route('quiz.play', $quiz);
    }

    /**
     * Halaman hasil akhir kelompok untuk kuis ini.
     */
    public function result(Quiz $quiz)
    {
        $group = $this->currentGroup($quiz);

        if (! $group) {
            return redirect()->route('quiz.landing', $quiz);
        }

        $answers = $group->answers()->with(['image.words'])->get();

        return view('quiz.result', [
            'quiz' => $quiz,
            'group' => $group,
            'answers' => $answers,
            'scoreOn100' => $group->scoreOn100(),
            'predikat' => $group->predikat(),
        ]);
    }

    /**
     * Sesi kelompok yang sedang main kuis ini diambil langsung dari akun kelompok
     * yang login (bukan session browser lagi) — jadi progres tetap nyambung
     * walau pindah perangkat, dan tidak bisa ketuker sama kelompok lain.
     */
    private function currentGroup(Quiz $quiz): ?QuizGroup
    {
        if (! $quiz->is_kelompok || ! Auth::guard('kelompok')->check()) {
            return null;
        }

        return QuizGroup::where('quiz_id', $quiz->id)
            ->where('kelompok_id', Auth::guard('kelompok')->id())
            ->first();
    }
}
