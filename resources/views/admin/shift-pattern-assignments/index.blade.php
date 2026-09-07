@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Teacher Assignment List</h1>
        <a href="{{ route('admin.shift-pattern-assignments.create', ['menu' => 'schedule']) }}" class="btn btn-primary btn-sm">＋ Assign Shift Pattern</a>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Teacher ID</th>
                            <th>Teacher Name</th>
                            <th>Shift Pattern</th>
                            <th class="text-center">Weekday</th>
                            <th>Start Date & End Date</th>
                            <th>Priority</th>
                            <th class="text-center">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php
                        $weekdayNames = [
                            0 => 'Sun',
                            1 => 'Mon',
                            2 => 'Tue',
                            3 => 'Wed',
                            4 => 'Thu',
                            5 => 'Fri',
                            6 => 'Sat'
                        ];
                    @endphp
                    @forelse($teachers as $teacher)
                        @php
                            $assignmentsList = $teacher->shiftAssignments ?? $teacher->shiftPatternAssignments ?? collect();
                            $groupedPatterns = $assignmentsList->groupBy('shift_pattern_id');
                            $patternCount = $groupedPatterns->count();
                        @endphp

                        @if($patternCount === 0)
                            <tr>
                                <td>{{ $teacher->id }}</td>
                                <td class="fw-bold">
                                    {{ trim(($teacher->user->first_name ?? '') . ' ' . ($teacher->user->last_name ?? '')) ?: ('Teacher #' . $teacher->id) }}
                                </td>
                                <td colspan="4" class="text-muted">No Shift Pattern Assigned</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.shift-pattern-assignments.create', ['teacher_ids' => [$teacher->id], 'menu' => 'schedule']) }}" class="btn btn-outline-primary btn-sm">
                                        ＋ Add Assignment
                                    </a>
                                </td>
                            </tr>
                        @else
                            @foreach($groupedPatterns as $patternId => $assignments)
                                @php
                                    $first = $assignments->first();
                                    $patternName = $first->shiftPattern->pattern_name ?? $first->shiftPattern->pattern_code ?? ('Pattern #' . $patternId);
                                    $weekdays = $assignments->pluck('weekday')->unique()->sort();
                                    $startDate = $assignments->min('start_date');
                                    $hasNoEnd = $assignments->contains(fn($a) => is_null($a->end_date));
                                    $endDate = $hasNoEnd ? null : $assignments->max('end_date');
                                    $priority = $assignments->max('priority');
                                @endphp
                                <tr>
                                    @if($loop->first)
                                        <td rowspan="{{ $patternCount }}">{{ $teacher->id }}</td>
                                        <td rowspan="{{ $patternCount }}" class="fw-bold">
                                            {{ trim(($teacher->user->first_name ?? '') . ' ' . ($teacher->user->last_name ?? '')) ?: ('Teacher #' . $teacher->id) }}
                                        </td>
                                    @endif

                                    {{-- Shift Pattern --}}
                                    <td class="fw-semibold text-primary">
                                        {{ $patternName }}
                                    </td>

                                    {{-- Weekday --}}
                                    <td>
                                        @foreach($weekdayNames as $num => $name)
                                            @if($weekdays->contains($num))
                                                <span class="badge bg-success me-1">{{ $name }}</span>
                                            @else
                                                <span class="badge bg-light text-muted border me-1">{{ $name }}</span>
                                            @endif
                                        @endforeach
                                    </td>

                                    {{-- Start Date & End Date --}}
                                    <td>
                                        <small class="text-muted">
                                            {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('Y-m-d') : '-' }} ~ {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('Y-m-d') : 'Indefinite' }}
                                        </small>
                                    </td>

                                    {{-- Priority --}}
                                    <td>{{ $priority }}</td>

                                    {{-- Actions --}}
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            @foreach($assignments as $item)
                                                <form action="{{ route('admin.shift-pattern-assignments.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete assignment for {{ $weekdayNames[$item->weekday] ?? $item->weekday }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-1" style="font-size: 0.75rem;" title="Delete {{ $weekdayNames[$item->weekday] ?? '' }}">
                                                        &times; {{ $weekdayNames[$item->weekday] ?? '' }}
                                                    </button>
                                                </form>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No teacher assignments found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $teachers->links() }}
    </div>
</div>
@endsection
