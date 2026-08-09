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
                'description' => 'Choose 5 out of 10 words that best describe each tourist destination — an Adjectives session followed by Verbs/Activities.',
                'narration' => 'Before diving into tourism vocabulary material, let\'s check how much your group already understands! For each image, pick 5 out of 10 words that best describe the destination. Ready for the adventure?',
                'duration_minutes' => 15,
                'icon' => 'fa-solid fa-magnifying-glass',
                'icon_color' => '--primary-yellow',
                'category_label' => 'Vocabulary',
                'category_color' => '--primary-cyan',
                'meta_primary' => '4 Images',
                'meta_secondary' => '~15 Minutes',
                'meta_tertiary' => 'Per Group',
                'is_playable' => true,
                'play_route' => 'quiz.landing',
                'order_number' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Guess the Tourism Activity',
                'description' => 'Match images of tourist activities with the verb that best describes them.',
                'narration' => null,
                'duration_minutes' => 10,
                'icon' => 'fa-solid fa-person-hiking',
                'icon_color' => '--primary-lime',
                'category_label' => 'Verbs',
                'category_color' => '--primary-pink',
                'meta_primary' => '5 Images',
                'meta_secondary' => '~10 Minutes',
                'meta_tertiary' => 'Per Group',
                'is_playable' => false,
                'play_route' => null,
                'order_number' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Nusantara Culture Quiz',
                'description' => 'Test your vocabulary about local culture and traditions through iconic images of the Indonesian archipelago.',
                'narration' => null,
                'duration_minutes' => 12,
                'icon' => 'fa-solid fa-masks-theater',
                'icon_color' => '--primary-purple',
                'category_label' => 'Culture',
                'category_color' => '--primary-yellow',
                'meta_primary' => '6 Images',
                'meta_secondary' => '~12 Minutes',
                'meta_tertiary' => 'Per Group',
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
