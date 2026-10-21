<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KelompokController;
use App\Http\Controllers\Admin\LeaderboardController;
use App\Http\Controllers\Admin\MasterKelasController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Admin\QuizImageController;
use App\Http\Controllers\Admin\RefleksiController as AdminRefleksiController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\SoalController;
use App\Http\Controllers\Admin\SoalIndividuController;
use App\Http\Controllers\IndividuPlayController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\RefleksiController;
use App\Http\Controllers\UserAuthController;
use Illuminate\Support\Facades\Route;

// ================= Publik: katalog & main kuis kelompok =================
Route::get('/', [QuizController::class, 'home'])->name('quiz.home');
Route::get('/kuis/{quiz}/mulai', [QuizController::class, 'landing'])->name('quiz.landing');
Route::post('/kuis/{quiz}/mulai', [QuizController::class, 'start'])->name('quiz.start');
Route::get('/kuis/{quiz}/main', [QuizController::class, 'play'])->name('quiz.play');
Route::post('/kuis/{quiz}/jawab', [QuizController::class, 'answer'])->name('quiz.answer');
Route::get('/kuis/{quiz}/hasil', [QuizController::class, 'result'])->name('quiz.result');

// ================= Publik: main kuis individu (tanpa login: isi nama + kelas) =================
Route::get('/kuis-individu/{quiz}/mulai', [IndividuPlayController::class, 'landing'])->name('quiz.individu.landing');
Route::post('/kuis-individu/{quiz}/mulai', [IndividuPlayController::class, 'start'])->name('quiz.individu.start');
Route::get('/kuis-individu/{quiz}/main', [IndividuPlayController::class, 'play'])->name('quiz.individu.play');
Route::post('/kuis-individu/{quiz}/jawab', [IndividuPlayController::class, 'answer'])->name('quiz.individu.answer');
Route::get('/kuis-individu/{quiz}/hasil', [IndividuPlayController::class, 'result'])->name('quiz.individu.result');
Route::post('/kuis-individu/{quiz}/ulang', [IndividuPlayController::class, 'retry'])->name('quiz.individu.retry');
Route::post('/kuis-individu/{quiz}/selesai', [IndividuPlayController::class, 'finish'])->name('quiz.individu.finish');

// ================= Publik: refleksi peserta didik (tanpa login: nama + kelas) =================
Route::get('/refleksi', [RefleksiController::class, 'create'])->name('refleksi.create');
Route::post('/refleksi', [RefleksiController::class, 'store'])->middleware('throttle:20,1')->name('refleksi.store');

// Login publik (siswa & kelompok) — dipakai form login di halaman utama, beda dari login admin.
Route::post('/login-siswa', [UserAuthController::class, 'loginSiswa'])->name('user.login.siswa');
Route::post('/login-kelompok', [UserAuthController::class, 'loginKelompok'])->name('user.login.kelompok');
Route::post('/logout-user', [UserAuthController::class, 'logout'])->name('user.logout');

// Profil siswa/kelompok yang login (ganti password) — beda dari profil admin.
Route::get('/profil-saya', [PublicProfileController::class, 'edit'])->name('user.profile.edit');
Route::put('/profil-saya', [PublicProfileController::class, 'update'])->name('user.profile.update');

// Laravel's built-in auth middleware redirect ke route bernama "login" kalau sesi
// habis/belum login. Kita gak punya route publik bernama itu (punya kita namanya
// admin.login), jadi tanpa alias ini middleware auth akan error "Route [login] not
// defined." tiap kali sesi admin expired. Alias ini bikin dia lempar ke halaman
// login admin yang benar.
Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');

