<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Station;
use App\Models\Teacher;
use App\Models\TeacherStationAssignment;
use App\Models\LessonStationOverride;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class TeacherStationAssignmentController extends Controller
{
    /**
     * Station Assignment 一覧
     */
    public function index()
    {
        $assignments = TeacherStationAssignment::with([
            'teacher.user',
            'station',
        ])
            ->orderBy('start_date')
            ->orderBy('teacher_id')
            ->get();

        return view(
            'admin.teacher-station-assignments.index',
            compact('assignments')
        );
    }

    /**
     * 新規登録画面
     */
    public function create()
    {
        $teachers = Teacher::with('user')
            ->orderBy('id')
            ->get();

        $stations = Station::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'admin.teacher-station-assignments.create',
            compact('teachers', 'stations')
        );
    }

    /**
     * 新規登録
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => [
                'required',
                'integer',
                'exists:teachers,id',
            ],
            'station_id' => [
                'required',
                'integer',
                'exists:stations,id',
            ],
            'start_date' => [
                'required',
                'date',
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
        ]);

        DB::transaction(function () use ($validated) {
            $this->applyNewAssignment(
                teacherId: (int) $validated['teacher_id'],
                stationId: (int) $validated['station_id'],
                startDate: Carbon::parse($validated['start_date']),
                endDate: isset($validated['end_date'])
                    ? Carbon::parse($validated['end_date'])
                    : null,
            );
        });

        return redirect()
            ->route('admin.teacher-station-assignments.index', [
                'menu' => 'station-assignment',
            ])
            ->with(
                'success',
                'Station assignment created successfully.'
            );
    }

    /**
     * 編集画面
     */
    public function edit(TeacherStationAssignment $teacherStationAssignment)
    {
        $teacherStationAssignment->load([
            'teacher.user',
            'station',
        ]);

        $teachers = Teacher::with('user')
            ->orderBy('id')
            ->get();

        $stations = Station::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'admin.teacher-station-assignments.edit',
            compact(
                'teacherStationAssignment',
                'teachers',
                'stations'
            )
        );
    }

    /**
     * 更新
     */
    public function update(
        Request $request,
        TeacherStationAssignment $teacherStationAssignment
    ) {
        $validated = $request->validate([
            'teacher_id' => [
                'required',
                'integer',
                'exists:teachers,id',
            ],
            'station_id' => [
                'required',
                'integer',
                'exists:stations,id',
            ],
            'start_date' => [
                'required',
                'date',
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $teacherStationAssignment
        ) {
            // 現在のレコードを一旦削除
            $teacherStationAssignment->delete();

            // 新しい期間を優先して登録
            $this->applyNewAssignment(
                teacherId: (int) $validated['teacher_id'],
                stationId: (int) $validated['station_id'],
                startDate: Carbon::parse($validated['start_date']),
                endDate: isset($validated['end_date'])
                    ? Carbon::parse($validated['end_date'])
                    : null,
            );
        });

        return redirect()
            ->route('admin.teacher-station-assignments.index', [
                'menu' => 'station-assignment',
            ])
            ->with(
                'success',
                'Station assignment updated successfully.'
            );
    }

    /**
     * レッスン単位のStation変更
     */
    public function override(
        Request $request,
        Reservation $reservation
    ) {
        $validated = $request->validate([
            'station_id' => [
                'required',
                'integer',
                'exists:stations,id',
            ],
        ]);

        LessonStationOverride::updateOrCreate(
            [
                'reservation_id' => $reservation->id,
            ],
            [
                'station_id' => $validated['station_id'],
            ]
        );

        return back()->with(
            'success',
            'Lesson station updated successfully.'
        );
    }

    /**
     * 新しい期間を優先して既存期間を調整する
     */
    private function applyNewAssignment(
        int $teacherId,
        int $stationId,
        Carbon $startDate,
        ?Carbon $endDate
    ): void {
        $existingAssignments = TeacherStationAssignment::where(
            'teacher_id',
            $teacherId
        )
            ->where(function ($query) use ($startDate, $endDate) {
                if ($endDate === null) {
                    // 新しい期間が終了日なしの場合
                    $query->where(function ($q) use ($startDate) {
                        $q->whereNull('end_date')
                            ->orWhereDate(
                                'end_date',
                                '>=',
                                $startDate->toDateString()
                            );
                    });
                } else {
                    // 通常の期間重複判定
                    $query->whereDate(
                        'start_date',
                        '<=',
                        $endDate->toDateString()
                    )
                        ->where(function ($q) use ($startDate) {
                            $q->whereNull('end_date')
                                ->orWhereDate(
                                    'end_date',
                                    '>=',
                                    $startDate->toDateString()
                                );
                        });
                }
            })
            ->get();

        foreach ($existingAssignments as $existing) {
            $oldStart = Carbon::parse($existing->start_date);
            $oldEnd = $existing->end_date
                ? Carbon::parse($existing->end_date)
                : null;

            /*
             * 新しい期間より前の部分を残す
             *
             * 例
             * 10/01 ～ 10/31
             * 新規 10/15 ～ 11/15
             *
             * ↓
             * 10/01 ～ 10/14
             */
            if ($oldStart->lt($startDate)) {
                $existing->update([
                    'end_date' => $startDate
                        ->copy()
                        ->subDay()
                        ->toDateString(),
                ]);

                /*
                 * 古い期間の後半も存在する場合
                 *
                 * 例
                 * 10/01 ～ 12/31
                 * 新規 10/15 ～ 11/15
                 *
                 * ↓
                 * 10/01 ～ 10/14
                 * 11/16 ～ 12/31
                 */
                if (
                    $oldEnd !== null
                    && $oldEnd->gt($endDate ?? $oldEnd)
                ) {
                    TeacherStationAssignment::create([
                        'teacher_id' => $teacherId,
                        'station_id' => $existing->station_id,
                        'start_date' => $endDate
                            ->copy()
                            ->addDay()
                            ->toDateString(),
                        'end_date' => $oldEnd->toDateString(),
                    ]);
                }

                continue;
            }

            /*
             * 古い期間の開始日が新しい期間内の場合
             * → 新しい期間に完全に吸収されるので削除
             */
            $existing->delete();
        }

        TeacherStationAssignment::create([
            'teacher_id' => $teacherId,
            'station_id' => $stationId,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate?->toDateString(),
        ]);
    }
}
