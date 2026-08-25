<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\ScheduleStoreRequest;
use App\Models\TeacherSchedule;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
// use Illuminate\Http\Request;

class ScheduleController extends Controller
{
        /**
     * 登録済み空き時間一覧（ログイン中Teacher）
     */
    public function index(): View
    {
        $teacherId = auth()->user()->teacher_id; // 確認が必要:

        $schedules = TeacherSchedule::query()
            ->where('teacher_id', $teacherId)
            ->orderBy('available_date')
            ->orderBy('start_time')
            ->paginate(20);

        return view('teachers.schedules.index', compact('schedules'));
    }
    /**
     * 空き時間登録画面
     */
    public function create(): View
    {
        return view('teachers.schedules.create');
    }

    /**
     * 空き時間登録
     */
    public function store(ScheduleStoreRequest $request): RedirectResponse
    {
        $user = auth()->user();

        // 確認が必要: User と Teacher の紐づけ方法
        // 例: users.teacher_id を持つ場合
        $teacherId = $user->teacher_id;

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
}
