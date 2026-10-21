<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IndividuAttempt;
use App\Models\Quiz;
use App\Models\QuizGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /**
     * Papan peringkat hasil pengisian kuis, dipisah kelompok & individu.
     * Urutan: skor tertinggi dulu, kalau skor sama yang waktu pengerjaannya paling
     * singkat menang.
     */
    public function index(Request $request)
    {
        $tipe = $request->query('tipe') === 'individu' ? 'individu' : 'kelompok';
        $quizId = $request->query('quiz_id');

        $quizList = Quiz::where('is_kelompok', $tipe === 'kelompok')
            ->orderBy('order_number')
            ->get();

        $rows = collect();

        // Urutan & tampilan skor pakai scoreOn100() (0-100), bukan skor mentah —
        // supaya kuis yang jumlah soalnya beda-beda tetap adil dibandingkan,
        // termasuk waktu filter "Semua Kuis" yang gabung banyak kuis sekaligus.
        if ($tipe === 'kelompok') {
            $rows = QuizGroup::with(['quiz', 'kelas'])
                ->withSum('answers', 'score')
                ->whereNotNull('finished_at')
                ->whereHas('quiz', fn ($query) => $query->where('is_kelompok', true))
                ->when($quizId, fn ($query) => $query->where('quiz_id', $quizId))
                ->get()
                ->sort(function (QuizGroup $a, QuizGroup $b) {
                    $scoreA = $a->scoreOn100();
                    $scoreB = $b->scoreOn100();

                    if ($scoreA !== $scoreB) {
                        return $scoreB <=> $scoreA;
                    }

                    return ($a->durationSeconds() ?? PHP_INT_MAX) <=> ($b->durationSeconds() ?? PHP_INT_MAX);
                })
                ->values();
        }

        if ($tipe === 'individu') {
            $rows = IndividuAttempt::with(['quiz', 'siswa.kelas'])
                ->withSum('answers', 'score')
                ->whereNotNull('finished_at')
                ->when($quizId, fn ($query) => $query->where('quiz_id', $quizId))
                ->get()
                ->sort(function (IndividuAttempt $a, IndividuAttempt $b) {
                    $scoreA = $a->scoreOn100();
                    $scoreB = $b->scoreOn100();

                    if ($scoreA !== $scoreB) {
                        return $scoreB <=> $scoreA;
                    }

                    return ($a->durationSeconds() ?? PHP_INT_MAX) <=> ($b->durationSeconds() ?? PHP_INT_MAX);
                })
                ->values();
        }

        return view('admin.leaderboard.index', compact('rows', 'quizList', 'tipe', 'quizId'));
    }

    /**
     * Hapus riwayat main satu kelompok (beserta semua jawabannya) supaya kelompok
     * itu bisa mengisi kuis dari awal lagi. Hapus permanen (bukan soft delete)
     * karena sesi main dicari lewat quiz_id + akun login.
     */
    public function destroyKelompok(QuizGroup $group)
    {
        $name = $group->name;

        DB::transaction(function () use ($group) {
            $group->answers()->withTrashed()->forceDelete();
            $group->forceDelete();
        });

        return back()->with('status', "Riwayat kuis kelompok {$name} dihapus. Kelompok ini bisa mengisi kuis lagi.");
    }

    /**
     * Sama seperti di atas, tapi untuk riwayat kuis individu satu siswa.
     * Wajib hapus permanen karena tabel punya unique (quiz_id, siswa_id).
     */
    public function destroyIndividu(IndividuAttempt $attempt)
    {
        $name = $attempt->siswa?->name ?? 'siswa';

        DB::transaction(function () use ($attempt) {
            $attempt->answers()->withTrashed()->forceDelete();
            $attempt->forceDelete();
        });

        return back()->with('status', "Riwayat kuis {$name} dihapus. Siswa ini bisa mengisi kuis lagi.");
    }
}
