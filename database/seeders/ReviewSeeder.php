<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Review;
use App\Models\Teacher;
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
        | Comments
        |--------------------------------------------------------------------------
        */

        $comments = [

            5 => [
                'Excellent lesson! The teacher was very friendly and helpful.',
                'I really enjoyed this lesson. The explanations were very clear.',
                'Great teacher! I learned a lot and enjoyed speaking English.',
                'The lesson was very helpful and easy to understand.',
                'Amazing lesson. I felt comfortable speaking English.',
                'The teacher gave me very useful feedback.',
                'I would definitely like to book another lesson.',
                'One of the best English lessons I have taken.',
            ],

            4 => [
                'Good lesson. The teacher explained everything clearly.',
                'I enjoyed the lesson and learned some useful expressions.',
                'The teacher was friendly and the lesson was interesting.',
                'It was a good lesson. I would like to take another class.',
                'The lesson was useful and easy to follow.',
                'I enjoyed practicing conversation with this teacher.',
            ],

            3 => [
                'The lesson was good, but I need more practice.',
                'It was helpful and I learned some new vocabulary.',
                'The lesson was okay. I would like to practice more speaking.',
                'The lesson was useful, although some parts were difficult.',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Review each teacher
        |--------------------------------------------------------------------------
        |
        | Teacherごとにcompleted予約を取得。
        | completed予約の80〜100%程度をレビュー済みにする。
        |
        */

        $teachers = Teacher::query()
            ->orderBy('id')
            ->limit(30)
            ->get();

        $totalReviews = 0;

        foreach ($teachers as $teacher) {

            /*
            |--------------------------------------------------------------------------
            | Teacher's completed reservations
            |--------------------------------------------------------------------------
            */

            $teacherReservations = $reservations
                ->where('teacher_id', $teacher->id)
                ->values();

            /*
            |--------------------------------------------------------------------------
            | completed予約がないTeacher
            |--------------------------------------------------------------------------
            */

            if ($teacherReservations->isEmpty()) {

                $this->command?->warn(
                    "Teacher {$teacher->id}: "
                    . 'completed reservation がないためレビューを作成できません。'
                );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Review count
            |--------------------------------------------------------------------------
            |
            | Teacherのcompleted予約の80〜100%をレビュー化
            |
            */

            $percentage = fake()->randomElement([
                0.8,
                0.9,
                1.0,
            ]);

            $reviewCount = max(
                1,
                (int) ceil(
                    $teacherReservations->count()
                    * $percentage
                )
            );

            $reviewReservations = $teacherReservations
                ->shuffle()
                ->take($reviewCount);

            /*
            |--------------------------------------------------------------------------
            | Create Reviews
            |--------------------------------------------------------------------------
            */

            foreach ($reviewReservations as $reservation) {

                /*
                |--------------------------------------------------------------------------
                | Rating
                |--------------------------------------------------------------------------
                |
                | ★4〜5中心
                | ★3も少しだけ入れる
                |
                */

                $rating = fake()->randomElement([
                    3,
                    4,
                    4,
                    4,
                    4,
                    5,
                    5,
                    5,
                    5,
                    5,
                ]);

                $comment = fake()->randomElement(
                    $comments[$rating]
                );

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

                $totalReviews++;
            }

            $this->command?->info(
                "Teacher {$teacher->id}: "
                . "{$reviewReservations->count()} reviews"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Update Teacher Rating Average
        |--------------------------------------------------------------------------
        |
        | reviewsテーブルの実際の評価から
        | teachers.rating_average を更新
        |
        */

        foreach ($teachers as $teacher) {

            $average = Review::query()
                ->where(
                    'teacher_id',
                    $teacher->id
                )
                ->avg('rating');

            if ($average !== null) {

                $teacher->update([
                    'rating_average' =>
                        round($average, 2),
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Result
        |--------------------------------------------------------------------------
        */

        $this->command?->info(
            "Total reviews created: {$totalReviews}"
        );
    }
}
