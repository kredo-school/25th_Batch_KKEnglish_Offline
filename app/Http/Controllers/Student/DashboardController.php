<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\Reservation;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ログイン中のユーザーに紐づく生徒
        $student = $request->user()->student;

        abort_unless(
            $student,
            403,
            '生徒ユーザーではありません。'
        );

        // 今日のレッスン
        $todayLessons = Reservation::query()

            // ログイン中の生徒だけ
            ->where('student_id', $student->id)

            // 今日の予約だけ
            ->whereBetween('start_at', [
                now()->startOfDay(),
                now()->endOfDay(),
            ])

            // 有効な予約だけ
            ->whereHas('status', function ($query) {
                $query->whereIn('status_code', [
                    'pending',
                    'confirmed',
                ]);
            })

            // 関連データも一緒に取得
            ->with([
                'teacher.user',
                'material',
                'status',
            ])

            // 時間順
            ->orderBy('start_at')
            ->get();

        $nextLessons = Reservation::query()

            // ログイン中の生徒だけ
            ->where('student_id', $student->id)

            // 終わっていない予約だけ
            ->where('end_at', '>', now())

            // 有効な予約だけ
            ->whereHas('status', function ($query) {
                $query->whereIn('status_code', [
                    'pending',
                    'confirmed',
                ]);
            })

            // 関連データも一緒に取得
            ->with([
                'teacher.user',
                'material',
                'status',
            ])

            // 時間順
            ->orderBy('start_at')

            ->get();

        $nextLesson = $nextLessons->first();

        /*
         * Student向けお知らせ
         */
        $announcements = Announcement::query()
            ->whereIn(
                'target',
                [
                    'all',
                    'students',
                ]
            )
            ->latest()
            ->get();


        // 可能であれば
        /*
        * カレンダー表示用予約
        */
        $calendarReservations = Reservation::query()

            ->where(
                'student_id',
                $student->id
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
                'teacher.user',
                'material',
                'status',
            ])
            ->get();


        /*
         * FullCalendar用データ
        */
        $calendarEvents = $calendarReservations
            ->map(
                function ($reservation) {
                    return [
                        'title' =>
                        $reservation
                            ->teacher
                            ->user
                            ->first_name,

                        'start' =>
                        $reservation->start_at,

                        'end' =>
                        $reservation->end_at,
                    ];
                }
            );


        return view(
            'students.dashboard',
            compact(
                'student',
                'todayLessons',
                'announcements',
                'nextLesson',
                'calendarReservations',
                'calendarEvents'
            )
        );
    }
}
