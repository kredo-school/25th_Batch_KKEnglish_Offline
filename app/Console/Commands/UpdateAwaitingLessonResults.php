<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use App\Models\ReservationStatus;

class UpdateAwaitingLessonResults extends Command
{
    /**
     * ターミナルから実行するときのコマンド名
     *
     * 実行例：
     * php artisan reservations:update-awaiting-results
     */
    protected $signature =
        'reservations:update-awaiting-results';

    /**
     * php artisan list で表示される説明
     */
    protected $description =
        '授業終了済みのconfirmed予約をawaiting_resultへ変更する';

    /**
     * Commandを実行したときに動く処理
     */
    public function handle(): int
    {
        /*
         * ========================================
         * awaiting_result のStatusを取得
         * ========================================
         *
         * reservation_statuses テーブルから
         *
         * status_code = awaiting_result
         *
         * のレコードを1件取得する
         */
        $awaitingStatus = ReservationStatus::query()
            ->where(
                'status_code',
                'awaiting_result'
            )
            ->firstOrFail();


        /*
         * ========================================
         * 対象のReservationを更新
         * ========================================
         *
         * 条件：
         *
         * ① 授業終了時刻を過ぎている
         *
         * ② 現在のstatusがconfirmed
         *
         * この2条件を満たすReservationを
         * awaiting_resultへ変更する
         */
        $updatedCount = Reservation::query()

            /*
             * 授業終了済み
             *
             * end_at <= 現在時刻
             */
            ->where(
                'end_at',
                '<=',
                now()
            )

            /*
             * statusがconfirmedの予約だけ
             */
            ->whereHas(
                'status',
                function ($query) {
                    $query->where(
                        'status_code',
                        'confirmed'
                    );
                }
            )

            /*
             * status_idを
             * awaiting_resultのstatus_idへ変更
             */
            ->update([
                'status_id' =>
                    $awaitingStatus->status_id,
            ]);


        /*
         * ========================================
         * ターミナルに結果を表示
         * ========================================
         *
         * 例：
         *
         * 3 reservations updated.
         */
        $this->info(
            "{$updatedCount} reservations updated."
        );


        /*
         * ========================================
         * Command正常終了
         * ========================================
         */
        return self::SUCCESS;
    }
}
