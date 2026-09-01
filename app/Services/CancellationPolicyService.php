<?php

namespace App\Services;

use App\Support\AppTime;
use Carbon\CarbonImmutable;

class CancellationPolicyService
{
    public function penaltyRate(CarbonImmutable $cancelAtUtc, CarbonImmutable $lessonStartUtc): float
    {
        $cancelBiz = $cancelAtUtc->setTimezone(AppTime::BUSINESS_TZ);
        $startBiz  = $lessonStartUtc->setTimezone(AppTime::BUSINESS_TZ);

        $minutes = $cancelBiz->diffInMinutes($startBiz, false);

        if ($minutes >= 12 * 60) return 0.0;
        if ($minutes >= 2 * 60)  return 0.5;
        return 1.0;
    }

    public function penaltyPoints(int $basePoints, float $rate): int
    {
        return (int) floor($basePoints * $rate); // 丸め仕様は確認余地あり
    }
}
