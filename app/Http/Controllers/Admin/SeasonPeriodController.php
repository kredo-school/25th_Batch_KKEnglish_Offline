<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeasonPeriod;
use Illuminate\Http\Request;

class SeasonPeriodController extends Controller
{
    public function index()
    {
        $periods = SeasonPeriod::orderBy('start_date', 'asc')->get();
        return view('admin.season-periods.index', compact('periods'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'season_type' => ['required', 'in:busy,normal,quiet'],
        ]);

        SeasonPeriod::create($data);

        return redirect()->route('admin.season-periods.index')->with('status', '期間設定を追加しました。');
    }

    public function destroy(SeasonPeriod $season_period)
    {
        $season_period->delete();
        return redirect()->route('admin.season-periods.index')->with('status', '期間設定を削除しました。');
    }
}