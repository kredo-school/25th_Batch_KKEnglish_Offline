<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
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
            // 'lessonRecord',
        ]);

        /*
         * Bladeへ渡す
         */
        return view(
            'teachers.reservations.show',
            compact('reservation')
        );
    }
}
