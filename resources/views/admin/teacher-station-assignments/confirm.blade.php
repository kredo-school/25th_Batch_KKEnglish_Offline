
@extends('layouts.app')

@section('title', 'Confirm Station Assignment')

@section('content')
<div class="container py-4">

    <h1 class="h3 mb-3">Confirm Station Assignment</h1>

    <p class="text-muted">
        A teacher with overlapping working hours at the same station has been found.
        Please review the details before registering.
    </p>

    <div class="alert alert-warning" role="alert">
        <strong>
            <i class="fas fa-exclamation-triangle"></i>
            Shift Time Overlap
        </strong>
        <div>
            Working hours overlap for the same date and station.
        </div>
    </div>

    {{-- 登録しようとしている内容 --}}
    <div class="card mb-4">
        <div class="card-header fw-bold">
            New Assignment
        </div>

        <div class="card-body">
            <p class="mb-1">
                <strong>Start Date:</strong>
                {{ \Carbon\Carbon::parse($assignmentData['start_date'])->locale('ja')->isoFormat('YYYY/MM/DD（ddd）') }}
            </p>

            <p class="mb-0">
                <strong>End Date:</strong>
                @if (!empty($assignmentData['end_date']))
                    {{ \Carbon\Carbon::parse($assignmentData['end_date'])->locale('ja')->isoFormat('YYYY/MM/DD（ddd）') }}
                @else
                    No end date
                @endif
            </p>
        </div>
    </div>

    {{-- 重複している勤務予定 --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Station</th>
                    <th>Date / Day</th>
                    <th>Existing Teacher</th>
                    <th>Existing Shift</th>
                    <th>New Teacher</th>
                    <th>New Shift</th>
                    <th>Overlapping Time</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($conflicts as $conflict)
                    <tr>
                        <td>
                            {{ $conflict['station_name'] }}
                        </td>

                        <td class="text-nowrap">
                            {{ \Carbon\Carbon::parse($conflict['available_date'])->locale('ja')->isoFormat('YYYY/MM/DD（ddd）') }}
                        </td>

                        <td>
                            {{ $conflict['existing_teacher_name'] ?: 'Unknown Teacher' }}
                        </td>

                        <td class="text-nowrap">
                            {{ substr($conflict['existing_start_time'], 0, 5) }}
                            -
                            {{ substr($conflict['existing_end_time'], 0, 5) }}
                        </td>

                        <td>
                            {{ $conflict['new_teacher_name'] ?: 'Unknown Teacher' }}
                        </td>

                        <td class="text-nowrap">
                            {{ substr($conflict['new_start_time'], 0, 5) }}
                            -
                            {{ substr($conflict['new_end_time'], 0, 5) }}
                        </td>

                        <td class="text-nowrap text-danger fw-bold">
                            {{ substr($conflict['overlap_start_time'], 0, 5) }}
                            -
                            {{ substr($conflict['overlap_end_time'], 0, 5) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="text-muted small">
        If you proceed with the registration, multiple teachers will be scheduled to work at the same Station during the time slots indicated above.
    </p>

    {{-- 確認後の登録 --}}
    @if ($mode === 'create')
        <form
            action="{{ route('admin.teacher-station-assignments.store') }}"
            method="POST"
        >
    @else
        <form
            action="{{ route('admin.teacher-station-assignments.update', $teacherStationAssignment) }}"
            method="POST"
        >
            @method('PUT')
    @endif

        @csrf

        <input
            type="hidden"
            name="teacher_id"
            value="{{ $assignmentData['teacher_id'] }}"
        >

        <input
            type="hidden"
            name="station_id"
            value="{{ $assignmentData['station_id'] }}"
        >

        <input
            type="hidden"
            name="start_date"
            value="{{ $assignmentData['start_date'] }}"
        >

        <input
            type="hidden"
            name="end_date"
            value="{{ $assignmentData['end_date'] ?? '' }}"
        >

        <input
            type="hidden"
            name="confirmed"
            value="1"
        >

        <div class="d-flex flex-wrap gap-2 mt-4">

            <a
                href="{{ $mode === 'create'
                    ? route('admin.teacher-station-assignments.create', [
                        'menu' => 'station-assignment'
                    ])
                    : route('admin.teacher-station-assignments.edit', [
                        'teacherStationAssignment' => $teacherStationAssignment->id,
                        'menu' => 'station-assignment'
                    ]) }}"
                class="btn btn-outline-secondary"
            >

                Cancel
            </a>

            <button type="submit" class="btn btn-warning">
                <i class="fas fa-check"></i>
                Confirm and Register
            </button>
        </div>

    </form>

</div>
@endsection
