<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\PointTransaction;

class PointHistoryController extends Controller
{
    public function index(Request $request): View
    {
        /*
         * ========================================
         * ログイン中Student取得
         * ========================================
         */
        $student = $request
            ->user()
            ->student;

        abort_unless(
            $student,
            403,
            '生徒ユーザーではありません。'
        );

        /*
         * ========================================
         * ポイント履歴取得
         * ========================================
         */
        $pointTransactions = PointTransaction::query()
            ->where(
                'student_id',
                $student->id
            )
            ->with([
                'transactionType',
                'reservation.teacher.user',
                'reservation.material',
            ])
            ->orderByDesc(
                'transaction_id'
            )
            ->get();

        /*
         * ========================================
         * Bladeへ渡す
         * ========================================
         */
        return view(
            'students.history.point-history',
            compact(
                'student',
                'pointTransactions'
            )
        );

    }
}
