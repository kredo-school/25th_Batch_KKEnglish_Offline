<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShiftPattern;
use App\Models\Teacher;
use App\Models\TeacherShiftPatternAssignment;
use App\Services\Admin\TeacherScheduleGenerationService; // スケジュール生成サービス
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShiftPatternAssignmentController extends Controller
{
    // スケジュール生成サービスを注入
    public function __construct(
        private readonly TeacherScheduleGenerationService $scheduleGenerator
    ) {}

    public function index(Request $request): View
    {
        // 割り当てが存在する先生を1人1行で取得（全先生を表示対象とする場合は whereHas を外してください）
        $teachers = Teacher::query()
            ->whereHas('shiftPatternAssignments')
            ->with([
                'user:id,first_name,last_name',
                'shiftPatternAssignments.shiftPattern'
            ])
            ->orderBy('id', 'desc')
            ->paginate(15);
        return view('admin.shift-pattern-assignments.index', [
            'teachers' => $teachers,
        ]);
    }

    public function create(Request $request): View
    {
        $patterns = ShiftPattern::query()
            ->orderBy('id', 'desc')
            ->get(['id', 'pattern_name', 'pattern_code']);

        $teachers = Teacher::query()
            ->with(['user:id,first_name,last_name'])
            ->orderBy('id', 'desc')
            ->get(['id', 'user_id']);

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
                'weekdays'            => ['required', 'array', 'min:1'],
                'weekdays.*'          => ['integer', 'in:0,1,2,3,4,5,6'],
                'start_date'          => ['required', 'date'],
                'end_date'            => ['nullable', 'date', 'after_or_equal:start_date'],
                'priority'            => ['nullable', 'integer', 'min:0'],
                'replace_overlapping' => ['nullable', 'boolean'],
            ]);

            $teacherIds = collect($data['teacher_ids'])->map(fn($v) => (int)$v)->unique()->values();
            $weekdays   = collect($data['weekdays'])->map(fn($v) => (int)$v)->unique()->values();
            $endDate    = !empty($data['end_date']) ? $data['end_date'] : null;
            $now        = now();
            $replaceOverlapping = (bool)($data['replace_overlapping'] ?? false);

            try {
                // 1) assignment保存だけをトランザクションで確定
                DB::transaction(function () use ($data, $teacherIds, $weekdays, $endDate, $now, $replaceOverlapping) {
                    $newStart = $data['start_date'];
                    $newEnd   = $endDate ?? '9999-12-31';

                    $overlapQuery = DB::table('teacher_shift_pattern_assignments')
                        ->whereIn('teacher_id', $teacherIds->all())
                        ->whereIn('weekday', $weekdays->all())
                        ->where(function ($q) use ($newStart, $newEnd) {
                            $q->where('start_date', '<=', $newEnd)
                            ->where(function ($qq) use ($newStart) {
                                $qq->whereNull('end_date')
                                    ->orWhere('end_date', '>=', $newStart);
                            });
                        });

                    if ($overlapQuery->exists()) {
                        if ($replaceOverlapping) {
                            $overlapQuery->delete();
                        } else {
                            throw new \InvalidArgumentException('重複するシフトパターン割り当てが存在します。');
                        }
                    }

                    // $rows = [];
                    foreach ($teacherIds as $teacherId) {
                        foreach ($weekdays as $weekday) {
                            $insertedIds[] = DB::table('teacher_shift_pattern_assignments')->insertGetId([
                                'shift_pattern_id' => (int)$data['shift_pattern_id'],
                                'teacher_id'       => (int)$teacherId,
                                'weekday'          => (int)$weekday,
                                'start_date'       => $data['start_date'],
                                'end_date'         => $endDate,
                                'priority'         => (int)($data['priority'] ?? 0),
                                'created_at'       => $now,
                                'updated_at'       => $now,
                            ]);
                        }
                    }

                    // if (!empty($rows)) {
                    //     DB::table('teacher_shift_pattern_assignments')->insert($rows);
                    // }
                });

                // 2) スケジュール生成は別 try（失敗しても assignment は残す）
                $generatedSlotsCount = 0;
                try {
                    $assignments = TeacherShiftPatternAssignment::with('shiftPattern.breaks')
                ->whereIn('id', $insertedIds)
                ->get();

            foreach ($assignments as $assignment) {
                $result = $this->scheduleGenerator->generateFromAssignment($assignment, (int)auth()->id());
                $generatedSlotsCount += $result['generated'];
                    }
                } catch (\Throwable $e) {
                    Log::error('Schedule generation failed: ' . $e->getMessage());
                }

                return redirect()
                    ->route('admin.shift-pattern-assignments.index', ['menu' => 'schedule'])
                    ->with('status', "シフト割当の登録完了。同時に {$generatedSlotsCount} 件のレッスン枠をスケジュールに反映しました。");

            } catch (\Exception $e) {
                Log::error('ShiftPatternAssignment Store Error: ' . $e->getMessage());

                return back()
                    ->withInput()
                    ->withErrors(['error' => '保存中にエラーが発生しました: ' . $e->getMessage()]);
            }
        }

    public function destroy(TeacherShiftPatternAssignment $assignment): RedirectResponse
    {
        $assignment->delete();

        return redirect()
            ->route('admin.shift-pattern-assignments.index', ['menu' => 'schedule'])
            ->with('status', 'Teacher assignmentを削除しました。');
    }

    public function destroyByTeacher(Teacher $teacher): RedirectResponse
{
    // 必要なら authorize を追加
    // $this->authorize('delete', TeacherShiftPatternAssignment::class);

    $deleted = TeacherShiftPatternAssignment::where('teacher_id', $teacher->id)->delete();

    return redirect()
        ->route('admin.shift-pattern-assignments.index', ['menu' => 'schedule'])
        ->with('status', "Teacher #{$teacher->id} の割り当てを {$deleted} 件削除しました。");
}
}
