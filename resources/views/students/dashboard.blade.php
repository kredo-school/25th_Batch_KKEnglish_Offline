@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')

<link
    href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css"
    rel="stylesheet"
>

<script
    src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"
></script>


<style>

    #calendar a {
        color: #000;
        text-decoration: none;
    }


    /* =========================================
       Dashboard Titles
    ========================================= */

    .dashboard-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0;
    }

    .dashboard-card-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6c757d;
    }


    /* =========================================
       Today's Lessons
    ========================================= */

    .today-lesson-item {
        transition: background-color 0.2s;
    }

    .today-lesson-item:hover {
        background-color: #f8f9fa;
    }


    /* =========================================
       FullCalendar
    ========================================= */

    #calendar .fc-toolbar-title {
        font-size: 1.1rem;
        font-weight: 700;
    }

</style>



<div class="container-fluid">


    {{-- =====================================================
         Hello Header
    ====================================================== --}}
    <div
        class="
            bg-light
            mb-4
            d-flex
            justify-content-between
            align-items-center
            px-4
            py-3
        "
    >

        {{-- Name / Date --}}
        <div>

            <h2 class="fw-bold mb-1">
                Hello, {{ auth()->user()->first_name }}
            </h2>

            <p class="text-secondary mb-0">
                {{ now()->format('l, F j') }}
            </p>

            <p class="fw-semibold mb-0">

                <i class="fa-regular fa-clock me-1"></i>

                <span id="currentTime">
                    {{ now()->format('H:i') }}
                </span>

            </p>

        </div>


        {{-- Book Lesson --}}
        <a
            href="{{ route('students.reservations.index') }}"
            class="btn btn-primary"
        >
            Book a Lesson
        </a>

    </div>



 {{-- =====================================================
     Main Dashboard
====================================================== --}}
<div class="row g-4 align-items-stretch">


    {{-- =================================================
         Left : Today's Lessons
    ================================================== --}}
    <div class="col-lg-7">

        <div class="card h-100">

            <div class="card-body p-4">


                {{-- Title --}}
                <div class="mb-4">

                    <h5 class="dashboard-section-title mb-1">
                        Today's Lessons
                    </h5>

                    <div class="text-secondary small">
                        {{ now()->format('F j') }}
                    </div>

                </div>


                @forelse ($todayLessons as $lesson)

                    <div
                        class="
                            today-lesson-item
                            border
                            rounded
                            px-3
                            py-3
                            mb-3
                        "
                    >

                        <div
                            class="
                                d-flex
                                justify-content-between
                                align-items-center
                                gap-3
                            "
                        >

                            <div>

                                {{-- Time --}}
                                <div class="fw-bold mb-2">

                                     {{ $lesson->start_at->format('H:i') }}
                                    -
                                    {{ $lesson->end_at->format('H:i') }}

                                    {{-- Status --}}
                                   @if ($lesson->statusType === 'ongoing')

                                        <span class="badge bg-danger ms-2">
                                            Ongoing
                                        </span>

                                    @elseif ($lesson->statusType === 'upcoming')

                                        <span class="badge bg-primary ms-2">
                                            Upcoming
                                        </span>

                                    @endif

                                </div>


                              {{-- Teacher / Material --}}
                            <div
                                class="
                                    d-flex
                                    align-items-center
                                    gap-2
                                    flex-wrap
                                "
                            >

                                {{-- Teacher Image --}}
                                @if ($lesson->teacher?->user?->profile_image)

                                    <img
                                        src="{{ $lesson->teacher->user->profile_image }}"
                                        alt="Teacher"
                                        class="rounded-circle"
                                        style="
                                            width: 36px;
                                            height: 36px;
                                            object-fit: cover;
                                        "
                                    >

                                @else

                                    <div
                                        class="
                                            rounded-circle
                                            bg-secondary-subtle
                                            d-flex
                                            align-items-center
                                            justify-content-center
                                        "
                                        style="
                                            width: 36px;
                                            height: 36px;
                                        "
                                    >
                                        <i class="fa-solid fa-user text-secondary"></i>
                                    </div>

                                @endif


                                {{-- Teacher Name --}}
                                <span>

                                    {{ $lesson->teacher?->user?->first_name ?? '' }}

                                </span>


                                {{-- Material --}}
                                <span
                                    class="
                                        badge
                                        bg-secondary-subtle
                                        text-dark
                                        border
                                    "
                                >
                                    {{ $lesson->material?->name ?? '-' }}
                                </span>

                            </div>

                            </div>

                            {{-- Cancel --}}
                            @if ($lesson->statusType === 'upcoming')

                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#cancelModal{{ $lesson->id }}"
                                >
                                    Cancel
                                </button>

                            @endif

                        </div>

                    </div>


                    {{-- Cancel Modal --}}
                 @if ($lesson->statusType === 'upcoming')
                    <div
                        class="modal fade"
                        id="cancelModal{{ $lesson->id }}"
                        tabindex="-1"
                        aria-hidden="true"
                    >

                        <div class="modal-dialog modal-dialog-centered">

                            <div class="modal-content">

                                <form
                                    action="{{ route(
                                        'students.reservations.cancel',
                                        $lesson
                                    ) }}"
                                    method="POST"
                                >

                                    @csrf
                                    @method('PATCH')


                                    <div class="modal-header">

                                        <h5 class="modal-title">
                                            Cancel Lesson
                                        </h5>

                                        <button
                                            type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal"
                                        ></button>

                                    </div>


                                    <div class="modal-body">

                                        <p class="mb-3">
                                            Are you sure you want to cancel this lesson?
                                        </p>

                                        <div class="bg-light rounded p-3 mb-4">

                                            <div class="fw-bold mb-1">
                                                {{ \Carbon\Carbon::parse($lesson->start_at)->format('F j') }}
                                            </div>

                                            <div class="mb-1">
                                                {{ \Carbon\Carbon::parse($lesson->start_at)->format('H:i') }}
                                                -
                                                {{ \Carbon\Carbon::parse($lesson->end_at)->format('H:i') }}
                                            </div>

                                            <div class="mb-2">
                                                {{ $lesson->teacher?->user?->first_name ?? '' }}
                                            </div>

                                            <span
                                                class="
                                                    badge
                                                    bg-secondary-subtle
                                                    text-dark
                                                    border
                                                "
                                            >
                                                {{ $lesson->material?->name ?? '-' }}
                                            </span>

                                        </div>


                                        <label
                                            for="reason{{ $lesson->id }}"
                                            class="form-label fw-bold"
                                        >
                                            Cancellation Reason
                                        </label>

                                        <textarea
                                            id="reason{{ $lesson->id }}"
                                            name="cancellation_reason"
                                            class="form-control"
                                            rows="3"
                                            maxlength="500"
                                            placeholder="Optional"
                                        ></textarea>

                                    </div>


                                    <div class="modal-footer">

                                        <button
                                            type="button"
                                            class="btn btn-outline-secondary"
                                            data-bs-dismiss="modal"
                                        >
                                            Back
                                        </button>

                                        <button
                                            type="submit"
                                            class="btn btn-danger"
                                        >
                                            Cancel Lesson
                                        </button>

                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

                  @endif


                @empty

                    <div class="text-center py-5">

                        <i
                            class="
                                fa-regular
                                fa-calendar-check
                                fa-2x
                                text-secondary
                                mb-3
                            "
                        ></i>

                        <p class="text-secondary mb-0">
                            No lessons scheduled for today.
                        </p>

                    </div>

                @endforelse


            </div>

        </div>

    </div>



    {{-- =================================================
         Right
    ================================================== --}}
    <div class="col-lg-5">

        <div class="d-flex flex-column h-100 gap-3">


            {{-- =========================================
                 Point / Level
            ========================================== --}}
            <div class="row g-3">


                {{-- Available Points --}}
                <div class="col-5">

                     <a
                        href="{{ route('students.point-history.index') }}"
                        class="text-decoration-none text-dark"
                    >

                    <div class="card h-100">

                        <div class="card-body p-3">

                            <div class="dashboard-card-label mb-2">
                                Available Points
                            </div>

                            <div
                                class="
                                    d-flex
                                    align-items-center
                                    gap-2
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-coins
                                        text-warning
                                    "
                                ></i>

                                <h5 class="fw-bold mb-0">

                                    {{
                                        number_format(
                                            $student->point_balance ?? 0
                                        )
                                    }}

                                </h5>

                                <span class="text-secondary small">
                                    pt
                                </span>

                            </div>

                        </div>

                    </div>

                  </a>

                </div>


               {{-- My Level --}}
                <div class="col-7">

                    <div class="card h-100">

                        <div class="card-body p-3">

                            <div class="dashboard-card-label mb-2">
                                My Level
                            </div>

                            <div
                                class="
                                    d-flex
                                    align-items-center
                                    gap-2
                                    flex-wrap
                                "
                            >

                                <h5 class="fw-bold mb-0">
                                   {{ $student->level ?? 'Not Set' }}
                                </h5>

                                <button
                                    type="button"
                                    class="
                                        btn
                                        btn-outline-primary
                                        btn-sm
                                        ms-auto
                                    "
                                    data-bs-toggle="modal"
                                    data-bs-target="#levelModal"
                                >
                                    Change
                                </button>

                            </div>

                        </div>

                    </div>

                </div>


            </div>



            {{-- =========================================
                 Announcements
            ========================================== --}}
            <div class="card flex-grow-1">

                <div class="card-body p-4">

                    <h5 class="dashboard-section-title mb-3">
                        Announcements
                    </h5>

                    @forelse ($announcements as $announcement)

                        <div
                            class="
                                border-bottom
                                pb-3
                                mb-3
                            "
                        >

                            <div class="fw-semibold mb-1">

                                {{ $announcement->title }}

                            </div>


                            <div class="text-secondary small">

                                {{ $announcement->content }}

                            </div>

                        </div>

                    @empty

                        <div class="text-center py-3">

                            <p class="text-secondary mb-0">
                                No announcements.
                            </p>

                        </div>

                    @endforelse

                </div>

            </div>


        </div>

    </div>


