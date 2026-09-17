<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\SeasonPeriod;
use App\Models\ExpectedReservationSetting;

class ScheduleMatrixController extends Controller
{
    /**
     * 1枚目：週間マトリクス画面 (9:00 - 21:00)
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $start = Carbon::parse($startDate);
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = $start->copy()->addDays($i);
        }

        $hours = range(9, 21); // 9:00 ~ 21:00

        // 該当期間の予約を取得
        $reservations = DB::table('reservations')
            ->whereNull('cancelled_at')
            ->whereBetween('start_at', [$start->copy()->startOfDay(), $dates[6]->copy()->endOfDay()])
            ->get(['start_at']);

        // 該当期間のスケジュール枠（シフト）を取得
        $schedules = DB::table('teacher_schedules')
            ->whereBetween('available_date', [$start->toDateString(), $dates[6]->toDateString()])
            ->whereNotIn('status', ['cancelled'])
            ->get(['available_date', 'start_time', 'end_time']);

        $matrix = [];
        $totalCapacity = 0;
        $totalBooked = 0;

        foreach ($dates as $date) {
            $dateStr = $date->toDateString();
            foreach ($hours as $hour) {
                $hourStr = sprintf('%02d', $hour);

                // この時間帯に開始する予約数をカウント
                $booked = $reservations->filter(function($r) use ($dateStr, $hourStr) {
                    $dt = Carbon::parse($r->start_at);
                    return $dt->toDateString() === $dateStr && $dt->format('H') === $hourStr;
                })->count();

                // この時間帯のレッスン可能コマ数（Capacity）をカウント（1コマ=30分想定）
                $capacity = 0;
                $hourStart = Carbon::parse("$dateStr $hourStr:00:00");
                $hourEnd = $hourStart->copy()->addHour();

                foreach ($schedules->where('available_date', $dateStr) as $sch) {
                    $schStart = Carbon::parse("$dateStr {$sch->start_time}");
                    $schEnd = Carbon::parse("$dateStr {$sch->end_time}");

                    $intersectStart = $schStart->max($hourStart);
                    $intersectEnd = $schEnd->min($hourEnd);

                    if ($intersectStart->lt($intersectEnd)) {
                        $minutes = $intersectStart->diffInMinutes($intersectEnd);
                        $capacity += intdiv($minutes, 30);
                    }
                }

                $totalBooked += $booked;
                $totalCapacity += $capacity;

                // 稼働状況の判定ロジック
                $percentage = $capacity > 0 ? ($booked / $capacity) * 100 : 0;
                $bgColor = '';
                $textColor = 'text-dark';

                if ($capacity === 0) {
                    $status = 'No Slot';
                    $bgColor = '#e9ecef'; // グレー
                } elseif ($percentage < 80) {
                    $status = 'Sufficient';
                    $bgColor = '#cce5ff'; // 薄い青
                } elseif ($percentage < 90) {
                    $status = 'Caution';
                    $bgColor = '#fff3cd'; // 黄色
                } elseif ($percentage < 95) {
                    $status = 'Risk of Insufficiency';
                    $bgColor = '#fd7e14'; // オレンジ
                    $textColor = 'text-white';
                } else {
                    $status = 'Insufficient';
                    $bgColor = '#dc3545'; // 赤
                    $textColor = 'text-white';
                }

                $matrix[$hour][$dateStr] = [
                    'booked' => $booked,
                    'capacity' => $capacity,
                    'status' => $status,
                    'bg' => $bgColor,
                    'text' => $textColor,
                ];
            }
        }

        return view('admin.schedules.matrix', compact('dates', 'hours', 'matrix', 'start', 'totalCapacity', 'totalBooked'));
    }

    /**
     * 2枚目：日付指定の詳細画面 (9:00 - 22:00, 30分間隔)
     */
    public function details(Request $request)
    {
        $dateStr = $request->input('date', Carbon::today()->toDateString());
        $date = Carbon::parse($dateStr);
        
        $intervals = [];
        $current = $date->copy()->setTime(9, 0);
        $end = $date->copy()->setTime(22, 0); // 22:00まで
        
        // シフト枠（配置人数用）の取得
        $schedules = DB::table('teacher_schedules')
            ->where('available_date', $dateStr)
            ->whereNotIn('status', ['cancelled'])
            ->get(['start_time', 'end_time']);

        // ========== 追加・変更箇所 ==========
        // 1. 日付から「平日 (weekday)」か「週末 (weekend)」かを自動判定
        $dayType = $date->isWeekend() ? 'weekend' : 'weekday';
        
        // 2. データベースの「期間設定（SeasonPeriod）」を確認して時期を判定する
        $seasonRecord = SeasonPeriod::where('start_date', '<=', $dateStr)
            ->where('end_date', '>=', $dateStr)
            ->first();
            
        // 該当する期間設定があればその時期を、設定がなければデフォルトで 'normal' を使用する
        $season = $seasonRecord ? $seasonRecord->season_type : 'normal';

        // 3. データベースから該当する予想予約数の設定を取得し、時間(H:i)をキーにした配列にする
        $expectedSettings = ExpectedReservationSetting::where('season_type', $season)
            ->where('day_type', $dayType)
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->target_time)->format('H:i');
            });
        // ===================================

        while ($current->lt($end)) {
            $slotStart = $current->copy();
            $slotEnd = $current->copy()->addMinutes(30);
            $timeStr = $slotStart->format('H:i');
            
            // 設定テーブルからこの時間の「予想予約数」を取得（設定されていなければ0）
            $booked = isset($expectedSettings[$timeStr]) ? $expectedSettings[$timeStr]->expected_count : 0;
            
            // 配置人数（その30分枠に勤務している講師数）
            $capacity = 0;
            foreach ($schedules as $sch) {
                $schStart = Carbon::parse("$dateStr {$sch->start_time}");
                $schEnd = Carbon::parse("$dateStr {$sch->end_time}");
                if ($schStart->lte($slotStart) && $schEnd->gte($slotEnd)) {
                    $capacity++;
                }
            }
            
            $spare = 2; // 予備人数（要件に応じて固定値、または今後DB化可能）
            $required = $booked + $spare; // 必要講師数 = 予想予約数 + 予備人数
            $diff = $capacity - $required; // 過不足
            
            $intervals[] = [
                'start' => $slotStart->format('H:i'),
                'end' => $slotEnd->format('H:i'),
                'booked' => $booked,
                'spare' => $spare,
                'required' => $required,
                'capacity' => $capacity,
                'diff' => $diff,
                'status' => $diff >= 0 ? '余裕' : '不足',
            ];
            
            $current->addMinutes(30);
        }
        
        return view('admin.schedules.matrix_details', compact('date', 'intervals'));
    }
}
