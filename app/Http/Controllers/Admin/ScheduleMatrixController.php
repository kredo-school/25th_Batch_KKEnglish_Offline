<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\SeasonPeriod;
use App\Models\ExpectedReservationSetting;
use App\Models\Station;
use App\Models\TeacherStationAssignment;

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

        // $stations = Station::where('is_active', true)
        //     ->orderBy('name')
        //     ->get();

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

        return view('admin.operational-status.matrix', compact('dates', 'hours', 'matrix', 'start', 'totalCapacity', 'totalBooked'));
    }

    /**
     * 2枚目：日付指定の詳細画面 (9:00 - 22:00, 30分間隔)
     */
    public function details(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | ① 表示対象の日付
        |--------------------------------------------------------------------------
        */
        $dateStr = $request->input('date', Carbon::today()->toDateString());
        $date = Carbon::parse($dateStr);

        /*
        |--------------------------------------------------------------------------
        | ② 30分単位の時間帯を作成
        |--------------------------------------------------------------------------
        */
        $intervals = [];
        $current = $date->copy()->setTime(9, 0);
        $end = $date->copy()->setTime(22, 0); // 22:00まで

        /*
        |--------------------------------------------------------------------------
        | ③ Teacher Shift
        |--------------------------------------------------------------------------
        |
        | teacher_schedules
        |
        | teacher_id
        | available_date
        | start_time
        | end_time
        | status
        |
        */
        $schedules = DB::table('teacher_schedules')
            ->where('available_date', $dateStr)
            ->whereNotIn('status', ['cancelled'])
            ->get(['teacher_id', 'start_time', 'end_time']);

        /*
        |--------------------------------------------------------------------------
        | ④ 既存のExpected Reservation分析
        |--------------------------------------------------------------------------
        */
        $dayType = $date->isWeekend()
            ? 'weekend'
            : 'weekday';

        $seasonRecord = SeasonPeriod::where(
                'start_date',
                '<=',
                $dateStr
            )
            ->where(
                'end_date',
                '>=',
                $dateStr
            )
            ->first();

        $season = $seasonRecord
            ? $seasonRecord->season_type
            : 'normal';


        $expectedSettings = ExpectedReservationSetting::where(
                'season_type',
                $season
            )
            ->where(
                'day_type',
                $dayType
            )
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse(
                    $item->target_time
                )->format('H:i');
            });

        /*
        |--------------------------------------------------------------------------
        | ⑤ 既存の30分単位のMatrixデータ
        |--------------------------------------------------------------------------
        */

        while ($current->lt($end)) {

            $slotStart = $current->copy();

            $slotEnd = $current
                ->copy()
                ->addMinutes(30);

            $timeStr = $slotStart->format('H:i');


            $booked = isset($expectedSettings[$timeStr])
                ? $expectedSettings[$timeStr]->expected_count
                : 0;


            $capacity = 0;

            foreach ($schedules as $sch) {

                $schStart = Carbon::parse(
                    "$dateStr {$sch->start_time}"
                );

                $schEnd = Carbon::parse(
                    "$dateStr {$sch->end_time}"
                );

                if (
                    $schStart->lte($slotStart)
                    &&
                    $schEnd->gte($slotEnd)
                ) {
                    $capacity++;
                }
            }


            $spare = 2;

            $required = $booked + $spare;

            $diff = $capacity - $required;


            $intervals[] = [

                'start' => $slotStart->format('H:i'),

                'end' => $slotEnd->format('H:i'),

                'booked' => $booked,

                'spare' => $spare,

                'required' => $required,

                'capacity' => $capacity,

                'diff' => $diff,

                'status' => $diff >= 0
                    ? '余裕'
                    : '不足',
            ];


            $current->addMinutes(30);
        }


        /*
        |--------------------------------------------------------------------------
        | ⑥ 全Active Stationを取得
        |--------------------------------------------------------------------------
        |
        | 予約が0件でもStationを表示する
        |
        */

        $stations = Station::where('is_active', true)
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | ⑦ Stationを最初に全部登録
        |--------------------------------------------------------------------------
        |
        | 予約がなくても、
        |
        | Station A
        | Station B
        | Station C
        |
        | のように表示するため。
        |
        */

        $locationTimeline = [];

        foreach ($stations as $station) {

            $locationTimeline[$station->id] = [

                'id' => $station->id,

                'name' => $station->name,

                'role' => 'Station',

                'blocks' => [],

                'shift_blocks' => [],
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | ⑧ 当日のTeacher Station Assignmentを取得
        |--------------------------------------------------------------------------
        |
        | start_date <= 対象日
        |
        | end_date が null
        | または
        | end_date >= 対象日
        |
        */

        $stationAssignments = TeacherStationAssignment::query()
            ->whereDate(
                'start_date',
                '<=',
                $dateStr
            )
            ->where(function ($query) use ($dateStr) {

                $query
                    ->whereNull('end_date')
                    ->orWhereDate(
                        'end_date',
                        '>=',
                        $dateStr
                    );
            })
            ->get([
                'teacher_id',
                'station_id',
            ]);


        /*
        |--------------------------------------------------------------------------
        | ⑨ Teacher → Station の対応表を作る
        |--------------------------------------------------------------------------
        */

        $stationByTeacher = $stationAssignments
            ->groupBy('teacher_id')
            ->map(function ($assignments) {

                /*
                * 同じTeacherに複数のAssignmentがある場合は
                * 現時点では最初のAssignmentを使用
                */

                return $assignments->first()->station_id;
            });


        /*
        |--------------------------------------------------------------------------
        | ⑩ 当日の予約を取得
        |--------------------------------------------------------------------------
        |
        | teacher_id
        | start_at
        | cancelled_at
        |
        */

        $reservations = DB::table('reservations')
            ->whereDate('start_at', $dateStr)
            ->whereNull('cancelled_at')
            ->get([
                'id',
                'teacher_id',
                'start_at',
            ]);


        /*
        |--------------------------------------------------------------------------
        | ⑪ Teacher名を取得
        |--------------------------------------------------------------------------
        */

        $teacherIds = $reservations
            ->pluck('teacher_id')
            ->filter()
            ->unique()
            ->values();


        $teacherNames = DB::table('teachers as t')
            ->join(
                'users as u',
                'u.id',
                '=',
                't.user_id'
            )
            ->whereIn('t.id', $teacherIds)
            ->get([
                't.id as teacher_id',
                'u.first_name',
                'u.last_name',
            ])
            ->keyBy('teacher_id');


        /*
        |--------------------------------------------------------------------------
        | ⑫ Reservation → Station に予約を追加
        |--------------------------------------------------------------------------
        |
        | 現段階では、
        |
        | TeacherのDefault Station
        |
        | を使用する。
        |
        */

        foreach ($reservations as $reservation) {

            if (!$reservation->teacher_id) {
                continue;
            }


            /*
            * Teacherに割り当てられたStationを取得
            */

            $stationId = $stationByTeacher->get(
                $reservation->teacher_id
            );


            /*
            * Stationがない場合は表示しない
            */

            if (!$stationId) {
                continue;
            }


            /*
            * StationがActiveでない場合も表示しない
            */

            if (!isset($locationTimeline[$stationId])) {
                continue;
            }


            /*
            * Teacher名
            */

            $teacher = $teacherNames->get(
                $reservation->teacher_id
            );


            $teacherName = trim(
                ($teacher?->first_name ?? '')
                . ' '
                . ($teacher?->last_name ?? '')
            );


            if ($teacherName === '') {
                $teacherName = 'Unknown Teacher';
            }


            /*
            * 開始時間
            */

            $startAt = Carbon::parse(
                $reservation->start_at
            );


            $startMinutes =
                $startAt->hour * 60
                + $startAt->minute;


            /*
            * 現在の予約システムは30分単位なので
            * 1予約 = 30分として表示
            */

            $durationMinutes = 30;

            $endAt = $startAt
                ->copy()
                ->addMinutes($durationMinutes);


            /*
            * Stationのblocksに追加
            */

            $locationTimeline[$stationId]['blocks'][] = [

                'reservation_id' => $reservation->id,

                'teacher_id' => $reservation->teacher_id,

                'title' => $teacherName,

                'teacher_name' => $teacherName,

                'start_time' => $startAt->format('H:i'),

                'end_time' => $endAt->format('H:i'),

                'start_minutes' => $startMinutes,

                'duration_minutes' => $durationMinutes,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | ⑬ StationごとのTeacher Shiftを追加
        |--------------------------------------------------------------------------
        |
        | Stationモードでも
        |
        | Station A
        |   └ John Smith のShift
        |
        | のように表示する。
        |
        */

        $scheduleTeacherIds = $schedules
            ->pluck('teacher_id')
            ->filter()
            ->unique()
            ->values();


        $shiftTeacherIds = $scheduleTeacherIds
            ->merge($stationByTeacher->keys())
            ->unique()
            ->values();


        $shiftTeacherNames = DB::table('teachers as t')
            ->join(
                'users as u',
                'u.id',
                '=',
                't.user_id'
            )
            ->whereIn('t.id', $shiftTeacherIds)
            ->get([
                't.id as teacher_id',
                'u.first_name',
                'u.last_name',
            ])
            ->keyBy('teacher_id');


        /*
        |--------------------------------------------------------------------------
        | ⑭ ShiftをStationに追加
        |--------------------------------------------------------------------------
        */

        foreach ($schedules as $schedule) {

            if (!$schedule->teacher_id) {
                continue;
            }


            /*
            * このTeacherが担当しているStation
            */

            $stationId = $stationByTeacher->get(
                $schedule->teacher_id
            );


            if (!$stationId) {
                continue;
            }


            if (!isset($locationTimeline[$stationId])) {
                continue;
            }


            /*
            * Teacher名
            */

            $teacher = $shiftTeacherNames->get(
                $schedule->teacher_id
            );


            $teacherName = trim(
                ($teacher?->first_name ?? '')
                . ' '
                . ($teacher?->last_name ?? '')
            );


            if ($teacherName === '') {
                $teacherName = 'Unknown Teacher';
            }


            /*
            * Shift開始・終了
            */

            $startTime = Carbon::parse(
                $schedule->start_time
            );

            $endTime = Carbon::parse(
                $schedule->end_time
            );


            $startMinutes =
                $startTime->hour * 60
                + $startTime->minute;


            $endMinutes =
                $endTime->hour * 60
                + $endTime->minute;


            /*
            * 深夜跨ぎ
            */

            if ($endMinutes < $startMinutes) {
                $endMinutes += 1440;
            }


            $durationMinutes =
                $endMinutes - $startMinutes;


            /*
            * StationにShiftを追加
            */

            $locationTimeline[$stationId]['shift_blocks'][] = [

                'teacher_id' => $schedule->teacher_id,

                'teacher_name' => $teacherName,

                'title' => $teacherName,

                'start_time' => $startTime->format('H:i'),

                'end_time' => $endTime->format('H:i'),

                'start_minutes' => $startMinutes,

                'duration_minutes' => $durationMinutes,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | ⑮ 配列のキーを0,1,2...に戻す
        |--------------------------------------------------------------------------
        */

        $locationTimeline = array_values(
            $locationTimeline
        );


        /*
        |--------------------------------------------------------------------------
        | ⑯ Teacher Timeline
        |--------------------------------------------------------------------------
        |
        | ここは現在のTeacher表示を壊さないため、
        | 今回は既存処理がある場合はそれを使用してください。
        |
        */

        $teacherTimeline = [];


        foreach ($schedules->groupBy('teacher_id') as $teacherId => $teacherSchedules) {

            $teacher = $shiftTeacherNames->get($teacherId);

            $teacherName = trim(
                ($teacher?->first_name ?? '')
                . ' '
                . ($teacher?->last_name ?? '')
            );

            if ($teacherName === '') {
                $teacherName = 'Unknown Teacher';
            }


            $shiftBlocks = [];

            foreach ($teacherSchedules as $schedule) {

                $startTime = Carbon::parse(
                    $schedule->start_time
                );

                $endTime = Carbon::parse(
                    $schedule->end_time
                );

                $startMinutes =
                    $startTime->hour * 60
                    + $startTime->minute;

                $endMinutes =
                    $endTime->hour * 60
                    + $endTime->minute;

                if ($endMinutes < $startMinutes) {
                    $endMinutes += 1440;
                }

                $shiftBlocks[] = [

                    'start_time' =>
                        $startTime->format('H:i'),

                    'end_time' =>
                        $endTime->format('H:i'),

                    'start_minutes' =>
                        $startMinutes,

                    'duration_minutes' =>
                        $endMinutes - $startMinutes,
                ];
            }


            $teacherTimeline[] = [

                'id' => $teacherId,

                'name' => $teacherName,

                'role' => 'Instructor',

                'blocks' => [],

                'shift_blocks' => $shiftBlocks,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | ⑰ Viewへ渡す
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.operational-status.matrix_details',
            compact(
                'date',
                'intervals',
                'teacherTimeline',
                'locationTimeline'
            )
        );
    }
}
