<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreScheduleExceptionRequest;
use App\Models\ExceptionType;
use App\Models\ScheduleException;
use App\Models\TeacherSchedule;
use App\Services\ScheduleExceptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleExceptionController extends Controller
{
    public function __construct(
        private readonly ScheduleExceptionService $service
    ) {
    }

public function index(Request $request): JsonResponse
{
    $teacher = $request->user()->teacher;

    abort_unless(
        $teacher,
        403,
        '講師ユーザーではありません。'
    );

    $schedules = TeacherSchedule::query()
        ->with([
            'shiftPattern',
            'exceptions.exceptionType',
            'reservations.status',
        ])
        ->where('teacher_id', $teacher->id)
        ->where('status', 'confirmed')
        ->whereDate('available_date', '>=', today())
        ->orderBy('available_date')
        ->orderBy('start_time')
        ->get();

    $formattedSchedules = $schedules->map(function ($schedule) {

        return [
            'schedule_id' => $schedule->schedule_id,

            'available_date' => $schedule->available_date
                instanceof \Carbon\Carbon
                    ? $schedule->available_date->format('Y-m-d')
                    : substr((string) $schedule->available_date, 0, 10),

            'start_time' => substr(
                (string) $schedule->start_time,
                0,
                8
            ),

            'end_time' => substr(
                (string) $schedule->end_time,
                0,
                8
            ),

            'status' => $schedule->status,

            'exceptions' => $schedule->exceptions->map(
                function ($exception) {

                    return [
                        'id' => $exception->id,

                        'exception_type_id' =>
                            $exception->exception_type_id,

                        'start_at' =>
                            $exception->start_at
                                instanceof \Carbon\Carbon
                                    ? $exception->start_at
                                        ->format('Y-m-d H:i:s')
                                    : (string) $exception->start_at,

                        'end_at' =>
                            $exception->end_at
                                instanceof \Carbon\Carbon
                                    ? $exception->end_at
                                        ->format('Y-m-d H:i:s')
                                    : (string) $exception->end_at,

                        'status' =>
                            $exception->status,

                        'reason' =>
                            $exception->reason,

                        'exception_type' =>
                            $exception->exceptionType,
                    ];
                }
            ),
        ];
    });

    return response()->json([
        'schedules' => $formattedSchedules,

        'exception_types' => ExceptionType::query()
            ->orderBy('type_name')
            ->get(),
    ]);
}

    public function store(
        StoreScheduleExceptionRequest $request
    ): JsonResponse {
        $teacher = $request->user()->teacher;

        abort_unless(
            $teacher,
            403,
            '講師ユーザーではありません。'
        );

        $exception = $this->service->createForTeacher(
            $teacher,
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'message' => '休日・休止時間を登録しました。',
            'exception' => $exception->load([
                'exceptionType',
                'schedule',
            ]),
        ], 201);
    }

    public function destroy(
        Request $request,
        ScheduleException $scheduleException
    ): JsonResponse {
        $teacher = $request->user()->teacher;

        abort_unless(
            $teacher,
            403,
            '講師ユーザーではありません。'
        );

        $exception = $this->service->cancelForTeacher(
            $scheduleException,
            $teacher,
            $request->user()
        );

        return response()->json([
            'message' => '休日・休止時間を取り消しました。',
            'exception' => $exception,
        ]);
    }
}
