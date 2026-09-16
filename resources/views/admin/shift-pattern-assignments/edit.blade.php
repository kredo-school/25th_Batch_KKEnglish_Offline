@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            Edit Shift Assignment
        </h1>
        <a href="{{ route('admin.shift-pattern-assignments.index', ['menu' => 'schedule']) }}" class="btn btn-secondary">
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

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.shift-pattern-assignments.update', $assignment) }}">
                @csrf
                @method('PUT')

                {{-- Teacher --}}
                <div class="mb-3">
                    <label class="form-label">
                        Teacher
                    </label>
                    <input type="text" class="form-control" value="Teacher #{{ $assignment->teacher_id }} - {{ trim(($assignment->teacher->user->first_name ?? '') . ' ' . ($assignment->teacher->user->last_name ?? '')) }}" disabled>
                </div>

                {{-- Shift Pattern --}}
                <div class="mb-3">
                    <label class="form-label">
                        Shift Pattern
                    </label>
                    <select name="shift_pattern_id" class="form-select" required>
                        @foreach ($patterns as $pattern)
                            <option value="{{ $pattern->id }}"
                                @selected((int) old('shift_pattern_id', $assignment->shift_pattern_id) === (int) $pattern->id)>
                                {{ $pattern->pattern_name }}
                                ({{ $pattern->pattern_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Weekday --}}
                <div class="mb-3">
                    <label class="form-label">
                        Weekday
                    </label>

                    @php
                        $weekdays = [
                            0 => 'Sunday',
                            1 => 'Monday',
                            2 => 'Tuesday',
                            3 => 'Wednesday',
                            4 => 'Thursday',
                            5 => 'Friday',
                            6 => 'Saturday',
                        ];
                    @endphp

                    <select name="weekday" class="form-select" required>
                        @foreach ($weekdays as $value => $label)
                            <option value="{{ $value }}"
                                @selected((int) old('weekday', $assignment->weekday) === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Start Date --}}
                <div class="mb-3">
                    <label class="form-label">
                        Start Date
                    </label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', \Carbon\Carbon::parse($assignment->start_date)->format('Y-m-d')) }}" required>
                </div>

                {{-- End Date --}}
                <div class="mb-3">
                    <label class="form-label">
                        End Date
                    </label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $assignment->end_date ? \Carbon\Carbon::parse($assignment->end_date)->format('Y-m-d') : '') }}">
                    <div class="form-text">
                        空欄の場合は終了日なしです。
                    </div>
                </div>

                {{-- Priority --}}
                <div class="mb-4">
                    <label class="form-label">
                        Priority
                    </label>
                    <input type="number" name="priority" class="form-control" min="0" value="{{ old('priority', $assignment->priority ?? 0) }}">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        Update Assignment
                    </button>
                    <a href="{{ route('admin.shift-pattern-assignments.index', ['menu' => 'schedule']) }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
