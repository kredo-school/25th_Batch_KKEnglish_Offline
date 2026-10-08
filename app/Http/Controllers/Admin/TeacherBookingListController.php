<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherBookingListController extends Controller
{
    /**
     * Teacher Booking List
     *
     * デフォルト：
     * 本日から未来の予約をすべて表示
     */
    public function index(Request $request)
    {
        // ==============================
        // 日付
        // ==============================

        // Fromはデフォルトで今日
        $from = $request->input(
            'from',
            now()->toDateString()
        );

        // Toはデフォルトでは空欄
        // 空欄 = 未来すべて
        $to = $request->input('to');

        // From > To の場合だけ入れ替える
        if ($to && $from > $to) {
            [$from, $to] = [$to, $from];
        }


        // ==============================
        // Teacher
        // ==============================

        $teacherId = $request->input('teacher_id');


        // ==============================
        // Status
        // ==============================

        // デフォルトはキャンセルされていない予約
        $status = $request->input('status', 'active');


        // ==============================
        // Teachers
        // ==============================

        $teachers = Teacher::with('user')
            ->get()
            ->sortBy(function ($teacher) {
                return $teacher->user?->first_name ?? '';
            });


        // ==============================
        // Reservations
        // ==============================

        $query = Reservation::query()
            ->with([
                'student.user',
                'teacher.user',
                'schedule',
                'stationOverride.station',
                'status',
            ])
            ->whereDate('start_at', '>=', $from);


        // ==============================
        // Toが指定されている場合
        // ==============================

        if ($to) {
            $query->whereDate('start_at', '<=', $to);
        }


        // ==============================
        // Teacher filter
        // ==============================

        if ($teacherId) {
            $query->where('teacher_id', $teacherId);
        }


        // ==============================
        // Status filter
        // ==============================

        if ($status === 'active') {

            // キャンセルされていない予約
            $query->whereNull('cancelled_at');

        } elseif ($status === 'cancelled') {

            // キャンセル済み予約
            $query->whereNotNull('cancelled_at');

        }

        // status = all の場合は条件なし


        // ==============================
        // Reservation取得
        // ==============================

        $reservations = $query
            ->orderBy('start_at')
            ->get();


        // ==============================
        // Booking List用データ
        // ==============================

        $bookings = $reservations->map(function ($reservation) {

            $teacher = $reservation->teacher;
            $student = $reservation->student;


            // ------------------------------
            // Teacher名
            // ------------------------------

            $teacherName = trim(
                ($teacher?->user?->first_name ?? '') . ' ' .
                ($teacher?->user?->last_name ?? '')
            );

            if ($teacherName === '') {
                $teacherName = 'Unknown Teacher';
            }


            // ------------------------------
            // Student名
            // ------------------------------

            $studentName = trim(
                ($student?->user?->first_name ?? '') . ' ' .
                ($student?->user?->last_name ?? '')
            );

            if ($studentName === '') {
                $studentName = 'Unknown Student';
            }


            // ------------------------------
            // Station
            // ------------------------------

            // Reservationモデルにある
            // displayStation() を利用する
            //
            // 予約単位のOverrideがあればOverrideを優先し、
            // なければTeacherの通常Stationを使用する。
            $station = $reservation->displayStation();


            // ------------------------------
            // Time
            // ------------------------------

            $startAt = $reservation->start_at;

            if ($reservation->end_at) {

                $endTime = $reservation->end_at->format('H:i');

            } else {

                // end_atがない場合は30分として表示
                $endTime = $startAt
                    ? $startAt->copy()
                        ->addMinutes(30)
                        ->format('H:i')
                    : null;
            }


            return [
                'reservation' => $reservation,

                'date' => $startAt?->format('Y-m-d'),

                'day' => $startAt?->format('D'),

                'time' => $startAt?->format('H:i'),

                'end_time' => $endTime,

                'teacher_name' => $teacherName,

                'student_name' => $studentName,

                'station_name' => $station?->name ?? 'No Station',

                'is_cancelled' => !is_null(
                    $reservation->cancelled_at
                ),
            ];
        });


        // ==============================
        // View
        // ==============================

        return view(
            'admin.teacher-booking-list.index',
            compact(
                'bookings',
                'teachers',
                'from',
                'to',
                'teacherId',
                'status'
            )
        );
    }
}
