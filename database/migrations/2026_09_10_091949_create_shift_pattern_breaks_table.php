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
        Schema::create('shift_pattern_breaks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('shift_pattern_id');
            $table->unsignedTinyInteger('weekday')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['shift_pattern_id', 'weekday']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_pattern_breaks');
    }
};
