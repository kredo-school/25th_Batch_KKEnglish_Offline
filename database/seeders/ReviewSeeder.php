<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Review;
use Illuminate\Database\Seeder;
use RuntimeException;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Completed Reservations
        |--------------------------------------------------------------------------
        |
        | completed かつ LessonRecord が存在する予約を取得
        |
        */

        $reservations = Reservation::query()
            ->with([
                'status',
                'student',
                'teacher',
                'lessonRecord',
            ])
            ->whereHas('status', function ($query) {
                $query->where(
                    'status_code',
                    'completed'
                );
            })
            ->whereHas('lessonRecord')
            ->orderBy('id')
            ->get();

        if ($reservations->isEmpty()) {
            throw new RuntimeException(
                'レビュー対象となるcompleted予約がありません。'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 約70%をレビュー済みにする
        |--------------------------------------------------------------------------
        */

        $reviewCount = (int) floor(
            $reservations->count() * 0.7
        );

        $reviewReservations = $reservations
            ->shuffle()
            ->take($reviewCount);

        /*
        |--------------------------------------------------------------------------
        | Reviews
        |--------------------------------------------------------------------------
        */

        foreach ($reviewReservations as $reservation) {

            /*
            |--------------------------------------------------------------------------
            | Rating
            |--------------------------------------------------------------------------
            |
            | 実際のデモ画面で★4〜5が多く見えるように分散
            |
            */

            $rating = fake()->randomElement([
                3,
                4,
                4,
                4,
                5,
                5,
                5,
                5,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Comment
            |--------------------------------------------------------------------------
            */

            $comments = [
                5 => [
                    'Excellent lesson! The teacher was very friendly and helpful.',
                    'I really enjoyed this lesson. The explanations were very clear.',
                    'Great teacher! I learned a lot and enjoyed speaking English.',
                    'The lesson was very helpful and easy to understand.',
                ],

                4 => [
                    'Good lesson. The teacher explained everything clearly.',
                    'I enjoyed the lesson and learned some useful expressions.',
                    'The teacher was friendly and the lesson was interesting.',
                    'It was a good lesson. I would like to take another class.',
                ],

                3 => [
                    'The lesson was good, but I need more practice.',
                    'It was helpful and I learned some new vocabulary.',
                    'The lesson was okay. I would like to practice more speaking.',
                ],
            ];

            $comment = fake()->randomElement(
                $comments[$rating]
            );

            /*
            |--------------------------------------------------------------------------
            | Create / Update
            |--------------------------------------------------------------------------
            */

            Review::updateOrCreate(
                [
                    'reservation_id' =>
                        $reservation->id,
                ],
                [
                    'student_id' =>
                        $reservation->student_id,

                    'teacher_id' =>
                        $reservation->teacher_id,

                    'rating' =>
                        $rating,

                    'comment' =>
                        $comment,
                ]
            );
        }

        $this->command?->info(
            "Reviews created: {$reviewReservations->count()}"
        );

        $this->command?->info(
            'Reservations without review: '
            . (
                $reservations->count()
                - $reviewReservations->count()
            )
        );
    }
}
