<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $materials = [
            [
                'name' => 'Callan Method',
                'cover_image' => 'https://www.qqeng.com/blog2/wordpress/wp-content/uploads/2026/01/img-callan.png',
                'description' => 'A fast-paced English learning method focused on speaking and repetition.',
                'level' => '2 - 10',
                'target_level' => 'Beginner - Advanced',
                'duration' => 50,
                'printed_textbook' => 'Purchase Required',
                'status' => true,
            ],

            [
                'name' => 'Topic Conversation',
                'cover_image' => 'https://www.qqeng.com/blog2/wordpress/wp-content/uploads/2026/01/topic-conversation01.png',
                'description' => 'Practice speaking English through conversations on a variety of topics.',
                'level' => '2 - 7',
                'target_level' => 'Beginner - Pre-Advanced',
                'duration' => 50,
                'printed_textbook' => 'Not Provided',
                'status' => true,
            ],

            [
                'name' => 'R.E.M.S.',
                'cover_image' => 'https://www.qqeng.com/blog2/wordpress/wp-content/uploads/2026/01/rems01.png',
                'description' => 'Improve English fluency through repetition and sentence transformation exercises.',
                'level' => '2 - 9',
                'target_level' => 'Beginner - Advanced',
                'duration' => 50,
                'printed_textbook' => 'Not Provided',
                'status' => true,
            ],

            [
                'name' => 'Basic English',
                'cover_image' => 'https://www.qqeng.com/blog2/wordpress/wp-content/uploads/2026/01/basicenglish01.png',
                'description' => 'Build fundamental English grammar, vocabulary, and communication skills.',
                'level' => '1 - 7',
                'target_level' => 'Intro - Pre-Advanced',
                'duration' => 50,
                'printed_textbook' => 'Not Provided',
                'status' => true,
            ],

            [
                'name' => 'Daily English',
                'cover_image' => 'https://www.qqeng.com/blog2/wordpress/wp-content/uploads/2026/01/daily-english03.png',
                'description' => 'Learn useful English expressions for everyday situations.',
                'level' => '3 - 5',
                'target_level' => 'Beginner - Pre-Intermediate',
                'duration' => 50,
                'printed_textbook' => 'Not Provided',
                'status' => true,
            ],

            [
                'name' => 'Welcome Aboard',
                'cover_image' => 'https://www.qqeng.com/blog2/wordpress/wp-content/uploads/2026/01/welcome-abroad.png',
                'description' => 'Practice practical English communication for travel and international situations.',
                'level' => '3 - 7',
                'target_level' => 'Beginner - Pre-Advanced',
                'duration' => 50,
                'printed_textbook' => 'Not Provided',
                'status' => true,
            ],

            [
                'name' => 'AIGC Curriculum',
                'cover_image' => 'https://www.qqeng.com/blog2/wordpress/wp-content/uploads/2026/01/AIGC.png',
                'description' => 'Personalized English lessons using AI-generated learning content.',
                'level' => '3 - 10',
                'target_level' => 'Beginner - Advanced',
                'duration' => 50,
                'printed_textbook' => 'Not Provided',
                'status' => true,
            ],
        ];

        foreach ($materials as $material) {
            Material::updateOrCreate(
                [
                    'name' => $material['name'],
                ],
                $material
            );
        }
    }
}
