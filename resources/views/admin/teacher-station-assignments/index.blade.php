@extends('layouts.app')

@section('title', 'Station Assignment')

@section('content')

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Station Assignment</h2>
            <p class="text-muted mb-0">
                Assign default stations to teachers by period.
            </p>
        </div>

        <a href="{{ route('admin.teacher-station-assignments.overrides.index') }}"
   class="btn btn-outline-primary">
    <i class="fa-solid fa-location-dot me-1"></i>
    Station Override
</a>
        <a href="{{ route('admin.teacher-station-assignments.create', ['menu' => 'station-assignment']) }}"
           class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i>
            Create Assignment
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">
                                Teacher
                            </th>

                            <th>
                                Station
                            </th>

                            <th>
                                Start Date
                            </th>

                            <th>
                                End Date
                            </th>

                            <th class="text-end pe-4">
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse ($assignments as $assignment)

                        <tr>

                            {{-- Teacher --}}
                            <td class="ps-4">

                                <div class="fw-semibold">
                                    {{ $assignment->teacher->user->first_name ?? '' }}
                                    {{ $assignment->teacher->user->last_name ?? '' }}
                                </div>

                                <small class="text-muted">
                                    Teacher ID:
                                    {{ $assignment->teacher_id }}
                                </small>

                            </td>

                            {{-- Station --}}
                            <td>

                                @if ($assignment->station)
                                    <span class="badge bg-primary">
                                        {{ $assignment->station->name }}
                                    </span>
                                @else
                                    <span class="text-muted">
                                        No station
                                    </span>
                                @endif

                            </td>

                            {{-- Start Date --}}
                            <td>

                                {{ $assignment->start_date?->format('Y-m-d') ?? '-' }}

                            </td>

                            {{-- End Date --}}
                            <td>

                                @if ($assignment->end_date)
                                    {{ $assignment->end_date->format('Y-m-d') }}
                                @else
                                    <span class="badge bg-success">
                                        No End Date
                                    </span>
                                @endif

                            </td>

                            {{-- Action --}}
                            <td class="text-end pe-4">

                                <a href="{{ route('admin.teacher-station-assignments.edit', [
                                    'teacherStationAssignment' => $assignment,
                                    'menu' => 'station-assignment'
                                ]) }}"
                                class="btn btn-sm btn-outline-primary">

                                    <i class="fa-solid fa-pen-to-square me-1"></i>
                                    Edit

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5"
                                class="text-center py-5 text-muted">

                                No station assignments found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection