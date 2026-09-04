<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialTeacherSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Materialごとに
         * 教えられるTeacherを設定
         */
        $materialTeachers = [

            1 => [2, 3, 5, 7],

            2 => [1, 4, 6, 8, 10],

            3 => [2, 4, 5, 9],

            4 => [1, 3, 6, 7, 9],

            5 => [2, 5, 8, 10],

            6 => [1, 4, 7, 8, 9],

            7 => [3, 5, 6, 9, 10],
        ];


        /*
         * 再実行時の重複防止
         */
        DB::table('teacher_materials')->delete();


        foreach ($materialTeachers as $materialId => $teacherIds) {

            foreach ($teacherIds as $teacherId) {

                DB::table('teacher_materials')->insert([
                    'teacher_id' => $teacherId,
                    'material_id' => $materialId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
