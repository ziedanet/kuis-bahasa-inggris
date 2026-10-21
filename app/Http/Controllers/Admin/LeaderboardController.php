<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IndividuAttempt;
use App\Models\Kelompok;
use App\Models\Quiz;
use App\Models\QuizGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /** Maksimal baris "Waiting" yang ditampilkan di bawah peringkat. */
    private const WAITING_LIMIT = 40;

    /**
     * Papan peringkat hasil pengisian kuis, dipisah kelompok & individu.
     * Urutan: skor tertinggi dulu, kalau skor sama yang waktu pengerjaannya paling
     * singkat menang. Halaman ini auto-refresh (lihat ?partial=1) buat ditampilkan di monitor.
     */
    public function index(Request $request)
    {
        $tipe = $request->query('tipe') === 'individu' ? 'individu' : 'kelompok';
        $quizId = $request->query('quiz_id');

        $quizList = Quiz::where('is_kelompok', $tipe === 'kelompok')
            ->orderBy('order_number')
            ->get();

        [$rows, $waiting, $waitingTotal] = $tipe === 'kelompok'
            ? $this->kelompokBoard($quizId)
            : $this->individuBoard($quizId);

        $data = compact('rows', 'waiting', 'waitingTotal', 'quizList', 'tipe', 'quizId');

        // Dipanggil JS tiap 10 detik: kirim isi papan saja, tanpa layout.
        if ($request->boolean('partial')) {
            return view('admin.leaderboard._rows', $data);
        }

        return view('admin.leaderboard.index', $data);
    }

    /**
     * Halaman cetak peringkat (Save as PDF lewat dialog cetak browser). Mengikuti
     * filter tipe & kuis yang sedang aktif di Leaderboard; hanya yang sudah selesai.
     */
    public function export(Request $request)
    {
        $tipe = $request->query('tipe') === 'individu' ? 'individu' : 'kelompok';
        $quizId = $request->query('quiz_id');

        [$rows] = $tipe === 'kelompok'
            ? $this->kelompokBoard($quizId)
            : $this->individuBoard($quizId);

        $quizTitle = $quizId ? Quiz::find($quizId)?->title : null;

        return view('admin.leaderboard.export', compact('rows', 'tipe', 'quizTitle'));
    }

    /**
     * Rincian jawaban satu kelompok: kata mana yang benar & salah per foto.
     */
    public function showKelompok(QuizGroup $group)
    {
        $group->load(['quiz' => fn ($q) => $q->withCount('images'), 'kelas'])->loadSum('answers', 'score');

        $answers = $group->answers()
            ->with('image.words')
            ->get()
            ->filter(fn ($answer) => $answer->image)
            ->sortBy(fn ($answer) => [
                $answer->image->session_type === 'adjectives' ? 0 : 1,
                $answer->image->order_number,
            ])
            ->values();

        return view('admin.leaderboard.show-kelompok', compact('group', 'answers'));
    }

    /**
     * Rincian jawaban satu siswa: pilihan mana yang benar & salah per soal.
     */
    public function showIndividu(IndividuAttempt $attempt)
    {
        $attempt->load(['quiz' => fn ($q) => $q->withCount('soalIndividu'), 'siswa.kelas', 'kelas'])->loadSum('answers', 'score');

        $answers = $attempt->answers()
            ->with(['soal.opsi', 'opsi'])
            ->get()
            ->filter(fn ($answer) => $answer->soal)
            ->sortBy(fn ($answer) => $answer->soal->order_number)
            ->values();

        return view('admin.leaderboard.show-individu', compact('attempt', 'answers'));
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

    /**
     * @return array{0: \Illuminate\Support\Collection, 1: \Illuminate\Support\Collection, 2: int}
     */
    private function kelompokBoard($quizId): array
    {
        // Urutan & tampilan skor pakai scoreOn100() (0-100), bukan skor mentah —
        // supaya kuis yang jumlah soalnya beda-beda tetap adil dibandingkan,
        // termasuk waktu filter "Semua Kuis" yang gabung banyak kuis sekaligus.
        // Live: kelompok yang sudah mulai menjawab (walau belum selesai) langsung masuk
        // peringkat dengan skor sementara. Yang belum menjawab apa pun tampil di bawah.
        $rows = QuizGroup::with(['quiz' => fn ($q) => $q->withCount('images'), 'kelas'])
            ->withSum('answers', 'score')
            ->withSum('answers', 'correct_count')
            ->withCount('answers')
            ->where(fn ($query) => $query->whereNotNull('finished_at')->orHas('answers'))
            ->whereHas('quiz', fn ($query) => $query->where('is_kelompok', true))
            ->when($quizId, fn ($query) => $query->where('quiz_id', $quizId))
            ->get()
            ->each(fn (QuizGroup $g) => $g->setAttribute('progress', $this->groupProgress($g)))
            ->sort(fn (QuizGroup $a, QuizGroup $b) => $this->compare($a, $b))
            ->values();

        // Akun kelompok tanpa jawaban sama sekali: "Waiting" (belum mulai) atau "Playing" (sudah masuk kuis).
        $playing = QuizGroup::whereNull('finished_at')
            ->whereNotNull('kelompok_id')
            ->whereDoesntHave('answers')
            ->whereHas('quiz', fn ($query) => $query->where('is_kelompok', true))
            ->when($quizId, fn ($query) => $query->where('quiz_id', $quizId))
            ->pluck('id', 'kelompok_id');

        $waitingQuery = Kelompok::with('kelas')
            ->whereNotIn('id', $rows->pluck('kelompok_id')->filter()->all())
            ->orderBy('name');

        $waitingTotal = (clone $waitingQuery)->count();
        $waiting = $waitingQuery->limit(self::WAITING_LIMIT)->get()->map(fn (Kelompok $k) => [
            'name' => $k->name,
            'kelas' => $k->kelas?->name,
            'playing' => $playing->has($k->id),
            'reset_url' => $playing->has($k->id)
                ? route('admin.leaderboard.kelompok.destroy', $playing[$k->id])
                : null,
        ]);

        return [$rows, $waiting, $waitingTotal];
    }

    /**
     * @return array{0: \Illuminate\Support\Collection, 1: \Illuminate\Support\Collection, 2: int}
     */
    private function individuBoard($quizId): array
    {
        $rows = IndividuAttempt::with(['quiz' => fn ($q) => $q->withCount('soalIndividu'), 'siswa.kelas', 'kelas'])
            ->withSum('answers', 'score')
            ->withCount([
                'answers',
                'answers as correct_answers_count' => fn ($q) => $q->where('is_correct', true),
            ])
            ->where(fn ($query) => $query->whereNotNull('finished_at')->orHas('answers'))
            ->when($quizId, fn ($query) => $query->where('quiz_id', $quizId))
            ->get()
            ->each(fn (IndividuAttempt $a) => $a->setAttribute('progress', $this->individuProgress($a)))
            ->sort(fn (IndividuAttempt $a, IndividuAttempt $b) => $this->compare($a, $b))
            ->values();

        // Baris redup: peserta yang sudah masuk tapi belum menjawab satu soal pun.
        $playingQuery = IndividuAttempt::with(['siswa.kelas', 'kelas'])
            ->whereNull('finished_at')
            ->whereDoesntHave('answers')
            ->when($quizId, fn ($query) => $query->where('quiz_id', $quizId))
            ->orderBy('created_at');

        $waitingTotal = (clone $playingQuery)->count();
        $waiting = $playingQuery->limit(self::WAITING_LIMIT)->get()->map(fn (IndividuAttempt $a) => [
            'name' => $a->displayName(),
            'kelas' => $a->displayKelas(),
            'playing' => true,
            'reset_url' => route('admin.leaderboard.individu.destroy', $a),
        ]);

        return [$rows, $waiting, $waitingTotal];
    }

    /**
     * Statistik live satu kelompok. Skor kelompok dihitung per KATA (5 kata per foto),
     * jadi benar/salah dilaporkan dalam kata, dan progres dalam foto.
     */
    private function groupProgress(QuizGroup $g): array
    {
        $total = (int) ($g->quiz?->images_count ?? 0);
        $answered = (int) $g->answers_count;
        $correct = (int) $g->answers_sum_correct_count;

        return [
            'unit' => 'foto',
            'total' => $total,
            'answered' => $answered,
            'remaining' => max(0, $total - $answered),
            'correct' => $correct,
            'wrong' => $answered * 5 - $correct,
            'correct_label' => 'kata benar',
            'wrong_label' => 'kata salah',
            'finished' => (bool) $g->finished_at,
        ];
    }

    private function individuProgress(IndividuAttempt $a): array
    {
        $total = (int) ($a->quiz?->soal_individu_count ?? 0);
        $answered = (int) $a->answers_count;
        $correct = (int) $a->correct_answers_count;

        return [
            'unit' => 'soal',
            'total' => $total,
            'answered' => $answered,
            'remaining' => max(0, $total - $answered),
            'correct' => $correct,
            'wrong' => $answered - $correct,
            'correct_label' => 'benar',
            'wrong_label' => 'salah',
            'finished' => (bool) $a->finished_at,
        ];
    }

    /** Skor tertinggi dulu; kalau seri, waktu pengerjaan tersingkat menang. */
    private function compare($a, $b): int
    {
        $scoreA = $a->scoreOn100();
        $scoreB = $b->scoreOn100();

        if ($scoreA !== $scoreB) {
            return $scoreB <=> $scoreA;
        }

        return ($a->durationSeconds() ?? PHP_INT_MAX) <=> ($b->durationSeconds() ?? PHP_INT_MAX);
    }
}
