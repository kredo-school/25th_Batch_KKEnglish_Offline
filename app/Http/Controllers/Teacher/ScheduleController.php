<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\ScheduleStoreRequest;
use App\Http\Requests\Teacher\ScheduleUpdateRequest;
use App\Models\Teacher;
use App\Models\TeacherSchedule;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        [$teacherId, $teachers] = $this->resolveTeacherContext($request);

        [$startOfWeek, $days, $viewStart, $viewEnd, $times] = $this->buildGridMeta($request);

        [$existingMap, $cellScheduleIdMap] = $this->buildWeeklyMaps($teacherId, $startOfWeek);

        return view('teachers.schedules.index', compact(
            'teacherId',
            'teachers',
            'startOfWeek',
            'days',
            'viewStart',
            'viewEnd',
            'times',
            'existingMap',
            'cellScheduleIdMap'
        ));
    }

    public function create(Request $request): View
    {
        [$teacherId, $teachers] = $this->resolveTeacherContext($request);

        [$startOfWeek, $days, $viewStart, $viewEnd, $times] = $this->buildGridMeta($request);

        [$existingMap, $cellScheduleIdMap] = $this->buildWeeklyMaps($teacherId, $startOfWeek);

        return view('teachers.schedules.create', compact(
            'teacherId',
            'teachers',
            'startOfWeek',
            'days',
            'viewStart',
            'viewEnd',
            'times',
            'existingMap',
            'cellScheduleIdMap'
        ));
    }

    public function storeGrid(Request $request): RedirectResponse
    {
        [$teacherId] = $this->resolveTeacherContext($request);

if ($teacherId <= 0) {
    return back()->with('error', '講師IDが不正です。teacherを選択してください。');
}
        $cells = $request->input('cells', []);

        if (empty($cells)) {
            return back()->with('error', '選択がありません。');
        }

        $byDate = [];
        foreach ($cells as $c) {
            $date = $c['date'] ?? null;
            $time = $c['time'] ?? null;

            if (!$date || !$time) {
                continue;
            }

            if ($date < now()->toDateString()) {
                return back()->with('error', "{$date} は過去日です。");
            }

            $byDate[$date][] = $time;
        }

        foreach ($byDate as $date => $times) {
            $times = array_values(array_unique($times));
            sort($times); // 'H:i'

            // 連続セルを区間へ圧縮
            $ranges = [];
            $start = $times[0] ?? null;
            $prev = $start;

            for ($i = 1; $i < count($times); $i++) {
                $curr = $times[$i];
                $prevPlus30 = Carbon::createFromFormat('H:i', $prev)->addMinutes(30)->format('H:i');

                if ($curr !== $prevPlus30) {
                    $ranges[] = [
                        $start,
                        Carbon::createFromFormat('H:i', $prev)->addMinutes(30)->format('H:i')
                    ];
                    $start = $curr;
                }

                $prev = $curr;
            }

            if ($start !== null) {
                $ranges[] = [
                    $start,
                    Carbon::createFromFormat('H:i', $prev)->addMinutes(30)->format('H:i')
                ];
            }

            foreach ($ranges as [$s, $e]) {
                // 重複禁止（既存予約と重なるものはスキップ）
                $overlapExists = TeacherSchedule::query()
                    ->where('teacher_id', $teacherId)
                    ->where('available_date', $date)
                    ->where('start_time', '<', $e . ':00')
                    ->where('end_time', '>', $s . ':00')
                    ->exists();

                if ($overlapExists) {
                    continue;
                }

                TeacherSchedule::create([
                    'teacher_id' => $teacherId,
                    'available_date' => $date,
                    'start_time' => $s . ':00',
                    'end_time' => $e . ':00',
                    'status' => 'available',
                ]);
            }
        }

        return redirect()
            ->route('teacher.schedules.index', [
                'week_start' => $request->input('week_start'),
                'teacher_id' => $request->input('teacher_id'),
                'view_start' => $request->input('view_start'),
                'view_end' => $request->input('view_end'),
            ])
            ->with('success', '選択した空き時間を登録しました。');
    }

    public function store(ScheduleStoreRequest $request): RedirectResponse
    {
        [$teacherId] = $this->resolveTeacherContext($request);

        TeacherSchedule::create([
            'teacher_id'     => $teacherId,
            'available_date' => $request->input('available_date'),
            'start_time'     => $request->input('start_time'),
            'end_time'       => $request->input('end_time'),
            'status'         => 'available',
        ]);

        return redirect()->route('teacher.schedules.index')
            ->with('success', '空き時間を登録しました。');
    }

public function edit(Request $request, TeacherSchedule $schedule): View|RedirectResponse
{
    $user = auth()->user();

    // teacherは自分のデータのみ
    if ($user->role === 'teacher' && (int)$schedule->teacher_id !== (int)$user->teacher_id) {
        abort(403);
    }

    // bookedは編集不可（adminも不可にする運用）
    if ($schedule->status === 'booked') {
        return redirect()
            ->route('teacher.schedules.index', [
                'week_start' => $request->input('week_start'),
                'teacher_id' => $request->input('teacher_id'),
                'view_start' => $request->input('view_start'),
                'view_end'   => $request->input('view_end'),
            ])
            ->with('error', '予約済みのスケジュールは編集できません。');
    }

    // 戻り導線を保持
    $backParams = [
        'week_start' => $request->input('week_start'),
        'teacher_id' => $request->input('teacher_id'),
        'view_start' => $request->input('view_start'),
        'view_end'   => $request->input('view_end'),
    ];

    return view('teachers.schedules.edit', compact('schedule', 'backParams'));
}

