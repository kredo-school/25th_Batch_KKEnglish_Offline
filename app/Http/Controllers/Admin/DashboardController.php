<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Dashboard: 当日の先生/場所切替タイムライン画面 (admin/dashboard.blade.php)
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'week_start' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $start = !empty($validated['week_start'])
            ? Carbon::createFromFormat('Y-m-d', $validated['week_start'])->startOfWeek(Carbon::MONDAY)->startOfDay()
            : now()->startOfWeek(Carbon::MONDAY)->startOfDay();

        $end = (clone $start)->addDays(6)->endOfDay();

        $jpDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    /*
    |--------------------------------------------------------------------------
    | 1. 週間の日付を作成
    |--------------------------------------------------------------------------
    */
        $days = collect(CarbonPeriod::create($start, '1 day', $end))
            ->map(fn ($d) => [
                'date'   => $d->toDateString(),
                'day_ja' => $jpDays[$d->dayOfWeek], // 0=日
                'label'  => $d->format('n/j'),
            ]);

    /*
    |--------------------------------------------------------------------------
    | 2. Teacher Schedule を取得
    |--------------------------------------------------------------------------
    |
    | teacher_schedules
    |     available_date
    |     start_time
    |     end_time
    |     shift_pattern_id
    |
    | shift_patterns
    |     slot_minutes
    |
    */
        $teacherSchedules = DB::table(
            'teacher_schedules as ts'
        )
            ->leftJoin(
                'shift_patterns as sp',
                'sp.id',
                '=',
                'ts.shift_pattern_id'
            )
            ->whereBetween(
                'ts.available_date',
                [
                    $start->toDateString(),
                    $end->toDateString(),
                ]
            )
            ->whereNotIn(
                'ts.status',
                ['cancelled']
            )
            ->select([
                'ts.teacher_id',
                'ts.available_date',
                'ts.start_time',
                'ts.end_time',
                'ts.shift_pattern_id',
                'sp.slot_minutes',
            ])
            ->get();

    /*
    |--------------------------------------------------------------------------
    | 3. 各日ごとの Total Available Lessons を計算
    |--------------------------------------------------------------------------
    |
    | 例:
    |
    | 09:00 - 13:00
    | slot_minutes = 30
    |
    | 240分 ÷ 30分 = 8 lessons
    |
    */
        $capacityByDate = $teacherSchedules
        ->groupBy('available_date')
        ->map(function ($schedules) {

            $totalLessons = 0;

            foreach ($schedules as $schedule) {

                /*
                 * slot_minutes が無い場合は30分を使用
                 */
                $slotMinutes = (int) (
                    $schedule->slot_minutes ?? 30
                );

                if ($slotMinutes <= 0) {
                    $slotMinutes = 30;
                }

                /*
                 * 勤務開始時間
                 */
                $startTime = Carbon::createFromFormat(
                    'H:i:s',
                    $schedule->start_time
                );

                /*
                 * 勤務終了時間
                 */
                $endTime = Carbon::createFromFormat(
                    'H:i:s',
                    $schedule->end_time
                );

                /*
                 * 勤務時間を分に変換
                 */
                $minutes = $startTime->diffInMinutes(
                    $endTime
                );

                /*
                 * 1つの勤務時間から可能なLesson数
                 */
                $lessons = intdiv(
                    $minutes,
                    $slotMinutes
                );

                $totalLessons += $lessons;
            }

            return $totalLessons;
        });

        // $capacityByDate = DB::table('teacher_schedules')
        //     ->selectRaw('DATE(start_time) as d, COUNT(*) as c')
        //     ->whereBetween('start_time', [$start, $end])
        //     ->groupBy('d')
        //     ->pluck('c', 'd');

    /*
    |--------------------------------------------------------------------------
    | 4. 予約数
    |--------------------------------------------------------------------------
    */
        $bookedByDate = DB::table('reservations')
            ->selectRaw('DATE(start_at) as d, COUNT(*) as c')
            ->whereBetween('start_at', [$start, $end])
            ->whereNull('cancelled_at')
            ->groupBy('d')
            ->pluck('c', 'd');

    /*
    |--------------------------------------------------------------------------
    | 5. Auto Booked
    |--------------------------------------------------------------------------
    |
    | 現在 reservations に is_auto_assigned が無い場合は0
    |
    */
        $autoBookedByDate = collect();

    /*
    |--------------------------------------------------------------------------
    | 6. 勤務している先生の人数
    |--------------------------------------------------------------------------
    */
            $workingTeachersByDate = $teacherSchedules
            ->groupBy('available_date')
            ->map(function ($schedules) {
                return $schedules
                    ->pluck('teacher_id')
                    ->unique()
                    ->count();
            });
        // $workingTeachersByDate = DB::table('teacher_schedules')
        //     ->selectRaw('DATE(start_time) as d, COUNT(DISTINCT teacher_id) as c')
        //     ->whereBetween('start_time', [$start, $end])
        //     ->groupBy('d')
        //     ->pluck('c', 'd');

    /*
    |--------------------------------------------------------------------------
    | 7. Dashboard rows
    |--------------------------------------------------------------------------
    */
        $rows = $days->map(function ($day) use ($capacityByDate, $bookedByDate, $autoBookedByDate, $workingTeachersByDate) {
            $d = $day['date'];
            return [
                ...$day,
                'capacity'         => (int)($capacityByDate[$d] ?? 0),
                'booked'           => (int)($bookedByDate[$d] ?? 0),
                'auto_booked'      => (int)($autoBookedByDate[$d] ?? 0),
                'working_teachers' => (int)($workingTeachersByDate[$d] ?? 0),
            ];
        });

    /*
    |--------------------------------------------------------------------------
    | 8. Announcements
    |--------------------------------------------------------------------------
    */
        $announcements = collect();

