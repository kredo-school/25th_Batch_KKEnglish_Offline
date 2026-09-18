@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Shift Pattern Assignment</h1>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.shift-pattern-assignments.store') }}">
        @csrf

        {{-- シフト種別の選択 --}}
        <div class="mb-4">
            <label class="form-label fw-semibold">Shift Type</label>
            <div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="assignment_type" id="type_weekly" value="weekly" checked onchange="toggleShiftType()">
                    <label class="form-check-label" for="type_weekly">Regular Shift (Weekly)</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="assignment_type" id="type_specific" value="specific_date" onchange="toggleShiftType()">
                    <label class="form-check-label" for="type_specific">Temporary Shift (Specific Date)</label>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Shift Pattern</label>
            <select name="shift_pattern_id" class="form-select" required>
                <option value="">Select Shift Pattern</option>
                @foreach($patterns as $p)
                    <option value="{{ $p->id }}" @selected(old('shift_pattern_id', $defaultPatternId ?? null)==$p->id)>
                        {{ $p->pattern_name ?? $p->pattern_code ?? ('Pattern #' . $p->id) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Teacher(s) (multiple selection)</label>
            <select name="teacher_ids[]" class="form-select" multiple required size="10">
        @foreach($teachers as $t)
            <option value="{{ $t->id }}"
                @selected(collect(old('teacher_ids', []))->contains($t->id))>
                {{ trim(($t->user->first_name ?? '') . ' ' . ($t->user->last_name ?? '')) ?: ('Teacher #'.$t->id) }}
            </option>
        @endforeach
    </select>
    <small class="text-muted">Ctrl(⌘)+Click to select multiple</small>
</div>

    {{-- 通常シフト用の入力エリア --}}
    <div id="weekly_section">
        <div class="mb-3">
            <label>Weekdays</label>
            <div>
            @php
                $weekdayLabels = [0=>'Sun',1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat'];
                // $oldWeekdays = collect(old('weekdays', [1,2,3,4,5]))->map(fn($v)=>(int)$v);
            @endphp
                @foreach($weekdayLabels as $num => $label)
                    <label class="me-3">
                        <input type="checkbox" name="weekdays[]" value="{{ $num }}" >
                        {{ $label }}
                    </label>
                @endforeach
            </div>
            {{-- <small class="text-muted">Select at least one weekday.</small> --}}
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Start Date</label>
                <input type="date" name="start_date" id="weekly_start" class="form-control" value="{{ old('start_date', now()->format('Y-m-d')) }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>End Date (optional)</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
            </div>
            {{-- <div class="col-md-4 mb-3">
                <label>Priority</label>
                <input type="number" min="0" name="priority" class="form-control" value="{{ old('priority', 0) }}">
            </div> --}}
        </div>
        </div>
            {{-- 臨時シフト用の入力エリア --}}
            <div id="specific_section" style="display: none;">
                <div class="mb-3 w-50">
                    <label class="fw-semibold">Specific Date</label>
                    <input type="date" name="specific_start_date" id="specific_start" class="form-control">
                    <small class="text-muted">Updates the shift for the specified date.</small>
                </div>
            </div>

        {{-- <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="replace_overlapping" name="replace_overlapping" value="1" @checked(old('replace_overlapping'))>
            <label class="form-check-label" for="replace_overlapping">
                Replace existing assignments for overlapping periods
            </label>
        </div> --}}

        <button type="submit" class="btn btn-primary">Assign Shift</button>
    </form>
</div>

<script>
    function toggleShiftType() {
        const isWeekly = document.getElementById('type_weekly').checked;
        document.getElementById('weekly_section').style.display = isWeekly ? 'block' : 'none';
        document.getElementById('specific_section').style.display = isWeekly ? 'none' : 'block';

        // 必須項目の切り替え
        document.getElementById('weekly_start').disabled = !isWeekly;
        document.getElementById('specific_start').disabled = isWeekly;

        if(!isWeekly) {
            document.getElementById('specific_start').name = 'start_date';
            document.getElementById('weekly_start').name = '';
        } else {
            document.getElementById('weekly_start').name = 'start_date';
            document.getElementById('specific_start').name = '';
        }
    }
    // 初期ロード時に実行
    toggleShiftType();
</script>
@endsection
