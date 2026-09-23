<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TeacherScheduleStatusService
{
    /**
     * User の active / inactive 変更に合わせて、
     * 本日以降の TeacherSchedule 状態を同期する。
     */
    public function syncForUserStatus(
        User $user,
        string $previousStatus,
        string $newStatus
    ): void {
        // 名前・メールアドレスだけを更新した場合に、
        // draft / confirmed を勝手に変更しない。
        if ($previousStatus === $newStatus) {
            return;
        }

        $teacherId = Teacher::query()
            ->where('user_id', $user->getKey())
            ->value('id');

        // Teacher ではない User の場合は何もしない。
        if (!$teacherId) {
            return;
        }

        $today = now()->toDateString();

        if ($newStatus === 'inactive') {
            // inactive 化：本日以降の confirmed を draft に戻す。
            DB::table('teacher_schedules')
                ->where('teacher_id', $teacherId)
                ->whereDate('available_date', '>=', $today)
                ->where('status', 'confirmed')
                ->update([
                    'status' => 'draft',
                    'updated_at' => now(),
                ]);

            return;
        }

        if ($newStatus === 'active') {
            // active 化：本日以降の draft を confirmed に戻す。
            DB::table('teacher_schedules')
                ->where('teacher_id', $teacherId)
                ->whereDate('available_date', '>=', $today)
                ->where('status', 'draft')
                ->update([
                    'status' => 'confirmed',
                    'updated_at' => now(),
                ]);
        }
    }
}
