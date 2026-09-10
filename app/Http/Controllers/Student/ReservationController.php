<?php

namespace App\Http\Controllers\Student;

use Carbon\CarbonImmutable;
use App\Services\ReservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Material;
use App\Models\TeacherSchedule;
use App\Models\Reservation;
use App\Models\ReservationStatus;
use App\Models\ReservationHistory;
use Illuminate\Contracts\View\View;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    public function __construct(
        private ReservationService $reservationService
    ) {}

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

    public function store(Request $request): RedirectResponse
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
            ->route('students.reservations.upcoming')
            ->with(
                'success',
                '予約が完了しました。'
            );
    }
    public function cancel(
        Request $request,
        Reservation $reservation
    ): RedirectResponse {

        /*
     * ========================================
     * ログイン中Student取得
     * ========================================
     */
        $student = $request
            ->user()
            ->student;

        abort_unless(
            $student,
            403,
            '生徒ユーザーではありません。'
        );


        /*
     * ========================================
     * キャンセル理由
     * ========================================
     */
        $validated = $request->validate([
            'cancellation_reason' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);


        /*
     * ========================================
     * Transaction
     * ========================================
     */
        DB::transaction(function () use (
            $request,
            $reservation,
            $student,
            $validated
        ) {

            /*
         * =====================================
         * Reservationを最新状態で再取得
         * + ロック
         * =====================================
         */
            $reservation = Reservation::query()
                ->with('status')
                ->whereKey(
                    $reservation->getKey()
                )
                ->lockForUpdate()
                ->firstOrFail();


            /*
         * =====================================
         * 自分の予約か確認
         * =====================================
         */
            abort_unless(
                (int) $reservation->student_id
                    ===
                    (int) $student->id,
                403,
                '他の生徒の予約はキャンセルできません。'
            );


            /*
         * =====================================
         * キャンセル可能statusか
         * =====================================
         */
            if (
                !in_array(
                    $reservation->status->status_code,
                    [
                        'pending',
                        'confirmed',
                    ],
                    true
                )
            ) {

                throw ValidationException::withMessages([
                    'reservation' =>
                    'この予約はキャンセルできません。',
                ]);
            }


            if ($reservation->start_at->lte(now())) {
                throw ValidationException::withMessages([
                    'reservation' =>
                    '開始済みまたは過去の予約はキャンセルできません。',
                ]);
            }

            /*
         * =====================================
         * cancelled status取得
         * =====================================
         */
            $cancelledStatus =
                ReservationStatus::query()
                ->where(
                    'status_code',
                    'cancelled'
                )
                ->firstOrFail();


            /*
         * =====================================
         * 変更前status
         * =====================================
         */
            $fromStatusId =
                $reservation->status_id;


            /*
         * =====================================
         * Reservation更新
         * =====================================
         */
            $reservation->update([

                'status_id' =>
                $cancelledStatus->status_id,

                'cancelled_by' =>
                $request->user()->id,

                'cancelled_at' =>
                now(),

                'cancellation_reason' =>
                $validated['cancellation_reason'] ?? null,
            ]);


            /*
         * =====================================
         * History作成
         * =====================================
         */
            ReservationHistory::create([

                'reservation_id' =>
                $reservation->id,

                'from_status_id' =>
                $fromStatusId,

                'to_status_id' =>
                $cancelledStatus->status_id,

                'changed_by' =>
                $request->user()->id,

                'reason' =>
                $validated['cancellation_reason'] ?? null,
            ]);
        });


        /*
     * ========================================
     * 完了
     * ========================================
     */
        return redirect()
            ->route(
                'students.reservations.upcoming'
            )
            ->with(
                'success',
                '予約をキャンセルしました。'
            );
    }

    public function myReservations(Request $request): View
    {
        /*
     * ログイン中のStudent取得
     */
        $student = $request->user()->student;

        abort_unless(
            $student,
            403,
            '生徒ユーザーではありません。'
        );

        /*
     * 現在の予約
     *
     * ・未来の予約
     * ・pendingまたはconfirmed
     */
        $upcomingReservations = Reservation::query()
            ->where('student_id', $student->id)
            ->where('start_at', '>=', now())
            ->whereHas('status', function ($query) {
                $query->whereIn('status_code', [
                    'pending',
                    'confirmed',
                ]);
            })
            ->with([
                'teacher.user',
                'material',
                'status',
            ])
            ->orderBy('start_at')
            ->get();

        /*
     * 予約履歴
     *
     * ・開始日時を過ぎている
     * または
     * ・pending、confirmed以外
     */
        $historyReservations = Reservation::query()
            ->where('student_id', $student->id)
            ->where(function ($query) {
                $query
                    ->where('start_at', '<', now())
                    ->orWhereHas('status', function ($statusQuery) {
                        $statusQuery->whereNotIn('status_code', [
                            'pending',
                            'confirmed',
                        ]);
                    });
            })
            ->with([
                'teacher.user',
                'material',
                'status',
            ])
            ->orderByDesc('start_at')
            ->get();

        /*
     * 既存Bladeとの互換性を残す
     */
        $reservations = $upcomingReservations;

        return view(
            'students.reservations.upcoming',
            compact(
                'reservations',
                'upcomingReservations',
                'historyReservations'
            )
        );
    }

public function teacherDetail(Request $request): View
{
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

        'date' => [
            'nullable',
            'date',
        ],

        // カレンダーの表示開始日
        'view_start' => [
            'nullable',
            'date',
        ],

        'mode' => [
            'required',
            'in:material,date',
        ],
    ]);

    $teacher = Teacher::query()
        ->with([
            'user',
            'materials',
        ])
        ->findOrFail(
            $validated['teacher_id']
        );

    $material = Material::query()
        ->findOrFail(
            $validated['material_id']
        );

    /*
     * 生徒が選択した日
     */
    $selectedDate = isset($validated['date'])
        ? CarbonImmutable::parse(
            $validated['date']
        )->startOfDay()
        : null;

    /*
     * 7日間の表示開始日
     *
     * view_startがなければ今日から表示
     */
    $viewStart = isset($validated['view_start'])
        ? CarbonImmutable::parse(
            $validated['view_start']
        )->startOfDay()
        : CarbonImmutable::today();

    return view(
        'students.reservations.teacher-detail',
        compact(
            'teacher',
            'material',
            'validated',
            'selectedDate',
            'viewStart'
        )
    );
}
    public function showReservation(
        Request $request,
        Reservation $reservation
    ): View {

        $student = $request->user()->student;

        abort_unless(
            $student,
            403,
            '生徒ユーザーではありません。'
        );

        /*
     * 他の生徒の予約を表示させない
     */
        abort_unless(
            (int) $reservation->student_id === (int) $student->id,
            403,
            'この予約を表示する権限がありません。'
        );

        /*
     * 詳細画面で必要な関連データを取得
     */
        $reservation->load([
            'teacher.user',
            'material',
            'status',
            'schedule',
            'histories',
            'lessonRecord',
        ]);

        return view(
            'students.reservations.teacher-detail',
            compact('reservation')
        );
    }
}
