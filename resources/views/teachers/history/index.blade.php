@extends('layouts.app')

@section('title', 'Lesson History')

@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | Dummy Data
    |--------------------------------------------------------------------------
    | バック側実装後はこの部分を削除
    */

    $pastReservations = collect([

        /*
         * Completed
         */
        (object) [
            'id' => 2,

            'start_at' => '2026-09-13 14:00:00',
            'end_at' => '2026-09-13 14:30:00',

            'student' => (object) [
                'user' => (object) [
                    'first_name' => 'Taro',
                    'last_name' => 'Yamada',
                    'profile_image' => null,
                ],
            ],

            'material' => (object) [
                'name' => 'Grammar',
            ],

            'status' => (object) [
                'status_code' => 'completed',
            ],
        ],


        /*
         * Absent
         */
        (object) [
            'id' => 3,

            'start_at' => '2026-09-12 09:00:00',
            'end_at' => '2026-09-12 09:30:00',

            'student' => (object) [
                'user' => (object) [
                    'first_name' => 'Hanako',
                    'last_name' => 'Sato',
                    'profile_image' => null,
                ],
            ],

            'material' => (object) [
                'name' => 'Business English',
            ],

            'status' => (object) [
                'status_code' => 'absent',
            ],
        ],


        /*
         * Awaiting Result
         */
        (object) [
            'id' => 4,

            'start_at' => '2026-09-11 16:00:00',
            'end_at' => '2026-09-11 16:30:00',

            'student' => (object) [
                'user' => (object) [
                    'first_name' => 'Ken',
                    'last_name' => 'Suzuki',
                    'profile_image' => null,
                ],
            ],

            'material' => (object) [
                'name' => 'Pronunciation',
            ],

            'status' => (object) [
                'status_code' => 'awaiting_result',
            ],
        ],

    ]);


    /*
    |--------------------------------------------------------------------------
    | Action Required
    |--------------------------------------------------------------------------
    */

    $awaitingReservations =
        $pastReservations->filter(
            function ($reservation) {

                return
                    $reservation
                        ->status
                        ->status_code
                    === 'awaiting_result';

            }
        );


    /*
    |--------------------------------------------------------------------------
    | Past Lessons
    |--------------------------------------------------------------------------
    */

    $completedReservations =
        $pastReservations->filter(
            function ($reservation) {

                return in_array(
                    $reservation
                        ->status
                        ->status_code,
                    [
                        'completed',
                        'absent',
                    ]
                );

            }
        );

@endphp


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

                                <td class="px-4">

                                    <div class="fw-bold">
                                        {{ $startAt->format('M d, Y') }}
                                    </div>

                                    <small class="text-secondary">
                                        {{ $startAt->format('l') }}
                                    </small>

                                </td>


                                <td>

                                    {{ $startAt->format('h:i A') }}

                                    -

                                    {{ $endAt->format('h:i A') }}

                                </td>


                                <td>

                                    {{
                                        $reservation
                                            ->student
                                            ->user
                                            ->first_name
                                    }}

                                    {{
                                        $reservation
                                            ->student
                                            ->user
                                            ->last_name
                                    }}

                                </td>


                                <td>

                                    {{
                                        $reservation
                                            ->material
                                            ->name
                                    }}

                                </td>


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


                                <td class="text-center">

                                    <a
                                        href="{{ route(
                                            'teachers.history.show.test',
                                            ['status' => 'awaiting_result']
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

                                        {{ $startAt->format('h:i A') }}

                                        -

                                        {{ $endAt->format('h:i A') }}

                                    </td>


                                    {{-- Student --}}
                                    <td>

                                        <div class="d-flex align-items-center">

                                            <i
                                                class="
                                                    fa-solid
                                                    fa-circle-user
                                                    fa-2x
                                                    text-secondary
                                                    me-2
                                                "
                                            ></i>

                                            <div class="fw-semibold">

                                                {{
                                                    $reservation
                                                        ->student
                                                        ->user
                                                        ->first_name
                                                }}

                                                {{
                                                    $reservation
                                                        ->student
                                                        ->user
                                                        ->last_name
                                                }}

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Material --}}
                                    <td>

                                        {{
                                            $reservation
                                                ->material
                                                ->name
                                        }}

                                    </td>


                                    {{-- Status --}}
                                    <td class="text-center">

                                        @if (
                                            $reservation
                                                ->status
                                                ->status_code
                                            === 'completed'
                                        )

                                            <span class="badge text-bg-success">
                                                Completed
                                            </span>


                                        @elseif (
                                            $reservation
                                                ->status
                                                ->status_code
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
                                                'teachers.history.show.test',
                                                ['status' => $reservation->status->status_code]
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



    {{-- ===============================
         Back
    ================================ --}}
    <div class="mt-4">

        <a
            href="{{ route(
                'teachers.reservations.index'
            ) }}"
            class="btn btn-outline-secondary"
        >
            Back
        </a>

    </div>

</div>

@endsection