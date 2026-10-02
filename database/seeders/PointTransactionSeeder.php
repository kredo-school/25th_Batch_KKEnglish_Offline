<?php

namespace Database\Seeders;

use App\Models\PointTransaction;
use App\Models\Reservation;
use App\Models\TransactionType;
use Illuminate\Database\Seeder;
use RuntimeException;

class PointTransactionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Transaction Types
        |--------------------------------------------------------------------------
        */

        $useType = TransactionType::query()
            ->where('type_code', 'reservation_use')
            ->first();

        $refundType = TransactionType::query()
            ->where('type_code', 'reservation_refund')
            ->first();


        if (!$useType) {
            throw new RuntimeException(
                'TransactionType: reservation_use がありません。'
            );
        }

        if (!$refundType) {
            throw new RuntimeException(
                'TransactionType: reservation_refund がありません。'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Reservations
        |--------------------------------------------------------------------------
        */

        $reservations = Reservation::query()
            ->with([
                'student',
                'status',
            ])
            ->get();

        if ($reservations->isEmpty()) {
            throw new RuntimeException(
                'Reservationがありません。ReservationSeederを先に実行してください。'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Point Transactions
        |--------------------------------------------------------------------------
        */

        foreach ($reservations as $reservation) {

            /*
            |--------------------------------------------------------------------------
            | 予約時のポイント消費
            |--------------------------------------------------------------------------
            |
            | 予約が作られた時点でポイントを消費した履歴
            |
            */

            PointTransaction::updateOrCreate(
                [
                    'student_id' => $reservation->student_id,
                    'transaction_type' => $useType->type_id,
                    'related_reservation_id' => $reservation->id,
                ],
                [
                    'point' => -$reservation->point_cost,

                    'note' =>
                        'Points used for reservation',

                    'created_by' =>
                        $reservation->student->user_id,

                    'created_at' =>
                        $reservation->created_at
                        ?? now(),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Cancelled Reservation
            |--------------------------------------------------------------------------
            |
            | キャンセル済みの場合はポイント返還
            |
            */

            if (
                $reservation->status
                && $reservation->status->status_code === 'cancelled'
            ) {

                PointTransaction::updateOrCreate(
                    [
                        'student_id' =>
                            $reservation->student_id,

                        'transaction_type' =>
                            $refundType->type_id,

                        'related_reservation_id' =>
                            $reservation->id,
                    ],
                    [
                        'point' =>
                            $reservation->point_cost,

                        'note' =>
                            'Points refunded for cancelled reservation',

                        'created_by' =>
                            $reservation->cancelled_by
                            ?? $reservation->student->user_id,

                        'created_at' =>
                            $reservation->cancelled_at
                            ?? now(),
                    ]
                );
            }
        }

        $this->command?->info(
            'Point transactions created.'
        );
    }
}
