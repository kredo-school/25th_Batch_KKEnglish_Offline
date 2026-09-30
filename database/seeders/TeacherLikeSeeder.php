<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherLike;
use Illuminate\Database\Seeder;
use RuntimeException;

class TeacherLikeSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        */

        $students = Student::query()
            ->orderBy('id')
            ->limit(50)
            ->get();

        if ($students->isEmpty()) {
            throw new RuntimeException(
                'Studentがありません。StudentSeederを先に実行してください。'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Teachers
        |--------------------------------------------------------------------------
        */

        $teachers = Teacher::query()
            ->orderBy('id')
            ->get();

        if ($teachers->isEmpty()) {
            throw new RuntimeException(
                'Teacherがありません。TeacherSeederを先に実行してください。'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Teacher Likes
        |--------------------------------------------------------------------------
        |
        | 各Studentがお気に入りTeacherを0〜3人登録
        |
        */

        $createdCount = 0;

        foreach ($students as $student) {

            $likeCount = fake()->numberBetween(0, 3);

            if ($likeCount === 0) {
                continue;
            }

            /*
             * Teacher数より多く選ばない
             */
            $likeCount = min(
                $likeCount,
                $teachers->count()
            );

            /*
             * Teacherを重複なしでランダム選択
             */
            $favoriteTeachers = $teachers
                ->shuffle()
                ->take($likeCount);

            foreach ($favoriteTeachers as $teacher) {

                TeacherLike::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'teacher_id' => $teacher->id,
                    ]
                );

                $createdCount++;
            }
        }

        $this->command?->info(
            "Teacher likes created: {$createdCount}"
        );
    }
}
