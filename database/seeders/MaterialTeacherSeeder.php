<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialTeacherSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Reset
        |--------------------------------------------------------------------------
        */

        DB::table('teacher_materials')->delete();


        /*
        |--------------------------------------------------------------------------
        | Get Teachers / Materials
        |--------------------------------------------------------------------------
        */

        $teacherIds = DB::table('teachers')
            ->pluck('id');

        $materialIds = DB::table('materials')
            ->pluck('material_id');


        /*
        |--------------------------------------------------------------------------
        | Assign Materials
        |--------------------------------------------------------------------------
        |
        | 各Teacherに2〜5個のMaterialをランダムに割り当て
        |
        */

        foreach ($teacherIds as $teacherId) {

            $count = min(
                rand(2, 5),
                $materialIds->count()
            );

            $selectedMaterialIds = $materialIds
                ->shuffle()
                ->take($count);

            foreach ($selectedMaterialIds as $materialId) {

                DB::table('teacher_materials')
                    ->insert([
                        'teacher_id' => $teacherId,
                        'material_id' => $materialId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }
        }
    }
}
