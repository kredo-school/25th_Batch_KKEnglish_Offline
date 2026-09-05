<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShiftPatternUpsertRequest;
use App\Models\ShiftPattern;
use App\Services\Admin\ShiftPatternAdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class ShiftPatternController extends Controller
{
    public function index()
    {
        $patterns = \App\Models\ShiftPattern::query()
            ->withCount('teachers')
            ->latest('id')
            ->paginate(20);

        return view('admin.shift-patterns.index', compact('patterns'));
    }

    public function create(): View
    {
        return view('admin.shift-patterns.create');
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
            ->route('admin.shift-patterns.edit', $pattern)
            ->with('status', 'Successfully created the shift pattern.');
    }

    public function edit(ShiftPattern $shiftPattern): View
    {
        $shiftPattern->load(['rules', 'breaks']);
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
        $service->delete($shiftPattern);

        return redirect()
            ->route('admin.shift-patterns.index')
            ->with('status', 'Successfully deleted the shift pattern.');
    }
}
