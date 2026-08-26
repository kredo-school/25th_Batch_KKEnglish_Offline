<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\ScheduleStoreRequest;
use App\Http\Requests\Teacher\ScheduleUpdateRequest;
use App\Models\TeacherSchedule;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
// use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $query = TeacherSchedule::query()
            ->orderBy('available_date')
            ->orderBy('start_time');

        // teacherは自分の分のみ、adminは全件
        if ($user->role === 'teacher') {
            $query->where('teacher_id', $user->teacher_id); // 確認が必要
        }

        $schedules = $query->paginate(20);

        return view('teachers.schedules.index', compact('schedules'));
    }

    public function create(): \Illuminate\Contracts\View\View
    {
        $user = auth()->user();
        $teacherId = $user->teacher_id; // 確認が必要

        $startOfWeek = request('week_start')
            ? \Carbon\Carbon::parse(request('week_start'))->startOfDay()
            : now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $endOfWeek = $startOfWeek->copy()->addDays(6);

        $rows = \App\Models\TeacherSchedule::query()
            ->where('teacher_id', $teacherId)
            ->whereBetween('available_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->get(['available_date','start_time','end_time','status']);

        $existingMap = [];
        foreach ($rows as $r) {
            $date = \Carbon\Carbon::parse($r->available_date)->toDateString();
            $start = \Carbon\Carbon::parse($r->start_time);
            $end = \Carbon\Carbon::parse($r->end_time);
            for ($t = $start->copy(); $t->lt($end); $t->addMinutes(30)) {
                $existingMap[$date][$t->format('H:i')] = $r->status;
            }
        }

        return view('teachers.schedules.create', compact('existingMap'));
    }

    public function storeGrid(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
        $teacherId = $user->teacher_id; // 確認が必要
        $cells = $request->input('cells', []);

        if (empty($cells)) return back()->with('error', '選択がありません。');

        // 日付ごとに時刻配列
        $byDate = [];
        foreach ($cells as $c) {
            $date = $c['date'] ?? null;
            $time = $c['time'] ?? null;
            if (!$date || !$time) continue;
            if ($date < now()->toDateString()) return back()->with('error', "{$date} は過去日です。");
            $byDate[$date][] = $time;
        }

        foreach ($byDate as $date => $times) {
            $times = array_values(array_unique($times));
            sort($times); // 'H:i' 文字列で時系列に並ぶ

            // 連続区間に圧縮
            $ranges = [];
            $start = $times[0] ?? null;
            $prev = $start;

            for ($i = 1; $i < count($times); $i++) {
                $curr = $times[$i];
                $prevPlus30 = \Carbon\Carbon::createFromFormat('H:i', $prev)->addMinutes(30)->format('H:i');

                if ($curr !== $prevPlus30) {
                    $ranges[] = [$start, \Carbon\Carbon::createFromFormat('H:i', $prev)->addMinutes(30)->format('H:i')];
                    $start = $curr;
                }
                $prev = $curr;
            }

            if ($start !== null) {
                $ranges[] = [$start, \Carbon\Carbon::createFromFormat('H:i', $prev)->addMinutes(30)->format('H:i')];
            }

            foreach ($ranges as [$s, $e]) {
                \App\Models\TeacherSchedule::firstOrCreate(
                    [
                        'teacher_id' => $teacherId,
                        'available_date' => $date,
                        'start_time' => $s . ':00',
                        'end_time' => $e . ':00',
                    ],
                    ['status' => 'available']
                );
            }
        }

        return redirect()->route('teacher.schedules.index')->with('success', '連続選択した空き時間を登録しました。');
}

    public function store(ScheduleStoreRequest $request): RedirectResponse
    {
        $user = auth()->user();

        // admin作成時のteacher_id指定は要件次第
        $teacherId = $user->role === 'admin'
            ? $request->input('teacher_id')
            : $user->teacher_id;

        TeacherSchedule::create([
            'teacher_id'     => $teacherId,
            'available_date' => $request->input('available_date'),
            'start_time'     => $request->input('start_time'),
            'end_time'       => $request->input('end_time'),
            'status'         => 'available',
        ]);

        return redirect()
            ->route('teacher.schedules.index')
            ->with('success', '空き時間を登録しました。');
    }

    public function edit(TeacherSchedule $schedule): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user->role === 'teacher') {
            if ((int)$schedule->teacher_id !== (int)$user->teacher_id) {
                abort(403);
            }

            if ($schedule->status === 'booked') {
                return redirect()->route('teacher.schedules.index')
                    ->with('error', '予約済みのスケジュールは編集できません。');
            }
        }

        return view('teachers.schedules.edit', compact('schedule'));
    }

    public function update(ScheduleUpdateRequest $request, TeacherSchedule $schedule): RedirectResponse
    {
        $user = auth()->user();

        if ($user->role === 'teacher') {
            if ((int)$schedule->teacher_id !== (int)$user->teacher_id) {
                abort(403);
            }

            if ($schedule->status === 'booked') {
                return redirect()->route('teacher.schedules.index')
                    ->with('error', '予約済みのスケジュールは更新できません。');
            }
        }

        $schedule->update([
            'available_date' => $request->input('available_date'),
            'start_time'     => $request->input('start_time'),
            'end_time'       => $request->input('end_time'),
            'status'         => $request->input('status', $schedule->status),
        ]);

        return redirect()->route('teacher.schedules.index')
            ->with('success', 'スケジュールを更新しました。');
    }

    public function destroy(TeacherSchedule $schedule): RedirectResponse
    {
        $user = auth()->user();

        if ($user->role === 'teacher') {
            if ((int)$schedule->teacher_id !== (int)$user->teacher_id) {
                abort(403);
            }

            if ($schedule->status === 'booked') {
                return redirect()->route('teacher.schedules.index')
                    ->with('error', '予約済みのスケジュールは削除できません。');
            }
        }

        $schedule->delete();

        return redirect()->route('teacher.schedules.index')
            ->with('success', 'スケジュールを削除しました。');
    }
}
