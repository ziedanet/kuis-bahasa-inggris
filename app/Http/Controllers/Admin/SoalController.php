<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class SoalController extends Controller
{
    public function index(Request $request)
    {
        $quizzes = Quiz::orderBy('title')->get();

        $selectedQuiz = $request->filled('quiz')
            ? $quizzes->firstWhere('id', (int) $request->query('quiz'))
            : $quizzes->first();

        $soal = $selectedQuiz
            ? $selectedQuiz->images()->withCount('words')->orderBy('order_number')->get()
            : collect();

        return view('admin.soal.index', [
            'quizzes' => $quizzes,
            'selectedQuiz' => $selectedQuiz,
            'soal' => $soal,
        ]);
    }
}
