<?php

namespace App\Services;

use App\Models\LessonRecord;
use App\Models\Reservation;
use App\Models\ReservationHistory;
use App\Models\ReservationStatus;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherSchedule;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    public function createForStudent(
        Student $student,
        array $data
    ): Reservation {
        return DB::transaction(function () use (
            $student,
            $data
        ) {

            $student = Student::query()
                ->whereKey($student->getKey())
                ->lockForUpdate()
                ->firstOrFail();


            $schedule = TeacherSchedule::query()
                ->whereKey($data['schedule_id'])
                ->lockForUpdate()
                ->firstOrFail();


            /*
             * Teacher と Schedule が一致するか
             */
            if (
                (int) $schedule->teacher_id
                !==
                (int) $data['teacher_id']
            ) {
                throw ValidationException::withMessages([
                    'teacher_id' =>
                    '指定された講師と勤務シフトが一致しません。',
                ]);
            }


            /*
             * confirmed Scheduleのみ
             */
            if ($schedule->status !== 'confirmed') {
                throw ValidationException::withMessages([
                    'schedule_id' =>
                    'この勤務シフトは予約できません。',
                ]);
            }


            $teacher = $schedule
                ->teacher()
                ->firstOrFail();


            /*
             * TeacherがMaterialを教えられるか
             */
            $canTeachMaterial = $teacher
                ->materials()
                ->where(
                    'materials.material_id',
                    $data['material_id']
                )
                ->exists();


            if (!$canTeachMaterial) {
                throw ValidationException::withMessages([
                    'material_id' =>
                    'この講師は選択した教材を担当できません。',
                ]);
            }


            $startAt = CarbonImmutable::parse(
                $data['start_at']
            );

            $endAt = CarbonImmutable::parse(
                $data['end_at']
            );


            /*
             * 過去は不可
             */
            if ($startAt->isPast()) {
                throw ValidationException::withMessages([
                    'start_at' =>
                    '過去の時間は予約できません。',
                ]);
            }


            /*
             * 30分予約
             */
            if (
                !$endAt->equalTo(
                    $startAt->addMinutes(30)
                )
            ) {
                throw ValidationException::withMessages([
                    'end_at' =>
                    '予約時間は30分で指定してください。',
                ]);
            }


            /*
             * Scheduleの時間内か
             */
            $date = $schedule
                ->available_date
                ->format('Y-m-d');

            $scheduleStart =
                CarbonImmutable::parse(
                    $date . ' '
                        . $schedule->start_time
                );

            $scheduleEnd =
                CarbonImmutable::parse(
                    $date . ' '
                        . $schedule->end_time
                );


            if (
                $startAt->lt($scheduleStart)
                ||
                $endAt->gt($scheduleEnd)
            ) {
                throw ValidationException::withMessages([
                    'start_at' =>
                    '勤務時間外のため予約できません。',
                ]);
            }


            /*
             * ScheduleException確認
             */
            $hasException = $schedule
                ->exceptions()
                ->where('status', 'active')
                ->where(
                    'start_at',
                    '<',
                    $endAt
                )
                ->where(
                    'end_at',
                    '>',
                    $startAt
                )
                ->exists();


            if ($hasException) {
                throw ValidationException::withMessages([
                    'start_at' =>
                    'この時間は講師が予約を受け付けていません。',
                ]);
            }


            /*
             * Teacherの予約重複確認
             */
            $hasTeacherReservation =
                $schedule
                ->reservations()
                ->whereHas(
                    'status',
                    function ($query) {
                        $query->whereIn(
                            'status_code',
                            [
                                'pending',
                                'confirmed',
                            ]
                        );
                    }
                )
                ->where(
                    'start_at',
                    '<',
                    $endAt
                )
                ->where(
                    'end_at',
                    '>',
                    $startAt
                )
                ->exists();


            if ($hasTeacherReservation) {
                throw ValidationException::withMessages([
                    'start_at' =>
                    'この時間はすでに予約されています。',
                ]);
            }


            /*
             * Student自身の重複予約
             */
            $hasStudentReservation =
                Reservation::query()
                ->where(
                    'student_id',
                    $student->id
                )
                ->whereHas(
                    'status',
                    function ($query) {
                        $query->whereIn(
                            'status_code',
                            [
                                'pending',
                                'confirmed',
                            ]
                        );
                    }
                )
                ->where(
                    'start_at',
                    '<',
                    $endAt
                )
                ->where(
                    'end_at',
                    '>',
                    $startAt
                )
                ->exists();


            if ($hasStudentReservation) {
                throw ValidationException::withMessages([
                    'start_at' =>
                    '同じ時間に別の予約があります。',
                ]);
            }


            /*
             * confirmed status
             */
            $status = ReservationStatus::query()
                ->where(
                    'status_code',
                    'confirmed'
                )
                ->firstOrFail();


            /*
             * Reservation作成
             */
            $reservation = Reservation::create([
                'student_id' =>
                $student->id,

                'teacher_id' =>
                $teacher->id,

                'schedule_id' =>
                $schedule->schedule_id,

                'material_id' =>
                $data['material_id'],

                'status_id' =>
                $status->status_id,

                'start_at' =>
                $startAt,

                'end_at' =>
                $endAt,

                'point_cost' =>
                (int) $teacher->point_consumed,
            ]);


            return $reservation->refresh();
        });
    }

    /*
 * ========================================
 * 講師による授業結果登録
 * ========================================
 */
    public function recordLessonResult(
        Teacher $teacher,
        Reservation $reservation,
        int $userId,
        array $data
    ): Reservation {

        return DB::transaction(function () use (
            $teacher,
            $reservation,
            $userId,
            $data
        ) {

            /*
         * =====================================
         * resultの確認
         * =====================================
         */
            $resultCode = $data['result'] ?? null;

            if (
                !in_array(
                    $resultCode,
                    [
                        'completed',
                        'absent',
                    ],
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    'result' =>
                    '授業結果には完了または欠席を指定してください。',
                ]);
            }

            /*
         * =====================================
         * Reservationを最新状態で再取得
         * 同時更新を防ぐためロック
         * =====================================
         */
            $reservation = Reservation::query()
                ->with('status')
                ->whereKey($reservation->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            /*
         * =====================================
         * ログイン講師が担当者か確認
         * =====================================
         */
            if (
                (int) $reservation->teacher_id
                !==
                (int) $teacher->id
            ) {
                throw ValidationException::withMessages([
                    'reservation' =>
                    '他の講師が担当する予約は変更できません。',
                ]);
            }

            /*
         * =====================================
         * confirmedの予約だけ結果登録可能
         * =====================================
         */
            if (
                $reservation->status->status_code
                !==
                'confirmed'
            ) {
                throw ValidationException::withMessages([
                    'reservation' =>
                    '予約確定状態のレッスンだけ結果を登録できます。',
                ]);
            }

            /*
         * =====================================
         * 完了はレッスン終了後のみ登録可能
         * =====================================
         */
            if (
                $resultCode === 'completed'
                &&
                $reservation->end_at->isFuture()
            ) {
                throw ValidationException::withMessages([
                    'result' =>
                    '終了前のレッスンを完了にはできません。',
                ]);
            }

            /*
         * =====================================
         * 欠席はレッスン開始後のみ登録可能
         * =====================================
         */
            if (
                $resultCode === 'absent'
                &&
                $reservation->start_at->isFuture()
            ) {
                throw ValidationException::withMessages([
                    'result' =>
                    '開始前のレッスンを欠席にはできません。',
                ]);
            }

            /*
         * =====================================
         * 完了時の授業内容を確認
         * =====================================
         */
            if (
                $resultCode === 'completed'
                &&
                (
                    empty($data['subject'])
                    ||
                    empty($data['progress_note'])
                )
            ) {
                throw ValidationException::withMessages([
                    'progress_note' =>
                    '完了時は授業内容と進捗を入力してください。',
                ]);
            }

            /*
         * =====================================
         * completedまたはabsentの
         * ReservationStatusを取得
         * =====================================
         */
            $resultStatus = ReservationStatus::query()
                ->where(
                    'status_code',
                    $resultCode
                )
                ->first();

            if (!$resultStatus) {
                throw ValidationException::withMessages([
                    'result' =>
                    '指定された予約ステータスが存在しません。',
                ]);
            }

            /*
         * =====================================
         * 変更前のstatus_idを保存
         * =====================================
         */
            $fromStatusId = $reservation->status_id;

            /*
         * =====================================
         * Reservationのstatusを更新
         * =====================================
         */
            $reservation->update([
                'status_id' =>
                $resultStatus->status_id,
            ]);

            /*
         * =====================================
         * completedの場合だけ
         * LessonRecordを保存
         * =====================================
         */
            if ($resultCode === 'completed') {
                LessonRecord::updateOrCreate(
                    [
                        'reservation_id' =>
                        $reservation->getKey(),
                    ],
                    [
                        'lesson_date' =>
                        $reservation
                            ->start_at
                            ->toDateString(),

                        'subject' =>
                        $data['subject'],

                        'progress_note' =>
                        $data['progress_note'],

                        'completed_by' =>
                        $userId,

                        'completed_at' =>
                        now(),
                    ]
                );
            }

            /*
         * =====================================
         * ReservationHistoryへ保存
         * =====================================
         */
            ReservationHistory::create([
                'reservation_id' =>
                $reservation->getKey(),

                'from_status_id' =>
                $fromStatusId,

                'to_status_id' =>
                $resultStatus->status_id,

                'changed_by' =>
                $userId,

                'reason' =>
                $resultCode === 'completed'
                    ? '講師がレッスン完了を登録'
                    : '講師が生徒の欠席を登録',
            ]);

            /*
         * =====================================
         * 更新後のReservationを返す
         * =====================================
         */
            return $reservation->refresh();
        });
    }
}
