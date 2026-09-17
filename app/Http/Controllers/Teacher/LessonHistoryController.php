<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Reservation;

class LessonHistoryController extends Controller
{
    public function lessonHistory(Request $request): View
    {
        /*
         * ログイン中Teacher取得
         */
        $teacher = $request->user()->teacher;

        abort_unless(
            $teacher,
            403,
            '講師ユーザーではありません。'
        );

        /*
         * 過去のレッスン取得
         */
        $reservations = Reservation::query()

            /*
             * 自分が担当した授業だけ
             */
            ->where(
                'teacher_id',
                $teacher->id
            )

            /*
             * 授業終了済み
             */
            ->where(
                'end_at',
                '<=',
                now()
            )

            /*
             * Bladeで使う関連データ
             */
            ->with([
                'student.user',
                'material',
                'status',
                'lessonRecord',
            ])

            /*
             * 新しい授業から表示
             */
            ->orderByDesc('start_at')
            ->get();

        return view(
            'teachers.reservations.history',
            compact(
                'teacher',
                'reservations'
            )
        );
    }

    public function historyDetail(
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
        * 自分が担当した授業か確認
        */
        abort_unless(
            (int) $reservation->teacher_id
                ===
            (int) $teacher->id,
            403,
            'この授業を表示する権限がありません。'
        );

        /*
        * 過去の授業であることを確認
        */
        abort_unless(
            $reservation->end_at <= now(),
            404,
            'この授業は履歴ではありません。'
        );

        $reservation->load([
            'student.user',
            'material',
            'status',
            'lessonRecord',
        ]);

        return view(
            'teachers.lessons.history-detail',
            compact(
                'teacher',
                'reservation'
            )
        );
    }
}
