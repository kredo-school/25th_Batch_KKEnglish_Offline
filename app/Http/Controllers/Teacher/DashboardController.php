<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Reservation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ログイン中のユーザーに紐づく講師
        $teacher = $request->user()->teacher;

        abort_unless(
            $teacher,
            403,
            '講師ユーザーではありません。'
        );


        /*
        |--------------------------------------------------------------------------
        | Today's Lessons
        |--------------------------------------------------------------------------
        */

        $todayLessons = Reservation::query()

            ->where(
                'teacher_id',
                $teacher->id
            )

            ->whereBetween(
                'start_at',
                [
                    now()->startOfDay(),
                    now()->endOfDay(),
                ]
            )

            ->whereHas(
                'status',
                function ($query) {
                    $query->whereIn(
                        'status_code',
                        [
                            'pending',
                            'confirmed',
                        ]
                    );
                }
            )

            ->with([
                'student.user',
                'material',
                'status',
            ])

            ->orderBy('start_at')

            ->get();


        /*
        |--------------------------------------------------------------------------
        | Next Lessons
        |--------------------------------------------------------------------------
        */

        $nextLessons = Reservation::query()

            ->where(
                'teacher_id',
                $teacher->id
            )

            ->where(
                'end_at',
                '>',
                now()
            )

            ->whereHas(
                'status',
                function ($query) {
                    $query->whereIn(
                        'status_code',
                        [
                            'pending',
                            'confirmed',
                        ]
                    );
                }
            )

            ->with([
                'student.user',
                'material',
                'status',
            ])

            ->orderBy('start_at')

            ->get();


        $nextLesson =
            $nextLessons->first();


        /*
        |--------------------------------------------------------------------------
        | Teacher Announcements
        |--------------------------------------------------------------------------
        */

        $announcements = Announcement::query()

            ->whereIn(
                'target',
                [
                    'all',
                    'teachers',
                ]
            )

            ->latest()

            ->get();


        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view(
            'teachers.dashboard',
            compact(
                'teacher',
                'todayLessons',
                'nextLessons',
                'nextLesson',
                'announcements'
            )
        );
    }
}