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
                        [{{ $p->pattern_code }}] {{ $p->pattern_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Teacher(s) (multiple selection)</label>
            <select name="teacher_ids[]" class="form-control" multiple size="12" required>
                @foreach($teachers as $t)
                    <option value="{{ $t->id }}" @selected(collect(old('teacher_ids', []))->contains($t->id))>
                        {{ $t->id }} - {{ $t->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Effective From</label>
                <input type="date" name="effective_from" class="form-control" value="{{ old('effective_from') }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>Effective To (optional)</label>
                <input type="date" name="effective_to" class="form-control" value="{{ old('effective_to') }}">
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
