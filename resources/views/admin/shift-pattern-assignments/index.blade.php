
@extends('layouts.app')

@section('content')

<style>
    /* Compact weekday badges */
    .weekday-badge {
        font-size: clamp(0.55rem, 0.65vw, 0.68rem);
        font-weight: 500;
        padding: 0.15em 0.3em;
        line-height: 1.1;
        min-width: 24px;
        text-align: center;
    }

    /* Shift Pattern text */
    .shift-pattern-name {
        font-size: 0.8rem;
        line-height: 1.2;
        font-weight: 600;
    }

    /* Compact action buttons */
    .assignment-action-btn {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
        line-height: 1.2;
        white-space: nowrap;
    }

    /* Compact assignment table */
    .assignment-table {
        font-size: 0.8rem;
    }

    .assignment-table th,
    .assignment-table td {
        padding: 0.4rem;
    }
</style>

<div class="container py-3">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h2 mb-0 fw-bold">Teacher Assignment List</h1>

        <a href="{{ route('admin.shift-pattern-assignments.create', ['menu' => 'schedule']) }}"
           class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i>Assign Shift Pattern
        </a>
    </div>

    {{-- Success Message --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- Assignment Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0 assignment-table">

                    <thead class="table-light">
                        <tr>
                            <th>Teacher ID</th>
                            <th>Teacher Name</th>
                            <th>Shift Pattern</th>
                            <th class="text-center">Weekday</th>
                            <th>Start Date &amp; End Date</th>
                            <th>Priority</th>
                            <th class="text-center">Action</th>
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
                                6 => 'Sat',
                            ];
                        @endphp

                        @forelse($teachers as $teacher)

                            @php
                                /*
                                |--------------------------------------------------------------------------
                                | Get Teacher Assignments
                                |--------------------------------------------------------------------------
                                */

                                $assignmentsList = $teacher->shiftAssignments
                                    ?? $teacher->shiftPatternAssignments
                                    ?? collect();

                                /*
                                |--------------------------------------------------------------------------
                                | Group Assignments with Identical Settings
                                |--------------------------------------------------------------------------
                                | Group by:
                                | - Shift Pattern
                                | - Start Date
                                | - End Date
                                | - Priority
                                |
                                | Weekday is intentionally excluded.
                                | Assignments with the same settings but different
                                | weekdays will be displayed in one row.
                                |--------------------------------------------------------------------------
                                */

                                $groupedPatterns = $assignmentsList->groupBy(function ($assignment) {
                                    return implode('|', [
                                        $assignment->shift_pattern_id,

                                        $assignment->start_date
                                            ? \Carbon\Carbon::parse($assignment->start_date)->format('Y-m-d')
                                            : '',

                                        $assignment->end_date
                                            ? \Carbon\Carbon::parse($assignment->end_date)->format('Y-m-d')
                                            : 'indefinite',

                                        $assignment->priority ?? 0,
                                    ]);
                                });

                                $patternCount = $groupedPatterns->count();
                            @endphp

                            {{-- No Assignment --}}
                            @if($patternCount === 0)

                                <tr>
                                    <td>{{ $teacher->id }}</td>

                                    <td class="fw-bold">
                                        {{ trim(($teacher->user->first_name ?? '') . ' ' . ($teacher->user->last_name ?? '')) ?: ('Teacher #' . $teacher->id) }}
                                    </td>

                                    <td colspan="4" class="text-muted">
                                        No Shift Pattern Assigned
                                    </td>

                                    <td class="text-end">
                                        <a href="{{ route('admin.shift-pattern-assignments.create', ['teacher_ids' => [$teacher->id], 'menu' => 'schedule']) }}"
                                           class="btn btn-outline-primary btn-sm">
                                            + Add Assignment
                                        </a>
                                    </td>
                                </tr>

                            @else

                                {{-- Display Each Group --}}
                                @foreach($groupedPatterns as $groupKey => $assignments)

                                    @php
                                        $first = $assignments->first();

                                        /*
                                        |--------------------------------------------------------------------------
                                        | Shift Pattern
                                        |--------------------------------------------------------------------------
                                        */

                                        $patternId = $first->shift_pattern_id;

                                        $patternName = $first->shiftPattern->pattern_name
                                            ?? $first->shiftPattern->pattern_code
                                            ?? ('Pattern #' . $patternId);

                                        /*
                                        |--------------------------------------------------------------------------
                                        | Weekdays
                                        |--------------------------------------------------------------------------
                                        | Remove duplicate weekdays and sort them.
                                        |--------------------------------------------------------------------------
                                        */

                                        $weekdays = $assignments
                                            ->pluck('weekday')
                                            ->unique()
                                            ->sort();

                                        /*
                                        |--------------------------------------------------------------------------
                                        | Dates and Priority
                                        |--------------------------------------------------------------------------
                                        | All assignments in this group have the same
                                        | start date, end date and priority.
                                        |--------------------------------------------------------------------------
                                        */

                                        $startDate = $first->start_date;
                                        $endDate = $first->end_date;
                                        $priority = $first->priority;
                                    @endphp

                                    <tr>

                                        {{-- Teacher ID and Name --}}
                                        @if($loop->first)

                                            <td rowspan="{{ $patternCount }}">
                                                {{ $teacher->id }}
                                            </td>

                                            <td rowspan="{{ $patternCount }}" class="fw-bold">
                                                {{ trim(($teacher->user->first_name ?? '') . ' ' . ($teacher->user->last_name ?? '')) ?: ('Teacher #' . $teacher->id) }}
                                            </td>

                                        @endif

                                        {{-- Shift Pattern --}}
                                        <td class="shift-pattern-name text-primary">
                                            {{ $patternName }}
                                        </td>


                                        {{-- Weekday --}}
                                        <td class="text-center">
                                            <div class="d-flex flex-wrap justify-content-center gap-1">
                                                @foreach($weekdayNames as $num => $name)
                                                    @if($weekdays->contains($num))
                                                        <span class="badge bg-success weekday-badge">
                                                            {{ $name }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light text-muted border weekday-badge">
                                                            {{ $name }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>


                                        {{-- Start Date & End Date --}}
                                        <td>
                                            {{ $startDate
                                                ? \Carbon\Carbon::parse($startDate)->format('Y-m-d')
                                                : '-' }}

                                            ~

                                            {{ $endDate
                                                ? \Carbon\Carbon::parse($endDate)->format('Y-m-d')
                                                : 'Indefinite' }}
                                        </td>

                                        {{-- Priority --}}
                                        <td>
                                            {{ $priority ?? 0 }}
                                        </td>

                                        {{-- Action --}}
                                        <td class="text-center">
                                            <div class="d-flex flex-column gap-1">

                                                {{-- Edit Assignments --}}
                                                <div class="mt-1">
                                                    <a href="{{ route('admin.shift-pattern-assignments.bulk-edit', $teacher) }}"
                                                    class="btn btn-outline-primary assignment-action-btn">
                                                        <i class="fa-solid fa-pen-to-square me-1"></i>Edit
                                                    </a>
                                                </div>

                                            </div>
                                        </td>

                                    </tr>

                                @endforeach

                            @endif

                        @empty

                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No teacher assignments found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $teachers->links() }}
    </div>

</div>
@endsection
