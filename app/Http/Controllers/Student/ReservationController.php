<?php

namespace App\Http\Controllers\Student;

use App\Services\ReservationService;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Material;
use App\Models\TeacherSchedule;
use Illuminate\Contracts\View\View;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    public function __construct(
        private ReservationService $reservationService
    ) {
    }
    public function index(): View
    {
        /**
         * Teacher一覧
         *
         * user
         * materials
         *
         * も一緒に取得
         */
        $teachers = Teacher::query()
            ->with(['user', 'materials'])
            ->get();

        /*
         * Material一覧
         */

        $materials = Material::query()
            ->orderBy('material_id')
            ->get();

        /*
         * Bladeへ
         * $teachers
         * $materials
         *
         * を渡す
         */
        return view(
            'students.reservations.index',
            compact('teachers', 'materials')
        );
    }
    public function confirm(Request $request): View
    {
        /*
     * ========================================
     * フロントから送られてきた予約情報を確認
     * ========================================
     */
        $validated = $request->validate([
            'teacher_id' => [
                'required',
                'integer',
                'exists:teachers,id',
            ],

            'material_id' => [
                'required',
                'integer',
                'exists:materials,material_id',
            ],

            'schedule_id' => [
                'required',
                'integer',
                'exists:teacher_schedules,schedule_id',
            ],

            'start_at' => [
                'required',
                'date',
            ],

            'end_at' => [
                'required',
                'date',
                'after:start_at',
            ],
        ]);


        /*
     * ========================================
     * Teacher取得
     * ========================================
     */
        $teacher = Teacher::query()
            ->with([
                'user',
                'materials',
            ])
            ->findOrFail(
                $validated['teacher_id']
            );


        /*
     * ========================================
     * Material取得
     * ========================================
     */
        $material = Material::query()
            ->findOrFail(
                $validated['material_id']
            );


        /*
     * ========================================
     * TeacherSchedule取得
     * ========================================
     */
        $schedule = TeacherSchedule::query()
            ->findOrFail(
                $validated['schedule_id']
            );


        /*
     * ========================================
     * ScheduleとTeacherが一致するか
     * ========================================
     */
        if (
            (int) $schedule->teacher_id
            !==
            (int) $teacher->id
        ) {
            throw ValidationException::withMessages([
                'teacher_id' =>
                '選択した講師と勤務スケジュールが一致しません。',
            ]);
        }


        /*
     * ========================================
     * confirmed Scheduleだけ許可
     * ========================================
     */
        if ($schedule->status !== 'confirmed') {

            throw ValidationException::withMessages([
                'schedule_id' =>
                'この勤務スケジュールは予約できません。',
            ]);
        }


        /*
     * ========================================
     * TeacherがMaterialを教えられるか確認
     * ========================================
     */
        $canTeachMaterial = $teacher
            ->materials
            ->contains(
                'material_id',
                $material->material_id
            );


        if (!$canTeachMaterial) {

            throw ValidationException::withMessages([
                'material_id' =>
                'この講師は選択した教材を担当できません。',
            ]);
        }


        /*
     * ========================================
     * 確認画面へ渡す
     * ========================================
     */
        return view(
            'students.reservations.confirm',
            compact(
                'teacher',
                'material',
                'schedule',
                'validated'
            )
        );
    }

    public function store(Request $request):RedirectResponse
    {
        $validated = $request->validate([

            'teacher_id' => ['required', 'integer', 'exists:teachers,id'],
            'material_id' => ['required', 'integer', 'exists:materials,material_id'],
            'schedule_id' => ['required', 'integer', 'exists:teacher_schedules,schedule_id'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
        ]);
        $student = $request
            ->user()
            ->student;


        abort_unless(
            $student,
            403,
            '生徒ユーザーではありません。'
        );


        $reservation =
            $this->reservationService
                ->createForStudent(
                    $student,
                    $validated
                );


        return redirect()
            ->route('students.reservations.index')
            ->with(
                'success',
                '予約が完了しました。'
            );
    }


}