// ================= Admin =================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');

        // Kelola daftar kuis (judul, tema, durasi, status, narasi)
        Route::get('/kuis', [AdminQuizController::class, 'index'])->name('quizzes.index');
        Route::get('/kuis-individu', [AdminQuizController::class, 'individu'])->name('quizzes.individu');
        Route::post('/kuis', [AdminQuizController::class, 'store'])->name('quizzes.store');
        Route::get('/kuis/{quiz}/edit', [AdminQuizController::class, 'edit'])->name('quizzes.edit');
        Route::put('/kuis/{quiz}', [AdminQuizController::class, 'update'])->name('quizzes.update');
        Route::delete('/kuis/{quiz}', [AdminQuizController::class, 'destroy'])->name('quizzes.destroy');

        // Kelola soal (gambar + 10 kata) per kuis kelompok
        Route::get('/soal', [SoalController::class, 'index'])->name('soal.index');
        Route::get('/gambar/create', [QuizImageController::class, 'create'])->name('images.create');
        Route::post('/gambar', [QuizImageController::class, 'store'])->name('images.store');
        Route::get('/gambar/{image}/edit', [QuizImageController::class, 'edit'])->name('images.edit');
        Route::put('/gambar/{image}', [QuizImageController::class, 'update'])->name('images.update');
        Route::delete('/gambar/{image}', [QuizImageController::class, 'destroy'])->name('images.destroy');

        // Kelola soal kuis individu (narasi + gambar opsional + 5 opsi, 1 benar)
        Route::get('/soal-individu', [SoalIndividuController::class, 'index'])->name('soal-individu.index');
        Route::get('/soal-individu/create', [SoalIndividuController::class, 'create'])->name('soal-individu.create');
        Route::post('/soal-individu', [SoalIndividuController::class, 'store'])->name('soal-individu.store');
        Route::get('/soal-individu/{soal}/edit', [SoalIndividuController::class, 'edit'])->name('soal-individu.edit');
        Route::put('/soal-individu/{soal}', [SoalIndividuController::class, 'update'])->name('soal-individu.update');
        Route::delete('/soal-individu/{soal}', [SoalIndividuController::class, 'destroy'])->name('soal-individu.destroy');

        Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');
        Route::get('/leaderboard/export', [LeaderboardController::class, 'export'])->name('leaderboard.export');
        Route::get('/leaderboard/kelompok/{group}', [LeaderboardController::class, 'showKelompok'])->name('leaderboard.kelompok.show');
        Route::get('/leaderboard/individu/{attempt}', [LeaderboardController::class, 'showIndividu'])->name('leaderboard.individu.show');
        Route::delete('/leaderboard/kelompok/{group}', [LeaderboardController::class, 'destroyKelompok'])->name('leaderboard.kelompok.destroy');
        Route::delete('/leaderboard/individu/{attempt}', [LeaderboardController::class, 'destroyIndividu'])->name('leaderboard.individu.destroy');

        // Rekap refleksi peserta didik
        Route::get('/refleksi', [AdminRefleksiController::class, 'index'])->name('refleksi.index');
        Route::delete('/refleksi/{refleksi}', [AdminRefleksiController::class, 'destroy'])->name('refleksi.destroy');

        // Master data kelas (dipakai sebagai pilihan dropdown, bukan input manual)
        Route::get('/kelas', [MasterKelasController::class, 'index'])->name('kelas.index');
        Route::get('/kelas/create', [MasterKelasController::class, 'create'])->name('kelas.create');
        Route::post('/kelas', [MasterKelasController::class, 'store'])->name('kelas.store');
        Route::get('/kelas/{kelas}/edit', [MasterKelasController::class, 'edit'])->name('kelas.edit');
        Route::put('/kelas/{kelas}', [MasterKelasController::class, 'update'])->name('kelas.update');
        Route::delete('/kelas/{kelas}', [MasterKelasController::class, 'destroy'])->name('kelas.destroy');

        // Master data siswa (peserta yang boleh isi kuis)
        Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
        Route::get('/siswa/create', [SiswaController::class, 'create'])->name('siswa.create');
        Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
        Route::get('/siswa/{siswa}/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
        Route::put('/siswa/{siswa}', [SiswaController::class, 'update'])->name('siswa.update');
        Route::delete('/siswa/{siswa}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
        Route::post('/siswa/{siswa}/reset-password', [SiswaController::class, 'resetPassword'])->name('siswa.reset-password');

        // Master data kelompok (akun login kelompok: username + password)
        Route::get('/kelompok', [KelompokController::class, 'index'])->name('kelompok.index');
        Route::get('/kelompok/create', [KelompokController::class, 'create'])->name('kelompok.create');
        Route::post('/kelompok', [KelompokController::class, 'store'])->name('kelompok.store');
        Route::get('/kelompok/{kelompok}/edit', [KelompokController::class, 'edit'])->name('kelompok.edit');
        Route::put('/kelompok/{kelompok}', [KelompokController::class, 'update'])->name('kelompok.update');
        Route::delete('/kelompok/{kelompok}', [KelompokController::class, 'destroy'])->name('kelompok.destroy');
    });
});
