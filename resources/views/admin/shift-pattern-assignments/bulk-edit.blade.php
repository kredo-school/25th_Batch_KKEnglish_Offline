@extends('layouts.app')

@section('content')
<div class="container py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Bulk Edit Teacher Assignments</h1>

        {{-- <a href="{{ route('admin.shift-pattern-assignments.index', ['menu' => 'schedule']) }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-angles-left"></i> Back
        </a> --}}
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <div class="fw-bold">Teacher</div>
            <div class="fs-5">
                {{ trim(($teacher->user->first_name ?? '') . ' ' . ($teacher->user->last_name ?? '')) ?: ('Teacher #' . $teacher->id) }}
            </div>
            <div class="text-muted small">
                Teacher ID: {{ $teacher->id }}
            </div>
        </div>
    </div>

    <form method="POST"
          action="{{ route('admin.shift-pattern-assignments.bulk-update', $teacher) }}">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">

                <div class="mb-4">
                    <label class="form-label fw-bold">Weekdays</label>

                    <div class="row">
                        @foreach($weekdayNames as $num => $name)
                            <div class="col-6 col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="weekdays[]"
                                           value="{{ $num }}"
                                           id="weekday_{{ $num }}"
                                           {{ in_array($num, $selectedWeekdays, true) ? 'checked' : '' }}>

                                    <label class="form-check-label"
                                           for="weekday_{{ $num }}">
                                        {{ $name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="form-text">
                        Select the weekdays you want to modify.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="shift_pattern_id" class="form-label fw-bold">
                        Shift Pattern
                    </label>

                    <select name="shift_pattern_id"
                            id="shift_pattern_id"
                            class="form-select"
                            required>
                        <option value="">Select Shift Pattern</option>

                        @foreach($patterns as $pattern)
                            <option value="{{ $pattern->id }}"
                                {{ (string)old('shift_pattern_id', $defaultPatternId) === (string)$pattern->id ? 'selected' : '' }}>
                                {{ $pattern->pattern_name }} ({{ $pattern->pattern_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label fw-bold">
                            Start Date
                        </label>

                        <input type="date"
                               name="start_date"
                               id="start_date"
                               class="form-control"
                               value="{{ old('start_date', $defaultStartDate) }}"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label fw-bold">
                            End Date
                        </label>

                        <input type="date"
                               name="end_date"
                               id="end_date"
                               class="form-control"
                               value="{{ old('end_date', $defaultEndDate) }}">

                        <div class="form-text">
                            If left blank, there will be no end date.
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="priority" class="form-label fw-bold">
                        Priority
                    </label>

                    <input type="number"
                           name="priority"
                           id="priority"
                           class="form-control"
                           min="0"
                           value="{{ old('priority', $defaultPriority) }}"
                           required>
                </div>

                <div class="alert alert-warning">
                    <strong>Caution:</strong>
                    Existing assignments for the same teacher and the same weekday after the specified start date will be replaced with the new assignment.
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        Save Changes
                    </button>
                    <a href="{{ route('admin.shift-pattern-assignments.index', ['menu' => 'schedule']) }}"
                       class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>

            </div>
        </div>
    </form>
    <hr class="my-4">
<div class="card border-danger mb-4">
    <div class="card-body">
        <h5 class="text-danger">All Shifts Deletion</h5>
        <p class="text-muted small">
            Deletes all shift assignments for this teacher and all future unreserved schedules. (Reserved lessons will not be deleted.)
        </p>
        <form action="{{ route('admin.shift-pattern-assignments.destroy-by-teacher', $teacher) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete all shifts for this teacher?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Execute Bulk Deletion</button>
        </form>
    </div>
</div>
</div>
@endsection
