<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Reservation;

class LessonHistoryController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user()->student;

    abort_unless(
        $student,
        403,
        '生徒ユーザーではありません。'
    );

    $reservations = Reservation::query()
        ->where('student_id', $student->id)
        ->whereHas('status', function ($query) {
            $query->whereIn('status_code', [
                'completed',
                'absent',
            ]);
        })
        ->with([
            'teacher.user',
            'material',
            'status',
            'lessonRecord',
        ])
        ->orderByDesc('start_at')
        ->get();

    return view(
        'students.history.index',
        compact('student', 'reservations')
    );

    }
}
