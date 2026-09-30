<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $announcements = [

            /*
            |--------------------------------------------------------------------------
            | All Users
            |--------------------------------------------------------------------------
            */

            [
                'title' => 'Welcome to KKEnglish',
                'target' => 'all',
                'content' =>
                    'Welcome to KKEnglish! We hope you enjoy learning English with our teachers.',
            ],

            [
                'title' => 'System Maintenance Notice',
                'target' => 'all',
                'content' =>
                    'The system will undergo scheduled maintenance. Some services may be temporarily unavailable.',
            ],

            [
                'title' => 'New Learning Materials Available',
                'target' => 'all',
                'content' =>
                    'New English learning materials have been added. Please check them before your next lesson.',
            ],


            /*
            |--------------------------------------------------------------------------
            | Students
            |--------------------------------------------------------------------------
            */

            [
                'title' => 'Book Your Next Lesson',
                'target' => 'students',
                'content' =>
                    'Do not forget to book your next English lesson. You can search for teachers by material, nationality, points, and rating.',
            ],

            [
                'title' => 'Review Your Completed Lessons',
                'target' => 'students',
                'content' =>
                    'You can now review your completed lessons and share feedback about your teacher.',
            ],

            [
                'title' => 'Check Your Point Balance',
                'target' => 'students',
                'content' =>
                    'Please check your current point balance before booking your next lesson.',
            ],


            /*
            |--------------------------------------------------------------------------
            | Teachers
            |--------------------------------------------------------------------------
            */

            [
                'title' => 'Check Your Upcoming Lessons',
                'target' => 'teachers',
                'content' =>
                    'Please check your upcoming lesson schedule regularly and prepare the required materials.',
            ],

            [
                'title' => 'Lesson Records Reminder',
                'target' => 'teachers',
                'content' =>
                    'Please complete the lesson record after each finished lesson.',
            ],

            [
                'title' => 'Schedule Update Reminder',
                'target' => 'teachers',
                'content' =>
                    'Please check your latest teaching schedule and report any unavailable periods when necessary.',
            ],
        ];

        foreach ($announcements as $announcement) {

            Announcement::updateOrCreate(
                [
                    'title' => $announcement['title'],
                    'target' => $announcement['target'],
                ],
                [
                    'content' => $announcement['content'],
                ]
            );
        }

        $this->command?->info(
            'Announcements created: '
            . count($announcements)
        );
    }
}
