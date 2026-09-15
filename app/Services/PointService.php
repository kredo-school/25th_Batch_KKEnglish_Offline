<?php

namespace App\Services;

use App\Models\Student;
use App\Models\PointTransaction;
use App\Models\TransactionType;
use App\Models\Reservation;
use Illuminate\Validation\ValidationException;


class PointService
{
        public function consume(
            Student $student,
            Reservation $reservation,
            int $points,
            int $createBy

        ): void
        {
            $alreadyConsumed = PointTransaction::query()
                ->where(
                    'related_reservation_id',
                    $reservation->id
                )
                ->whereHas(
                    'transactionType',
                    function ($query) {
                        $query->where(
                            'type_code',
                            'reservation_use'
                        );
                    }
                )
                ->exists();

            if ($alreadyConsumed) {
                throw ValidationException::withMessages([
                    'points' =>
                        'この予約のポイントはすでに消費されています。',
                ]);
            }

            if ($student->point_balance < $points) {
                throw ValidationException::withMessages([
                    'points' => 'Insufficient points balance.',
                ]);
            }

            /*
            * ポイント取引種類取得
            */
            $transactionType = TransactionType::query()
                ->where('type_code', 'reservation_use')
                ->firstOrFail();

            /*
            * 生徒ポイント減算
            */
            $student->decrement('point_balance', $points);

            /*
            * ポイント履歴保存
            */
            PointTransaction::create([
                'student_id' => $student->id,
                'transaction_type' => $transactionType->type_id,
                'point' => -$points,
                'related_reservation_id' => $reservation->id,
                'note' => 'Points consumed for reservation.',
                'created_by' => $createBy,
            ]);
        }

        public function refund(
            Student $student,
            Reservation $reservation,
            int $createBy
        ): void {
            $alreadyRefunded = PointTransaction::query()
                ->where(
                    'related_reservation_id',
                    $reservation->id
                )
                ->whereHas(
                    'transactionType',
                    function ($query) {
                        $query->where(
                            'type_code',
                            'reservation_refund'
                        );
                    }
                )
                ->exists();

            if ($alreadyRefunded) {
                throw ValidationException::withMessages([
                    'points' =>
                        'この予約のポイントはすでに返還されています。',
                ]);
            }

            $point = (int) $reservation->point_cost;

            $transactionType = TransactionType::query()
                ->where('type_code',
                        'reservation_refund'
                )
                ->firstOrFail();

            /*
            * 生徒ポイント返還
            */
            $student->increment('point_balance', $point);
            /*
            * ポイント履歴保存
            */

            PointTransaction::create([
                'student_id' => $student->id,
                'transaction_type' => $transactionType->type_id,
                'point' => $point,
                'related_reservation_id' => $reservation->id,
                'note' => 'Points refunded for reservation.',
                'created_by' => $createBy,
            ]);
        }


}
