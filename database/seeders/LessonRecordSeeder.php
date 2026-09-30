<?php

namespace Database\Seeders;

use App\Models\LessonRecord;
use App\Models\Reservation;
use Illuminate\Database\Seeder;
use RuntimeException;

class LessonRecordSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Completed Reservations
        |--------------------------------------------------------------------------
        |
        | completed の予約だけ取得
        |
        */

        $reservations = Reservation::query()
            ->with([
                'status',
                'teacher.user',
                'material',
            ])
            ->whereHas('status', function ($query) {
                $query->where(
                    'status_code',
                    'completed'
                );
            })
            ->get();

        if ($reservations->isEmpty()) {
            throw new RuntimeException(
                'completed のReservationがありません。'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Lesson Records
        |--------------------------------------------------------------------------
        */

        foreach ($reservations as $reservation) {

            /*
            |--------------------------------------------------------------------------
            | Teacher User
            |--------------------------------------------------------------------------
            |
            | completed_by は users.id を保存
            |
            */

            $teacherUserId =
                $reservation->teacher?->user_id;

            if (!$teacherUserId) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Subject
            |--------------------------------------------------------------------------
            |
            | Reservationで選択されたMaterial名を使用
            |
            */

            $subject =
                $reservation->material?->name
                ?? 'English Lesson';

            /*
            |--------------------------------------------------------------------------
            | Progress Note
            |--------------------------------------------------------------------------
            */

            $progressNote = fake()->randomElement([
                'The student participated actively and completed the lesson successfully.',

                'The student showed good understanding of the lesson material.',

                'The student practiced speaking and responded well to questions.',

                'The student completed the lesson and made good progress.',

                'The student needs more practice with vocabulary and pronunciation.',

                'The student showed improvement in speaking confidence.',

                'The student understood the main points of the lesson.',

                'The student participated well and asked several questions.',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Create / Update
            |--------------------------------------------------------------------------
            */

            LessonRecord::updateOrCreate(
                [
                    'reservation_id' =>
                        $reservation->id,
                ],
                [
                    'lesson_date' =>
                        $reservation
                            ->start_at
                            ->toDateString(),

                    'subject' =>
                        $subject,

                    'progress_note' =>
                        $progressNote,

                    'completed_by' =>
                        $teacherUserId,

                    'completed_at' =>
                        $reservation->end_at,
                ]
            );
        }

        $this->command?->info(
            'Lesson records created successfully.'
        );
    }
}
