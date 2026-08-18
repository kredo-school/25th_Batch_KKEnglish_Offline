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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->text('biography')->nullable();
            $table->integer('point_consumed')->default(100);
            $table->string('career')->nullable();
            $table->string('graduation_school')->nullable();
            $table->string('certification')->nullable();
            $table->text('about_me')->nullable();
            $table->string('specialty')->nullable();
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
