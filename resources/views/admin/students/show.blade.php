@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h2 mb-0 fw-bold">Student Details</h1>
        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-angles-left"></i> Back to List</a>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-md-5">
            <div class="card h-100">
                <div class="card-header">Student Information</div>
                <div class="card-body">
                    <p><strong>ID:</strong>{{ $student->id }}</p>
                    <p><strong>Name:</strong>{{ trim(($student->user->first_name ?? '').' '.($student->user->last_name ?? '')) ?: 'Name Not Registered' }}</p>
                    <p><strong>Enrollment Date:</strong>{{ $student->created_at?->format('Y-m-d') ?? '-' }}</p>
                    <p><strong>Graduation Date:</strong>{{ $student->graduation_date?->format('Y-m-d') ?? '-' }}</p>
                    <p class="mb-0"><strong>Active:</strong>
                        @if(($student->user->status ?? null) === 'active')<span class="badge bg-success">ON</span>
                        @else<span class="badge bg-secondary">OFF</span>@endif
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card h-100">
                <div class="card-header">Point Management</div>
                <div class="card-body">
                    <div class="text-muted small">Current Remaining Points</div>
                    <div class="display-6 fw-bold">{{ number_format($pointBalance) }} <span class="fs-5">pt</span></div>
                    <a href="{{ route('admin.students.points.create', $student) }}" class="btn btn-primary mt-3">
                        + Add Points
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">Point Transaction History</div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr><th>Date & Time</th><th>Type</th><th class="text-end">Points</th><th>Note</th><th>Creator</th></tr>
                </thead>
                <tbody>
                @forelse($student->pointTransactions as $transaction)
                    <tr>
                        <td>{{ $transaction->created_at?->format('Y-m-d H:i') }}</td>
                        <td>{{ $transaction->transactionType->type_name ?? '-' }}</td>
                        <td class="text-end fw-bold">
                            @if($transaction->point > 0)
                                <span class="text-success">+{{ number_format($transaction->point) }}</span>
                            @else
                                <span class="text-danger">{{ number_format($transaction->point) }}</span>
                            @endif
                        </td>
                        <td>{{ $transaction->note ?? '-' }}</td>
                        <td>{{ $transaction->creator?->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No point transaction history.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
