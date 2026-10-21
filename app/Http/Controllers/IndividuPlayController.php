<?php

namespace App\Http\Controllers;

use App\Models\IndividuAttempt;
use App\Models\IndividuAttemptAnswer;
use App\Models\MasterKelas;
use App\Models\Quiz;
use App\Models\SoalIndividu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Alur main publik kuis individu — TANPA login. Peserta mengisi nama + kelas,
 * lalu progresnya dikenali lewat session browser (key per kuis). Beda dari
 * QuizController yang khusus kuis kelompok (wajib login kelompok).
 */
class IndividuPlayController extends Controller
{
    /** Nilai minimal (0-100) buat dianggap lulus di halaman hasil. */
    private const PASS_SCORE = 60;

    /**
     * Form awal: nama lengkap + pilih kelas.
     */
    public function landing(Quiz $quiz)
    {
        $this->ensureIndividu($quiz);

        // Peserta yang masih mengerjakan dilanjutkan, bukan disuruh isi data lagi.
        $attempt = $this->currentAttempt($quiz);
        if ($attempt && ! $attempt->finished_at) {
            return redirect()->route('quiz.individu.play', $quiz);
        }

        $kelasList = MasterKelas::orderBy('name')->get();
        $totalSoal = SoalIndividu::where('quiz_id', $quiz->id)->where('is_active', true)->count();

        return view('quiz.individu-landing', compact('quiz', 'kelasList', 'totalSoal'));
    }

    /**
     * Simpan data diri peserta dan mulai (buat attempt baru).
     */
    public function start(Request $request, Quiz $quiz)
    {
        $this->ensureIndividu($quiz);

        $data = $request->validate([
            'participant_name' => ['required', 'string', 'max:150'],
            'kelas_id' => ['required', 'integer', 'exists:zd_master_kelas,id'],
        ], [
            'participant_name.required' => 'Please enter your full name.',
            'kelas_id.required' => 'Please choose your class.',
            'kelas_id.exists' => 'Please choose your class.',
        ]);

        $attempt = IndividuAttempt::create([
            'quiz_id' => $quiz->id,
            'participant_name' => trim($data['participant_name']),
            'kelas_id' => $data['kelas_id'],
        ]);

        session([$this->sessionKey($quiz) => $attempt->id]);

        return redirect()->route('quiz.individu.play', $quiz);
    }

    /**
     * Tampilkan soal berikutnya yang belum dijawab (1 langkah = 1 soal).
     */
    public function play(Quiz $quiz)
    {
        $this->ensureIndividu($quiz);

        $attempt = $this->currentAttempt($quiz);
        if (! $attempt) {
            return redirect()->route('quiz.individu.landing', $quiz);
        }

        $answeredSoalIds = $attempt->answers()->pluck('soal_id');

        $nextSoal = SoalIndividu::where('quiz_id', $quiz->id)
            ->where('is_active', true)
            ->whereNotIn('id', $answeredSoalIds)
            ->orderBy('order_number')
            ->orderBy('id')
            ->with(['opsi' => fn ($query) => $query->orderBy('id')])
            ->first();

        if (! $nextSoal) {
            if (! $attempt->finished_at) {
                $attempt->update(['finished_at' => now()]);
            }

            return redirect()->route('quiz.individu.result', $quiz);
        }

        $totalSoal = SoalIndividu::where('quiz_id', $quiz->id)->where('is_active', true)->count();

        return view('quiz.individu-play', [
            'quiz' => $quiz,
            'attempt' => $attempt->loadMissing('kelas'),
            'soal' => $nextSoal,
            // Urutan tetap (A-D mengikuti urutan input admin) supaya huruf di halaman hasil konsisten.
            'opsiList' => $nextSoal->opsi->values(),
            'currentStep' => $answeredSoalIds->count() + 1,
            'totalSoal' => $totalSoal,
            'isLast' => $answeredSoalIds->count() + 1 >= $totalSoal,
        ]);
    }

    public function answer(Request $request, Quiz $quiz)
    {
        $this->ensureIndividu($quiz);

        $attempt = $this->currentAttempt($quiz);
        if (! $attempt) {
            return redirect()->route('quiz.individu.landing', $quiz);
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
        ]);

        $soal = SoalIndividu::where('quiz_id', $quiz->id)->findOrFail($validated['soal_id']);
        $opsi = $soal->opsi()->find($validated['opsi_id']);

        if (! $opsi) {
            return back()->withErrors(['opsi_id' => 'Invalid answer choice, please try again.']);
        }

        // withTrashed: pulihkan jawaban lama yang ke-soft-delete supaya tidak kena unique constraint.
        $record = IndividuAttemptAnswer::withTrashed()->firstOrNew(
            ['attempt_id' => $attempt->id, 'soal_id' => $soal->id]
        );
        $record->fill([
            'opsi_id' => $opsi->id,
            'is_correct' => $opsi->is_correct,
            'score' => $opsi->is_correct ? 1 : 0,
        ]);
        $record->deleted_at = null;
        $record->save();

        return redirect()->route('quiz.individu.play', $quiz);
    }

    public function result(Quiz $quiz)
    {
        $this->ensureIndividu($quiz);

        $attempt = $this->currentAttempt($quiz);
        if (! $attempt) {
            return redirect()->route('quiz.individu.landing', $quiz);
        }

        if (! $attempt->finished_at) {
            return redirect()->route('quiz.individu.play', $quiz);
        }

        $attempt->load('kelas');
        $answers = $attempt->answers()
            ->with(['soal.opsi' => fn ($query) => $query->orderBy('id'), 'opsi'])
            ->get()
            ->filter(fn ($answer) => $answer->soal)
            ->sortBy(fn ($answer) => $answer->soal->order_number)
            ->values();

        $scoreOn100 = $attempt->scoreOn100();

        return view('quiz.individu-result', [
            'quiz' => $quiz,
            'attempt' => $attempt,
            'answers' => $answers,
            'scoreOn100' => $scoreOn100,
            'passed' => $scoreOn100 >= self::PASS_SCORE,
            'passScore' => self::PASS_SCORE,
        ]);
    }

    /**
     * "Try again": jawaban sebelumnya ditimpa (dihapus), waktu mulai di-reset,
     * lalu langsung ke soal pertama tanpa isi data diri lagi.
     */
    public function retry(Quiz $quiz)
    {
        $this->ensureIndividu($quiz);

        $attempt = $this->currentAttempt($quiz);
        if (! $attempt) {
            return redirect()->route('quiz.individu.landing', $quiz);
        }

        $attempt->answers()->withTrashed()->forceDelete();
        $attempt->finished_at = null;
        $attempt->created_at = now();
        $attempt->save();

        return redirect()->route('quiz.individu.play', $quiz);
    }

    /**
     * "Finish": lepaskan sesi peserta ini lalu balik ke halaman utama.
     */
    public function finish(Quiz $quiz)
    {
        $this->ensureIndividu($quiz);

        session()->forget($this->sessionKey($quiz));

        return redirect()->route('quiz.home');
    }

    private function ensureIndividu(Quiz $quiz): void
    {
        if ($quiz->is_kelompok || ! $quiz->is_active) {
            abort(404);
        }
    }

    private function sessionKey(Quiz $quiz): string
    {
        return 'individu_attempt_' . $quiz->id;
    }

    private function currentAttempt(Quiz $quiz): ?IndividuAttempt
    {
        $id = session($this->sessionKey($quiz));

        return $id
            ? IndividuAttempt::where('quiz_id', $quiz->id)->find($id)
            : null;
    }
}
