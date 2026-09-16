<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id('transaction_id');

            /*
             * どの生徒のポイントか
             *
             * students の主キーは
             * $table->id();
             * なので students.id を参照
             */
            $table->foreignId('student_id')
                ->constrained('students')
                ->restrictOnDelete();

            /*
             * ポイント取引種類
             *
             * transaction_types の主キーは type_id
             */
            $table->foreignId('transaction_type')
                ->constrained('transaction_types', 'type_id')
                ->restrictOnDelete();

            /*
             * ポイント増減量
             *
             * 予約：-300
             * 返還：+300
             */
            $table->integer('point');

            /*
             * 関連する予約
             *
             * reservations の主キーは
             * $table->id();
             * なので reservations.id を参照
             */
            $table->foreignId('related_reservation_id')
                ->nullable()
                ->constrained('reservations')
                ->nullOnDelete();

            /*
             * 備考
             */
            $table->string('note')->nullable();

            /*
             * この処理を行ったユーザー
             *
             * users.id を参照
             */
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * ポイント取引発生日時
             */
            $table->timestamp('created_at')
                ->useCurrent();

            /*
             * 検索用INDEX
             */
            $table->index(['student_id', 'created_at']);
            $table->index('related_reservation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
