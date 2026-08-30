<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // teacher_idの削除ルールをcascadeからrestrictへ変更
        Schema::table('teacher_schedules', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
        });

        Schema::table('teacher_schedules', function (Blueprint $table) {
            $table->foreign('teacher_id')
                ->references('id')
                ->on('teachers')
                ->restrictOnDelete();
        });

        Schema::table('teacher_schedules', function (Blueprint $table) {
            $table->foreignId('shift_pattern_id')
                ->nullable()
                ->after('teacher_id')
                ->constrained('shift_patterns')
                ->restrictOnDelete();

            $table->string('status', 20)
                ->default('draft')
                ->change();

            $table->foreignId('created_by')
                ->after('status')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('confirmed_by')
                ->nullable()
                ->after('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('confirmed_at')
                ->nullable()
                ->after('confirmed_by');

            $table->foreignId('cancelled_by')
                ->nullable()
                ->after('confirmed_at')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('cancelled_at')
                ->nullable()
                ->after('cancelled_by');

            $table->index([
                'teacher_id',
                'available_date',
                'status',
            ]);

            $table->index([
                'shift_pattern_id',
                'available_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('teacher_schedules', function (Blueprint $table) {
            $table->dropIndex([
                'shift_pattern_id',
                'available_date',
            ]);

            $table->dropIndex([
                'teacher_id',
                'available_date',
                'status',
            ]);

            $table->dropConstrainedForeignId('cancelled_by');
            $table->dropColumn('cancelled_at');

            $table->dropConstrainedForeignId('confirmed_by');
            $table->dropColumn('confirmed_at');

            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('shift_pattern_id');
        });

        Schema::table('teacher_schedules', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
        });

        Schema::table('teacher_schedules', function (Blueprint $table) {
            $table->foreign('teacher_id')
                ->references('id')
                ->on('teachers')
                ->cascadeOnDelete();

            $table->string('status')
                ->default('available')
                ->change();
        });
    }
};
