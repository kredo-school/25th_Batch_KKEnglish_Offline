<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function store(
        Request $request,
        Reservation $reservation
    ): RedirectResponse {

        /*
         * ログイン中Student取得
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
         * 自分の予約か確認
         */
        abort_unless(
            (int) $reservation->student_id
                ===
                (int) $student->id,
            403,
            'この予約をレビューする権限がありません。'
        );


        /*
         * Reservationのstatusを取得
         */
        $reservation->load('status');


        /*
         * completedのみレビュー可能
         */
        if (
            $reservation->status->status_code
            !==
            'completed'
        ) {
            throw ValidationException::withMessages([
                'review' =>
                    '完了した授業のみレビューできます。',
            ]);
        }


        /*
         * 二重レビュー防止
         */
        $alreadyReviewed = Review::query()
            ->where(
                'reservation_id',
                $reservation->id
            )
            ->exists();

        if ($alreadyReviewed) {
            throw ValidationException::withMessages([
                'review' =>
                    'この授業はすでにレビュー済みです。',
            ]);
        }


        /*
         * Validation
         */
        $validated = $request->validate([
            'rating' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],

            'comment' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);


        /*
         * Review保存
         */
        Review::create([
            'reservation_id' =>
                $reservation->id,

            'student_id' =>
                $student->id,

            'teacher_id' =>
                $reservation->teacher_id,

            'rating' =>
                $validated['rating'],

            'comment' =>
                $validated['comment'] ?? null,
        ]);


        return back()->with(
            'success',
            'レビューを投稿しました。'
        );
    }
}
