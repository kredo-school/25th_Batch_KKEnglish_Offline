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
        Schema::create('materials', function (Blueprint $table) {
            $table->id('material_id');
            $table->string('name');
            $table->string('cover_image')->nullable();
            $table->text('description')->nullable();
            $table->string('level')->nullable();
            $table->string('target_level')->nullable();
            $table->unsignedBigInteger('duration')->nullable();
            $table->string('printed_textbook')->nullable();
            $table->string('available_materials')->nullable();

            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
