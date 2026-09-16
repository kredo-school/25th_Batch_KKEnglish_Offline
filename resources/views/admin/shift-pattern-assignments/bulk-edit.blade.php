@extends('layouts.app')

@section('content')
<div class="container py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Bulk Edit Teacher Assignments</h1>

        <a href="{{ route('admin.shift-pattern-assignments.index', ['menu' => 'schedule']) }}"
           class="btn btn-outline-secondary btn-sm">
            Back
        </a>
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
                        変更したい曜日を選択してください。
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
                            空欄の場合は終了日なしです。
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
                    <strong>注意：</strong>
                    指定した開始日以降の同じ先生・同じ曜日の既存Assignmentは、
                    新しいAssignmentに置き換えられます。
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.shift-pattern-assignments.index', ['menu' => 'schedule']) }}"
                       class="btn btn-outline-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Save Changes
                    </button>
                </div>

            </div>
        </div>
    </form>
    <hr class="my-4">
<div class="card border-danger mb-4">
    <div class="card-body">
        <h5 class="text-danger">全シフトの一括削除</h5>
        <p class="text-muted small">
            この先生に設定されているすべてのシフト割り当てと、未来の未予約スケジュールを全て削除します。（予約済みのレッスンは削除されません）
        </p>
        <form action="{{ route('admin.shift-pattern-assignments.destroy-by-teacher', $teacher) }}" method="POST" onsubmit="return confirm('本当にこの先生のすべてのシフトを一括削除しますか？');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">一括削除を実行する</button>
        </form>
    </div>
</div>
</div>
@endsection
