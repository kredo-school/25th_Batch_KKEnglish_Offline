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
        Schema::table('shift_pattern_breaks', function (Blueprint $table) {
            $table->foreign(['shift_pattern_id'])->references(['id'])->on('shift_patterns')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_pattern_breaks', function (Blueprint $table) {
            $table->dropForeign('shift_pattern_breaks_shift_pattern_id_foreign');
        });
    }
};
