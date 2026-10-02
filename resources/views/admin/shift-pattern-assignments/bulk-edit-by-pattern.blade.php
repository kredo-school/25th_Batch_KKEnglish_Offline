@extends('layouts.app')

@section('title', 'Bulk Shift Pattern Assignment')

@section('content')

<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                Bulk Shift Pattern Assignment
            </h2>

            <div class="text-muted">
                Current Shift Pattern:
                <strong>
                    {{ $shiftPattern->pattern_name }}
                </strong>

                @if($shiftPattern->pattern_code)
                    <span class="ms-2">
                        ({{ $shiftPattern->pattern_code }})
                    </span>
                @endif
            </div>
        </div>

        <a href="{{ route('admin.shift-patterns.index') }}"
           class="btn btn-outline-secondary btn-sm">

            <i class="fa-solid fa-angles-left me-1"></i>
            Back

        </a>

    </div>


    {{-- Validation Error --}}
    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>Please check the following:</strong>

            <ul class="mb-0 mt-2">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- Current Pattern --}}
    <div class="card mb-4">

        <div class="card-header fw-semibold">

            Current Shift Pattern

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">

                    <div class="text-muted small">
                        Pattern Code
                    </div>

                    <div class="fw-semibold">
                        {{ $shiftPattern->pattern_code }}
                    </div>

                </div>

                <div class="col-md-4">

                    <div class="text-muted small">
                        Pattern Name
                    </div>

                    <div class="fw-semibold">
                        {{ $shiftPattern->pattern_name }}
                    </div>

                </div>

                <div class="col-md-4">

                    <div class="text-muted small">
                        Assigned Teachers
                    </div>

                    <div class="fw-semibold">
                        {{ $assignments->count() }}
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Bulk Update Form --}}
    <form method="POST"
          action="{{ route('admin.shift-pattern-assignments.bulk-update-by-pattern', [
              'shiftPattern' => $shiftPattern->id
          ]) }}">

        @csrf
        @method('PUT')


        {{-- Teacher List --}}
        <div class="card mb-4">

            <div class="card-header">

                <div class="d-flex justify-content-between align-items-center">

                    <span class="fw-semibold">
                        Teachers
                    </span>

                    <div>

                        <button type="button"
                                class="btn btn-outline-secondary btn-sm"
                                id="select-all">

                            Select All

                        </button>

                        <button type="button"
                                class="btn btn-outline-secondary btn-sm"
                                id="clear-all">

                            Clear All

                        </button>

                    </div>

                </div>

            </div>


            <div class="card-body">

                @if($assignments->isEmpty())

                    <div class="text-muted">
                        No teachers are currently assigned to this Shift Pattern.
                    </div>

                @else
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">選択</th>
                                <th>Teacher</th>
                                <th>Weekday</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $weekdayLabels = [
                                    0 => 'Sunday',
                                    1 => 'Monday',
                                    2 => 'Tuesday',
                                    3 => 'Wednesday',
                                    4 => 'Thursday',
                                    5 => 'Friday',
                                    6 => 'Saturday',
                                ];

                                $groupedAssignments = $assignments->groupBy('teacher_id');
                            @endphp

                            @foreach ($groupedAssignments as $teacherId => $teacherAssignments)

                                @php
                                    $firstAssignment = $teacherAssignments->first();

                                    $teacherName = trim(
                                        ($firstAssignment->teacher->user->first_name ?? '') . ' ' .
                                        ($firstAssignment->teacher->user->last_name ?? '')
                                    );

                                    if ($teacherName === '') {
                                        $teacherName = 'Unknown Teacher';
                                    }
                                @endphp

                                {{-- 先生名 --}}
                                <tr>
                                    <td colspan="5"
                                        style="
                                            border-top: 2px solid #000;
                                            border-left: 2px solid #000;
                                            border-right: 2px solid #000;
                                            padding: 10px;
                                            background-color: #f8f9fa;
                                        ">

                                        <div class="form-check">
                                            <input
                                                type="checkbox"
                                                class="form-check-input teacher-select"
                                                data-teacher-id="{{ $teacherId }}"
                                                id="teacher-{{ $teacherId }}"
                                            >

                                            <label
                                                class="form-check-label fw-bold"
                                                for="teacher-{{ $teacherId }}"
                                            >
                                                {{ $teacherName }}
                                            </label>
                                        </div>

                                    </td>
                                </tr>

                                {{-- 曜日 --}}
                                @foreach ($teacherAssignments as $assignment)
                                    @php
                                        $weekday = $assignment->weekday;
                                        if (is_numeric($weekday)) {
                                            $weekdayLabel =
                                                $weekdayLabels[(int) $weekday]
                                                ?? $weekday;
                                        } else {
                                            $weekdayLabel = ucfirst($weekday);
                                        }
                                    @endphp

                                    <tr>
                                        <td style="border-left: 2px solid #000;">
                                            <input
                                                type="checkbox"
                                                class="form-check-input assignment-checkbox teacher-{{ $teacherId }}"
                                                name="assignment_ids[]"
                                                value="{{ $assignment->id }}"
                                                data-teacher-id="{{ $teacherId }}"
                                                {{ in_array(
                                                    $assignment->id,
                                                    old('assignment_ids', []),
                                                    true
                                                ) ? 'checked' : '' }}
                                            >
                                        </td>

                                        <td>
                                            {{ $teacherName }}
                                        </td>

                                        <td>
                                            {{ $weekdayLabel }}
                                        </td>

                                        <td>
                                            {{ $assignment->start_date
                                                ? \Carbon\Carbon::parse($assignment->start_date)->format('Y-m-d')
                                                : '-' }}
                                        </td>

                                        <td style="border-right: 2px solid #000;">
                                            {{ $assignment->end_date
                                                ? \Carbon\Carbon::parse($assignment->end_date)->format('Y-m-d')
                                                : '-' }}
                                        </td>
                                    </tr>

                                @endforeach

                                {{-- 先生ごとの下側の太枠 --}}
                                <tr>
                                    <td colspan="5"
                                        style="
                                            border-bottom: 2px solid #000;
                                            border-left: 2px solid #000;
                                            border-right: 2px solid #000;
                                            height: 5px;
                                            padding: 0;
                                        ">
                                    </td>
                                </tr>

                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- New Shift Pattern --}}
        <div class="card mb-4">

            <div class="card-header fw-semibold">
                Change Shift Pattern
            </div>

            <div class="card-body">

                {{-- Shift Pattern --}}
                <div class="mb-3">

                    <label for="shift_pattern_id"
                        class="form-label fw-semibold">

                        New Shift Pattern

                    </label>

                    <select name="shift_pattern_id"
                            id="shift_pattern_id"
                            class="form-select"
                            required>

                        <option value="">
                            -- Select Shift Pattern --
                        </option>

                        @foreach($patterns as $pattern)

                            <option value="{{ $pattern->id }}"
                                {{ old('shift_pattern_id') == $pattern->id
                                    ? 'selected'
                                    : '' }}>

                                {{ $pattern->pattern_name }}

                                @if($pattern->pattern_code)
                                    ({{ $pattern->pattern_code }})
                                @endif

                            </option>

                        @endforeach

                    </select>
                </div>

                {{-- Start Date --}}
                <div class="mb-3">

                    <label for="start_date"
                        class="form-label fw-semibold">

                        Start Date

                    </label>

                    <input type="date"
                        name="start_date"
                        id="start_date"
                        class="form-control"
                        value="{{ old('start_date', now()->toDateString()) }}"
                        min="{{ now()->toDateString() }}"
                        required>

                    <div class="form-text">
                        The Shift Pattern change will apply from this date.
                        Past dates cannot be changed.
                    </div>

                </div>

            </div>

        </div>
                    <div class="form-text">

                        Selected teachers will be changed to this Shift Pattern.

        </div>


        {{-- Buttons --}}
        <div class="d-flex justify-content-between">

            <a href="{{ route('admin.shift-patterns.index') }}"
               class="btn btn-outline-secondary">

                <i class="fa-solid fa-xmark me-1"></i>
                Cancel

            </a>


            <button type="submit"
                    class="btn btn-primary"
                    id="update-button">

                <i class="fa-solid fa-save me-1"></i>
                Update Selected Teachers

            </button>

        </div>

    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const selectAllButton =
        document.getElementById('select-all');

    const clearAllButton =
        document.getElementById('clear-all');

    const form =
        document.querySelector('form');

    const updateButton =
        document.getElementById('update-button');

    const teacherCheckboxes =
        document.querySelectorAll('.teacher-select');

    const assignmentCheckboxes =
        document.querySelectorAll('.assignment-checkbox');

    /*
    |--------------------------------------------------------------------------
    | Select All
    |--------------------------------------------------------------------------
    */
    selectAllButton?.addEventListener('click', function () {
        assignmentCheckboxes.forEach(function (checkbox) {
            checkbox.checked = true;
        });

        // 先生のチェックもすべてON
        teacherCheckboxes.forEach(function (teacherCheckbox) {
            teacherCheckbox.checked = true;
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Clear All
    |--------------------------------------------------------------------------
    */
    clearAllButton?.addEventListener('click', function () {
        assignmentCheckboxes.forEach(function (checkbox) {
            checkbox.checked = false;
        });

        // 先生のチェックもすべてOFF
        teacherCheckboxes.forEach(function (teacherCheckbox) {
            teacherCheckbox.checked = false;
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Teacher Select
    |--------------------------------------------------------------------------
    |
    | 先生にチェックを入れる
    | ↓
    | その先生の曜日をすべてチェック
    |
    */
    teacherCheckboxes.forEach(function (teacherCheckbox) {
        teacherCheckbox.addEventListener('change', function () {

            const teacherId =
                this.dataset.teacherId;

            const assignments =
                document.querySelectorAll(
                    '.assignment-checkbox[data-teacher-id="' +
                    teacherId +
                    '"]'
                );
            assignments.forEach(function (assignment) {
                assignment.checked =
                    teacherCheckbox.checked;
            });
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Individual Assignment Select
    |--------------------------------------------------------------------------
    |
    | 曜日を1つでも外す
    | ↓
    | 先生のチェックをOFF
    |
    | すべての曜日がチェックされる
    | ↓
    | 先生のチェックをON
    |
    */
    assignmentCheckboxes.forEach(function (assignmentCheckbox) {
        assignmentCheckbox.addEventListener('change', function () {

            const teacherId =
                this.dataset.teacherId;

            const teacherCheckbox =
                document.querySelector(
                    '.teacher-select[data-teacher-id="' +
                    teacherId +
                    '"]'
                );

            const assignments =
                document.querySelectorAll(
                    '.assignment-checkbox[data-teacher-id="' +
                    teacherId +
                    '"]'
                );

            const allChecked =
                Array.from(assignments).every(function (assignment) {
                    return assignment.checked;
                });
            teacherCheckbox.checked = allChecked;
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Submit Confirmation
    |--------------------------------------------------------------------------
    */
    form?.addEventListener('submit', function (event) {

        const selected =
            document.querySelectorAll(
                '.assignment-checkbox:checked'
            );

        const pattern =
            document.getElementById(
                'shift_pattern_id'
            );

        /*
        | 1件も選択されていない
        */
        if (selected.length === 0) {
            event.preventDefault();
            alert(
                'Please select at least one assignment.'
            );
            return;
        }

        /*
        | Shift Patternが選択されていない
        */
        if (!pattern.value) {
            event.preventDefault();
            alert(
                'Please select a Shift Pattern.'
            );
            return;
        }

        /*
        | 確認ダイアログ
        */
        const message =
            selected.length +
            ' assignment(s) will be changed to the selected Shift Pattern.\n\n' +
            'Continue?';

        if (!confirm(message)) {
            event.preventDefault();
            return;
        }
    });
});
</script>

@endsection
