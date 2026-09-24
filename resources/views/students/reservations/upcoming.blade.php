@extends('layouts.app')

@section('title', 'Upcoming Lessons')

@section('content')

<style>
.upcoming-monkey-area {
    display: flex;
    align-items: center;
    margin-left: 6px;
}

.upcoming-monkey {
    width: 52px;
    height: 52px;
    object-fit: contain;
}

.upcoming-monkey-bubble {
    margin-left: 8px;
    background: white;
    border: 1px solid #ccc;
    border-radius: 12px;
    padding: 8px 12px;
    font-size: 0.85rem;
    font-weight: 600;
    white-space: nowrap;

    opacity: 0;
    transform: translateX(-5px);
    transition: all 0.4s ease;
}

.upcoming-monkey-bubble.show {
    opacity: 1;
    transform: translateX(0);
}
</style>

<div class="container-fluid">

    {{-- ===============================
        Title
    =============================== --}}
    <div class="mb-4">

        <div class="d-flex align-items-center mb-1">

            <h2 class="fw-bold mb-0">
                Upcoming Lessons
            </h2>

            <div class="upcoming-monkey-area">

                <img
                    src="{{ asset('images/kk-monkey.png') }}"
                    alt="KK English Monkey"
                    class="upcoming-monkey"
                >

                <div
                    id="upcomingMonkeyBubble"
                    class="upcoming-monkey-bubble"
                >
                    Enjoy your lesson!
                </div>

            </div>

        </div>

        <p class="text-secondary mb-0">
            View and manage your upcoming reservations.
        </p>

    </div>


    {{-- ===============================
         Success Message
    ================================ --}}
    @if (session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ===============================
         Reservation List
    ================================ --}}
    @if ($reservations->isNotEmpty())

        <div class="card shadow-sm">

            <div class="card-header bg-white py-3">

                <h5 class="fw-bold mb-0">
                    Reserved Lessons
                </h5>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">


                        {{-- ===============================
                             Header
                        ================================ --}}
                        <thead class="table-light">

                            <tr>

                                <th class="px-4 py-3">
                                    Date
                                </th>

                                <th class="py-3">
                                    Time
                                </th>

                                <th class="py-3">
                                    Teacher
                                </th>

                                <th class="py-3">
                                    Material
                                </th>

                                <th class="py-3">
                                    Points
                                </th>

                                <th class="py-3 text-center">
                                    Status
                                </th>

                                <th class="py-3 text-center">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        {{-- ===============================
                             Body
                        ================================ --}}
                        <tbody>

                            @foreach ($reservations as $reservation)

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
                                         Date
                                    ================================ --}}
                                    <td class="px-4">

                                        <div class="fw-bold">

                                            {{
                                                $startAt->format(
                                                    'M d, Y'
                                                )
                                            }}

                                        </div>

                                        <small class="text-secondary">

                                            {{
                                                $startAt->format(
                                                    'l'
                                                )
                                            }}

                                        </small>

                                    </td>


                                    {{-- ===============================
                                         Time
                                    ================================ --}}
                                    <td>

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

                                    </td>


                                    {{-- ===============================
                                         Teacher
                                    ================================ --}}
                                    <td>

                                        <div class="d-flex align-items-center">


                                            {{-- Teacher Image --}}
                                            @if (
                                                $reservation->teacher
                                                &&
                                                $reservation->teacher->user
                                                &&
                                                $reservation->teacher->user->profile_image
                                            )

                                                <img
                                                    src="{{ $reservation->teacher->user->profile_image }}"
                                                    alt="{{ $reservation->teacher->user->first_name }}"
                                                    width="45"
                                                    height="45"
                                                    class="rounded-circle me-2"
                                                    style="object-fit: cover;"
                                                >

                                            @else

                                                <div
                                                    class="
                                                        rounded-circle
                                                        bg-secondary
                                                        d-flex
                                                        justify-content-center
                                                        align-items-center
                                                        text-white
                                                        me-2
                                                    "
                                                    style="
                                                        width: 45px;
                                                        height: 45px;
                                                    "
                                                >

                                                    <i class="fa-solid fa-user"></i>

                                                </div>

                                            @endif


                                            {{-- Teacher Name --}}
                                            <div>

                                                <div class="fw-semibold">

                                                    {{
                                                        $reservation
                                                            ->teacher
                                                            ?->user
                                                            ?->first_name
                                                        ?? 'Teacher'
                                                    }}

                                                    {{
                                                        $reservation
                                                            ->teacher
                                                            ?->user
                                                            ?->last_name
                                                        ?? ''
                                                    }}

                                                </div>


                                                <small class="text-secondary">

                                                    {{
                                                        $reservation
                                                            ->teacher
                                                            ?->user
                                                            ?->nationality
                                                        ?? 'N/A'
                                                    }}

                                                </small>

                                            </div>

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
                                         Points
                                    ================================ --}}
                                    <td>

                                        <span
                                            class="
                                                badge
                                                bg-white
                                                text-dark
                                                border
                                                px-2
                                                py-2
                                            "
                                        >

                                            {{
                                                number_format(
                                                    $reservation->point_cost
                                                    ?? 0
                                                )
                                            }}
                                            pt

                                        </span>

                                    </td>


                                    {{-- ===============================
                                         Status
                                    ================================ --}}
                                    <td class="text-center">

                                        @if (
                                            $reservation
                                                ->status
                                                ?->status_code
                                            === 'confirmed'
                                        )

                                            <span class="badge text-bg-primary">
                                                Confirmed
                                            </span>


                                        @elseif (
                                            $reservation
                                                ->status
                                                ?->status_code
                                            === 'pending'
                                        )

                                            <span class="badge text-bg-warning">
                                                Pending
                                            </span>


                                        @elseif (
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
                                            === 'cancelled'
                                        )

                                            <span class="badge text-bg-secondary">
                                                Cancelled
                                            </span>


                                        @else

                                            <span class="badge text-bg-secondary">

                                                {{
                                                    ucfirst(
                                                        $reservation
                                                            ->status
                                                            ?->status_code
                                                        ?? 'unknown'
                                                    )
                                                }}

                                            </span>

                                        @endif

                                    </td>


                                    {{-- ===============================
                                         Action
                                    ================================ --}}
                                    <td class="text-center">

                                        @if (
                                            in_array(
                                                $reservation
                                                    ->status
                                                    ?->status_code,
                                                [
                                                    'pending',
                                                    'confirmed'
                                                ]
                                            )
                                        )

                                            <button
                                                type="button"
                                                class="btn btn-outline-danger btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#cancelModal{{ $reservation->id }}"
                                            >
                                                Cancel
                                            </button>

                                        @else

                                            <span class="text-secondary">
                                                -
                                            </span>

                                        @endif

                                    </td>

                                </tr>


                                {{-- ===============================
                                     Cancel Modal
                                ================================ --}}
                                <div
                                    class="modal fade"
                                    id="cancelModal{{ $reservation->id }}"
                                    tabindex="-1"
                                    aria-hidden="true"
                                >

                                    <div class="modal-dialog modal-dialog-centered">

                                        <div class="modal-content">


                                            {{-- Modal Header --}}
                                            <div class="modal-header">

                                                <h5 class="modal-title fw-bold">
                                                    Cancel Reservation
                                                </h5>

                                                <button
                                                    type="button"
                                                    class="btn-close"
                                                    data-bs-dismiss="modal"
                                                    aria-label="Close"
                                                ></button>

                                            </div>


                                            {{-- Modal Body --}}
                                            <div class="modal-body">

                                                <p>
                                                    Are you sure you want to cancel this lesson?
                                                </p>


                                                <div class="small text-secondary mb-2">

                                                    {{
                                                        $startAt->format(
                                                            'M d, Y'
                                                        )
                                                    }}

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


                                                {{-- Refund Point --}}
                                                <div class="small">

                                                    Refund:

                                                    <span class="fw-semibold">

                                                        {{
                                                            number_format(
                                                                $reservation->point_cost
                                                                ?? 0
                                                            )
                                                        }}
                                                        pt

                                                    </span>

                                                </div>

                                            </div>


                                            {{-- Modal Footer --}}
                                            <div class="modal-footer">

                                                <button
                                                    type="button"
                                                    class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal"
                                                >
                                                    Keep Reservation
                                                </button>


                                                <form
                                                    method="POST"
                                                    action="{{ route(
                                                        'students.reservations.cancel',
                                                        $reservation
                                                    ) }}"
                                                >

                                                    @csrf
                                                    @method('PATCH')


                                                    <button
                                                        type="submit"
                                                        class="btn btn-danger"
                                                    >
                                                        Cancel Reservation
                                                    </button>

                                                </form>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


    @else


        {{-- ===============================
             Empty State
        ================================ --}}
        <div class="card shadow-sm">

            <div class="card-body text-center py-5">

                <i
                    class="
                        fa-regular
                        fa-calendar-check
                        fa-2x
                        text-secondary
                        mb-3
                    "
                ></i>


                <h5 class="fw-bold">
                    No upcoming lessons
                </h5>


                <p class="text-secondary mb-3">
                    You don't have any reservations.
                </p>


                <a
                    href="{{ route('students.reservations.index') }}"
                    class="btn btn-primary"
                >
                    Book a Lesson
                </a>

            </div>

        </div>

    @endif

</div>

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const bubble =
            document.getElementById('upcomingMonkeyBubble');

        if (bubble) {

            setTimeout(() => {
                bubble.classList.add('show');
            }, 300);

            setTimeout(() => {
                bubble.classList.remove('show');
            }, 3500);

        }
    }
);
</script>

@endsection