<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\TeacherLike;
use Illuminate\Http\RedirectResponse;

class TeacherLikeController extends Controller
{
    public function store(
        Request $request,
        Teacher $teacher
    ): RedirectResponse {

        $student = $request->user()->student;

        abort_unless(
            $student,
            403,
            '生徒ユーザーではありません。'
        );

        TeacherLike::firstOrCreate([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
        ]);

        return back()->with(
            'success',
            '講師をお気に入りに追加しました。'
        );
    }

    public function destroy(
        Request $request,
        Teacher $teacher
    ): RedirectResponse {

        $student = $request->user()->student;

        abort_unless(
            $student,
            403,
            '生徒ユーザーではありません。'
        );

        TeacherLike::query()
            ->where('student_id', $student->id)
            ->where('teacher_id', $teacher->id)
            ->delete();

        return back()->with(
            'success',
            'お気に入りを解除しました。'
        );
    }
    //
}
