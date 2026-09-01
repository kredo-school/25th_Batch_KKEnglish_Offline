<?php

namespace App\Services;

use App\Support\AppTime;
use Carbon\CarbonImmutable;
use DomainException;

class BookingPolicyService
{
    public function assertReservable(
        CarbonImmutable $nowUtc,
        CarbonImmutable $startUtc,
        CarbonImmutable $endUtc,
        string $lessonType
    ): void {
        $nowBiz   = $nowUtc->setTimezone(AppTime::BUSINESS_TZ);
        $startBiz = $startUtc->setTimezone(AppTime::BUSINESS_TZ);
        $endBiz   = $endUtc->setTimezone(AppTime::BUSINESS_TZ);

        if ($nowBiz->gt($startBiz->subHours(2))) {
            throw new DomainException('予約は開始2時間前までです。');
        }

        $duration = $startBiz->diffInMinutes($endBiz);

        if ($lessonType === 'online') {
            if (!($duration === 30 && in_array($startBiz->minute, [0, 30], true))) {
                throw new DomainException('オンラインは30分、開始は00分または30分のみです。');
            }
            return;
        }

        if ($lessonType === 'in_person') {
            if (!($duration === 60 && $startBiz->minute === 0)) {
                throw new DomainException('対面は60分、開始は毎時00分のみです。');
            }
            return;
        }

        throw new DomainException('不正なlesson_typeです。');
    }
}
