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
        Schema::create('teacher_station_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            $table->foreignId('station_id')
                ->constrained('stations')
                ->cascadeOnDelete();

            $table->timestamps();

            /*
             * 1人のTeacherに同じStationを
             * 重複登録できないようにする
             */
            $table->unique([
                'teacher_id',
                'station_id',
                ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_station_assignments');
    }
};
