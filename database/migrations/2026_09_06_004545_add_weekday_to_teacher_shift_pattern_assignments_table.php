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
        Schema::table('teacher_shift_pattern_assignments', function (Blueprint $table) {
            $table->unsignedTinyInteger('weekday')->after('shift_pattern_id')->comment('0=Sun ... 6=Sat');
            $table->index(
                ['teacher_id', 'weekday', 'start_date', 'end_date'],
                'idx_tspa_teacher_weekday_period'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_shift_pattern_assignments', function (Blueprint $table) {
            $table->dropIndex('idx_tspa_teacher_weekday_period');
            $table->dropColumn('weekday');
        });
    }
};