if (Schema::hasTable('announcements')) {
    $columns = [];

    // title
    if (Schema::hasColumn('announcements', 'title')) {
        $columns[] = 'title';
    }

    // target
    if (Schema::hasColumn('announcements', 'target')) {
        $columns[] = 'target';
    }

    // 本文カラム
    if (Schema::hasColumn('announcements', 'body')) {
        $columns[] = 'body';
    } elseif (Schema::hasColumn('announcements', 'content')) {
        $columns[] = 'content';
    } elseif (Schema::hasColumn('announcements', 'description')) {
        $columns[] = 'description';
    }

    // 日付カラム候補
    $dateColumn = null;
    foreach (['published_at', 'created_at', 'updated_at', 'date'] as $candidate) {
        if (Schema::hasColumn('announcements', $candidate)) {
            $dateColumn = $candidate;
            $columns[] = $candidate;
            break;
        }
    }

    if (!empty($columns)) {
        $query = DB::table('announcements')->select($columns);

        if ($dateColumn !== null) {
            $query->orderByDesc($dateColumn);
        }

        $announcements = $query->limit(2)->get();
    }
}

    /*
    |--------------------------------------------------------------------------
    | 9. View
    |--------------------------------------------------------------------------
    */
        return view('admin.dashboard', [
            'weekStart'     => $start->toDateString(),
            'weekNo'        => $start->isoWeek(),   // 例: 36
            'weekYear'      => $start->isoWeekYear(), // 年またぎ対策
            'dashboardRows' => $rows,
            'announcements' => $announcements,
        ]);
    }
    // {
    //     $today = Carbon::today();
    //     $todayStr = $today->toDateString();
    //     // 1. サマリー数値
    //     $capacity = DB::table('teacher_schedules')
    //         ->whereDate('start_time', $todayStr)
    //         ->count();
    //     $booked = DB::table('reservations')
    //         ->whereDate('start_at', $todayStr)
    //         ->whereNull('cancelled_at')
    //         ->count();
    //     $autoBooked = DB::table('reservations')
    //         ->whereDate('start_at', $todayStr)
    //         ->where('is_auto_assigned', 1)
    //         ->whereNull('cancelled_at')
    //         ->count();
    //     // 2. 先生データ & 予約データ
    //     $teachers = DB::table('teachers as t')
    //         ->join('users as u', 'u.id', '=', 't.user_id')
    //         ->select([
    //             't.id',
    //             't.user_id',
    //             DB::raw("CONCAT(COALESCE(u.last_name,''), ' ', COALESCE(u.first_name,'')) as name"),
    //             't.specialty as role',
    //         ])
    //         ->get();
    //     $reservations = DB::table('reservations as r')
    //         ->join('students as s', 's.id', '=', 'r.student_id')
    //         ->join('users as su', 'su.id', '=', 's.user_id')
    //         ->join('materials as m', 'm.material_id', '=', 'r.material_id')
    //         ->whereDate('r.start_at', $todayStr)
    //         ->whereNull('r.cancelled_at')
    //         ->select([
    //             'r.id',
    //             'r.teacher_id',
    //             'r.start_at',
    //             'r.end_at',
    //             DB::raw("CONCAT(COALESCE(su.last_name,''), ' ', COALESCE(su.first_name,'')) as student_name"),
    //             'm.name as material_name'
    //         ])
    //         ->get();
    //     // 先生モード用タイムライン
    //     $teacherTimeline = $teachers->map(function ($teacher) use ($reservations) {
    //         $teacherReservations = $reservations->where('teacher_id', $teacher->id)->map(function ($res) {
    //             $start = Carbon::parse($res->start_at);
    //             $end = Carbon::parse($res->end_at);
    //             return [
    //                 'id' => $res->id,
    //                 'title' => $res->student_name . ' (' . $res->material_name . ')',
    //                 'start_time' => $start->format('H:i'),
    //                 'end_time' => $end->format('H:i'),
    //                 'start_minutes' => $start->hour * 60 + $start->minute,
    //                 'duration_minutes' => $end->diffInMinutes($start),
    //                 'type' => 'booked'
    //             ];
    //         })->values();
    //         return [
    //             'id' => 'teacher_' . $teacher->id,
    //             'name' => $teacher->name ?: 'Teacher #' . $teacher->id,
    //             'role' => $teacher->role ?: 'Instructor',
    //             'blocks' => $teacherReservations
    //         ];
    //     });
    //     // 3. 場所（教室）モード用タイムライン
    //     $rooms = collect([
    //         ['id' => 'room_1', 'name' => 'Station A (Room 101)', 'role' => 'Main Building'],
    //         ['id' => 'room_2', 'name' => 'Station B (Room 102)', 'role' => 'Main Building'],
    //         ['id' => 'room_3', 'name' => 'Station C (Room 103)', 'role' => 'Annex'],
    //         ['id' => 'room_4', 'name' => 'Online Booth 1', 'role' => 'Remote'],
    //         ['id' => 'room_5', 'name' => 'Online Booth 2', 'role' => 'Remote'],
    //     ]);
    //     $roomCount = $rooms->count();
    //     $locationTimeline = $rooms->map(function ($room, $index) use ($reservations, $roomCount) {
    //         $assignedReservations = $reservations->filter(function ($res, $key) use ($index, $roomCount) {
    //             return $roomCount > 0 && ($key % $roomCount) === $index;
    //         })->map(function ($res) {
    //             $start = Carbon::parse($res->start_at);
    //             $end = Carbon::parse($res->end_at);
    //             return [
    //                 'id' => $res->id,
    //                 'title' => $res->student_name . ' - Lesson',
    //                 'start_time' => $start->format('H:i'),
    //                 'end_time' => $end->format('H:i'),
    //                 'start_minutes' => $start->hour * 60 + $start->minute,
    //                 'duration_minutes' => $end->diffInMinutes($start),
    //                 'type' => 'room_used'
    //             ];
    //         })->values();
    //         return [
    //             'id' => $room['id'],
    //             'name' => $room['name'],
    //             'role' => $room['role'],
    //             'blocks' => $assignedReservations
    //         ];
    //     });
    //     $now = Carbon::now();
    //     return view('admin.dashboard', [
    //         'todayDate' => $today->format('Y/m/d'),
    //         'capacity' => $capacity,
    //         'booked' => $booked,
    //         'autoBooked' => $autoBooked,
    //         'teacherTimeline' => $teacherTimeline,
    //         'locationTimeline' => $locationTimeline,
    //         'currentTimeMinutes' => $now->hour * 60 + $now->minute,
    //         'currentTimeStr' => $now->format('H:i'),
    //     ]);
    // }

    /**
     * Schedule Management: 週間予定表画面 (admin/schedules/index.blade.php)
     */
    public function schedulesIndex(Request $request)
{
    $today = Carbon::today();
    $todayStr = $today->toDateString();

    // 1) サマリー
    $capacity = DB::table('teacher_schedules')
        ->whereDate('available_date', $todayStr)
        ->whereNotIn('status', ['cancelled'])
        ->count();

    $booked = DB::table('reservations')
        ->whereDate('start_at', $todayStr)
        ->whereNull('cancelled_at')
        ->count();

    $autoBooked = DB::table('reservations')
        ->whereDate('start_at', $todayStr)
        ->where('is_auto_assigned', 1)
        ->whereNull('cancelled_at')
        ->count();

    // 2) 先生マスタ（名前）
    $teachersMaster = DB::table('teachers as t')
        ->leftJoin('users as u', 'u.id', '=', 't.user_id')
        ->select([
            't.id as teacher_id',
            DB::raw("CONCAT(COALESCE(u.last_name,''), ' ', COALESCE(u.first_name,'')) as name"),
            't.specialty as role',
        ])
        ->get()
        ->keyBy('teacher_id');

    // 3) 予約（フラット）: Station用
    $reservationsFlat = DB::table('reservations as r')
        ->leftJoin('students as s', 's.id', '=', 'r.student_id')
        ->leftJoin('users as su', 'su.id', '=', 's.user_id')
        ->leftJoin('materials as m', 'm.material_id', '=', 'r.material_id')
        ->whereDate('r.start_at', $todayStr)
        ->whereNull('r.cancelled_at')
        ->select([
            'r.id',
            'r.teacher_id',
            'r.start_at',
            'r.end_at',
            DB::raw("CONCAT(COALESCE(su.last_name,''), ' ', COALESCE(su.first_name,'')) as student_name"),
            'm.name as material_name',
        ])
        ->orderBy('r.start_at')
        ->get();

    // 4) 予約（teacher_id別）: Teacher用
    $reservationsByTeacher = $reservationsFlat->groupBy('teacher_id');

    // 5) shift（teacher_id別）: Teacher表示の主データ
    $allShifts = DB::table('teacher_schedules as ts')
        ->whereDate('ts.available_date', $todayStr)
        ->whereNotIn('ts.status', ['cancelled'])
        ->select([
            'ts.teacher_id',
            'ts.available_date',
            'ts.start_time',
            'ts.end_time',
        ])
        ->orderBy('ts.start_time')
        ->get()
        ->groupBy('teacher_id');

    // 6) 表示対象teacher_id（shift優先。なければ予約補完）
    $teacherIds = $allShifts->keys()->sort()->values();
    if ($teacherIds->isEmpty()) {
        $teacherIds = $reservationsByTeacher->keys()->sort()->values();
    }

    // 7) Teacher timeline
    $teacherTimeline = $teacherIds->map(function ($teacherId) use ($allShifts, $reservationsByTeacher, $teachersMaster, $todayStr) {
        $teacher = $teachersMaster->get($teacherId);

        $shiftRows = $allShifts->get($teacherId, collect())->values();
        $shiftBlocks = $shiftRows->map(function ($shift, $idx) use ($teacherId, $todayStr) {
            $start = Carbon::parse($shift->start_time);
            $end = Carbon::parse($shift->end_time);
            $minutes = max($end->diffInMinutes($start), 1);

            return [
                'id' => 'shift_' . $teacherId . '_' . $todayStr . '_' . $idx,
                'title' => 'Shift',
                'start_time' => $start->format('H:i'),
                'end_time' => $end->format('H:i'),
                'start_minutes' => $start->hour * 60 + $start->minute,
                'duration_minutes' => $minutes,
                'type' => 'shift_assignment',
            ];
        });

        $bookedRows = $reservationsByTeacher->get($teacherId, collect())->values();
        $bookedBlocks = $bookedRows->map(function ($res) {
            $start = Carbon::parse($res->start_at);
            $end = Carbon::parse($res->end_at);

            return [
                'id' => 'booked_' . $res->id,
                'title' => ($res->student_name ?: 'Unknown Student') . ' (' . ($res->material_name ?: 'Lesson') . ')',
                'start_time' => $start->format('H:i'),
                'end_time' => $end->format('H:i'),
                'start_minutes' => $start->hour * 60 + $start->minute,
                'duration_minutes' => max($end->diffInMinutes($start), 1),
                'type' => 'booked',
            ];
        });

        return [
            'id' => 'teacher_' . $teacherId,
            'name' => (isset($teacher->name) && trim($teacher->name) !== '') ? $teacher->name : ('Teacher #' . $teacherId),
            'role' => $teacher->role ?? 'Instructor',
            'shift_blocks' => $shiftBlocks,
            'blocks' => $bookedBlocks,
        ];
    })->values();

    // 8) Station timeline（フラット予約を使う）
    $rooms = collect([
        ['id' => 'room_1', 'name' => 'Station A (Room 101)', 'role' => 'Main Building'],
        ['id' => 'room_2', 'name' => 'Station B (Room 102)', 'role' => 'Main Building'],
        ['id' => 'room_3', 'name' => 'Station C (Room 103)', 'role' => 'Annex'],
        ['id' => 'room_4', 'name' => 'Online Booth 1', 'role' => 'Remote'],
        ['id' => 'room_5', 'name' => 'Online Booth 2', 'role' => 'Remote'],
    ]);

    $roomCount = $rooms->count();

    $locationTimeline = $rooms->map(function ($room, $index) use ($reservationsFlat, $roomCount) {
        $assignedReservations = $reservationsFlat
            ->values()
            ->filter(function ($res, $key) use ($index, $roomCount) {
                return $roomCount > 0 && ($key % $roomCount) === $index;
            })
            ->map(function ($res) {
                $start = Carbon::parse($res->start_at);
                $end = Carbon::parse($res->end_at);

                return [
                    'id' => $res->id,
                    'title' => ($res->student_name ?: 'Unknown Student') . ' - Lesson',
                    'start_time' => $start->format('H:i'),
                    'end_time' => $end->format('H:i'),
                    'start_minutes' => $start->hour * 60 + $start->minute,
                    'duration_minutes' => max($end->diffInMinutes($start), 1),
                    'type' => 'room_used',
                ];
            })
            ->values();

        return [
            'id' => $room['id'],
            'name' => $room['name'],
            'role' => $room['role'],
            'blocks' => $assignedReservations,
        ];
    });

    $now = Carbon::now();

    return view('admin.schedules.index', [
        'todayDate' => $today->format('Y/m/d'),
        'capacity' => $capacity,
        'booked' => $booked,
        'autoBooked' => $autoBooked,
        'teacherTimeline' => $teacherTimeline,
        'locationTimeline' => $locationTimeline,
        'currentTimeMinutes' => $now->hour * 60 + $now->minute,
        'currentTimeStr' => $now->format('H:i'),
    ]);
}
    // {
    //     $validated = $request->validate([
    //         'week_start' => ['nullable', 'date_format:Y-m-d'],
    //     ]);

    //     $start = !empty($validated['week_start'])
    //         ? Carbon::createFromFormat('Y-m-d', $validated['week_start'])->startOfWeek(Carbon::MONDAY)->startOfDay()
    //         : now()->startOfWeek(Carbon::MONDAY)->startOfDay();

    //     $end = (clone $start)->addDays(6)->endOfDay();

    //     $jpDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    // /*
    // |--------------------------------------------------------------------------
    // | 1. 週間の日付を作成
    // |--------------------------------------------------------------------------
    // */
    //     $days = collect(CarbonPeriod::create($start, '1 day', $end))
    //         ->map(fn ($d) => [
    //             'date'   => $d->toDateString(),
    //             'day_ja' => $jpDays[$d->dayOfWeek], // 0=日
    //             'label'  => $d->format('n/j'),
    //         ]);

    // /*
    // |--------------------------------------------------------------------------
    // | 2. Teacher Schedule を取得
    // |--------------------------------------------------------------------------
    // |
    // | teacher_schedules
    // |     available_date
    // |     start_time
    // |     end_time
    // |     shift_pattern_id
    // |
    // | shift_patterns
    // |     slot_minutes
    // |
    // */
    //     $teacherSchedules = DB::table(
    //         'teacher_schedules as ts'
    //     )
    //         ->leftJoin(
    //             'shift_patterns as sp',
    //             'sp.id',
    //             '=',
    //             'ts.shift_pattern_id'
    //         )
    //         ->whereBetween(
    //             'ts.available_date',
    //             [
    //                 $start->toDateString(),
    //                 $end->toDateString(),
    //             ]
    //         )
    //         ->whereNotIn(
    //             'ts.status',
    //             ['cancelled']
    //         )
    //         ->select([
    //             'ts.teacher_id',
    //             'ts.available_date',
    //             'ts.start_time',
    //             'ts.end_time',
    //             'ts.shift_pattern_id',
    //             'sp.slot_minutes',
    //         ])
    //         ->get();

    // /*
    // |--------------------------------------------------------------------------
    // | 3. 各日ごとの Total Available Lessons を計算
    // |--------------------------------------------------------------------------
    // |
    // | 例:
    // |
    // | 09:00 - 13:00
    // | slot_minutes = 30
    // |
    // | 240分 ÷ 30分 = 8 lessons
    // |
    // */
    //     $capacityByDate = $teacherSchedules
    //     ->groupBy('available_date')
    //     ->map(function ($schedules) {

    //         $totalLessons = 0;

    //         foreach ($schedules as $schedule) {

    //             /*
    //              * slot_minutes が無い場合は30分を使用
    //              */
    //             $slotMinutes = (int) (
    //                 $schedule->slot_minutes ?? 30
    //             );

    //             if ($slotMinutes <= 0) {
    //                 $slotMinutes = 30;
    //             }

    //             /*
    //              * 勤務開始時間
    //              */
    //             $startTime = Carbon::createFromFormat(
    //                 'H:i:s',
    //                 $schedule->start_time
    //             );

    //             /*
    //              * 勤務終了時間
    //              */
    //             $endTime = Carbon::createFromFormat(
    //                 'H:i:s',
    //                 $schedule->end_time
    //             );

    //             /*
    //              * 勤務時間を分に変換
    //              */
    //             $minutes = $startTime->diffInMinutes(
    //                 $endTime
    //             );

    //             /*
    //              * 1つの勤務時間から可能なLesson数
    //              */
    //             $lessons = intdiv(
    //                 $minutes,
    //                 $slotMinutes
    //             );

    //             $totalLessons += $lessons;
    //         }

    //         return $totalLessons;
    //     });

    //     // $capacityByDate = DB::table('teacher_schedules')
    //     //     ->selectRaw('DATE(start_time) as d, COUNT(*) as c')
    //     //     ->whereBetween('start_time', [$start, $end])
    //     //     ->groupBy('d')
    //     //     ->pluck('c', 'd');

    // /*
    // |--------------------------------------------------------------------------
    // | 4. 予約数
    // |--------------------------------------------------------------------------
    // */
    //     $bookedByDate = DB::table('reservations')
    //         ->selectRaw('DATE(start_at) as d, COUNT(*) as c')
    //         ->whereBetween('start_at', [$start, $end])
    //         ->whereNull('cancelled_at')
    //         ->groupBy('d')
    //         ->pluck('c', 'd');

    // /*
    // |--------------------------------------------------------------------------
    // | 5. Auto Booked
    // |--------------------------------------------------------------------------
    // |
    // | 現在 reservations に is_auto_assigned が無い場合は0
    // |
    // */
    //     $autoBookedByDate = collect();

    // /*
    // |--------------------------------------------------------------------------
    // | 6. 勤務している先生の人数
    // |--------------------------------------------------------------------------
    // */
    //         $workingTeachersByDate = $teacherSchedules
    //         ->groupBy('available_date')
    //         ->map(function ($schedules) {
    //             return $schedules
    //                 ->pluck('teacher_id')
    //                 ->unique()
    //                 ->count();
    //         });
    //     // $workingTeachersByDate = DB::table('teacher_schedules')
    //     //     ->selectRaw('DATE(start_time) as d, COUNT(DISTINCT teacher_id) as c')
    //     //     ->whereBetween('start_time', [$start, $end])
    //     //     ->groupBy('d')
    //     //     ->pluck('c', 'd');

    // /*
    // |--------------------------------------------------------------------------
    // | 7. Dashboard rows
    // |--------------------------------------------------------------------------
    // */
    //     $rows = $days->map(function ($day) use ($capacityByDate, $bookedByDate, $autoBookedByDate, $workingTeachersByDate) {
    //         $d = $day['date'];
    //         return [
    //             ...$day,
    //             'capacity'         => (int)($capacityByDate[$d] ?? 0),
    //             'booked'           => (int)($bookedByDate[$d] ?? 0),
    //             'auto_booked'      => (int)($autoBookedByDate[$d] ?? 0),
    //             'working_teachers' => (int)($workingTeachersByDate[$d] ?? 0),
    //         ];
    //     });

    // /*
    // |--------------------------------------------------------------------------
    // | 8. Announcements
    // |--------------------------------------------------------------------------
    // */
    //     $announcements = collect();
    //     if (Schema::hasTable('announcements')) {
    //         $announcements = DB::table('announcements')
    //             ->select(['title', 'body', 'published_at'])
    //             ->orderByDesc('published_at')
    //             ->limit(5)
    //             ->get();
    //     }

    // /*
    // |--------------------------------------------------------------------------
    // | 9. View
    // |--------------------------------------------------------------------------
    // */
    //     return view('admin.schedules/index', [
    //         'weekStart'     => $start->toDateString(),
    //         'weekNo'        => $start->isoWeek(),   // 例: 36
    //         'weekYear'      => $start->isoWeekYear(), // 年またぎ対策
    //         'dashboardRows' => $rows,
    //         'announcements' => $announcements,
    //     ]);
    // }

    /**
     * Dashboard / Schedule Details (詳細表示画面)
     */
    public function details(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'type' => ['required', 'in:capacity,booked,working_teachers,auto_booked'],
        ]);

        $date = $data['date'];
        $type = $data['type'];

        $items = collect();

    /*
    |--------------------------------------------------------------------------
    | Total Available Lessons
    |--------------------------------------------------------------------------
    */
        if ($type === 'capacity') {
    $items = DB::table('teacher_schedules as ts')
        ->leftJoin('teachers as t', 't.id', '=', 'ts.teacher_id')
        ->leftJoin('users as u', 'u.id', '=', 't.user_id')
        ->leftJoin('shift_patterns as sp', 'sp.id', '=', 'ts.shift_pattern_id')

        ->whereDate('ts.available_date', $date)
        ->whereNotIn('ts.status', ['cancelled'])
        ->select(['ts.teacher_id', 'ts.available_date', 'ts.start_time', 'ts.end_time', 'sp.slot_minutes',

            DB::raw("CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as teacher_name"),
        ])
        ->orderBy('ts.teacher_id')
        ->orderBy('ts.start_time')

        ->get()
        ->map(function ($item) {

            /*
                * slot_minutes
                *
                * 例:
                * 30分 → 30
                * 60分 → 60
                */
            $slotMinutes = (int) (
                $item->slot_minutes ?? 30
            );

            if ($slotMinutes <= 0) {
                $slotMinutes = 30;
            }

            /*
                * 開始時間
                */
            $start = Carbon::createFromFormat(
                'H:i:s',
                $item->start_time
            );

            /*
                * 終了時間
                */
            $end = Carbon::createFromFormat(
                'H:i:s',
                $item->end_time
            );

            /*
                * 勤務時間（分）
                */
            $minutes = $start->diffInMinutes($end);

            /*
                * Lesson可能コマ数
                */
            $lessons = intdiv(
                $minutes,
                $slotMinutes
            );

            return (object) [
                'teacher_name' => $item->teacher_name
                    ?: 'Teacher #' . $item->teacher_id,

                'available_date' => $item->available_date,

                'start_time' => $item->start_time,

                'end_time' => $item->end_time,

                'slot_minutes' => $slotMinutes,

                'available_lessons' => $lessons,
            ];
        });

    /*
    |--------------------------------------------------------------------------
    | Booked
    |--------------------------------------------------------------------------
    */
} elseif ($type === 'booked') {
    $items = DB::table('reservations as r')
        ->leftJoin('teachers as t', 't.id', '=', 'r.teacher_id')
        ->leftJoin('users as u', 'u.id', '=', 't.user_id')
        ->leftJoin('students as s', 's.id', '=', 'r.student_id')
        ->leftJoin('users as su', 'su.id', '=', 's.user_id')
        ->whereDate('r.start_at', $date)
        ->whereNull('r.cancelled_at')
        ->select(['r.id', 'r.student_id',
            DB::raw("CONCAT(COALESCE(su.first_name,''), ' ', COALESCE(su.last_name,'')) as student_name"),
            'r.start_at', 'r.end_at',
            DB::raw("CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as teacher_name"),
        ])
        ->orderBy('r.start_at')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Working Teachers
    |--------------------------------------------------------------------------
    */
} elseif ($type === 'working_teachers') {
    $items = DB::table('teacher_schedules as ts')
        ->leftJoin('teachers as t', 't.id', '=', 'ts.teacher_id')
        ->leftJoin('users as u', 'u.id', '=', 't.user_id')
        ->whereDate('ts.available_date', $date)
        ->whereNotIn('ts.status', ['cancelled'])
        ->select(['ts.teacher_id',
            DB::raw("CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as teacher_name"),
        ])
        ->distinct()
        ->orderBy('teacher_name')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Auto Booked
    |--------------------------------------------------------------------------
    */
} elseif ($type === 'auto_booked') {
    // 現状仕様では0想定。列が無いので空
    $items = collect();
}

        return view('admin.schedules.index_details', [
            'date' => $date,
            'type' => $type,
            'items' => $items,
        ]);
    }

    public function dashboardDetails(Request $request)
    {
        $data = $this->validateDetailsRequest($request);

        return view('admin.dashboard_details', [
            'date' => $data['date'],
            'type' => $data['type'],
            'items' => $this->getDetailItems($data['date'], $data['type']),
        ]);
    }

    private function validateDetailsRequest(Request $request): array
    {
        return $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'type' => ['required', 'in:capacity,booked,working_teachers,auto_booked'],
        ]);
    }

    private function getDetailItems(string $date, string $type)
    {
        if ($type === 'capacity') {
            return DB::table('teacher_schedules as ts')
                ->leftJoin('teachers as t', 't.id', '=', 'ts.teacher_id')
                ->leftJoin('users as u', 'u.id', '=', 't.user_id')
                ->leftJoin('shift_patterns as sp', 'sp.id', '=', 'ts.shift_pattern_id')
                ->whereDate('ts.available_date', $date)
                ->whereNotIn('ts.status', ['cancelled'])
                ->select([
                    'ts.teacher_id',
                    'ts.available_date',
                    'ts.start_time',
                    'ts.end_time',
                    'sp.slot_minutes',
                    DB::raw("CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as teacher_name"),
                ])
                ->orderBy('ts.teacher_id')
                ->orderBy('ts.start_time')
                ->get()
                ->map(function ($item) {
                    $slotMinutes = (int) ($item->slot_minutes ?? 30);

                    if ($slotMinutes <= 0) {
                        $slotMinutes = 30;
                    }

                    $start = Carbon::createFromFormat('H:i:s', $item->start_time);
                    $end = Carbon::createFromFormat('H:i:s', $item->end_time);

                    $minutes = $start->diffInMinutes($end);

                    return (object) [
                        'teacher_name' => $item->teacher_name ?: 'Teacher #' . $item->teacher_id,
                        'available_date' => $item->available_date,
                        'start_time' => $item->start_time,
                        'end_time' => $item->end_time,
                        'slot_minutes' => $slotMinutes,
                        'available_lessons' => intdiv($minutes, $slotMinutes),
                    ];
                });
        }

        if ($type === 'booked') {
            return DB::table('reservations as r')
                ->leftJoin('teachers as t', 't.id', '=', 'r.teacher_id')
                ->leftJoin('users as u', 'u.id', '=', 't.user_id')
                ->leftJoin('students as s', 's.id', '=', 'r.student_id')
                ->leftJoin('users as su', 'su.id', '=', 's.user_id')
                ->whereDate('r.start_at', $date)
                ->whereNull('r.cancelled_at')
                ->select([
                    'r.id',
                    'r.student_id',
                    DB::raw("CONCAT(COALESCE(su.first_name,''), ' ', COALESCE(su.last_name,'')) as student_name"),
                    'r.start_at',
                    'r.end_at',
                    DB::raw("CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as teacher_name"),
                ])
                ->orderBy('r.start_at')
                ->get();
        }

        if ($type === 'working_teachers') {
            return DB::table('teacher_schedules as ts')
                ->leftJoin('teachers as t', 't.id', '=', 'ts.teacher_id')
                ->leftJoin('users as u', 'u.id', '=', 't.user_id')
                ->whereDate('ts.available_date', $date)
                ->whereNotIn('ts.status', ['cancelled'])
                ->select([
                    'ts.teacher_id',
                    DB::raw("CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as teacher_name"),
                ])
                ->distinct()
                ->orderBy('teacher_name')
                ->get();
        }

        if ($type === 'auto_booked') {
            return collect();
        }

        return collect();
    }
}
