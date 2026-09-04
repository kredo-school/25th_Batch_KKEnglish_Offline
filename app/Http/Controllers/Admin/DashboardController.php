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

        return view('admin.dashboard', [
            'weekStart'     => $start->toDateString(),
            'weekNo'        => $start->isoWeek(),   // 例: 36
            'weekYear'      => $start->isoWeekYear(), // 年またぎ対策
            'dashboardRows' => $rows,
            'announcements' => $announcements,
        ]);
    }
}
