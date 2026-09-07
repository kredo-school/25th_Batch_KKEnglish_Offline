<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShiftPattern;
use App\Models\Teacher;
use App\Models\TeacherShiftPatternAssignment;
use App\Services\Admin\GenerateTeacherSchedulesService; // スケジュール生成サービス
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShiftPatternAssignmentController extends Controller
{
    // スケジュール生成サービスを注入
    public function __construct(
        private readonly GenerateTeacherSchedulesService $scheduleGenerator
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
            'replace_overlapping' => ['nullable'],
        ]);

        $teacherIds = collect($data['teacher_ids'])->map(fn($v) => (int)$v)->unique()->values();
        $weekdays   = collect($data['weekdays'])->map(fn($v) => (int)$v)->unique()->values();
        $endDate    = !empty($data['end_date']) ? $data['end_date'] : null;
        $now        = now();

        try {
            // トランザクション処理の戻り値として生成件数を取得
            $generatedSlotsCount = DB::transaction(function () use ($data, $teacherIds, $weekdays, $endDate, $now) {
                $newStart = $data['start_date'];
                $newEnd   = $endDate ?? '9999-12-31';

                // 重複判定＆上書き削除
                $overlapQuery = DB::table('teacher_shift_pattern_assignments')
                    ->whereIn('teacher_id', $teacherIds)
                    ->whereIn('weekday', $weekdays)
                    ->where(function ($q) use ($newStart, $newEnd) {
                        $q->where('start_date', '<=', $newEnd)
                          ->where(function ($qq) use ($newStart) {
                              $qq->whereNull('end_date')
                                 ->orWhere('end_date', '>=', $newStart);
                          });
                    });

                if ($overlapQuery->exists()) {
                    if (!empty($data['replace_overlapping'])) {
                        $overlapQuery->delete();
                    } else {
                        throw new \InvalidArgumentException('重複するシフトパターン割り当てが存在します。');
                    }
                }

                // 1. teacher_shift_pattern_assignments（割り当て情報）の保存
                $rows = [];
                foreach ($teacherIds as $teacherId) {
                    foreach ($weekdays as $weekday) {
                        $rows[] = [
                            'shift_pattern_id' => (int)$data['shift_pattern_id'],
                            'teacher_id'       => (int)$teacherId,
                            'weekday'          => (int)$weekday,
                            'start_date'       => $data['start_date'],
                            'end_date'         => $endDate,
                            'priority'         => (int)($data['priority'] ?? 0),
                            'created_at'       => $now,
                            'updated_at'       => $now,
                        ];
                    }
                }

                if (!empty($rows)) {
                    DB::table('teacher_shift_pattern_assignments')->insert($rows);
                }

                // 2. teacher_schedules（実スケジュール枠）への生成・反映
                $count = 0;
                $pattern = ShiftPattern::findOrFail($data['shift_pattern_id']);
                foreach ($teacherIds as $teacherId) {
                    $count += $this->scheduleGenerator->generate(
                        teacherId: (int)$teacherId,
                        pattern: $pattern,
                        effectiveFrom: $data['start_date'],
                        effectiveTo: $endDate,
                        createdBy: (int)auth()->id()
                    );
                }

                return $count;
            });

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
}
