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
        Schema::create('lesson_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')
                  ->unique()
                  ->constrained('reservations')
                  ->restrictOnDelete();
            $table->date('lesson_date');
            $table->string('subject');
            $table->text('progress_note');
            $table->foreignId('completed_by')
                  ->constrained('users')
                  ->restrictOnDelete();
            $table->timestamp('completed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_records');
    }
};
