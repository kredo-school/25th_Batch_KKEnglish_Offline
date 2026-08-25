<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function cancel(int $reservation): RedirectResponse
    {
        $user = auth()->user();

        $record = Reservation::query()->findOrFail($reservation);

        // 自分の予約のみキャンセル可
        if ((int)$record->student_id !== (int)$user->id) {
            abort(403);
        }

        // 既にキャンセル済み
        if ($record->status === 'cancelled') {
            return back()->with('error', 'この予約はすでにキャンセル済みです。');
        }

        // 必要なら期限チェック（例: 開始1時間前まで）
        // if (now()->gte($record->start_at->subHour())) { ... }

        $record->update([
            'status' => 'cancelled',
        ]);

        // Reservation更新後に追加（カラム設計に応じて調整）
        $record->schedule()->update(['status' => 'available']);

        return back()->with('success', '授業予約をキャンセルしました。');
    }
}
