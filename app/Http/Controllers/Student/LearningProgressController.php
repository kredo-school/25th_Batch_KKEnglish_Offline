<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class LearningProgressController extends Controller
{
    public function index(): View
    {
        $student = Auth::user()->student;

        abort_unless(
            $student,
            403,
            '生徒ユーザーではありません。'
        );

        // ログイン中の生徒の、受講完了した予約のみ取得
        $completedLessons = Reservation::query()
            ->with('material')
            ->where('student_id', $student->id)
            ->whereHas('status', function ($query) {
                $query->where('status_code', 'completed');
            })
            ->get();

        // 累計受講回数
        $totalLessons = $completedLessons->count();

        // 今月の範囲：月初以上、翌月の月初未満
        $monthStart = CarbonImmutable::now()->startOfMonth();
        $nextMonthStart = $monthStart->addMonth();

        // 今月に開始した、受講完了済みのレッスン数
        $thisMonthLessons = $completedLessons
            ->filter(function ($reservation) use (
                $monthStart,
                $nextMonthStart
            ) {
                $start = CarbonImmutable::parse(
                    $reservation->start_at
                );

                return $start->gte($monthStart)
                    && $start->lt($nextMonthStart);
            })
            ->count();

        // 学習時間：完了した予約の合計時間
        $studyMinutes = $completedLessons
            ->sum(function ($reservation) {
                $start = CarbonImmutable::parse(
                    $reservation->start_at
                );

                $end = CarbonImmutable::parse(
                    $reservation->end_at
                );

                return max(
                    0,
                    $start->diffInMinutes($end, false)
                );
            });

        $studyHours = round($studyMinutes / 60, 1);

        // 現在のレベル：生徒に保存された値を表示
        $levelDefinitions = [
            'A1' => [
                'label' => 'Beginner',
                'description' => 'You can use basic words and phrases.',
            ],
            'A2' => [
                'label' => 'Elementary',
                'description' => 'You can communicate in simple everyday situations.',
            ],
            'B1' => [
                'label' => 'Intermediate',
                'description' => 'You can handle familiar everyday conversations.',
            ],
            'B2' => [
                'label' => 'Upper Intermediate',
                'description' => 'You can discuss a wide range of topics.',
            ],
            'C1' => [
                'label' => 'Advanced',
                'description' => 'You can express yourself fluently and flexibly.',
            ],
            'C2' => [
                'label' => 'Proficient',
                'description' => 'You can communicate precisely in complex situations.',
            ],
        ];

        $savedLevel = strtoupper(
            trim((string) $student->level)
        );

        $levelInfo = $levelDefinitions[$savedLevel] ?? null;

        $currentLevel = $levelInfo ? $savedLevel : '—';

        $levelLabel = $levelInfo['label']
            ?? 'Not set';

        $levelDescription = $levelInfo['description']
            ?? 'Please set your level in your profile.';

        // 教材ごとの受講回数を数え、最も多い教材を取得
        $mostStudiedGroup = $completedLessons
            ->filter(function ($reservation) {
                return $reservation->material !== null;
            })
            ->groupBy('material_id')
            ->sortByDesc(function ($lessons) {
                return $lessons->count();
            })
            ->first();

        $material = $mostStudiedGroup
            ? $mostStudiedGroup->first()->material
            : null;

        $mostStudiedMaterialName = $material?->name
            ?? 'No lessons completed yet';

        $mostStudiedMaterialLessons = $mostStudiedGroup
            ? $mostStudiedGroup->count()
            : 0;

        // 次の25回区切りの目標
        $nextMilestone = (
            intdiv($totalLessons, 25) + 1
        ) * 25;

        $remainingLessons = $nextMilestone - $totalLessons;

        $lessonWord = $remainingLessons === 1
            ? 'lesson'
            : 'lessons';

        $monkeyMessage = $totalLessons === 0
            ? 'Take your first lesson and start your journey!'
            : "Great job! {$remainingLessons} more {$lessonWord} "
                . "to reach {$nextMilestone}!";

        return view(
            'students.progress.index',
            compact(
                'student',
                'totalLessons',
                'thisMonthLessons',
                'studyHours',
                'currentLevel',
                'levelLabel',
                'levelDescription',
                'mostStudiedMaterialName',
                'mostStudiedMaterialLessons',
                'monkeyMessage'
            )
        );
    }
}
