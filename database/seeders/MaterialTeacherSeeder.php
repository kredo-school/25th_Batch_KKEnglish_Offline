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
         *
         * Teacher ID：1〜19
         */
        $materialTeachers = [

            // Material 1
            1 => [
                1, 2, 3, 5, 7, 9, 13, 15, 17, 19
            ],

            // Material 2
            2 => [
                1, 4, 6, 8, 10, 12, 14, 16, 18
            ],

            // Material 3
            3 => [
                2, 4, 5, 7, 9, 13, 16, 19
            ],

            // Material 4
            4 => [
                1, 3, 6, 7, 9, 10, 12, 14, 17, 18
            ],

            // Material 5
            5 => [
                2, 5, 8, 10, 13, 15, 17, 19
            ],

            // Material 6
            6 => [
                1, 4, 6, 8, 9, 12, 14, 16, 18, 19
            ],

            // Material 7
            7 => [
                3, 5, 6, 7, 10, 13, 15, 16, 18
            ],
        ];


        /*
         * 再実行時の重複防止
         */
        DB::table('teacher_materials')->delete();


        /*
         * Pivotテーブルへ登録
         */
        foreach ($materialTeachers as $materialId => $teacherIds) {

            foreach ($teacherIds as $teacherId) {

                DB::table('teacher_materials')->insert([
                    'teacher_id'  => $teacherId,
                    'material_id' => $materialId,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    }
}
