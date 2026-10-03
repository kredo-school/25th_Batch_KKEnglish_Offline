@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')

<div class="container-fluid" id="teacher-dashboard-page"
    data-teacher-id="{{ auth()->user()->teacher?->id }}">

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
                                Station
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
                                    Station
                                ================================ --}}
                                <td>

                                    <div class="text-secondary small">

                                        <i class="fa-solid fa-location-dot me-1"></i>

                                        Station 3

                                    </div>

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
                                    colspan="6"
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



{{-- ===============================
     Announcements
================================ --}}
<div class="card mt-4">

    {{-- Header --}}
    <div class="card-header bg-white py-3">

        <h5 class="fw-bold mb-0">
            Announcements
        </h5>

    </div>


    {{-- Body --}}
    <div class="card-body">

        @forelse ($announcements as $announcement)

            <div class="border-bottom pb-3 mb-3">

                {{-- Title / Date --}}
                <div
                    class="
                        d-flex
                        justify-content-between
                        align-items-start
                        gap-3
                        mb-1
                    "
                >

                    <div class="fw-semibold">
                        {{ $announcement->title }}
                    </div>

                    <small class="text-secondary text-nowrap">
                        {{ $announcement->created_at?->format('M d') }}
                    </small>

                </div>


                {{-- Content --}}
                <div class="text-secondary small">
                    {{ $announcement->content }}
                </div>

            </div>

        @empty

            <div class="text-center py-4">

                <p class="text-secondary mb-0">
                    No announcements.
                </p>

            </div>

        @endforelse

        {{-- View All --}}
        <div class="text-end">

            <button
                type="button"
                class="btn btn-link btn-sm text-decoration-none p-0"
                data-bs-toggle="modal"
                data-bs-target="#teacherAnnouncementsModal"
            >
                View All
                <i class="fa-solid fa-chevron-right ms-1"></i>
            </button>

        </div>

    </div>

</div>

</div>

{{-- =====================================================
     Teacher Announcements Modal
====================================================== --}}
<div
    class="modal fade"
    id="teacherAnnouncementsModal"
    tabindex="-1"
    aria-labelledby="teacherAnnouncementsModalLabel"
    aria-hidden="true"
>

    <div
        class="
            modal-dialog
            modal-dialog-centered
            modal-dialog-scrollable
            modal-lg
        "
    >

        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header">

                <h5
                    class="modal-title fw-bold"
                    id="teacherAnnouncementsModalLabel"
                >
                    Announcements
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            {{-- Body --}}
            <div class="modal-body">

                @forelse ($allAnnouncements as $announcement)

                    <div class="border-bottom py-3">

                        {{-- Title / Date --}}
                        <div
                            class="
                                d-flex
                                justify-content-between
                                align-items-start
                                gap-3
                                mb-2
                            "
                        >

                            <div class="fw-semibold">
                                {{ $announcement->title }}
                            </div>

                            <small class="text-secondary text-nowrap">
                                {{ $announcement->created_at?->format('M d, Y') }}
                            </small>

                        </div>


                        {{-- Content --}}
                        <div class="text-secondary small">
                            {{ $announcement->content }}
                        </div>

                    </div>

                @empty

                    <div class="text-center py-4">

                        <i
                            class="
                                fa-regular
                                fa-bell
                                fa-2x
                                text-secondary
                                mb-3
                            "
                        ></i>

                        <p class="text-secondary mb-0">
                            No announcements.
                        </p>

                    </div>

                @endforelse

            </div>


            {{-- Footer --}}
            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal"
                >
                    Close
                </button>

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
