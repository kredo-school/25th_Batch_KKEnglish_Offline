<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonStationOverride;
use App\Models\Reservation;
use App\Models\Station;
use App\Models\Teacher;
use Illuminate\Http\Request;

class LessonStationOverrideController extends Controller
{
    /**
     * Station Override一覧
     */
    public function index()
    {
        // Teacher一覧
        $teachers = Teacher::with('user')
            ->get()
            ->sortBy(function ($teacher) {
                return strtolower(
                    ($teacher->user->first_name ?? '') . ' ' .
                    ($teacher->user->last_name ?? '')
                );
            })
            ->values();

        // Station一覧
        $stations = Station::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Lesson一覧
        $reservations = Reservation::with([
            'student.user',
            'teacher.user',
        ])
            ->whereNull('cancelled_at')
            ->where('start_at', '>', now())
            ->orderBy('start_at')
            ->get();

        // 既存Override
        $overrides = LessonStationOverride::with([
            'reservation.teacher.user',
            'reservation.student.user',
            'station',
        ])
            ->orderByDesc('created_at')
            ->get();

        return view(
            'admin.teacher-station-assignments.overrides.index',
            compact(
                'teachers',
                'stations',
                'reservations',
                'overrides'
            )
        );
    }

    /**
     * Override登録
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => [
                'required',
            ],

            'reservation_id' => [
                'required',
                'exists:reservations,id',
            ],

            'station_id' => [
                'required',
                'exists:stations,id',
            ],
        ]);


        // 選択されたTeacherの予約か確認
        $reservation = Reservation::where('id', $validated['reservation_id'])
            ->where('teacher_id', $validated['teacher_id'])
            ->whereNull('cancelled_at')
            ->firstOrFail();


        // 同じ予約にOverrideがあれば更新
        // なければ新規作成
        LessonStationOverride::updateOrCreate(
            [
                'reservation_id' => $reservation->id,
            ],
            [
                'station_id' => $validated['station_id'],
            ]
        );


        return redirect()
            ->route('admin.teacher-station-assignments.overrides.index')
            ->with(
                'success',
                'Station Overrideを登録しました。'
            );
    }


    /**
     * Override削除
     */
    public function destroy(
        LessonStationOverride $lessonStationOverride
    ) {
        $lessonStationOverride->delete();


        return redirect()
            ->route('admin.teacher-station-assignments.overrides.index')
            ->with(
                'success',
                'Station Overrideを削除しました。'
            );
    }
}