public function update(ScheduleUpdateRequest $request, TeacherSchedule $schedule): RedirectResponse
{
    $user = auth()->user();

    if ($user->role === 'teacher' && (int)$schedule->teacher_id !== (int)$user->teacher_id) {
        abort(403);
    }

    if ($schedule->status === 'booked') {
        return redirect()
            ->route('teacher.schedules.index', [
                'week_start' => $request->input('week_start'),
                'teacher_id' => $request->input('teacher_id'),
                'view_start' => $request->input('view_start'),
                'view_end'   => $request->input('view_end'),
            ])
            ->with('error', '予約済みのスケジュールは更新できません。');
    }

    $schedule->update([
        'available_date' => $request->input('available_date'),
        'start_time'     => $request->input('start_time'),
        'end_time'       => $request->input('end_time'),
        // adminのみstatus変更可
        'status'         => $user->role === 'admin'
            ? $request->input('status', $schedule->status)
            : $schedule->status,
    ]);

    return redirect()
        ->route('teacher.schedules.index', [
            'week_start' => $request->input('week_start'),
            'teacher_id' => $request->input('teacher_id'),
            'view_start' => $request->input('view_start'),
            'view_end'   => $request->input('view_end'),
        ])
        ->with('success', 'スケジュールを更新しました。');
}

    public function destroy(Request $request, TeacherSchedule $schedule): RedirectResponse
{
    $user = auth()->user();

    if ($user->role === 'teacher' && (int) $schedule->teacher_id !== (int) $user->teacher_id) {
        abort(403);
    }

    if ($schedule->status === 'booked') {
        return redirect()->route('teacher.schedules.index', [
            'week_start' => $request->input('week_start'),
            'teacher_id' => $request->input('teacher_id'),
            'view_start' => $request->input('view_start'),
            'view_end'   => $request->input('view_end'),
        ])->with('error', '予約済みのスケジュールは削除できません。');
    }

    $schedule->delete();

    return redirect()->route('teacher.schedules.index', [
        'week_start' => $request->input('week_start'),
        'teacher_id' => $request->input('teacher_id'),
        'view_start' => $request->input('view_start'),
        'view_end'   => $request->input('view_end'),
    ])->with('success', 'スケジュールを削除しました。');
}

    /**
     * teacher_id と teachers一覧を解決
     * - teacher: 自分固定
     * - admin: request teacher_id を使用（未指定なら先頭teacher）
     */
    private function resolveTeacherContext(Request $request): array
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $teachers = Teacher::query()
                ->orderBy('name')
                ->get(['teacher_id', 'name']);

            $teacherId = (int) $request->input('teacher_id');

            if ($teacherId <= 0 && $teachers->isNotEmpty()) {
                $teacherId = (int) $teachers->first()->teacher_id;
            }

            return [$teacherId, $teachers];
        }

        $teacherId = (int) $user->teacher_id; // 確認が必要
        return [$teacherId, collect()];
    }

    /**
     * 週・表示レンジ・時間軸配列を生成
     */
    private function buildGridMeta(Request $request): array
    {
        $startOfWeek = $request->input('week_start')
            ? Carbon::parse($request->input('week_start'))->startOfDay()
            : now()->startOfWeek(Carbon::MONDAY);

        $days = collect(range(0, 6))->map(fn ($i) => $startOfWeek->copy()->addDays($i));

        $viewStart = $request->input('view_start', '06:00');
        $viewEnd = $request->input('view_end', '21:30');

        if (!preg_match('/^\d{2}:\d{2}$/', $viewStart)) {
            $viewStart = '06:00';
        }
        if (!preg_match('/^\d{2}:\d{2}$/', $viewEnd)) {
            $viewEnd = '21:30';
        }

        $start = Carbon::createFromFormat('H:i', $viewStart);
        $end = Carbon::createFromFormat('H:i', $viewEnd);

        if ($start->gte($end)) {
            $viewStart = '06:00';
            $viewEnd = '21:30';
            $start = Carbon::createFromTimeString($viewStart);
            $end = Carbon::createFromTimeString($viewEnd);
        }

        $times = [];
        for ($t = $start->copy(); $t->lte($end); $t->addMinutes(30)) {
            $times[] = $t->format('H:i');
        }

        return [$startOfWeek, $days, $viewStart, $viewEnd, $times];
    }

    /**
     * 1週間分を30分セルへ展開
     */
    private function buildWeeklyMaps(int $teacherId, Carbon $startOfWeek): array
    {
        $endOfWeek = $startOfWeek->copy()->addDays(6);

        $rows = TeacherSchedule::query()
            ->where('teacher_id', $teacherId)
            ->whereBetween('available_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->orderBy('available_date')
            ->orderBy('start_time')
            ->get(['schedule_id', 'available_date', 'start_time', 'end_time', 'status']);

        $existingMap = [];
        $cellScheduleIdMap = [];

        foreach ($rows as $r) {
            $date = Carbon::parse($r->available_date)->toDateString();
            $start = Carbon::parse($r->start_time);
            $end = Carbon::parse($r->end_time);

            for ($t = $start->copy(); $t->lt($end); $t->addMinutes(30)) {
                $hhmm = $t->format('H:i');
                $existingMap[$date][$hhmm] = $r->status;
                $cellScheduleIdMap[$date][$hhmm] = $r->schedule_id;
            }
        }

        return [$existingMap, $cellScheduleIdMap];
    }
}
