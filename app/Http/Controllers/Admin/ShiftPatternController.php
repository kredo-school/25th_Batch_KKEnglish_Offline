<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShiftPatternUpsertRequest;
use App\Models\ShiftPattern;
use App\Services\Admin\ShiftPatternAdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Teacher;
use DomainException;

class ShiftPatternController extends Controller
{
    public function index()
    {
        $today = today();

        $patterns = ShiftPattern::query()
            ->withCount([
                'assignments as teachers_count' => function ($query) use ($today) {
                    $query
                        ->whereDate('start_date', '<=', $today)
                        ->where(function ($q) use ($today) {
                            $q->whereNull('end_date')
                                ->orWhereDate('end_date', '>=', $today);
                        })
                        ->select(DB::raw('COUNT(DISTINCT teacher_id)'));
                },

                // 過去シフトが存在するか
                'assignments as past_assignments_count' => function ($query) use ($today) {
                    $query->whereDate('end_date', '<', $today);
                },
            ])
            ->latest('id')
            ->paginate(10);

        return view('admin.shift-patterns.index', compact('patterns'));
    }

    public function create(Request $request): View
    {
        $patterns = ShiftPattern::query()
            ->orderBy('id', 'desc')
            ->get(['id', 'pattern_name', 'pattern_code']);
        $teachers = Teacher::query()
            ->with(['user:id,first_name,last_name'])
            ->orderBy('id', 'desc')
            ->get(['id', 'user_id']);
        return view('admin.shift-patterns.create', [
            'patterns' => $patterns,
            'teachers' => $teachers,
            'defaultPatternId' => $request->integer('pattern_id') ?: null,
        ]);
    }

    public function store(
        ShiftPatternUpsertRequest $request,
        ShiftPatternAdminService $service
    ): RedirectResponse {
        $pattern = $service->upsert(
            $request->validated(),
            null,
            (int) $request->user()->id
        );

        return redirect()
            ->route('admin.shift-patterns.index', $pattern)
            ->with('status', 'Successfully created the shift pattern.');
    }

    public function edit(ShiftPattern $shiftPattern): View
    {
        $shiftPattern->load(['breaks']);
        return view('admin.shift-patterns.edit', compact('shiftPattern'));
    }

    public function update(
        ShiftPatternUpsertRequest $request,
        ShiftPattern $shiftPattern,
        ShiftPatternAdminService $service
    ): RedirectResponse {
        $service->upsert(
            $request->validated(),
            $shiftPattern,
            (int) $request->user()->id
        );

        return back()->with('status', 'Successfully updated the shift pattern.');
    }

    public function destroy(ShiftPattern $shiftPattern, ShiftPatternAdminService $service): RedirectResponse
    {
        try {
            $service->delete($shiftPattern);

            return redirect()
                ->route('admin.shift-patterns.index')
                ->with('status', 'Successfully deleted the shift pattern.');

        } catch (DomainException $e) {
            return redirect()
                ->route('admin.shift-patterns.index')
                ->with('error', $e->getMessage());
        }
    }
}
