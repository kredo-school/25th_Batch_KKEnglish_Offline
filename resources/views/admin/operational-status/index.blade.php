@extends('layouts.app')

@section('title', 'Operational Status')

@section('content')

<div class="container-fluid py-4">

    {{-- =========================================================
         Header
    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                Operational Status
            </h2>

            <div class="text-muted">
                {{ $monthStart->format('F Y') }}
            </div>
        </div>


        {{-- Period --}}

        <form
            method="GET"
            action="{{ route('admin.operational-status.index') }}"
            class="d-flex gap-2"
        >

            <select
                name="year"
                class="form-select"
            >

                @for(
                    $y = now()->year - 2;
                    $y <= now()->year + 1;
                    $y++
                )

                    <option
                        value="{{ $y }}"
                        @selected($year == $y)
                    >
                        {{ $y }}
                    </option>

                @endfor

            </select>


            <select
                name="month"
                class="form-select"
            >

                @for($m = 1; $m <= 12; $m++)

                    <option
                        value="{{ $m }}"
                        @selected($month == $m)
                    >
                        {{ \Carbon\Carbon::create()
                            ->month($m)
                            ->format('F') }}
                    </option>

                @endfor

            </select>


            <button
                type="submit"
                class="btn btn-primary"
            >
                View
            </button>

        </form>

    </div>


    {{-- =========================================================
         Monthly Overview
    ========================================================== --}}

    <h5 class="fw-bold mb-3">
        Monthly Overview
    </h5>


    <div class="row g-3 mb-4">


        {{-- Total Reservations --}}

        <div class="col-md-3">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small">
                        Total Reservations
                    </div>

                    <div class="fs-2 fw-bold">
                        {{ number_format($monthlyReservations) }}
                    </div>

                </div>

            </div>

        </div>


        {{-- Completed Lessons --}}

        <div class="col-md-3">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small">
                        Completed Lessons
                    </div>

                    <div class="fs-2 fw-bold">
                        {{ number_format($completedLessonCount) }}
                    </div>

                    <div class="small text-muted">
                        Lesson report completed
                    </div>

                </div>

            </div>

        </div>


        {{-- Operation Rate --}}

        <div class="col-md-3">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small">
                        Operation Rate
                    </div>

                    <div class="fs-2 fw-bold">
                        {{ number_format($operationRate, 1) }}%
                    </div>

                    <div class="small text-muted">
                        Completed / Valid Reservations
                    </div>

                </div>

            </div>

        </div>


        {{-- Cancellation Rate --}}

        <div class="col-md-3">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small">
                        Cancellation Rate
                    </div>

                    <div class="fs-2 fw-bold">
                        {{ number_format($cancellationRate, 1) }}%
                    </div>

                    <div class="small text-muted">
                        {{ number_format($monthlyCancellations) }}
                        cancellations
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         Shift Analysis
    ========================================================== --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white">

            <h5 class="fw-bold mb-0">
                Shift Analysis
            </h5>

        </div>


        <div class="card-body">

            <div class="row g-4">


                <div class="col-md-4">

                    <div class="text-muted small">
                        Total Shifts
                    </div>

                    <div class="fs-3 fw-bold">
                        {{ number_format($shiftCount) }}
                    </div>

                </div>


                <div class="col-md-4">

                    <div class="text-muted small">
                        Active Teachers
                    </div>

                    <div class="fs-3 fw-bold">
                        {{ number_format($activeTeacherCount) }}
                    </div>

                </div>


                <div class="col-md-4">

                    <div class="text-muted small">
                        Working Hours
                    </div>

                    <div class="fs-3 fw-bold">
                        {{ number_format($workingHours, 1) }}
                    </div>

                </div>


            </div>

        </div>

    </div>


    {{-- =========================================================
         Reservation Analysis
    ========================================================== --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white">

            <h5 class="fw-bold mb-0">
                Reservation Analysis
            </h5>

        </div>


        <div class="card-body">

            <div class="row text-center">


                <div class="col-md-4">

                    <div class="text-muted small">
                        Average / Day
                    </div>

                    <div class="fs-3 fw-bold">
                        {{ number_format(
                            $reservationAverage,
                            1
                        ) }}
                    </div>

                </div>


                <div class="col-md-4">

                    <div class="text-muted small">
                        Maximum / Day
                    </div>

                    <div class="fs-3 fw-bold">
                        {{ number_format(
                            $reservationMaximum
                        ) }}
                    </div>

                </div>


                <div class="col-md-4">

                    <div class="text-muted small">
                        Minimum / Day
                    </div>

                    <div class="fs-3 fw-bold">
                        {{ number_format(
                            $reservationMinimum
                        ) }}
                    </div>

                </div>


            </div>

        </div>

    </div>


    {{-- =========================================================
         Cancellation Analysis
    ========================================================== --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white">

            <h5 class="fw-bold mb-0">
                Cancellation Analysis
            </h5>

        </div>


        <div class="card-body">

            <div class="row">


                <div class="col-md-4">

                    <div class="text-muted small">
                        Total Cancellations
                    </div>

                    <div class="fs-3 fw-bold">
                        {{ number_format(
                            $monthlyCancellations
                        ) }}
                    </div>

                </div>


                <div class="col-md-4">

                    <div class="text-muted small">
                        Cancellation Rate
                    </div>

                    <div class="fs-3 fw-bold">
                        {{ number_format(
                            $cancellationRate,
                            1
                        ) }}%
                    </div>

                </div>


                <div class="col-md-4">

                    <div class="text-muted small">
                        Valid Reservations
                    </div>

                    <div class="fs-3 fw-bold">
                        {{ number_format(
                            $validReservations
                        ) }}
                    </div>

                </div>


            </div>

        </div>

    </div>

    {{-- =========================================================
     Teacher Rating Analysis
    ========================================================= --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">
                Teacher Rating Analysis
            </h5>
        </div>

        <div class="card-body">

            {{-- Overall Rating --}}
            <div class="row g-3 mb-4">

                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">

                        <div class="text-secondary small">
                            Average Rating
                        </div>

                        <div class="fs-3 fw-bold">
                            {{ $averageRating !== null
                                ? number_format($averageRating, 2)
                                : '-' }}
                        </div>

                        <div class="text-secondary small">
                            / 5.00
                        </div>

                    </div>
                </div>


                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">

                        <div class="text-secondary small">
                            Highest Rating
                        </div>

                        <div class="fs-3 fw-bold">
                            {{ $maxRating ?? '-' }}
                        </div>

                        <div class="text-secondary small">
                            / 5
                        </div>

                    </div>
                </div>


                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">

                        <div class="text-secondary small">
                            Lowest Rating
                        </div>

                        <div class="fs-3 fw-bold">
                            {{ $minRating ?? '-' }}
                        </div>

                        <div class="text-secondary small">
                            / 5
                        </div>

                    </div>
                </div>


                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">

                        <div class="text-secondary small">
                            Review Count
                        </div>

                        <div class="fs-3 fw-bold">
                            {{ $reviewCount }}
                        </div>

                        <div class="text-secondary small">
                            reviews
                        </div>

                    </div>
                </div>

            </div>


            {{-- Teacher Comparison --}}

            <h6 class="fw-bold mb-3">
                Teacher Rating Comparison
            </h6>

            @if ($teacherRatingComparison->isEmpty())

                <div class="text-secondary">
                    No reviews found for this period.
                </div>

            @else

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead class="table-light">

                            <tr>
                                <th>Teacher</th>
                                <th class="text-center">
                                    Average
                                </th>
                                <th class="text-center">
                                    Highest
                                </th>
                                <th class="text-center">
                                    Lowest
                                </th>
                                <th class="text-center">
                                    Reviews
                                </th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach (
                                $teacherRatingComparison
                                as $teacherRating
                            )

                                <tr>

                                    <td class="fw-semibold">
                                        {{ $teacherRating['teacher_name'] ?: '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ number_format(
                                            $teacherRating['average_rating'],
                                            2
                                        ) }}
                                        / 5
                                    </td>

                                    <td class="text-center">
                                        {{ $teacherRating['max_rating'] }}
                                    </td>

                                    <td class="text-center">
                                        {{ $teacherRating['min_rating'] }}
                                    </td>

                                    <td class="text-center">
                                        {{ $teacherRating['review_count'] }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>

    {{-- =========================================================
         Annual Trend
    ========================================================== --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white">

            <h5 class="fw-bold mb-0">
                Annual Trend
            </h5>

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table
                    class="table table-bordered table-hover align-middle mb-0"
                >

                    <thead class="table-light">

                        <tr>

                            <th>
                                Month
                            </th>

                            <th class="text-end">
                                Reservations
                            </th>

                            <th class="text-end">
                                Valid
                            </th>

                            <th class="text-end">
                                Completed
                            </th>

                            <th class="text-end">
                                Operation Rate
                            </th>

                            <th class="text-end">
                                Cancellations
                            </th>

                            <th class="text-end">
                                Cancellation Rate
                            </th>

                            <th class="text-end">
                                Reviews
                            </th>
                            <th class="text-end">
                                Avg Rating
                            </th>
                            <th class="text-end">
                                Max / Min
                            </th>
                        </tr>

                    </thead>


                    <tbody>

                        @foreach($monthlyTrend as $row)

                            <tr>

                                <td class="fw-semibold">
                                    {{ $row['month'] }}
                                </td>


                                <td class="text-end">
                                    {{ number_format(
                                        $row['reservations']
                                    ) }}
                                </td>


                                <td class="text-end">
                                    {{ number_format(
                                        $row['valid']
                                    ) }}
                                </td>


                                <td class="text-end">
                                    {{ number_format(
                                        $row['completed']
                                    ) }}
                                </td>


                                <td class="text-end fw-semibold">
                                    {{ number_format(
                                        $row['operation_rate'],
                                        1
                                    ) }}%
                                </td>


                                <td class="text-end">
                                    {{ number_format(
                                        $row['cancellations']
                                    ) }}
                                </td>


                                <td class="text-end">
                                    {{ number_format(
                                        $row['cancellation_rate'],
                                        1
                                    ) }}%
                                </td>

                                <td class="text-center">
                                    {{ $row['review_count'] }}
                                </td>

                                <td class="text-center">
                                    {{ $row['average_rating'] !== null
                                        ? number_format($row['average_rating'], 2)
                                        : '-' }}
                                </td>

                                <td class="text-center">
                                    {{-- @if ($row['max_rating'] !== null) --}}
                                        {{ $row['max_rating'] }}
                                        /
                                        {{ $row['min_rating'] }}
                                    {{-- @else
                                        -
                                    @endif --}}
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

{{-- =========================================================
     Annual Total
========================================================= --}}

<div class="mt-4">

    <h6 class="fw-bold mb-3">
        Annual Total
    </h6>

    <div class="table-responsive">

        <table class="table table-bordered align-middle mb-0">

            <thead class="table-light">

                <tr>

                    <th>
                        Annual Total
                    </th>

                    <th class="text-end">
                        Reservations
                    </th>

                    <th class="text-end">
                        Valid
                    </th>

                    <th class="text-end">
                        Completed
                    </th>

                    <th class="text-end">
                        Operation Rate
                    </th>

                    <th class="text-end">
                        Cancellations
                    </th>

                    <th class="text-end">
                        Cancellation Rate
                    </th>

                    <th class="text-end">
                        Reviews
                    </th>

                    <th class="text-end">
                        Avg Rating
                    </th>

                    <th class="text-end">
                        Max / Min
                    </th>

                </tr>

            </thead>


            <tbody>

                <tr class="fw-bold">

                    <td>
                        {{ $year }} Total
                    </td>


                    <td class="text-end">
                        {{ number_format($annualReservations) }}
                    </td>


                    <td class="text-end">
                        {{ number_format($annualValid) }}
                    </td>


                    <td class="text-end">
                        {{ number_format($annualCompleted) }}
                    </td>


                    <td class="text-end">
                        {{ number_format($annualOperationRate, 1) }}%
                    </td>


                    <td class="text-end">
                        {{ number_format($annualCancellations) }}
                    </td>


                    <td class="text-end">
                        {{ number_format($annualCancellationRate, 1) }}%
                    </td>


                    <td class="text-end">
                        {{ number_format($annualReviewCount) }}
                    </td>


                    <td class="text-end">

                        {{ $annualAverageRating !== null
                            ? number_format($annualAverageRating, 2)
                            : '-' }}

                    </td>


                    <td class="text-end">

                        @if ($annualMaxRating !== null)

                            {{ $annualMaxRating }}
                            /
                            {{ $annualMinRating }}

                        @else

                            -

                        @endif

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>

            </div>

        </div>

    </div>

</div>

@endsection
