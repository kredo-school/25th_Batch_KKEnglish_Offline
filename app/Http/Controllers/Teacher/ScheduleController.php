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

    public function create(): View
    {
        return view('teachers.schedules.create');
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
