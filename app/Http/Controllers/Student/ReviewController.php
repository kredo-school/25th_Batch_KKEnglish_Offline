<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function create(
        Request $request,
        Reservation $reservation
    ): View {
        $student = $this->getStudent($request);

        $this->validateReviewableReservation(
            $reservation,
            $student
        );

        $reservation->load([
            'teacher.user',
            'material',
            'status',
        ]);

        return view(
            'students.reviews.create',
            compact(
                'reservation',
                'student'
            )
        );
    }


    public function store(
        Request $request,
        Reservation $reservation
    ): RedirectResponse {
        $student = $this->getStudent($request);

        $this->validateReviewableReservation(
            $reservation,
            $student
        );

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

        return redirect()
            ->route('students.history.index')
            ->with(
                'success',
                'レビューを投稿しました。'
            );
    }


    /**
     * ログイン中のStudent取得
     */
    private function getStudent(
        Request $request
    ): Student {
        $student = $request
            ->user()
            ->student;

        abort_unless(
            $student,
            403,
            '生徒ユーザーではありません。'
        );

        return $student;
    }


    /**
     * レビュー可能な予約か確認
     */
    private function validateReviewableReservation(
        Reservation $reservation,
        Student $student
    ): void {
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
         * Status取得
         */
        $reservation->loadMissing('status');

        /*
         * completedのみ
         */
        if (
            $reservation->status->status_code
            !==
            'completed' &&
            $reservation->status->status_code
            !==
            'awaiting_result'
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
    }
}
