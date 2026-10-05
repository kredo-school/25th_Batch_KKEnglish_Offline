<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperationalStatusController extends Controller
{
    /**
     * Operational Status
     */
    public function index(Request $request): View
    {
        /*
        |--------------------------------------------------------------------------
        | 1. 対象年月
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

        $monthEnd = $monthStart->copy()->endOfMonth();


        /*
        |--------------------------------------------------------------------------
        | 2. Monthly Reservations
        |--------------------------------------------------------------------------
        |
        | 対象月の予約総数
        |
        */

        $monthlyReservations = Reservation::query()
            ->whereBetween('start_at', [
                $monthStart,
                $monthEnd,
            ])
            ->count();


        /*
        |--------------------------------------------------------------------------
        | 3. Cancellations
        |--------------------------------------------------------------------------
        |
        | cancelled_at が入っている予約
        |
        */

        $monthlyCancellations = Reservation::query()
            ->whereBetween('start_at', [
                $monthStart,
                $monthEnd,
            ])
            ->whereNotNull('cancelled_at')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | 4. Valid Reservations
        |--------------------------------------------------------------------------
        |
        | キャンセルされていない予約
        |
        */

        $validReservations = Reservation::query()
            ->whereBetween('start_at', [
                $monthStart,
                $monthEnd,
            ])
            ->whereNull('cancelled_at')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | 5. Cancellation Rate
        |--------------------------------------------------------------------------
        |
        | キャンセル数 ÷ 全予約数 × 100
        |
        */

        $cancellationRate = $monthlyReservations > 0
            ? round(
                ($monthlyCancellations / $monthlyReservations) * 100,
                1
            )
            : 0;


        /*
        |--------------------------------------------------------------------------
        | 6. Completed Lessons
        |--------------------------------------------------------------------------
        |
        | lesson_records.completed_at が存在するレッスン
        |
        | キャンセル予約は除外
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
        | 7. Operation Rate
        |--------------------------------------------------------------------------
        |
        | 完了レッスン数 ÷ 有効予約数 × 100
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
        | 8. Teacher Rating Analysis
        |--------------------------------------------------------------------------
        |
        | Review
        |   ↓ reservation_id
        | Reservation
        |   ↓ teacher_id
        | Teacher
        |   ↓ user_id
        | User
        |
        */


        /*
        | 対象月のレビュー
        |
        | キャンセルされていない予約に対するレビューのみ
        |
        */

        $monthlyReviews = Review::query()
            ->whereHas('reservation', function ($query) use (
                $monthStart,
                $monthEnd
            ) {
                $query
                    ->whereBetween('start_at', [
                        $monthStart,
                        $monthEnd,
                    ])
                    ->whereNull('cancelled_at');
            })
            ->whereNotNull('rating')
            ->get();


        /*
        | Average Rating
        */

        $averageRating = $monthlyReviews->isNotEmpty()
            ? round(
                (float) $monthlyReviews->avg('rating'),
                2
            )
            : null;


        /*
        | Highest Rating
        */

        $maxRating = $monthlyReviews->isNotEmpty()
            ? (float) $monthlyReviews->max('rating')
            : null;


        /*
        | Lowest Rating
        */

        $minRating = $monthlyReviews->isNotEmpty()
            ? (float) $monthlyReviews->min('rating')
            : null;


        /*
        | Review Count
        */

        $reviewCount = $monthlyReviews->count();


        /*
        |--------------------------------------------------------------------------
        | 9. Teacher Rating Comparison
        |--------------------------------------------------------------------------
        |
        | TeacherはReview.teacher_idではなく、
        | Reservation.teacher_idから取得する。
        |
        */

        $teacherRatingComparison = DB::table('reviews')
            ->join(
                'reservations',
                'reservations.id',
                '=',
                'reviews.reservation_id'
            )
            ->join(
                'teachers',
                'teachers.id',
                '=',
                'reservations.teacher_id'
            )
            ->join(
                'users',
                'users.id',
                '=',
                'teachers.user_id'
            )
            ->whereBetween('reservations.start_at', [
                $monthStart,
                $monthEnd,
            ])
            ->whereNull('reservations.cancelled_at')
            ->whereNotNull('reviews.rating')
            ->select(
                'reservations.teacher_id',
                'users.first_name',
                'users.last_name',
                DB::raw('AVG(reviews.rating) as average_rating'),
                DB::raw('MAX(reviews.rating) as max_rating'),
                DB::raw('MIN(reviews.rating) as min_rating'),
                DB::raw('COUNT(*) as review_count')
            )
            ->groupBy(
                'reservations.teacher_id',
                'users.first_name',
                'users.last_name'
            )
            ->orderByDesc('average_rating')
            ->get()
            ->map(function ($row) {

                return [
                    'teacher_id' => $row->teacher_id,

                    'teacher_name' => trim(
                        ($row->first_name ?? '')
                        . ' '
                        . ($row->last_name ?? '')
                    ),

                    'average_rating' => round(
                        (float) $row->average_rating,
                        2
                    ),

                    'max_rating' => (float) $row->max_rating,

                    'min_rating' => (float) $row->min_rating,

                    'review_count' => (int) $row->review_count,
                ];
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | 10. Shift Analysis
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
        | 11. Shift Count
        |--------------------------------------------------------------------------
        */

        $shiftCount = $shiftSchedules->count();


        /*
        |--------------------------------------------------------------------------
        | 12. Active Teacher Count
        |--------------------------------------------------------------------------
        */

        $activeTeacherCount = $shiftSchedules
            ->pluck('teacher_id')
            ->filter()
            ->unique()
            ->count();


        /*
        |--------------------------------------------------------------------------
        | 13. Working Hours
        |--------------------------------------------------------------------------
        */

        $workingMinutes = 0;

        foreach ($shiftSchedules as $schedule) {

            if (
                !$schedule->start_time ||
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
        | 14. Daily Reservation Analysis
        |--------------------------------------------------------------------------
        */

        $dailyReservations = Reservation::query()
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
        | 15. Average / Maximum / Minimum
        |--------------------------------------------------------------------------
        */

        $dailyReservationValues = $dailyReservations
            ->pluck('total')
            ->map(
                fn ($value) => (int) $value
            );


        $reservationAverage = $dailyReservationValues->isNotEmpty()
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
        | 16. Annual Monthly Trend
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
            | Total Reservations
            */

            $reservations = Reservation::query()
                ->whereBetween('start_at', [
                    $start,
                    $end,
                ])
                ->count();


            /*
            | Valid Reservations
            */

            $valid = Reservation::query()
                ->whereBetween('start_at', [
                    $start,
                    $end,
                ])
                ->whereNull('cancelled_at')
                ->count();


            /*
            | Cancellations
            */

            $cancellations = Reservation::query()
                ->whereBetween('start_at', [
                    $start,
                    $end,
                ])
                ->whereNotNull('cancelled_at')
                ->count();


            /*
            | Completed Lessons
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
            | Monthly Operation Rate
            */

            $monthlyOperationRate = $valid > 0
                ? round(
                    ($completed / $valid) * 100,
                    1
                )
                : 0;


            /*
            | Cancellation Rate
            */

            $monthlyCancellationRate = $reservations > 0
                ? round(
                    ($cancellations / $reservations) * 100,
                    1
                )
                : 0;


            /*
            | Reviews
            */

            $reviews = Review::query()
                ->whereHas('reservation', function ($query) use (
                    $start,
                    $end
                ) {
                    $query
                        ->whereBetween('start_at', [
                            $start,
                            $end,
                        ])
                        ->whereNull('cancelled_at');
                })
                ->whereNotNull('rating')
                ->get();


            $monthlyReviewCount = $reviews->count();


            $monthlyAverageRating = $monthlyReviewCount > 0
                ? round(
                    (float) $reviews->avg('rating'),
                    2
                )
                : null;


            $monthlyMaxRating = $monthlyReviewCount > 0
                ? (float) $reviews->max('rating')
                : null;


            $monthlyMinRating = $monthlyReviewCount > 0
                ? (float) $reviews->min('rating')
                : null;


            /*
            | Monthly Trend
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
                    $monthlyOperationRate,

                'cancellations' =>
                    $cancellations,

                'cancellation_rate' =>
                    $monthlyCancellationRate,

                'review_count' =>
                    $monthlyReviewCount,

                'average_rating' =>
                    $monthlyAverageRating,

                'max_rating' =>
                    $monthlyMaxRating,

                'min_rating' =>
                    $monthlyMinRating,
            ];
        }

/*
|--------------------------------------------------------------------------
| 16-2. Annual Total
|--------------------------------------------------------------------------
|
| 選択された年の年間総計
|
*/

$annualReservations = 0;
$annualValid = 0;
$annualCompleted = 0;
$annualCancellations = 0;
$annualReviewCount = 0;

$annualRatingValues = collect();


foreach ($monthlyTrend as $row) {

    $annualReservations += $row['reservations'];

    $annualValid += $row['valid'];

    $annualCompleted += $row['completed'];

    $annualCancellations += $row['cancellations'];

    $annualReviewCount += $row['review_count'];

    if ($row['average_rating'] !== null) {
        /*
        | 月平均をそのまま足したり平均したりすると
        | レビュー件数を考慮できないため、
        | 年間評価は別途レビューから計算します。
        */
    }
}


/*
|--------------------------------------------------------------------------
| Annual Reviews
|--------------------------------------------------------------------------
*/

$annualReviews = Review::query()
    ->whereHas('reservation', function ($query) use (
        $year
    ) {
        $query
            ->whereYear('start_at', $year)
            ->whereNull('cancelled_at');
    })
    ->whereNotNull('rating')
    ->get();


$annualReviewCount = $annualReviews->count();


$annualAverageRating = $annualReviewCount > 0
    ? round(
        (float) $annualReviews->avg('rating'),
        2
    )
    : null;


$annualMaxRating = $annualReviewCount > 0
    ? (float) $annualReviews->max('rating')
    : null;


$annualMinRating = $annualReviewCount > 0
    ? (float) $annualReviews->min('rating')
    : null;


/*
|--------------------------------------------------------------------------
| Annual Operation Rate
|--------------------------------------------------------------------------
*/

$annualOperationRate = $annualValid > 0
    ? round(
        ($annualCompleted / $annualValid) * 100,
        1
    )
    : 0;


/*
|--------------------------------------------------------------------------
| Annual Cancellation Rate
|--------------------------------------------------------------------------
*/

$annualCancellationRate = $annualReservations > 0
    ? round(
        ($annualCancellations / $annualReservations) * 100,
        1
    )
    : 0;


        /*
        |--------------------------------------------------------------------------
        | 17. View
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
                'annualReservations',
                'annualValid',
                'annualCompleted',
                'annualOperationRate',
                'annualCancellations',
                'annualCancellationRate',
                'annualReviewCount',
                'annualAverageRating',
                'annualMaxRating',
                'annualMinRating',
            )
        );
    }
}

