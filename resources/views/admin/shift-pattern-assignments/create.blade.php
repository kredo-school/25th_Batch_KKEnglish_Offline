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

        <div class="mb-3">
            <label>Shift Pattern</label>
            <select name="shift_pattern_id" class="form-control" required>
                <option value="">Select</option>
                @foreach($patterns as $p)
                    <option value="{{ $p->id }}" @selected(old('shift_pattern_id')==$p->id)>
                        Pattern #{{ $p->id }}
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
    <small class="text-muted">Ctrl(⌘)+クリックで複数選択</small>
</div>

    <div class="mb-3">
        <label>Weekdays</label>
        @php
            $weekdayLabels = [0=>'Sun',1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat'];
            $oldWeekdays = collect(old('weekdays', [1,2,3,4,5]))->map(fn($v)=>(int)$v);
        @endphp
        <div>
            @foreach($weekdayLabels as $num => $label)
                <label class="me-3">
                    <input type="checkbox" name="weekdays[]" value="{{ $num }}" @checked($oldWeekdays->contains($num))>
                    {{ $label }}
                </label>
            @endforeach
        </div>
        <small class="text-muted">少なくとも1曜日を選択してください。</small>
    </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>End Date (optional)</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Priority</label>
                <input type="number" min="0" name="priority" class="form-control" value="{{ old('priority', 0) }}">
            </div>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="replace_overlapping" name="replace_overlapping" value="1" @checked(old('replace_overlapping'))>
            <label class="form-check-label" for="replace_overlapping">
                Replace existing assignments for overlapping periods
            </label>
        </div>

        <button type="submit" class="btn btn-primary">Assign in Bulk</button>
    </form>
</div>
@endsection
