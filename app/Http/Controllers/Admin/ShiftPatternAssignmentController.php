<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShiftPattern;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

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
            'shift_pattern_id'    => ['required', 'integer', 'exists:shift_patterns,id'],
            'teacher_ids'         => ['required', 'array', 'min:1'],
            'teacher_ids.*'       => ['integer', 'exists:teachers,id'],
            'start_date'          => ['required', 'date'],
            'end_date'            => ['nullable', 'date', 'after_or_equal:start_date'],
            'priority'            => ['nullable', 'integer', 'min:0'],
            'replace_overlapping' => ['nullable', 'boolean'],
        ]);

        $teacherIds = collect($data['teacher_ids'])->unique()->values();
        $now = now();

        DB::transaction(function () use ($data, $teacherIds, $now) {
            // チェック時: 期間が重なる既存割当を削除
            if (!empty($data['replace_overlapping'])) {
                $newStart = $data['start_date'];
                $newEnd   = $data['end_date'] ?? '9999-12-31';

                DB::table('teacher_shift_pattern_assignments')
                    ->whereIn('teacher_id', $teacherIds)
                    ->where(function ($q) use ($newStart, $newEnd) {
                        $q->where('start_date', '<=', $newEnd)
                        ->where(function ($qq) use ($newStart) {
                            $qq->whereNull('end_date')
                                ->orWhere('end_date', '>=', $newStart);
                        });
                    })
                    ->delete();
            }

            $rows = $teacherIds->map(fn ($teacherId) => [
                'shift_pattern_id' => $data['shift_pattern_id'],
                'teacher_id'       => $teacherId,
                'start_date'       => $data['start_date'],
                'end_date'         => $data['end_date'] ?? null,
                'priority'         => (int)($data['priority'] ?? 0),
                'created_at'       => $now,
                'updated_at'       => $now,
            ])->all();

            DB::table('teacher_shift_pattern_assignments')->insert($rows);
        });

        return redirect()
            ->route('admin.shift-pattern-assignments.create', ['menu' => 'schedule'])
            ->with('status', $teacherIds->count().'件のTeacher assignmentを作成しました。');
    }
}
