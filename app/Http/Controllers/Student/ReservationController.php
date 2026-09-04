<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Material;
use Illuminate\Contracts\View\View;

class ReservationController extends Controller
{
    public function index(): View
    {
        /**
         * Teacher一覧
         *
         * user
         * materials
         *
         * も一緒に取得
         */
        $teachers = Teacher::query()
            ->with(['user', 'materials'])
            ->get();

         /*
         * Material一覧
         */

        $materials = Material::query()
            ->orderBy('material_id')
            ->get();

        /*
         * Bladeへ
         * $teachers
         * $materials
         *
         * を渡す
         */
        return view(
            'students.reservations.index',
            compact('teachers','materials')
        );
    }
}
