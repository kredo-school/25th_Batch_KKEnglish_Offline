@extends('layouts.app')

@section('title', 'Teacher Booking List')

@section('content')

<div class="container-fluid py-4">

    {{-- ==============================
        Header
    =============================== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="mb-1">
                {{-- <i class="fa-solid fa-list-check me-2"></i> --}}
                Teacher Booking List
            </h2>

            <div class="text-muted">
                You can view and filter bookings for teachers here.
            </div>
        </div>

        <div class="text-muted">
            {{ $bookings->count() }} bookings
        </div>

    </div>


    {{-- ==============================
        Search / Filter
    =============================== --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header bg-dark text-white">
            <i class="fa-solid fa-filter me-2"></i>
            Search / Filter
        </div>

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('admin.teacher-booking-list.index') }}"
            >

                <div class="row g-3 align-items-end">

                    {{-- From --}}
                    <div class="col-md-2">

                        <label class="form-label fw-bold">
                            From
                        </label>

                        <input
                            type="text"
                            name="from"
                            value="{{ $from }}"
                            class="form-control"
                            placeholder="YYYY-MM-DD"
                            inputmode="numeric"
                            maxlength="10"
                        >

                    </div>


                    {{-- To --}}
                    <div class="col-md-2">

                        <label class="form-label fw-bold">
                            To
                            <span class="text-muted fw-normal small">
                                (Blank = No Limit)
                            </span>
                        </label>

                        <input
                            type="text"
                            name="to"
                            value="{{ $to }}"
                            class="form-control"
                            placeholder="YYYY-MM-DD"
                            inputmode="numeric"
                            maxlength="10"
                        >

                    </div>


                    {{-- Teacher --}}
                    <div class="col-md-3">

                        <label class="form-label fw-bold">
                            Teacher
                        </label>

                        <select
                            name="teacher_id"
                            class="form-select"
                        >

                            <option value="">
                                All Teachers
                            </option>

                            @foreach($teachers as $teacher)

                                @php
                                    $teacherName = trim(
                                        ($teacher->user?->first_name ?? '') . ' ' .
                                        ($teacher->user?->last_name ?? '')
                                    );
                                @endphp

                                <option
                                    value="{{ $teacher->id }}"
                                    @selected((string) $teacherId === (string) $teacher->id)
                                >
                                    {{ $teacherName ?: 'Unknown Teacher' }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Status --}}
                    <div class="col-md-3">

                        <label class="form-label fw-bold">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select"
                        >

                            <option
                                value="active"
                                @selected($status === 'active')
                            >
                                Active Bookings
                            </option>

                            <option
                                value="all"
                                @selected($status === 'all')
                            >
                                All
                            </option>

                            <option
                                value="cancelled"
                                @selected($status === 'cancelled')
                            >
                                Cancelled
                            </option>

                        </select>

                    </div>


                    {{-- Search --}}
                    <div class="col-md-2">

                        <button
                            type="submit"
                            class="btn btn-dark w-100"
                        >
                            <i class="fa-solid fa-magnifying-glass me-1"></i>
                            Search
                        </button>

                    </div>

                </div>


                {{-- Quick Date Buttons --}}
                <div class="mt-3 d-flex flex-wrap gap-2">

                    <a
                        href="{{ route('admin.teacher-booking-list.index', [
                            'from' => now()->toDateString(),
                            'to' => now()->toDateString(),
                            'teacher_id' => $teacherId,
                            'status' => $status,
                        ]) }}"
                        class="btn btn-outline-dark btn-sm"
                    >
                        Today
                    </a>


                    <a
                        href="{{ route('admin.teacher-booking-list.index', [
                            'from' => now()->startOfWeek()->toDateString(),
                            'to' => now()->endOfWeek()->toDateString(),
                            'teacher_id' => $teacherId,
                            'status' => $status,
                        ]) }}"
                        class="btn btn-outline-dark btn-sm"
                    >
                        This Week
                    </a>


                    <a
                        href="{{ route('admin.teacher-booking-list.index', [
                            'from' => now()->toDateString(),
                            'to' => now()->addDays(7)->toDateString(),
                            'teacher_id' => $teacherId,
                            'status' => $status,
                        ]) }}"
                        class="btn btn-outline-dark btn-sm"
                    >
                        Next 7 Days
                    </a>


                    <a
                        href="{{ route('admin.teacher-booking-list.index', [
                            'from' => now()->subDays(7)->toDateString(),
                            'to' => now()->toDateString(),
                            'teacher_id' => $teacherId,
                            'status' => $status,
                        ]) }}"
                        class="btn btn-outline-secondary btn-sm"
                    >
                        Previous 7 Days
                    </a>

                </div>

            </form>

        </div>

    </div>


    {{-- ==============================
        Booking Table
    =============================== --}}
    <div class="card shadow-sm">

        <div class="card-header bg-white">

            <div class="d-flex justify-content-between align-items-center">

                <strong>
                    Booking Results
                </strong>

                <span class="text-muted small">
                    {{ $from }} ～ {{ $to }}
                </span>

            </div>

        </div>


        <div class="card-body p-0">

            @if($bookings->isEmpty())

                <div class="text-center py-5 text-muted">

                    <i class="fa-solid fa-calendar-xmark fa-2x mb-3"></i>

                    <div>
                        No bookings found.
                    </div>

                </div>

            @else

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th class="text-nowrap">
                                    Date
                                </th>

                                <th>
                                    Day
                                </th>

                                <th class="text-nowrap">
                                    Time
                                </th>

                                <th>
                                    Teacher
                                </th>

                                <th>
                                    Student
                                </th>

                                <th>
                                    Station
                                </th>

                                <th>
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($bookings as $booking)

                                <tr
                                    class="{{ $booking['is_cancelled'] ? 'table-secondary' : '' }}"
                                >

                                    {{-- Date --}}
                                    <td class="text-nowrap">

                                        {{ \Carbon\Carbon::parse($booking['date'])->format('Y/m/d') }}

                                    </td>


                                    {{-- Day --}}
                                    <td>

                                        @php
                                            $dayLabels = [
                                                'Sun' => 'Sun',
                                                'Mon' => 'Mon',
                                                'Tue' => 'Tue',
                                                'Wed' => 'Wed',
                                                'Thu' => 'Thu',
                                                'Fri' => 'Fri',
                                                'Sat' => 'Sat',
                                            ];
                                        @endphp

                                        {{ $dayLabels[$booking['day']] ?? $booking['day'] }}

                                    </td>


                                    {{-- Time --}}
                                    <td class="text-nowrap">

                                        <strong>
                                            {{ $booking['time'] }}
                                        </strong>

                                        <span class="text-muted">
                                            -
                                            {{ $booking['end_time'] }}
                                        </span>

                                    </td>


                                    {{-- Teacher --}}
                                    <td>

                                        <span class="fw-semibold">
                                            {{ $booking['teacher_name'] }}
                                        </span>

                                    </td>


                                    {{-- Student --}}
                                    <td>

                                        {{ $booking['student_name'] }}

                                    </td>


                                    {{-- Station --}}
                                    <td>

                                        @if($booking['station_name'] !== 'No Station')

                                            <span class="badge bg-info text-dark">
                                                <i class="fa-solid fa-location-dot me-1"></i>
                                                {{ $booking['station_name'] }}
                                            </span>

                                        @else

                                            <span class="text-muted">
                                                No Station
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Status --}}
                                    <td>

                                        @if($booking['is_cancelled'])

                                            <span class="badge bg-secondary">
                                                Cancelled
                                            </span>

                                        @else

                                            <span class="badge bg-success">
                                                Booked
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('input[name="from"], input[name="to"]')
        .forEach(function (input) {

            input.addEventListener('input', function () {

                let value = this.value.replace(/\D/g, '');

                // YYYYMMDD → YYYY-MM-DD
                if (value.length === 8) {

                    value =
                        value.substring(0, 4) + '-' +
                        value.substring(4, 6) + '-' +
                        value.substring(6, 8);
                }

                this.value = value.substring(0, 10);
            });

        });

});
</script>
@endsection
