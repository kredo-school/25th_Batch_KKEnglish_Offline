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
        $today = Carbon::today();
        $todayStr = $today->toDateString();
        // 1. サマリー数値
        $capacity = DB::table('teacher_schedules')
            ->whereDate('start_time', $todayStr)
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
        // 2. 先生データ & 予約データ
        $teachers = DB::table('teachers as t')
            ->join('users as u', 'u.id', '=', 't.user_id')
            ->select([
                't.id',
                't.user_id',
                DB::raw("CONCAT(COALESCE(u.last_name,''), ' ', COALESCE(u.first_name,'')) as name"),
                't.specialty as role',
            ])
            ->get();
        $reservations = DB::table('reservations as r')
            ->join('students as s', 's.id', '=', 'r.student_id')
            ->join('users as su', 'su.id', '=', 's.user_id')
            ->join('materials as m', 'm.material_id', '=', 'r.material_id')
            ->whereDate('r.start_at', $todayStr)
            ->whereNull('r.cancelled_at')
            ->select([
                'r.id',
                'r.teacher_id',
                'r.start_at',
                'r.end_at',
                DB::raw("CONCAT(COALESCE(su.last_name,''), ' ', COALESCE(su.first_name,'')) as student_name"),
                'm.name as material_name'
            ])
            ->get();
        // 先生モード用タイムライン
        $teacherTimeline = $teachers->map(function ($teacher) use ($reservations) {
            $teacherReservations = $reservations->where('teacher_id', $teacher->id)->map(function ($res) {
                $start = Carbon::parse($res->start_at);
                $end = Carbon::parse($res->end_at);
                return [
                    'id' => $res->id,
                    'title' => $res->student_name . ' (' . $res->material_name . ')',
                    'start_time' => $start->format('H:i'),
                    'end_time' => $end->format('H:i'),
                    'start_minutes' => $start->hour * 60 + $start->minute,
                    'duration_minutes' => $end->diffInMinutes($start),
                    'type' => 'booked'
                ];
            })->values();
            return [
                'id' => 'teacher_' . $teacher->id,
                'name' => $teacher->name ?: 'Teacher #' . $teacher->id,
                'role' => $teacher->role ?: 'Instructor',
                'blocks' => $teacherReservations
            ];
        });
        // 3. 場所（教室）モード用タイムライン
        $rooms = collect([
            ['id' => 'room_1', 'name' => 'Station A (Room 101)', 'role' => 'Main Building'],
            ['id' => 'room_2', 'name' => 'Station B (Room 102)', 'role' => 'Main Building'],
            ['id' => 'room_3', 'name' => 'Station C (Room 103)', 'role' => 'Annex'],
            ['id' => 'room_4', 'name' => 'Online Booth 1', 'role' => 'Remote'],
            ['id' => 'room_5', 'name' => 'Online Booth 2', 'role' => 'Remote'],
        ]);
        $roomCount = $rooms->count();
        $locationTimeline = $rooms->map(function ($room, $index) use ($reservations, $roomCount) {
            $assignedReservations = $reservations->filter(function ($res, $key) use ($index, $roomCount) {
                return $roomCount > 0 && ($key % $roomCount) === $index;
            })->map(function ($res) {
                $start = Carbon::parse($res->start_at);
                $end = Carbon::parse($res->end_at);
                return [
                    'id' => $res->id,
                    'title' => $res->student_name . ' - Lesson',
                    'start_time' => $start->format('H:i'),
                    'end_time' => $end->format('H:i'),
                    'start_minutes' => $start->hour * 60 + $start->minute,
                    'duration_minutes' => $end->diffInMinutes($start),
                    'type' => 'room_used'
                ];
            })->values();
            return [
                'id' => $room['id'],
                'name' => $room['name'],
                'role' => $room['role'],
                'blocks' => $assignedReservations
            ];
        });
        $now = Carbon::now();
        return view('admin.dashboard', [
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

    /**
     * Schedule Management: 週間予定表画面 (admin/schedules/index.blade.php)
     */ 
    public function schedulesIndex(Request $request)
    {
        $validated = $request->validate([
            'week_start' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $start = !empty($validated['week_start'])
            ? Carbon::createFromFormat('Y-m-d', $validated['week_start'])->startOfWeek(Carbon::MONDAY)->startOfDay()
            : now()->startOfWeek(Carbon::MONDAY)->startOfDay();

        $end = (clone $start)->addDays(6)->endOfDay();

        $jpDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        $days = collect(CarbonPeriod::create($start, '1 day', $end))
            ->map(fn ($d) => [
                'date'   => $d->toDateString(),
                'day_ja' => $jpDays[$d->dayOfWeek], // 0=日
                'label'  => $d->format('n/j'),
            ]);

        $capacityByDate = DB::table('teacher_schedules')
            ->selectRaw('DATE(start_time) as d, COUNT(*) as c')
            ->whereBetween('start_time', [$start, $end])
            ->groupBy('d')
            ->pluck('c', 'd');

        $bookedByDate = DB::table('reservations')
            ->selectRaw('DATE(start_at) as d, COUNT(*) as c')
            ->whereBetween('start_at', [$start, $end])
            ->groupBy('d')
            ->pluck('c', 'd');

        // 自動予約フラグ列が reservations に無いため暫定 0 扱い
        $autoBookedByDate = collect();

        $workingTeachersByDate = DB::table('teacher_schedules')
            ->selectRaw('DATE(start_time) as d, COUNT(DISTINCT teacher_id) as c')
            ->whereBetween('start_time', [$start, $end])
            ->groupBy('d')
            ->pluck('c', 'd');

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

        $announcements = collect();
        if (Schema::hasTable('announcements')) {
            $announcements = DB::table('announcements')
                ->select(['title', 'body', 'published_at'])
                ->orderByDesc('published_at')
                ->limit(5)
                ->get();
        }

        return view('admin.schedules/index', [
            'weekStart'     => $start->toDateString(),
            'weekNo'        => $start->isoWeek(),   // 例: 36
            'weekYear'      => $start->isoWeekYear(), // 年またぎ対策
            'dashboardRows' => $rows,
            'announcements' => $announcements,
        ]);
    }

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

        if ($type === 'capacity') {
    $items = DB::table('teacher_schedules as ts')
        ->leftJoin('teachers as t', 't.id', '=', 'ts.teacher_id')
        ->leftJoin('users as u', 'u.id', '=', 't.user_id')
        ->whereDate('ts.start_time', $date)
        ->select([
            'ts.start_time',
            'ts.end_time',
            DB::raw("CONCAT(COALESCE(u.last_name,''), ' ', COALESCE(u.first_name,'')) as teacher_name"),
        ])
        ->orderBy('ts.start_time')
        ->get();
} elseif ($type === 'booked') {
    $items = DB::table('reservations as r')
        ->leftJoin('teachers as t', 't.id', '=', 'r.teacher_id')
        ->leftJoin('users as u', 'u.id', '=', 't.user_id')
        ->whereDate('r.start_at', $date)
        ->select([
            'r.id',
            'r.student_id',
            'r.start_at',
            DB::raw("CONCAT(COALESCE(u.last_name,''), ' ', COALESCE(u.first_name,'')) as teacher_name"),
        ])
        ->orderBy('r.start_at')
        ->get();
} elseif ($type === 'working_teachers') {
    $items = DB::table('teacher_schedules as ts')
        ->leftJoin('teachers as t', 't.id', '=', 'ts.teacher_id')
        ->leftJoin('users as u', 'u.id', '=', 't.user_id')
        ->whereDate('ts.start_time', $date)
        ->select([
            DB::raw("CONCAT(COALESCE(u.last_name,''), ' ', COALESCE(u.first_name,'')) as teacher_name"),
        ])
        ->distinct()
        ->orderBy('teacher_name')
        ->get();
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
}
