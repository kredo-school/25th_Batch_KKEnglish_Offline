<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignShiftPatternRequest;
use App\Models\ShiftPattern;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Services\Admin\ShiftPatternAssignmentService;
use App\Services\Admin\GenerateTeacherSchedulesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShiftPatternAssignmentController extends Controller
{

    public function create(Request $request): View
{
    $patterns = ShiftPattern::query()
        ->orderBy('id', 'desc')
        ->get(['id']); // name を外す

    $teachers = Teacher::query()
        ->with(['user:id,first_name,last_name']) // users から表示名取得
        ->orderBy('id', 'desc')
        ->get(['id', 'user_id']); // ここも name が無いなら同様に id のみに

    return view('admin.shift-pattern-assignments.create', [
        'patterns' => $patterns,
        'teachers' => $teachers,
        'defaultPatternId' => $request->integer('pattern_id') ?: null,
    ]);
}

    public function store(Request $request): RedirectResponse
{
    $data = $request->validate([
        'shift_pattern_id' => ['required', 'integer', 'exists:shift_patterns,id'],
        'teacher_ids'      => ['required', 'array', 'min:1'],
        'teacher_ids.*'    => ['integer', 'exists:teachers,id'],
        'start_date'       => ['required', 'date'],
        'end_date'         => ['nullable', 'date', 'after_or_equal:start_date'],
    ]);

    $now = now();
    $rows = collect($data['teacher_ids'])
        ->unique()
        ->map(fn ($teacherId) => [
            'shift_pattern_id' => $data['shift_pattern_id'],
            'teacher_id'       => $teacherId,
            'start_date'       => $data['start_date'],
            'end_date'         => $data['end_date'],
            'created_at'       => $now,
            'updated_at'       => $now,
        ])->values()->all();

    \DB::table('teacher_shift_pattern_assignments')->insert($rows);

    return redirect()
        ->route('shift-pattern-assignments.create', ['menu' => 'schedule'])
        ->with('status', count($rows).'件のTeacher assignmentを作成しました。');
}
}
