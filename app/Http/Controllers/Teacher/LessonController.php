<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Reservation;


class LessonController extends Controller
{
    public function upcomingDetail(
    Request $request,
    Reservation $reservation
): View {

    $teacher = $request->user()->teacher;

    abort_unless(
        $teacher,
        403,
        '講師ユーザーではありません。'
    );

    /*
     * 自分の担当授業か確認
     */
    abort_unless(
        (int) $reservation->teacher_id
            ===
        (int) $teacher->id,
        403,
        'この授業を表示する権限がありません。'
    );

    /*
     * 今回の授業
     */
    $reservation->load([
        'student.user',
        'material',
        'status',
        'lessonRecord',
    ]);

    /*
     * このStudentの過去の授業記録
     * 他のTeacherの記録も含む
     */
    $pastReservations = Reservation::query()
        ->where(
            'student_id',
            $reservation->student_id
        )
        ->where(
            'start_at',
            '<',
            $reservation->start_at
        )
        ->whereHas('lessonRecord')
        ->with([
            'teacher.user',
            'material',
            'status',
            'lessonRecord',
        ])
        ->orderByDesc('start_at')
        ->get();

    return view(
        'teachers.lessons.show',
        compact(
            'teacher',
            'reservation',
            'pastReservations'
        )
    );
}


}