</div>



    {{-- =====================================================
         Reservation Calendar
    ====================================================== --}}
    <div class="row mt-5">

        <div class="col-12">

            <div class="card">

                <div class="card-body">


                    <div
                        class="
                            d-flex
                            align-items-center
                            mb-3
                        "
                    >

                        <i
                            class="
                                fa-regular
                                fa-calendar
                                me-2
                            "
                        ></i>

                        <h5 class="dashboard-section-title">
                            Reservation Calendar
                        </h5>

                    </div>


                    <div id="calendar"></div>


                </div>

            </div>

        </div>

    </div>


</div>



{{-- =============================================================
     English Level Modal
============================================================= --}}
<div
    class="modal fade"
    id="levelModal"
    tabindex="-1"
    aria-labelledby="levelModalLabel"
    aria-hidden="true"
>

    <div
        class="
            modal-dialog
            modal-dialog-centered
        "
    >

        <div class="modal-content">

            <form
                action="{{ route('students.update.Level') }}"
                method="POST"
            >
                @csrf
                @method('PATCH')


            {{-- Header --}}
            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="levelModalLabel"
                >
                    Select Your English Level
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


                <div class="list-group">


                    {{-- A1 Beginner --}}
                    <label class="list-group-item py-3">

                        <div
                            class="
                                d-flex
                                align-items-start
                                gap-3
                            "
                        >

                            <input
                                class="form-check-input mt-1"
                                type="radio"
                                name="level"
                                value="A1"
                                {{ $student->level === 'A1' ? 'checked' : '' }}
                            >

                            <div>

                                <div class="fw-bold">
                                    A1 - Beginner
                                </div>

                                <div class="text-secondary small">
                                    Just starting to learn English.
                                </div>

                            </div>

                        </div>

                    </label>



                     {{-- A2 Elementary --}}
                    <label class="list-group-item py-3">

                        <div
                            class="
                                d-flex
                                align-items-start
                                gap-3
                            "
                        >

                            <input
                                class="form-check-input mt-1"
                                type="radio"
                                name="level"
                                value="A2"
                                {{ $student->level === 'A2' ? 'checked' : '' }}
                            >

                            <div>

                                <div class="fw-bold">
                                    A2 - Elementary
                                </div>

                                <div class="text-secondary small">
                                    Can manage simple everyday English.
                                </div>

                            </div>

                        </div>

                    </label>



                     {{-- B1 Intermediate --}}
                    <label class="list-group-item py-3">

                        <div
                            class="
                                d-flex
                                align-items-start
                                gap-3
                            "
                        >

                            <input
                                class="form-check-input mt-1"
                                type="radio"
                                name="level"
                                value="B1"
                                {{ $student->level === 'B1' ? 'checked' : '' }}
                            >

                            <div>

                                <div class="fw-bold">
                                    B1 - Intermediate
                                </div>

                                <div class="text-secondary small">
                                   Can handle everyday conversations.
                                </div>

                            </div>

                        </div>

                    </label>

                    {{-- B2 Upper Intermediate --}}
                    <label class="list-group-item py-3">

                        <div class="d-flex align-items-start gap-3">

                            <input
                                class="form-check-input mt-1"
                                type="radio"
                                name="level"
                                value="B2"
                                {{ $student->level === 'B2' ? 'checked' : '' }}
                            >

                            <div>
                                <div class="fw-bold">
                                    B2 - Upper Intermediate
                                </div>

                                <div class="text-secondary small">
                                    Can communicate clearly in many situations.
                                </div>
                            </div>

                        </div>

                    </label>



                     {{-- C1 Advanced --}}
                    <label class="list-group-item py-3">

                        <div
                            class="
                                d-flex
                                align-items-start
                                gap-3
                            "
                        >

                            <input
                                class="form-check-input mt-1"
                                type="radio"
                                name="level"
                                value="C1"
                                {{ $student->level === 'C1' ? 'checked' : '' }}
                            >

                            <div>

                                <div class="fw-bold">
                                    C1 - Advanced
                                </div>

                                <div class="text-secondary small">
                                    Can communicate fluently and in detail.
                                </div>

                            </div>

                        </div>

                    </label>

                    {{-- C2 Proficient --}}
                    <label class="list-group-item py-3">

                        <div class="d-flex align-items-start gap-3">

                            <input
                                class="form-check-input mt-1"
                                type="radio"
                                name="level"
                                value="C2"
                                {{ $student->level === 'C2' ? 'checked' : '' }}
                            >

                            <div>
                                <div class="fw-bold">
                                    C2 - Proficient
                                </div>

                                <div class="text-secondary small">
                                    Can understand and communicate English at a highly proficient level.
                                </div>
                            </div>

                        </div>

                    </label>


                </div>


            </div>



            {{-- Footer --}}
            <div class="modal-footer">


                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Save
                </button>


            </div>


          </form>
        </div>

    </div>

