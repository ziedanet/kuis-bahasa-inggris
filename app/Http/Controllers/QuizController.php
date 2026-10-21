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

        // Halaman ini boleh dibuka tanpa login; login diminta saat tombol "Start" ditekan.
        $kelompokAccount = Auth::guard('kelompok')->user();

        $activeImages = QuizImage::where('quiz_id', $quiz->id)->where('is_active', true);
        $adjCount = (clone $activeImages)->where('session_type', 'adjectives')->count();
        $verbCount = (clone $activeImages)->where('session_type', 'verbs')->count();

        return view('quiz.landing', compact('quiz', 'kelompokAccount', 'adjCount', 'verbCount'));
    }

    /**
     * Buat (atau lanjutkan) sesi main kuis kelompok ini buat akun kelompok yang login.
     */
    public function start(Request $request, Quiz $quiz)
    {
        if (! $quiz->is_kelompok) {
            abort(404);
        }

        // Belum login? Minta username & password kelompok dulu, lalu langsung lanjut mulai.
        if (! Auth::guard('kelompok')->check()) {
            $credentials = $request->validate([
                'username' => ['required', 'string'],
                'password' => ['required', 'string'],
            ]);

            if (! Auth::guard('kelompok')->attempt($credentials, true)) {
                return back()->withErrors([
                    'username' => 'Username or password is incorrect.',
                ])->onlyInput('username');
            }

            $request->session()->regenerate();
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
        $totalImages = QuizImage::where('quiz_id', $quiz->id)->where('is_active', true)->count();

        // Langkah "Check answers": habis jawaban disimpan, tampilkan benar/salah
        // soal yang barusan dijawab dulu (sekali saja, lewat flash session).
        $reviewImageId = session('review_image_id');
        if ($reviewImageId) {
            $reviewImage = QuizImage::where('quiz_id', $quiz->id)->with('words')->find($reviewImageId);
            $reviewAnswer = $reviewImage
                ? $group->answers()->where('quiz_image_id', $reviewImage->id)->first()
                : null;

            if ($reviewImage && $reviewAnswer) {
                $upcoming = $this->nextImage($quiz, $answeredImageIds);

                return view('quiz.play', [
                    'quiz' => $quiz,
                    'group' => $group,
                    'image' => $reviewImage,
                    'words' => $reviewImage->words->shuffle($reviewImage->id + $group->id)->values(),
                    'currentStep' => $answeredImageIds->count(),
                    'totalImages' => $totalImages,
                    'mode' => 'review',
                    'sessionNo' => $reviewImage->session_type === 'adjectives' ? 1 : 2,
                    'selectedIds' => array_map('intval', $reviewAnswer->selected_word_ids ?? []),
                    'answer' => $reviewAnswer,
                    'hasNext' => (bool) $upcoming,
                    'nextNewSession' => $upcoming && $upcoming->session_type !== $reviewImage->session_type,
                ]);
            }
        }

        $nextImage = $this->nextImage($quiz, $answeredImageIds);

        if (! $nextImage) {
            // Soal terakhir baru saja dijawab (atau kuis ini memang tidak punya soal) —
            // catat waktu selesai sekali saja, dipakai buat urutan leaderboard.
            if (! $group->finished_at) {
                $group->update(['finished_at' => now()]);
            }

            return redirect()->route('quiz.result', $quiz);
        }

        $currentStep = $answeredImageIds->count() + 1;

        // Acak urutan kata supaya posisi jawaban benar tidak selalu sama
        // (seed tetap per gambar+kelompok, jadi urutannya sama persis pas halaman dicek).
        $words = $nextImage->words->shuffle($nextImage->id + $group->id)->values();

        return view('quiz.play', [
            'quiz' => $quiz,
            'group' => $group,
            'image' => $nextImage,
            'words' => $words,
            'currentStep' => $currentStep,
            'totalImages' => $totalImages,
            'mode' => 'play',
            'sessionNo' => $nextImage->session_type === 'adjectives' ? 1 : 2,
            'selectedIds' => [],
            'answer' => null,
            'hasNext' => true,
            'nextNewSession' => false,
        ]);
    }

    /**
     * Soal aktif berikutnya yang belum dijawab (sesi adjectives dulu, lalu verbs).
     */
    private function nextImage(Quiz $quiz, $answeredImageIds): ?QuizImage
    {
        return QuizImage::where('quiz_id', $quiz->id)
            ->where('is_active', true)
            ->whereNotIn('id', $answeredImageIds)
            ->orderByRaw("CASE WHEN session_type = 'adjectives' THEN 0 ELSE 1 END")
            ->orderBy('order_number')
            ->with('words')
            ->first();
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

        // Tampilkan benar/salah soal ini di halaman berikutnya (langkah "Check answers").
        session()->flash('review_image_id', $image->id);

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

        // Ringkasan benar/total kata per sesi buat boarding pass.
        $sessionStats = [
            'adjectives' => ['correct' => 0, 'max' => 0],
            'verbs' => ['correct' => 0, 'max' => 0],
        ];
        foreach ($answers as $item) {
            $type = $item->image->session_type;
            if (isset($sessionStats[$type])) {
                $sessionStats[$type]['correct'] += $item->correct_count;
                $sessionStats[$type]['max'] += 5;
            }
        }

        return view('quiz.result', [
            'quiz' => $quiz,
            'group' => $group,
            'answers' => $answers,
            'sessionStats' => $sessionStats,
            'durationSeconds' => $group->durationSeconds(),
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
