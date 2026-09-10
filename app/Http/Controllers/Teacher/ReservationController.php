<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Services\ReservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class ReservationController extends Controller
{
    public function __construct(
        private ReservationService $reservationService
    ) {}
    /*
     * ========================================
     * 講師自身の予約一覧
     * ========================================
     */
    public function index(Request $request): View
    {
        /*
         * ログイン中の講師を取得
         */
        $teacher = $request->user()->teacher;

        abort_unless(
            $teacher,
            403,
            '講師ユーザーではありません。'
        );

        /*
         * 本日の予約
         */
        $todayReservations = Reservation::query()
            ->where('teacher_id', $teacher->id)
            ->whereDate('start_at', today())
            ->whereHas('status', function ($query) {
                $query->whereIn('status_code', [
                    'pending',
                    'confirmed',
                ]);
            })
            ->with([
                'student.user',
                'material',
                'status',
            ])
            ->orderBy('start_at')
            ->get();

        /*
         * 明日以降の予約
         */
        $upcomingReservations = Reservation::query()
            ->where('teacher_id', $teacher->id)
            ->where('start_at', '>', today()->endOfDay())
            ->whereHas('status', function ($query) {
                $query->whereIn('status_code', [
                    'pending',
                    'confirmed',
                ]);
            })
            ->with([
                'student.user',
                'material',
                'status',
            ])
            ->orderBy('start_at')
            ->get();

        /*
         * Bladeへ渡す
         */
        return view(
            'teachers.reservations.index',
            compact(
                'todayReservations',
                'upcomingReservations'
            )
        );
    }

    /*
     * ========================================
     * 講師自身の予約詳細
     * ========================================
     */
    public function show(
        Request $request,
        Reservation $reservation
    ): View {

        /*
         * ログイン中の講師を取得
         */
        $teacher = $request->user()->teacher;

        abort_unless(
            $teacher,
            403,
            '講師ユーザーではありません。'
        );

        /*
         * この講師が担当する予約か確認
         */
        abort_unless(
            (int) $reservation->teacher_id
                ===
                (int) $teacher->id,
            403,
            'この予約を表示する権限がありません。'
        );

        /*
         * 詳細画面で必要な関連データを取得
         */
        $reservation->load([
            'student.user',
            'material',
            'status',
            'schedule',
            'histories',
            'lessonRecord',
        ]);

        /*
         * Bladeへ渡す
         */
        return view(
            'teachers.reservations.show',
            compact('reservation')
        );
    }

    /*
 * ========================================
 * 授業結果登録
 * ========================================
 */
    public function updateResult(
        Request $request,
        Reservation $reservation
    ): RedirectResponse {

        /*
     * ========================================
     * 入力内容のValidation
     * ========================================
     */
        $validated = $request->validate([
            'result' => [
                'required',
                Rule::in([
                    'completed',
                    'absent',
                ]),
            ],

            'subject' => [
                'nullable',
                'required_if:result,completed',
                'string',
                'max:255',
            ],

            'progress_note' => [
                'nullable',
                'required_if:result,completed',
                'string',
                'max:2000',
            ],
        ]);

        /*
     * ========================================
     * ログイン中の講師を取得
     * ========================================
     */
        $teacher = $request->user()->teacher;

        abort_unless(
            $teacher,
            403,
            '講師ユーザーではありません。'
        );

        /*
     * ========================================
     * Serviceへ処理を依頼
     * ========================================
     */
        $this->reservationService
            ->recordLessonResult(
                $teacher,
                $reservation,
                (int) $request->user()->getKey(),
                $validated
            );

        /*
     * ========================================
     * 予約詳細画面へ戻る
     * ========================================
     */
        return redirect()
            ->route(
                'teachers.reservations.show',
                $reservation
            )
            ->with(
                'success',
                '授業結果を登録しました。'
            );
    }
}
