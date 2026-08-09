<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Seeder;

class QuizCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'title' => 'Find The Right Word',
                'description' => 'Pilih 5 dari 10 kata yang paling tepat menggambarkan tiap objek wisata — sesi Adjectives lalu Verbs/Activities.',
                'narration' => 'Sebelum masuk ke materi kosakata objek wisata, yuk cek dulu seberapa jauh pemahaman awal kelompok kalian! Di tiap gambar, pilih 5 dari 10 kata yang paling tepat menggambarkan objek wisatanya. Siap berpetualang?',
                'duration_minutes' => 15,
                'icon' => 'fa-solid fa-magnifying-glass',
                'icon_color' => '--primary-yellow',
                'category_label' => 'Vocabulary',
                'category_color' => '--primary-cyan',
                'meta_primary' => '4 Gambar',
                'meta_secondary' => '~15 Menit',
                'meta_tertiary' => 'Per Kelompok',
                'is_playable' => true,
                'play_route' => 'quiz.landing',
                'order_number' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Tebak Aktivitas Wisata',
                'description' => 'Cocokkan gambar aktivitas wisatawan dengan kata kerja yang paling pas menggambarkannya.',
                'narration' => null,
                'duration_minutes' => 10,
                'icon' => 'fa-solid fa-person-hiking',
                'icon_color' => '--primary-lime',
                'category_label' => 'Verbs',
                'category_color' => '--primary-pink',
                'meta_primary' => '5 Gambar',
                'meta_secondary' => '~10 Menit',
                'meta_tertiary' => 'Per Kelompok',
                'is_playable' => false,
                'play_route' => null,
                'order_number' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Kuis Budaya Nusantara',
                'description' => 'Uji kosakata seputar budaya dan tradisi lokal lewat gambar-gambar ikonik Nusantara.',
                'narration' => null,
                'duration_minutes' => 12,
                'icon' => 'fa-solid fa-masks-theater',
                'icon_color' => '--primary-purple',
                'category_label' => 'Culture',
                'category_color' => '--primary-yellow',
                'meta_primary' => '6 Gambar',
                'meta_secondary' => '~12 Menit',
                'meta_tertiary' => 'Per Kelompok',
                'is_playable' => false,
                'play_route' => null,
                'order_number' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            Quiz::updateOrCreate(['title' => $item['title']], $item);
        }
    }
}
