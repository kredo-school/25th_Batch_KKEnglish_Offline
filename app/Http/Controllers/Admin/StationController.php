<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Station;
use Illuminate\Http\Request;

class StationController extends Controller
{
    /**
     * Station一覧
     */
    public function index()
    {
        $today = today();

        $stations = Station::query()
            ->with([
                'teacherStationAssignments' => function ($query) use ($today) {
                    $query
                        ->where(function ($q) use ($today) {

                            // 現在有効
                            $q->where(function ($q2) use ($today) {
                                $q2->whereDate('start_date', '<=', $today)
                                    ->where(function ($q3) use ($today) {
                                        $q3->whereNull('end_date')
                                            ->orWhereDate('end_date', '>=', $today);
                                    });
                            })

                            // または未来に開始
                            ->orWhereDate('start_date', '>', $today);
                        })
                        ->with('teacher.user')
                        ->orderBy('start_date');
                },
            ])
            ->orderBy('id')
            ->get();

        return view('admin.stations.index', compact('stations'));
    }

    /**
     * Station作成画面
     */
    public function create()
    {
        return view('admin.stations.create');
    }

    /**
     * Station登録
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:stations,code'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Station::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.stations.index', ['menu' => 'station'])
            ->with('success', 'Stationを追加しました。');
    }

    /**
     * Station編集画面
     */
    public function edit(Station $station)
    {
        return view('admin.stations.edit', compact('station'));
    }

    /**
     * Station更新
     */
    public function update(Request $request, Station $station)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:stations,code,' . $station->id,
            ],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $station->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.stations.index', ['menu' => 'station'])
            ->with('success', 'Stationを更新しました。');
    }

    /**
     * Station削除
     */
    public function destroy(Station $station)
    {
        // Teacher Station Assignmentで使用中の場合は削除しない
        if ($station->teacherStationAssignments()->exists()) {
            return redirect()
                ->route('admin.stations.index', ['menu' => 'station'])
                ->with('error', 'このStationはTeacherの割当に使用されているため削除できません。');
        }

        // Lesson単位のStation変更で使用中の場合も削除しない
        if ($station->lessonStationOverrides()->exists()) {
            return redirect()
                ->route('admin.stations.index', ['menu' => 'station'])
                ->with('error', 'このStationはレッスンで使用されているため削除できません。');
        }

        $station->delete();

        return redirect()
            ->route('admin.stations.index', ['menu' => 'station'])
            ->with('success', 'Stationを削除しました。');
    }
}
