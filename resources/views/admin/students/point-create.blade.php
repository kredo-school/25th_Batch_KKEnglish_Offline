@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Add Points</h1>
        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <div class="text-muted small">Student</div>
            <div class="fs-5 fw-bold">
                #{{ $student->id }}
                {{ trim(($student->first_name ?? '').' '.($student->last_name ?? '')) ?: 'Name Not Registered' }}
            </div>
            <div class="mt-2">Current Remaining Points: <strong>{{ number_format($pointBalance) }} pt</strong></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Add Points</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.students.points.store', $student) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">Transaction Type</label>
                    <select name="transaction_type" class="form-select" required>
                        <option value="">Please select</option>
                        @foreach($types as $type)
                            <option value="{{ $type->type_id }}" @selected(old('transaction_type') == $type->type_id)>
                                {{ $type->type_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Points to Add</label>
                    <input type="number" name="point" class="form-control" min="1" step="1"
                           value="{{ old('point') }}" placeholder="e.g., 300" required>
                    <div class="form-text">Enter the number of points to add here.</div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Note</label>
                    <input type="text" name="note" class="form-control" maxlength="255"
                           value="{{ old('note') }}" placeholder="e.g., Campaign Bonus">
                </div>
                <div class="alert alert-warning">
                    Saving will add a new point transaction record and increase the remaining points.
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-secondary">Cancel</a>
                    <button class="btn btn-primary">Add Points</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
