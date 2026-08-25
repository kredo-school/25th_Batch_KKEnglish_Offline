<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;

class TeacherController extends Controller
{
    /**
     * 講師一覧
     */
    public function index(): View
    {
    /**
     * 講師一覧
     */
        $teachers = Teacher::query()
            ->latest('id')
            ->paginate(20);

        return view('teachers.index', compact('teachers'));
    }

    /**
     * 講師プロフィール
     */
    public function show(int $id): View
    {
        $teacher = Teacher::query()
            ->findOrFail($id);

        return view('teachers.show', compact('teacher'));
    }
}
