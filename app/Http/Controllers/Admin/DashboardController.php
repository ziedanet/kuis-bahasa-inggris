<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizGroup;
use App\Models\QuizImage;

class DashboardController extends Controller
{
    /**
     * Dashboard sekarang cuma fokus statistik & grafik.
     * Tabel manajemen kuis dipindah ke halaman "Daftar Kuis" (Admin\QuizController::index).
     */
    public function index()
    {
        $quizzes = Quiz::withCount('images')
            ->orderBy('order_number')
            ->orderBy('title')
            ->get();

        $totalKuis = $quizzes->count();
        $totalSoal = QuizImage::count();

        $pesertaSelesai = 0;
        $participationLabels = [];
        $participationCounts = [];
        $predikatCounts = [
            'Sangat Baik' => 0,
            'Baik' => 0,
            'Cukup' => 0,
            'Perlu Bimbingan' => 0,
        ];

        foreach ($quizzes as $quiz) {
            $soalCount = $quiz->images_count;

            $groups = QuizGroup::where('quiz_id', $quiz->id)->withCount('answers')->get();

            $participationLabels[] = $quiz->title;
            $participationCounts[] = $groups->count();

            if ($soalCount < 1) {
                continue;
            }

            foreach ($groups as $group) {
                if ($group->answers_count >= $soalCount) {
                    $pesertaSelesai++;
                    $group->setRelation('quiz', $quiz);
                    $predikatCounts[$group->predikat()]++;
                }
            }
        }

        return view('admin.dashboard', compact(
            'totalKuis',
            'totalSoal',
            'pesertaSelesai',
            'participationLabels',
            'participationCounts',
            'predikatCounts'
        ));
    }
}
