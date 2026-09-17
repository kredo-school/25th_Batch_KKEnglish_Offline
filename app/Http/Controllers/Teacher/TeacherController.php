<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use App\Models\TeacherLike;

class TeacherController extends Controller
{
public function index(): View
{
    $student = Auth::user()->student;

    abort_unless(
        $student,
        403,
        '生徒ユーザーではありません。'
    );

    $teachers = Teacher::query()
        ->with([
            'user',
            'materials',
        ])
        ->latest('id')
        ->paginate(20);

    $favoriteTeacherIds = TeacherLike::query()
        ->where('student_id', $student->id)
        ->pluck('teacher_id');

    $favoriteTeachers = Teacher::query()
        ->with([
            'user',
            'materials',
        ])
        ->whereIn('id', $favoriteTeacherIds)
        ->get();

    return view(
        'students.teacher-list',
        compact(
            'teachers',
            'favoriteTeachers',
            'favoriteTeacherIds'
        )
    );
}

    /**
     * 講師プロフィール
     */
    public function show(int $id): View
    {
        $teacher = Teacher::findOrFail($id);

        return view('teachers.show', compact('teacher'));
    }
}
