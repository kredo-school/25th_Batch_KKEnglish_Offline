<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation;
use App\Models\Review;

class OperationalStatusController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Operational Status
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        /*
        |--------------------------------------------------------------------------
        | 対象年月
        |--------------------------------------------------------------------------
        */

        $year = (int) (
            $request->input('year')
            ?: now()->year
        );

        $month = (int) (
            $request->input('month')
            ?: now()->month
        );

        $monthStart = Carbon::create(
            $year,
            $month,
            1
        )->startOfMonth();

        $monthEnd = Carbon::create(
            $year,
            $month,
            1
        )->endOfMonth();


        /*
        |--------------------------------------------------------------------------
        | 1. Monthly Reservations
        |--------------------------------------------------------------------------
        |
        | 月内の予約総数
        |
        */

        $monthlyReservations = DB::table('reservations')
            ->whereBetween('start_at', [
                $monthStart,
                $monthEnd,
            ])
            ->count();


        /*
        |--------------------------------------------------------------------------
        | 2. Monthly Cancellations
        |--------------------------------------------------------------------------
        |
        | cancelled_at が入っている予約
        |
        */

        $monthlyCancellations = DB::table('reservations')
            ->whereBetween('start_at', [
                $monthStart,
                $monthEnd,
            ])
            ->whereNotNull('cancelled_at')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | 3. Valid Reservations
        |--------------------------------------------------------------------------
        |
        | キャンセルされていない予約
        |
        */

        $validReservations = DB::table('reservations')
            ->whereBetween('start_at', [
                $monthStart,
                $monthEnd,
            ])
            ->whereNull('cancelled_at')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | 4. Cancellation Rate
        |--------------------------------------------------------------------------
        */

        $cancellationRate = $monthlyReservations > 0
            ? round(
                ($monthlyCancellations / $monthlyReservations) * 100,
                1
            )
            : 0;


        /*
        |--------------------------------------------------------------------------
        | 5. Completed Lessons
        |--------------------------------------------------------------------------
        |
        | ★重要
        |
        | lesson_records.completed_at が入っているものを
        | 「先生がレッスン報告まで完了したレッスン」
        | としてカウントします。
        |
        | reservation.start_at を基準に対象月を判定します。
        |
        */

        $completedLessonCount = DB::table('lesson_records')
            ->join(
                'reservations',
                'reservations.id',
                '=',
                'lesson_records.reservation_id'
            )
            ->whereBetween('reservations.start_at', [
                $monthStart,
                $monthEnd,
            ])
            ->whereNull('reservations.cancelled_at')
            ->whereNotNull('lesson_records.completed_at')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | 6. Operation Rate
        |--------------------------------------------------------------------------
        |
        | 稼働率
        |
        | レッスン報告完了数
        | ----------------
        | キャンセルされていない予約数
        |
        */

        $operationRate = $validReservations > 0
            ? round(
                ($completedLessonCount / $validReservations) * 100,
                1
            )
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Teacher Rating Analysis
        |--------------------------------------------------------------------------
        */

        $monthlyReviews = Review::query()
            ->whereHas('reservation', function ($query) use ($monthStart, $monthEnd) {
                $query
                    ->whereBetween('start_at', [
                        $monthStart,
                        $monthEnd,
                    ])
                    ->whereNull('cancelled_at');
            })
            ->with([
                'teacher.user',
                'student.user',
            ])
            ->get();


        // 全体の評価平均
        $averageRating = $monthlyReviews->avg('rating');

        // 最高評価
        $maxRating = $monthlyReviews->max('rating');

        // 最低評価
        $minRating = $monthlyReviews->min('rating');

        // 評価件数
        $reviewCount = $monthlyReviews->count();

        /*
        |--------------------------------------------------------------------------
        | Teacher Rating Comparison
        |--------------------------------------------------------------------------
        */

        $teacherRatingComparison = $monthlyReviews
            ->groupBy('teacher_id')
            ->map(function ($reviews) {

                $teacher = $reviews->first()->teacher;

                return [
                    'teacher_id' => $teacher?->id,

                    'teacher_name' =>
                        trim(
                            ($teacher?->user?->first_name ?? '')
                            . ' '
                            . ($teacher?->user?->last_name ?? '')
                        ),

                    'average_rating' => round(
                        $reviews->avg('rating'),
                        2
                    ),

                    'max_rating' => $reviews->max('rating'),

                    'min_rating' => $reviews->min('rating'),

                    'review_count' => $reviews->count(),
                ];
            })
            ->sortByDesc('average_rating')
            ->values();

            $annualTrend = collect();

            for ($monthNumber = 1; $monthNumber <= 12; $monthNumber++) {

                $start = Carbon::create(
                    $year,
                    $monthNumber,
                    1
                )->startOfMonth();

                $end = Carbon::create(
                    $year,
                    $monthNumber,
                    1
                )->endOfMonth();

                // 予約
                $reservations = Reservation::query()
                    ->whereBetween('start_at', [$start, $end])
                    ->count();

                // キャンセルされていない予約
                $valid = Reservation::query()
                    ->whereBetween('start_at', [$start, $end])
                    ->whereNull('cancelled_at')
                    ->count();

                // レッスンレポート完了
                $completed = DB::table('lesson_records')
                    ->join(
                        'reservations',
                        'reservations.id',
                        '=',
                        'lesson_records.reservation_id'
                    )
                    ->whereBetween(
                        'reservations.start_at',
                        [$start, $end]
                    )
                    ->whereNull('reservations.cancelled_at')
                    ->whereNotNull('lesson_records.completed_at')
                    ->count();

                // キャンセル
                $cancellations = Reservation::query()
                    ->whereBetween('start_at', [$start, $end])
                    ->whereNotNull('cancelled_at')
                    ->count();

                // レビュー
                $reviews = Review::query()
                    ->whereHas('reservation', function ($query) use ($start, $end) {
                        $query
                            ->whereBetween('start_at', [$start, $end])
                            ->whereNull('cancelled_at');
                    })
                    ->get();

                $reviewCount = $reviews->count();

                $averageRating = $reviewCount > 0
                    ? round($reviews->avg('rating'), 2)
                    : null;

                $maxRating = $reviewCount > 0
                    ? $reviews->max('rating')
                    : null;

                $minRating = $reviewCount > 0
                    ? $reviews->min('rating')
                    : null;

                $operationRate = $valid > 0
                    ? round(($completed / $valid) * 100, 1)
                    : 0;

                $cancellationRate = $reservations > 0
                    ? round(($cancellations / $reservations) * 100, 1)
                    : 0;

                $annualTrend->push([
                    'month' => $monthNumber,

                    'reservations' => $reservations,

                    'valid' => $valid,

                    'completed' => $completed,

                    'operation_rate' => $operationRate,

                    'cancellations' => $cancellations,

                    'cancellation_rate' => $cancellationRate,

                    'review_count' => $reviewCount,

                    'average_rating' => $averageRating,

                    'max_rating' => $maxRating,

                    'min_rating' => $minRating,
                ]);
            }

        /*
        |--------------------------------------------------------------------------
        | 7. Shift Analysis
        |--------------------------------------------------------------------------
        */

        $shiftSchedules = DB::table('teacher_schedules')
            ->whereBetween('available_date', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->where('status', '!=', 'cancelled')
            ->get([
                'teacher_id',
                'available_date',
                'start_time',
                'end_time',
                'status',
            ]);


        /*
        |--------------------------------------------------------------------------
        | 8. Shift Count
        |--------------------------------------------------------------------------
        */

        $shiftCount = $shiftSchedules->count();


        /*
        |--------------------------------------------------------------------------
        | 9. Active Teacher Count
        |--------------------------------------------------------------------------
        */

        $activeTeacherCount = $shiftSchedules
            ->pluck('teacher_id')
            ->filter()
            ->unique()
            ->count();


        /*
        |--------------------------------------------------------------------------
        | 10. Working Hours
        |--------------------------------------------------------------------------
        */

        $workingMinutes = 0;

        foreach ($shiftSchedules as $schedule) {

            if (
                !$schedule->start_time
                ||
                !$schedule->end_time
            ) {
                continue;
            }

            $start = Carbon::parse(
                $schedule->available_date
                . ' '
                . $schedule->start_time
            );

            $end = Carbon::parse(
                $schedule->available_date
                . ' '
                . $schedule->end_time
            );

            if ($end->gt($start)) {
                $workingMinutes +=
                    $start->diffInMinutes($end);
            }
        }

        $workingHours = round(
            $workingMinutes / 60,
            1
        );


        /*
        |--------------------------------------------------------------------------
        | 11. Daily Reservation Analysis
        |--------------------------------------------------------------------------
        */

        $dailyReservations = DB::table('reservations')
            ->selectRaw(
                'DATE(start_at) as reservation_date,
                 COUNT(*) as total'
            )
            ->whereBetween('start_at', [
                $monthStart,
                $monthEnd,
            ])
            ->whereNull('cancelled_at')
            ->groupByRaw('DATE(start_at)')
            ->orderBy('reservation_date')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 12. Average / Maximum / Minimum
        |--------------------------------------------------------------------------
        */

        $dailyReservationValues = $dailyReservations
            ->pluck('total')
            ->map(
                fn ($value) => (int) $value
            );

        $reservationAverage = $dailyReservationValues->count()
            ? round(
                $dailyReservationValues->avg(),
                1
            )
            : 0;

        $reservationMaximum =
            $dailyReservationValues->max() ?? 0;

        $reservationMinimum =
            $dailyReservationValues->min() ?? 0;


        /*
        |--------------------------------------------------------------------------
        | 13. Annual Monthly Trend
        |--------------------------------------------------------------------------
        |
        | 選択された年の1月～12月
        |
        */

        $monthlyTrend = [];

        for ($m = 1; $m <= 12; $m++) {

            $start = Carbon::create(
                $year,
                $m,
                1
            )->startOfMonth();

            $end = $start->copy()->endOfMonth();


            /*
            |--------------------------------------------------------------
            | Total Reservations
            |--------------------------------------------------------------
            */

            $reservations = DB::table('reservations')
                ->whereBetween('start_at', [
                    $start,
                    $end,
                ])
                ->count();


            /*
            |--------------------------------------------------------------
            | Valid Reservations
            |--------------------------------------------------------------
            */

            $valid = DB::table('reservations')
                ->whereBetween('start_at', [
                    $start,
                    $end,
                ])
                ->whereNull('cancelled_at')
                ->count();


            /*
            |--------------------------------------------------------------
            | Cancellations
            |--------------------------------------------------------------
            */

            $cancellations = DB::table('reservations')
                ->whereBetween('start_at', [
                    $start,
                    $end,
                ])
                ->whereNotNull('cancelled_at')
                ->count();


            /*
            |--------------------------------------------------------------
            | Completed Lessons
            |--------------------------------------------------------------
            */

            $completed = DB::table('lesson_records')
                ->join(
                    'reservations',
                    'reservations.id',
                    '=',
                    'lesson_records.reservation_id'
                )
                ->whereBetween('reservations.start_at', [
                    $start,
                    $end,
                ])
                ->whereNull('reservations.cancelled_at')
                ->whereNotNull('lesson_records.completed_at')
                ->count();


            /*
            |--------------------------------------------------------------
            | Operation Rate
            |--------------------------------------------------------------
            */

            $operation = $valid > 0
                ? round(
                    ($completed / $valid) * 100,
                    1
                )
                : 0;

            /*
            |--------------------------------------------------------------------------
            | Review Analysis
            |--------------------------------------------------------------------------
            */

            $reviews = Review::query()
                ->whereHas('reservation', function ($query) use ($start, $end) {
                    $query
                        ->whereBetween('start_at', [
                            $start,
                            $end,
                        ])
                        ->whereNull('cancelled_at');
                })
                ->get();

            $reviewCount = $reviews->count();

            $averageRating = $reviewCount > 0
                ? round(
                    $reviews->avg('rating'),
                    2
                )
                : null;

            $maxRating = $reviewCount > 0
                ? $reviews->max('rating')
                : null;

            $minRating = $reviewCount > 0
                ? $reviews->min('rating')
                : null;


            /*
            |--------------------------------------------------------------------------
            | Monthly Trend
            |--------------------------------------------------------------------------
            */

            $monthlyTrend[] = [

                'month' =>
                    $start->format('M'),

                'reservations' =>
                    $reservations,

                'valid' =>
                    $valid,

                'completed' =>
                    $completed,

                'operation_rate' =>
                    $operation,

                'cancellations' =>
                    $cancellations,

                'cancellation_rate' =>
                    $reservations > 0
            ? round(($cancellations / $reservations) * 100, 1)
            : 0,

                'review_count' =>
                    $reviewCount,

                'average_rating' =>
                    $averageRating,

                'max_rating' =>
                    $maxRating,

                'min_rating' =>
                    $minRating,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.operational-status.index',
            compact(

                'year',

                'month',

                'monthStart',

                'monthEnd',

                'monthlyReservations',

                'monthlyCancellations',

                'validReservations',

                'cancellationRate',

                'completedLessonCount',

                'operationRate',

                'shiftCount',

                'activeTeacherCount',

                'workingHours',

                'reservationAverage',

                'reservationMaximum',

                'reservationMinimum',

                'monthlyTrend',

                'averageRating',
                'maxRating',
                'minRating',
                'reviewCount',
                'teacherRatingComparison',
            )
        );
    }
}
