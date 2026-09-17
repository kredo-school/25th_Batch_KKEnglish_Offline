<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expected_reservation_settings', function (Blueprint $table) {
            $table->id();
            $table->string('season_type', 20)->comment('busy:混雑期, normal:通常期, quiet:閑散期');
            $table->string('day_type', 20)->comment('weekday:平日, weekend:週末');
            $table->time('target_time')->comment('時間帯 (例: 09:00:00)');
            $table->integer('expected_count')->default(0)->comment('予想予約数');
            $table->timestamps();
            // 同じ組み合わせが重複しないようにユニーク制約をかける
            $table->unique(['season_type', 'day_type', 'target_time'], 'exp_res_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expected_reservation_settings');
    }
};
