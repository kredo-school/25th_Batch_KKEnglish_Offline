<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ログイン中のユーザーに紐づく講師
        $teacher = $request->user()->teacher;

        abort_unless(
            $teacher,
            403,
            '講師ユーザーではありません。'
        );

        $todayLessons = Reservation::query()

            // ログイン中の講師だけ
            ->where('teacher_id', $teacher->id)

            // 今日の予約だけ
            ->whereBetween('start_at', [
                now()->startOfDay(),
                now()->endOfDay(),
            ])

            // 有効な予約だけ
            ->whereHas('status', function ($query) {
                $query->whereIn('status_code', [
                    'pending',
                    'confirmed',
                ]);
            })

            // 関連データも一緒に取得
            ->with([
                'student.user',
                'material',
                'status',
            ])

            // 時間順
            ->orderBy('start_at')

            ->get();

        return view('teachers.dashboard', compact('teacher','todayLessons'));
    }
}
