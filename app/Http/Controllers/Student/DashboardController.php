<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ログイン中のユーザーに紐づく生徒
        $student = $request->user()->student;

        abort_unless(
            $student,
            403,
            '生徒ユーザーではありません。'
        );

        // 今日のレッスン
        $todayLessons = Reservation::query()

            // ログイン中の生徒だけ
            ->where('student_id', $student->id)

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
                'teacher.user',
                'material',
                'status',
            ])

            // 時間順
            ->orderBy('start_at')

            ->get();

        return view(
            'students.dashboard',
            compact('todayLessons')
        );
    }

}
