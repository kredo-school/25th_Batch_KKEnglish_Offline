@extends('layouts.app')

@section('title', 'My Lessons')

@section('content')

<div class="container-fluid py-4">

    {{-- ===============================
         Title
    ================================ --}}
    <div class="mb-4">

        <h2 class="fw-bold mb-1">
            My Lessons
        </h2>

        <p class="text-secondary mb-0">
            View your upcoming scheduled lessons.
        </p>

    </div>


    {{-- ===============================
         Upcoming Lessons
    ================================ --}}
    <div class="card">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                Upcoming Lessons
            </h5>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    {{-- Header --}}
                    <thead class="table-light">

                        <tr>

                            <th class="px-4 py-3">
                                Date
                            </th>

                            <th class="py-3">
                                Time
                            </th>

                            <th class="py-3">
                                Student
                            </th>

                            <th class="py-3">
                                Material
                            </th>

                            <th class="py-3">
                                Status
                            </th>

                            <th class="py-3 text-end pe-4">
                                Action
                            </th>

                        </tr>

                    </thead>


                    {{-- Body --}}
                    <tbody>

                        @forelse ($upcomingReservations as $reservation)

                            @php

                                $startAt = \Carbon\Carbon::parse(
                                    $reservation->start_at
                                );

                                $endAt = \Carbon\Carbon::parse(
                                    $reservation->end_at
                                );

                            @endphp


                            <tr>

                                {{-- Date --}}
                                <td class="px-4">

                                    <div class="fw-bold">

                                        {{ $startAt->format('M d, Y') }}

                                    </div>

                                    <small class="text-secondary">

                                        {{ $startAt->format('l') }}

                                    </small>

                                </td>


                                {{-- Time --}}
                                <td>

                                    {{ $startAt->format('h:i A') }}

                                    -

                                    {{ $endAt->format('h:i A') }}

                                </td>


                                {{-- Student --}}
                                <td>

                                    <i class="fa-solid fa-circle-user me-2"></i>

                                    {{ $reservation->student->user->first_name }}

                                    {{ $reservation->student->user->last_name }}

                                </td>


                                {{-- Material --}}
                                <td>

                                    {{ $reservation->material->name }}

                                </td>


                                {{-- Status --}}
                                <td>

                                    @if (
                                        $reservation->status->status_code
                                        === 'confirmed'
                                    )

                                        <span class="badge text-bg-primary">
                                            Confirmed
                                        </span>

                                    @elseif (
                                        $reservation->status->status_code
                                        === 'pending'
                                    )

                                        <span class="badge text-bg-warning">
                                            Pending
                                        </span>

                                    @elseif (
                                        $reservation->status->status_code
                                        === 'cancelled'
                                    )

                                        <span class="badge text-bg-danger">
                                            Cancelled
                                        </span>

                                    @else

                                        <span class="badge text-bg-secondary">

                                            {{
                                                ucfirst(
                                                    $reservation
                                                        ->status
                                                        ->status_code
                                                )
                                            }}

                                        </span>

                                    @endif

                                </td>


                                {{-- Action --}}
                                <td class="text-end pe-4">

                                    <a
                                        href="{{ route(
                                            'teachers.reservations.show',
                                            $reservation->id
                                        ) }}"
                                        class="btn btn-outline-primary btn-sm"
                                    >
                                        Details
                                    </a>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center py-5 text-secondary"
                                >

                                    No upcoming lessons.

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
