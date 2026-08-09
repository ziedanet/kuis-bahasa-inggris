<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizGroup;
use App\Models\QuizImage;

class ResultController extends Controller
{
    public function index()
    {
        $totalImages = QuizImage::where('is_active', true)->count();

        $groups = QuizGroup::with('kelas')
            ->withCount('answers')
            ->withSum('answers', 'score')
            ->orderByDesc('answers_sum_score')
            ->get()
            ->map(function (QuizGroup $group) use ($totalImages) {
                $group->total_score = $group->answers_sum_score ?? 0;
                $group->predikat_label = $group->predikat();
                $group->is_complete = $group->answers_count >= $totalImages;

                return $group;
            });

        return view('admin.results', compact('groups', 'totalImages'));
    }

    public function destroy(QuizGroup $group)
    {
        $group->delete();

        return back()->with('status', 'Data kelompok dihapus.');
    }
}
