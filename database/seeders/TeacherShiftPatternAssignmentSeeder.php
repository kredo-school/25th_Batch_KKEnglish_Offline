<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeacherShiftPatternAssignment;

class TeacherShiftPatternAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $startDate = '2026-10-16';

        for ($teacherId = 1; $teacherId <= 30; $teacherId++) {

            /*
             * TeacherごとにShift Patternを1つだけランダムに決定
             *
             * 1人のTeacherは、選択された5曜日すべて
             * 同じShift Patternを使用する。
             */
            $shiftPatternId = rand(1, 3);

            /*
             * 日曜日〜土曜日の7曜日から
             * ランダムに5曜日を選択
             *
             * 0 = 日曜日
             * 1 = 月曜日
             * 2 = 火曜日
             * 3 = 水曜日
             * 4 = 木曜日
             * 5 = 金曜日
             * 6 = 土曜日
             */
            $weekdays = collect(range(0, 6))
                ->shuffle()
                ->take(5);

            foreach ($weekdays as $weekday) {

                /*
                 * 同じTeacher・同じ曜日・同じ開始日の
                 * Assignmentがなければ作成。
                 *
                 * すでに存在する場合は何もしない。
                 */
                TeacherShiftPatternAssignment::firstOrCreate(
                    [
                        'teacher_id' => $teacherId,
                        'weekday' => $weekday,
                        'start_date' => $startDate,
                    ],
                    [
                        'shift_pattern_id' => $shiftPatternId,
                        'end_date' => null,
                        'priority' => 0,
                    ]
                );
            }
        }
    }
}
