<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpectedReservationSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExpectedReservationSettingController extends Controller
{
    public function edit()
    {
        // 既存の設定をすべて取得
        $records = ExpectedReservationSetting::all();

        // データを扱いやすいように配列に整理
        $settings = [];
        foreach ($records as $record) {
            $time = Carbon::parse($record->target_time)->format('H:i');
            $settings[$record->season_type][$record->day_type][$time] = $record->expected_count;
        }

        // 時間帯のリスト（9:00 〜 21:30）を作成
        $times = [];
        $current = Carbon::createFromTime(9, 0);
        $end = Carbon::createFromTime(22, 0);
        while ($current->lt($end)) {
            $times[] = $current->format('H:i');
            $current->addMinutes(30);
        }

        $seasons = [
            'normal' => '通常期 (Normal)',
            'busy' => '繁忙期 (Busy)',
            'quiet' => '閑散期 (Quiet)',
        ];

        return view('admin.expected-reservations.edit', compact('settings', 'times', 'seasons'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'expected' => ['required', 'array'],
        ]);

        // 保存処理
        foreach ($data['expected'] as $season => $dayTypes) {
            foreach ($dayTypes as $dayType => $times) {
                foreach ($times as $time => $count) {
                    ExpectedReservationSetting::updateOrCreate(
                        [
                            'season_type' => $season,
                            'day_type' => $dayType,
                            'target_time' => $time . ':00',
                        ],
                        [
                            'expected_count' => (int) $count,
                        ]
                    );
                }
            }
        }

        return redirect()->route('admin.expected-reservations.edit')->with('status', '予想予約数の設定を保存しました。');
    }
}
