@extends('layouts.app')

@section('title', 'Lesson History')

@section('content')

<div class="container-fluid">

    {{-- ===============================
         Title
    ================================ --}}
    <div class="mb-4">

        <h2 class="fw-bold mb-1">
            Lesson History
        </h2>

        <p class="text-secondary mb-0">
            View your past lessons and lesson records.
        </p>

    </div>


    {{-- ===============================
         Pending Lesson Records
    ================================ --}}
    @if ($awaitingReservations->isNotEmpty())

        <div class="card shadow-sm mb-5">

            <div class="card-header bg-light py-3">

                <div
                    class="
                        d-flex
                        justify-content-between
                        align-items-center
                    "
                >

                    <div>

                        <h5 class="fw-bold mb-1">

                            <i class="fa-regular fa-clipboard me-1"></i>

                            Pending Lesson Records

                        </h5>

                        <p class="text-secondary small mb-0">
                            Please complete these lesson records when you have time.
                        </p>

                    </div>

                </div>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

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

                                <th class="py-3 text-center">
                                    Status
                                </th>

                                <th class="py-3 text-center">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach ($awaitingReservations as $reservation)

                                @php

                                    $startAt =
                                        \Carbon\Carbon::parse(
                                            $reservation->start_at
                                        );

                                    $endAt =
                                        \Carbon\Carbon::parse(
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

                                        {{ $startAt->format('H:i') }}

                                        -

                                        {{ $endAt->format('H:i') }}

                                    </td>


                                    {{-- Student --}}
                                    <td>

                                        <div class="d-flex align-items-center">

                                            @if (
                                                $reservation
                                                    ->student
                                                    ?->user
                                                    ?->profile_image
                                            )

                                                <img

                                                    src="{{ str_starts_with(
                                                        $reservation->student->user->profile_image,
                                                        'http'
                                                    )
                                                        ? $reservation->student->user->profile_image
                                                        : asset(
                                                            'storage/' .
                                                            $reservation->student->user->profile_image
                                                        )
                                                    }}"

                                                    alt="{{ $reservation->student->user->first_name }}"
                                                    width="40"
                                                    height="40"
                                                    class="rounded-circle me-2"
                                                    style="object-fit: cover;"
                                                >

                                            @else

                                               <i
                                                    class="
                                                        fa-solid
                                                        fa-circle-user
                                                        text-secondary
                                                        me-2
                                                    "
                                                    style="font-size: 40px;"
                                                ></i>

                                            @endif


                                            <div class="fw-semibold">

                                                {{
                                                    $reservation
                                                        ->student
                                                        ?->user
                                                        ?->first_name
                                                    ?? ''
                                                }}

                                                {{
                                                    $reservation
                                                        ->student
                                                        ?->user
                                                        ?->last_name
                                                    ?? ''
                                                }}

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Material --}}
                                    <td>

                                        {{
                                            $reservation
                                                ->material
                                                ?->name
                                            ?? '-'
                                        }}

                                    </td>


                                    {{-- Status --}}
                                    <td class="text-center">

                                        <span
                                            class="
                                                badge
                                                bg-light
                                                text-danger
                                                border
                                                border-danger
                                            "
                                        >
                                            Awaiting Result
                                        </span>

                                    </td>


                                    {{-- Action --}}
                                    <td class="text-center">

                                        <a
                                            href="{{ route(
                                                'teachers.history.show',
                                                $reservation
                                            ) }}"
                                            class="
                                                btn
                                                btn-outline-primary
                                                btn-sm
                                            "
                                        >
                                            Add Record
                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


    @else

        <div class="card shadow-sm mb-5">

            <div class="card-body text-center py-4">

                <i
                    class="
                        fa-regular
                        fa-circle-check
                        fa-2x
                        text-secondary
                        mb-2
                    "
                ></i>

                <p class="text-secondary mb-0">
                    No pending lesson records.
                </p>

            </div>

        </div>

    @endif



    {{-- ===============================
         Past Lessons
    ================================ --}}
    <div class="card shadow-sm">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-1">
                Past Lessons
            </h5>

            <p class="text-secondary small mb-0">
                Completed lessons and attendance history.
            </p>

        </div>


        <div class="card-body p-0">

            @if ($completedReservations->isNotEmpty())

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

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

                                <th class="py-3 text-center">
                                    Status
                                </th>

                                <th class="py-3 text-center">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach (
                                $completedReservations
                                as $reservation
                            )

                                @php

                                    $startAt =
                                        \Carbon\Carbon::parse(
                                            $reservation->start_at
                                        );

                                    $endAt =
                                        \Carbon\Carbon::parse(
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

                                        {{ $startAt->format('H:i') }}

                                        -

                                        {{ $endAt->format('H:i') }}

                                    </td>


                                    {{-- Student --}}
                                   <td>

                                        <div class="d-flex align-items-center">

                                            @if (
                                                $reservation
                                                    ->student
                                                    ?->user
                                                    ?->profile_image
                                            )

                                                <img

                                                    src="{{ str_starts_with(
                                                        $reservation->student->user->profile_image,
                                                        'http'
                                                    )
                                                        ? $reservation->student->user->profile_image
                                                        : asset(
                                                            'storage/' .
                                                            $reservation->student->user->profile_image
                                                        )
                                                    }}"

                                                    alt="{{ $reservation->student->user->first_name }}"
                                                    width="40"
                                                    height="40"
                                                    class="rounded-circle me-2"
                                                    style="object-fit: cover;"
                                                >

                                            @else

                                                <i
                                                    class="
                                                        fa-solid
                                                        fa-circle-user
                                                        text-secondary
                                                        me-2
                                                    "
                                                    style="font-size: 40px;"
                                                ></i>

                                            @endif


                                            <div class="fw-semibold">

                                                {{
                                                    $reservation
                                                        ->student
                                                        ?->user
                                                        ?->first_name
                                                    ?? ''
                                                }}

                                                {{
                                                    $reservation
                                                        ->student
                                                        ?->user
                                                        ?->last_name
                                                    ?? ''
                                                }}

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Material --}}
                                    <td>

                                        {{
                                            $reservation
                                                ->material
                                                ?->name
                                            ?? '-'
                                        }}

                                    </td>


                                    {{-- Status --}}
                                    <td class="text-center">

                                        @if (
                                            $reservation
                                                ->status
                                                ?->status_code
                                            === 'completed'
                                        )

                                            <span class="badge text-bg-success">
                                                Completed
                                            </span>


                                        @elseif (
                                            $reservation
                                                ->status
                                                ?->status_code
                                            === 'absent'
                                        )

                                            <span class="badge text-bg-secondary">
                                                Absent
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Action --}}
                                    <td class="text-center">

                                        <a
                                            href="{{ route(
                                                'teachers.history.show',
                                                $reservation
                                            ) }}"
                                            class="
                                                btn
                                                btn-outline-primary
                                                btn-sm
                                            "
                                        >
                                            View Details
                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


            @else

                <div class="text-center py-5">

                    <i
                        class="
                            fa-solid
                            fa-clock-rotate-left
                            fa-2x
                            text-secondary
                            mb-3
                        "
                    ></i>

                    <h5 class="fw-bold">
                        No lesson history
                    </h5>

                    <p class="text-secondary mb-0">
                        Completed lessons will appear here.
                    </p>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection
