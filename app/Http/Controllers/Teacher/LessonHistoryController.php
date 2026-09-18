<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Reservation;

class LessonHistoryController extends Controller
{
    /*
    * ========================================
    * Lesson History 一覧
    * ========================================
    */
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
         * ========================================
         * 結果入力待ち
         * awaiting_result
         * ========================================
         */
        $awaitingReservations = Reservation::query()

            /*
             * 自分が担当した授業だけ
             */
            ->where(
                'teacher_id',
                $teacher->id
            )

            ->whereHas(
                'status',
                function ($query) {
                    $query->where(
                        'status_code',
                        'awaiting_result'
                    );
                }
            )

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

        /*
         * ========================================
         * 登録済みの過去授業
         * completed / absent
         * ========================================
         */
        $completedReservations = Reservation::query()

            /*
             * 自分が担当した授業だけ
             */
            ->where(
                'teacher_id',
                $teacher->id
            )

            ->whereHas(
                'status',
                function ($query) {
                    $query->whereIn(
                        'status_code',
                        [
                            'completed',
                            'absent',
                        ]
                    );
                }
            )

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
            'teachers.history.index',
            compact(
                'teacher',
                'awaitingReservations',
                'completedReservations'
            )
        );
    }

    /*
     * ========================================
     * Lesson History 詳細
     * ========================================
     */

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
         * History画面で使用できるstatusか確認
         */
        $reservation->load([
            'student.user',
            'material',
            'status',
            'lessonRecord',
        ]);

        abort_unless(
            in_array(
                $reservation->status->status_code,
                [
                    'awaiting_result',
                    'completed',
                    'absent',
                ],
                true
            ),
            404,
            'この授業は履歴ではありません。'
        );


        return view(
            'teachers.lessons.history-detail',
            compact(
                'teacher',
                'reservation'
            )
        );
    }
}
