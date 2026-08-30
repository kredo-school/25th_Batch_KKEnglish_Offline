<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_materials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->restrictOnDelete();

            $table->foreignId('material_id')
                ->constrained('materials', 'material_id')
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique([
                'teacher_id',
                'material_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_materials');
    }
};
