<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizImage;
use Illuminate\Database\Seeder;

/**
 * PENTING: data di bawah ini SENGAJA dibuat jadi data contoh/demo (bukan kunci
 * jawaban asesmen sungguhan), karena file seeder ini ikut ke-commit ke repo Git
 * (termasuk kalau repo-nya Public) — jadi tidak aman diisi jawaban asli yang
 * masih dipakai buat menilai siswa.
 *
 * Buat kuis yang beneran dipakai asesmen: input soal & kunci jawabannya lewat
 * Admin Panel (menu Daftar Kuis Kelompok/Individu → Soal), bukan lewat file ini.
 * Data yang diinput lewat Admin Panel tersimpan di database, bukan di kode,
 * jadi tidak pernah ikut ke-push ke GitHub sama sekali.
 */
class FindTheWordSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan QuizCatalogSeeder sudah jalan duluan supaya kuis induknya ada.
        $quiz = Quiz::where('title', 'Find The Right Word')->first();

        $data = [
            // ================= Sesi 1 - Adjectives (CONTOH/DEMO) =================
            [
                'session_type' => 'adjectives',
                'title' => 'Contoh Objek Wisata 1 (Demo)',
                'instruction' => 'Contoh soal bawaan — pilih 5 dari 10 kata sifat yang paling tepat menggambarkan gambar ini.',
                'order_number' => 1,
                'image' => 'images/soal/taman-tengku-mahratu.jpg',
                'words' => ['spacious', 'shady', 'tidy', 'fresh', 'scenic', 'noisy', 'muddy', 'pricey', 'risky', 'cramped'],
                'correct' => ['spacious', 'shady', 'tidy', 'fresh', 'scenic'],
            ],
            [
                'session_type' => 'adjectives',
                'title' => 'Contoh Objek Wisata 2 (Demo)',
                'instruction' => 'Contoh soal bawaan — pilih 5 dari 10 kata sifat yang paling tepat menggambarkan gambar ini.',
                'order_number' => 2,
                'image' => 'images/soal/pantai-rupat.jpg',
                'words' => ['tranquil', 'breezy', 'pristine', 'sunny', 'open', 'packed', 'murky', 'costly', 'risky', 'tiny'],
                'correct' => ['tranquil', 'breezy', 'pristine', 'sunny', 'open'],
            ],

            // ================= Sesi 2 - Verbs/Activities (CONTOH/DEMO) =================
            [
                'session_type' => 'verbs',
                'title' => 'Contoh Aktivitas 1 (Demo)',
                'instruction' => 'Contoh soal bawaan — pilih 5 dari 10 kata kerja yang paling mungkin dilakukan di tempat ini.',
                'order_number' => 1,
                'image' => 'images/soal/asia-heritage.jpg',
                'words' => ['stroll', 'photograph', 'observe', 'admire', 'unwind', 'dive', 'trek', 'angle', 'camp', 'plow'],
                'correct' => ['stroll', 'photograph', 'observe', 'admire', 'unwind'],
            ],
            [
                'session_type' => 'verbs',
                'title' => 'Contoh Aktivitas 2 (Demo)',
                'instruction' => 'Contoh soal bawaan — pilih 5 dari 10 kata kerja yang paling mungkin dilakukan di tempat ini.',
                'order_number' => 2,
                'image' => 'images/soal/mall-ska-pekanbaru.jpg',
                'words' => ['browse', 'dine', 'watch films', 'chill', 'wander', 'dive', 'trek', 'angle', 'camp', 'plow'],
                'correct' => ['browse', 'dine', 'watch films', 'chill', 'wander'],
            ],
        ];

        foreach ($data as $item) {
            $image = QuizImage::updateOrCreate(
                ['title' => $item['title'], 'session_type' => $item['session_type']],
                [
                    'quiz_id' => $quiz?->id,
                    'instruction' => $item['instruction'],
                    'order_number' => $item['order_number'],
                    'is_active' => true,
                ]
            );

            // Jangan timpa gambar yang sudah diupload manual lewat admin panel.
            if (! $image->image_path) {
                $image->update(['image_path' => $item['image']]);
            }

            $image->words()->delete();

            foreach ($item['words'] as $word) {
                $image->words()->create([
                    'word' => $word,
                    'is_correct' => in_array($word, $item['correct'], true),
                ]);
            }
        }
    }
}