</div>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        // ==========================
        // Current Time
        // ==========================
        function updateCurrentTime() {

            const now = new Date();

            const hours = String(
                now.getHours()
            ).padStart(2, '0');

            const minutes = String(
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


        // ==========================
        // FullCalendar
        // ==========================

        const calendarEl =
           document.getElementById('calendar');

        const calendar =
            new FullCalendar.Calendar(
                calendarEl,
                {

                    initialView:
                        'dayGridMonth',

                    locale:
                        'ja',

                    headerToolbar: {
                        left: 'title',
                        center: '',
                        right: 'prev,next'
                    },


                    // ==========================
                    // Reservations
                    // ==========================
                    events:
                         @json($calendarEvents),


                    // ==========================
                    // Time
                    // ==========================
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    },


                    // ==========================
                    // Event Design
                    // ==========================
                eventContent: function (info) {

                    const wrapper =
                        document.createElement('div');

                    const statusType =
                        info.event.extendedProps.statusType;


                    wrapper.className =
                        'w-100 rounded px-2 py-1';

                    wrapper.style.fontSize =
                        '0.8rem';

                    wrapper.style.fontWeight =
                        '500';

                    wrapper.style.whiteSpace =
                        'nowrap';

                    wrapper.style.overflow =
                        'hidden';

                    wrapper.style.textOverflow =
                        'ellipsis';


                    // ==========================
                    // Past
                    // ==========================
                    if (statusType === 'past') {

                        wrapper.style.backgroundColor =
                            '#f1f3f5';

                        wrapper.style.color =
                            '#868e96';

                        wrapper.style.border =
                            '1px solid #dee2e6';

                    }

                    // ==========================
                    // Ongoing
                    // ==========================
                    else if (statusType === 'ongoing') {

                        wrapper.style.backgroundColor =
                            '#fde2e2';

                        wrapper.style.color =
                            '#b42318';

                        wrapper.style.border =
                            '1px solid #f5b7b1';

                    }

                    // ==========================
                    // Upcoming
                    // ==========================
                    else {

                        wrapper.style.backgroundColor =
                            '#eef5ff';

                        wrapper.style.color =
                            '#2563eb';

                        wrapper.style.border =
                            '1px solid #bfdbfe';

                    }


                    wrapper.textContent =
                        `${info.timeText} ${info.event.title}`;


                    return {
                        domNodes: [wrapper]
                    };

                },

                    height:
                        'auto'

                }
            );


        calendar.render();

    }
);

</script>


@endsection