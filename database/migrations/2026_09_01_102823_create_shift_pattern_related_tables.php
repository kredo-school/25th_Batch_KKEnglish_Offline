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
        Schema::create('shift_pattern_related_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_pattern_id')->constrained('shift_patterns')->cascadeOnDelete();
            $table->unsignedTinyInteger('weekday'); // 0-6
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('lesson_type', ['online', 'in_person', 'both'])->default('both');
            $table->timestamps();

            $table->index(['shift_pattern_id', 'weekday']);
        });

        Schema::create('shift_pattern_breaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_pattern_id')->constrained('shift_patterns')->cascadeOnDelete();
            $table->unsignedTinyInteger('weekday');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['shift_pattern_id', 'weekday']);
        });

        Schema::create('teacher_shift_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('shift_pattern_id')->constrained('shift_patterns')->cascadeOnDelete();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->unsignedInteger('priority')->default(0);
            $table->timestamps();

            $table->index(['teacher_id', 'effective_from', 'effective_to'], 'idx_tsa_teacher_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_shift_assignments');
        Schema::dropIfExists('shift_pattern_breaks');
        Schema::dropIfExists('shift_pattern_rules');
    }
};
