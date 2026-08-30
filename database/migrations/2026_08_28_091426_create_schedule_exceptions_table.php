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
        Schema::create('schedule_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers');
            $table->foreignId('schedule_id')->nullable()->constrained('teacher_schedules','schedule_id');
            $table->foreignId('exception_type_id')->constrained('exception_types','exception_type_id');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('status',20)->default('active');
            $table->text('reason')->nullable();

            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('cancelled_by')
                  ->nullable()
                  ->constrained('users')
                  ->restrictOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index([
                'teacher_id',
                'start_at',
                'end_at',
            ]);

            $table->index([
                'schedule_id',
                'status',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_exceptions');
    }
};
