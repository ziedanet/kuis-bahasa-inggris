<?php

namespace App\Http\Controllers;

use App\Models\IndividuAttempt;
use App\Models\IndividuAttemptAnswer;
use App\Models\Quiz;
use App\Models\SoalIndividu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Alur main publik kuis individu (per siswa, wajib login siswa) — beda dari
 * QuizController yang khusus kuis kelompok.
 */
class IndividuPlayController extends Controller
{
    public function play(Quiz $quiz)
    {
        if ($quiz->is_kelompok) {
            abort(404);
        }

        if (! Auth::guard('siswa')->check()) {
            return redirect()->route('quiz.home')->withFragment('user-login')
                ->withErrors(['login' => 'Please sign in as a student first to start the quiz "' . $quiz->title . '".']);
        }

        $siswa = Auth::guard('siswa')->user();

        $attempt = IndividuAttempt::firstOrCreate([
            'quiz_id' => $quiz->id,
            'siswa_id' => $siswa->id,
        ]);

        if ($attempt->finished_at) {
            return redirect()->route('quiz.individu.result', $quiz);
        }

        $answeredSoalIds = $attempt->answers()->pluck('soal_id');

        $nextSoal = SoalIndividu::where('quiz_id', $quiz->id)
            ->where('is_active', true)
            ->whereNotIn('id', $answeredSoalIds)
            ->orderBy('order_number')
            ->with('opsi')
            ->first();

        if (! $nextSoal) {
            if (! $attempt->finished_at) {
                $attempt->update(['finished_at' => now()]);
            }

            return redirect()->route('quiz.individu.result', $quiz);
        }

        $totalSoal = SoalIndividu::where('quiz_id', $quiz->id)->where('is_active', true)->count();
        $currentStep = $answeredSoalIds->count() + 1;

        // Acak urutan opsi jawaban supaya posisi jawaban benar tidak selalu sama.
        $opsiList = $nextSoal->opsi->shuffle()->values();

        return view('quiz.individu-play', [
            'quiz' => $quiz,
            'soal' => $nextSoal,
            'opsiList' => $opsiList,
            'currentStep' => $currentStep,
            'totalSoal' => $totalSoal,
        ]);
    }

    public function answer(Request $request, Quiz $quiz)
    {
        if ($quiz->is_kelompok) {
            abort(404);
        }

        if (! Auth::guard('siswa')->check()) {
            return redirect()->route('quiz.home')->withFragment('user-login');
        }

        $siswa = Auth::guard('siswa')->user();

        $attempt = IndividuAttempt::where('quiz_id', $quiz->id)->where('siswa_id', $siswa->id)->first();

        if (! $attempt) {
            return redirect()->route('quiz.individu.play', $quiz);
        }

        $validated = $request->validate([
            'soal_id' => [
                'required', 'integer',
                Rule::exists('zd_soal_individu', 'id')->where('quiz_id', $quiz->id),
            ],
            'opsi_id' => [
                'required', 'integer',
                Rule::exists('zd_opsi_individu', 'id')->where('soal_id', $request->input('soal_id')),
            ],
        ], [
            'opsi_id.required' => 'Please choose an answer before continuing.',
            'opsi_id.exists' => 'Pilihan jawaban tidak valid, coba lagi.',
        ]);

        $soal = SoalIndividu::with('opsi')->findOrFail($validated['soal_id']);
        $opsi = $soal->opsi->firstWhere('id', (int) $validated['opsi_id']);

        if (! $opsi) {
            return back()->withErrors(['opsi_id' => 'Pilihan jawaban tidak valid, coba lagi.']);
        }

        IndividuAttemptAnswer::updateOrCreate(
            ['attempt_id' => $attempt->id, 'soal_id' => $soal->id],
            [
                'opsi_id' => $opsi->id,
                'is_correct' => $opsi->is_correct,
                'score' => $opsi->is_correct ? 1 : 0,
            ]
        );

        return redirect()->route('quiz.individu.play', $quiz);
    }

    public function result(Quiz $quiz)
    {
        if ($quiz->is_kelompok) {
            abort(404);
        }

        if (! Auth::guard('siswa')->check()) {
            return redirect()->route('quiz.home')->withFragment('user-login');
        }

        $siswa = Auth::guard('siswa')->user();

        $attempt = IndividuAttempt::where('quiz_id', $quiz->id)->where('siswa_id', $siswa->id)->first();

        if (! $attempt) {
            return redirect()->route('quiz.individu.play', $quiz);
        }

        $answers = $attempt->answers()->with(['soal', 'opsi'])->get();

        return view('quiz.individu-result', [
            'quiz' => $quiz,
            'attempt' => $attempt,
            'answers' => $answers,
            'scoreOn100' => $attempt->scoreOn100(),
            'predikat' => $attempt->predikat(),
        ]);
    }
}
