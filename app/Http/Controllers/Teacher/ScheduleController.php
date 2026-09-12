<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherSchedule;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user()->teacher;

        abort_unless(
            $teacher,
            403,
            '講師ユーザーではありません。'
        );

        $startOfWeek = $request->filled('week_start')
            ? Carbon::parse($request->input('week_start'))
                ->startOfWeek(Carbon::MONDAY)
            : now()->startOfWeek(Carbon::MONDAY);

        $viewStart = $request->input('view_start', '06:00');
        $viewEnd = $request->input('view_end', '23:00');

        $days = collect(range(0, 6))->map(
            fn (int $number) =>
                $startOfWeek->copy()->addDays($number)
        );

        $times = collect();

        $cursor = Carbon::createFromFormat('H:i', $viewStart);
        $end = Carbon::createFromFormat('H:i', $viewEnd);

        while ($cursor->lt($end)) {
            $times->push($cursor->format('H:i'));
            $cursor->addMinutes(30);
        }

        $schedules = TeacherSchedule::query()
            ->where('teacher_id', $teacher->id)
            ->where('status', 'confirmed')
            ->whereBetween('available_date', [
                $startOfWeek->toDateString(),
                $startOfWeek->copy()
                    ->addDays(6)
                    ->toDateString(),
            ])
            ->with([
                'shiftPattern',
                'exceptions.exceptionType',
                'reservations' => function ($query) {
                    $query
                        ->whereHas('status', function ($statusQuery) {
                            $statusQuery->whereIn(
                                'status_code',
                                ['pending', 'confirmed']
                            );
                        })
                        ->with([
                            'student.user',
                            'material',
                            'status',
                        ]);
                },
            ])
            ->orderBy('available_date')
            ->orderBy('start_time')
            ->get();

        $existingMap = [];
        $cellScheduleIdMap = [];
        $reservationMap = [];

        foreach ($schedules as $schedule) {
            $date = $schedule->available_date->format('Y-m-d');

            $scheduleStart = Carbon::parse(
                $date . ' ' . $schedule->start_time
            );

            $scheduleEnd = Carbon::parse(
                $date . ' ' . $schedule->end_time
            );

            if ($scheduleEnd->lte($scheduleStart)) {
                $scheduleEnd->addDay();
            }

            $slotStart = $scheduleStart->copy();

            while ($slotStart->lt($scheduleEnd)) {
                $slotEnd = $slotStart->copy()->addMinutes(30);

                $cellDate = $slotStart->format('Y-m-d');
                $cellTime = $slotStart->format('H:i');

                $existingMap[$cellDate][$cellTime] = 'available';

                $cellScheduleIdMap[$cellDate][$cellTime] =
                    $schedule->schedule_id;

                /*
                 * このセルと時間が重なる予約を検索
                 */
                $reservation = $schedule->reservations->first(
                    function ($reservation) use (
                        $slotStart,
                        $slotEnd
                    ) {
                        return $reservation->start_at->lt($slotEnd)
                            && $reservation->end_at->gt($slotStart);
                    }
                );

                if ($reservation) {
                    $existingMap[$cellDate][$cellTime] = 'booked';

                    $reservationMap[$cellDate][$cellTime] =
                        $reservation;
                }

                $slotStart->addMinutes(30);
            }
        }

        $teacherId = $teacher->id;
        $teachers = collect();

        return view('teachers.schedule', compact(
            'startOfWeek',
            'viewStart',
            'viewEnd',
            'days',
            'times',
            'existingMap',
            'cellScheduleIdMap',
            'reservationMap',
            'teacherId',
            'teachers'
        ));
    }
}
