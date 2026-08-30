<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->restrictOnDelete();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->restrictOnDelete();

            $table->foreignId('schedule_id')
                ->constrained('teacher_schedules', 'schedule_id')
                ->restrictOnDelete();

            $table->foreignId('material_id')
                ->constrained('materials', 'material_id')
                ->restrictOnDelete();

            $table->foreignId('status_id')
                ->constrained('reservation_statuses', 'status_id')
                ->restrictOnDelete();

            $table->dateTime('start_at');
            $table->dateTime('end_at');

            $table->unsignedInteger('point_cost')->default(0);

            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->timestamps();

            $table->index(['teacher_id', 'start_at', 'end_at']);
            $table->index(['student_id', 'start_at', 'end_at']);
            $table->index(['schedule_id', 'status_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
