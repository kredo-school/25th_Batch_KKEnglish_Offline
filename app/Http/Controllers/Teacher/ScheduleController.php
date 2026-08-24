<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\ScheduleStoreRequest;
use App\Models\Schedule;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
// use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function create(): View
    {
        return view('teachers.schedules.create');
    }

    public function store(ScheduleStoreRequest $request): RedirectResponse
    {
        $user = auth()->user();

        // 確認が必要: User と Teacher の紐づけ方法
        // 例: users.teacher_id を持つ場合
        $teacherId = $user->teacher_id;

        Schedule::create([
            'teacher_id' => $teacherId,
            'start_at'   => $request->input('start_at'),
            'end_at'     => $request->input('end_at'),
        ]);

        return redirect()
            ->route('teacher.schedules.index')
            ->with('success', '空き時間を登録しました。');
    }
}
