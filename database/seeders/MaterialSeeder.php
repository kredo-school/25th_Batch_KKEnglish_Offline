<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Material;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materials = [
            [
                'name' => 'Callan Method',
                'cover_image' => 'materials/callan-method.jpg',
                'description' => 'A fast-paced English learning method focused on speaking and repetition.',
                'level' => '2 - 10',
                'target_level' => 'Beginner - Advanced',
                'duration' => 25,
                'printed_textbook' => 'Purchase Required',
                'status' => true,
            ],

            [
                'name' => 'Topic Conversation',
                'cover_image' => 'materials/topic-conversation.jpg',
                'description' => 'Practice speaking English through conversations on a variety of topics.',
                'level' => '2 - 7',
                'target_level' => 'Beginner - Pre-Advanced',
                'duration' => 25,
                'printed_textbook' => 'Not Provided',
                'status' => true,
            ],

            [
                'name' => 'R.E.M.S.',
                'cover_image' => 'materials/rems.jpg',
                'description' => 'Improve English fluency through repetition and sentence transformation exercises.',
                'level' => '2 - 9',
                'target_level' => 'Beginner - Advanced',
                'duration' => 25,
                'printed_textbook' => 'Not Provided',
                'status' => true,
            ],

            [
                'name' => 'Basic English',
                'cover_image' => 'materials/basic-english.jpg',
                'description' => 'Build fundamental English grammar, vocabulary, and communication skills.',
                'level' => '1 - 7',
                'target_level' => 'Intro - Pre-Advanced',
                'duration' => 25,
                'printed_textbook' => 'Not Provided',
                'status' => true,
            ],

            [
                'name' => 'Daily English',
                'cover_image' => 'materials/daily-english.jpg',
                'description' => 'Learn useful English expressions for everyday situations.',
                'level' => '3 - 5',
                'target_level' => 'Beginner - Pre-Intermediate',
                'duration' => 25,
                'printed_textbook' => 'Not Provided',
                'status' => true,
            ],

            [
                'name' => 'Welcome Aboard',
                'cover_image' => 'materials/welcome-aboard.jpg',
                'description' => 'Practice practical English communication for travel and international situations.',
                'level' => '3 - 7',
                'target_level' => 'Beginner - Pre-Advanced',
                'duration' => 25,
                'printed_textbook' => 'Not Provided',
                'status' => true,
            ],

            [
                'name' => 'AIGC Curriculum',
                'cover_image' => 'materials/aigc.jpg',
                'description' => 'Personalized English lessons using AI-generated learning content.',
                'level' => '3 - 10',
                'target_level' => 'Beginner - Advanced',
                'duration' => 25,
                'printed_textbook' => 'Not Provided',
                'status' => true,
            ],
        ];

        foreach ($materials as $material) {
            Material::create($material);
        }
    }
}
