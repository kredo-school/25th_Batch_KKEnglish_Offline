<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignShiftPatternRequest;
use App\Models\ShiftPattern;
use App\Models\Teacher;
use App\Services\Admin\ShiftPatternAssignmentService;
use App\Services\Admin\GenerateTeacherSchedulesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShiftPatternAssignmentController extends Controller
{

    public function create(): View
    {
        $patterns = ShiftPattern::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('id')
            ->get(['id', 'pattern_name', 'pattern_code']);

        $teachers = Teacher::query()
            ->orderBy('id')
            ->get(['id', 'name']);

        return view('admin.shift-pattern-assignments.create', compact('patterns', 'teachers'));
    }

    public function store(
    \App\Http\Requests\Admin\AssignShiftPatternRequest $request,
    \App\Services\Admin\ShiftPatternAssignmentService $assignmentService,
    \App\Services\Admin\GenerateTeacherSchedulesService $generateService
) {
    $data = $request->validated(); // ← 必須

    try {
        $result = $assignmentService->assign($data, auth()->id());

        return redirect()->back()->with('status', 'assigned');
    } catch (\DomainException $e) { // ← 先頭に \ を付けて確実化
        return redirect()->back()->withErrors([
            'assign' => $e->getMessage(),
        ])->withInput();
    }
}
}
