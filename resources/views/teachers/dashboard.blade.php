@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')

<div class="container-fluid">

    {{-- ===============================
         Hello Header
    ================================ --}}
    <div class="bg-light mb-4 px-4 py-3">

        <h2 class="fw-bold mb-1">

            Hello,
            {{ auth()->user()->first_name }}

        </h2>

        <p class="text-secondary mb-1">

            {{ now()->format('l, F j') }}

        </p>

        <p class="fw-semibold mb-0">

            <i class="fa-regular fa-clock me-1"></i>

            <span id="currentTime">
                {{ now()->format('H:i') }}
            </span>

        </p>

    </div>


    {{-- ===============================
         Today's Lessons
    ================================ --}}
    <div class="card">

        <div class="card-header bg-white py-3">

            <div
                class="
                    d-flex
                    justify-content-between
                    align-items-center
                "
            >

                <div>

                    <h5 class="fw-bold mb-1">
                        Today's Lessons
                    </h5>

                </div>


                {{-- My Lessons --}}
                <a
                    href="{{ route(
                        'teachers.reservations.index'
                    ) }}"
                    class="btn btn-outline-primary btn-sm"
                >
                    View My Lessons
                </a>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table
                    class="
                        table
                        table-hover
                        align-middle
                        mb-0
                    "
                >

                    {{-- Header --}}
                    <thead class="table-light">

                        <tr>

                            <th class="px-4 py-3">
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

                        @forelse (
                            $todayLessons
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

                                {{-- ===============================
                                     Time
                                ================================ --}}
                                <td class="px-4">

                                    <div class="fw-bold">

                                        {{
                                            $startAt->format(
                                                'H:i'
                                            )
                                        }}

                                        -

                                        {{
                                            $endAt->format(
                                                'H:i'
                                            )
                                        }}

                                    </div>

                                </td>


                                {{-- ===============================
                                     Student
                                ================================ --}}
                                <td>

                                    <div
                                        class="
                                            d-flex
                                            align-items-center
                                        "
                                    >
                                    @if ($reservation->student?->user?->profile_image)
                                        <img
                                            src="{{ asset('storage/' . $reservation->student->user->profile_image) }}"
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
                                                fa-3x
                                                me-2
                                            "
                                        ></i>
                                    @endif

                                        <span>

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

                                        </span>

                                    </div>

                                </td>


                                {{-- ===============================
                                     Material
                                ================================ --}}
                                <td>

                                    {{
                                        $reservation
                                            ->material
                                            ?->name
                                        ?? '-'
                                    }}

                                </td>


                                {{-- ===============================
                                     Status
                                ================================ --}}
                                <td>

                                    @if (
                                        $reservation
                                            ->status
                                            ?->status_code
                                        === 'confirmed'
                                    )

                                        <span
                                            class="
                                                badge
                                                text-bg-primary
                                            "
                                        >
                                            Confirmed
                                        </span>

                                    @elseif (
                                        $reservation
                                            ->status
                                            ?->status_code
                                        === 'pending'
                                    )

                                        <span
                                            class="
                                                badge
                                                text-bg-warning
                                            "
                                        >
                                            Pending
                                        </span>

                                    @else

                                        <span
                                            class="
                                                badge
                                                text-bg-secondary
                                            "
                                        >

                                            {{
                                                ucfirst(
                                                    $reservation
                                                        ->status
                                                        ?->status_code
                                                    ?? ''
                                                )
                                            }}

                                        </span>

                                    @endif

                                </td>


                                {{-- ===============================
                                     Action
                                ================================ --}}
                                <td class="text-end pe-4">

                                    <a
                                        href="{{ route(
                                            'teachers.reservations.show',
                                            $reservation
                                        ) }}"
                                        class="
                                            btn
                                            btn-outline-primary
                                            btn-sm
                                        "
                                    >
                                        Details
                                    </a>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="
                                        text-center
                                        py-5
                                        text-secondary
                                    "
                                >

                                    No lessons scheduled for today.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        function updateCurrentTime() {

            const now = new Date();

            const hours =
                String(
                    now.getHours()
                ).padStart(2, '0');

            const minutes =
                String(
                    now.getMinutes()
                ).padStart(2, '0');

            document
                .getElementById('currentTime')
                .textContent =
                    `${hours}:${minutes}`;
        }


        updateCurrentTime();

        setInterval(
            updateCurrentTime,
            60000
        );

    }
);

</script>

@endsection
