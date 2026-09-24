@extends('layouts.app')

@section('title', 'Lesson History')

@section('content')

<style>
.lesson-history-monkey {
    width: 60px;
    height: 60px;
    object-fit: contain;
    margin-left: -10px;
    margin-top: -8px;
    flex-shrink: 0;
}

.lesson-monkey-area {
    display: flex;
    align-items: center;
    margin-left: 6px;
}

.lesson-history-monkey {
    width: 52px;
    height: 52px;
    object-fit: contain;
}

.lesson-monkey-bubble {
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

.lesson-monkey-bubble.show {
    opacity: 1;
    transform: translateX(0);
}

</style>

<div class="container-fluid">

    {{-- ===============================
         Title
    ================================ --}}
    <div class="d-flex justify-content-between align-items-start mb-4">

       <div class="d-flex align-items-center mb-1">

    <h2 class="fw-bold mb-0">
        Lesson History
    </h2>

    <div class="lesson-monkey-area">

        <img
            src="{{ asset('images/kk-monkey-cap.png') }}"
            alt="KK English Monkey"
            class="lesson-history-monkey"
        >

        <div
            id="lessonMonkeyBubble"
            class="lesson-monkey-bubble"
        >
            Great job!
        </div>

    </div>

</div>


        {{-- Point History --}}
        <a
            href="{{ route('students.point-history.index') }}"
            class="btn btn-outline-secondary"
        >
            <i class="fa-solid fa-coins me-1"></i>
            Point History
        </a>

    </div>


    {{-- ===============================
         History List
    ================================ --}}
    <div class="card">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                Completed Lessons
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
                                Teacher
                            </th>

                            <th class="py-3">
                                Material
                            </th>

                            <th class="py-3 text-center">
                                Status
                            </th>

                            <th class="py-3 text-center">
                                Review
                            </th>

                        </tr>

                    </thead>


                    {{-- Body --}}
                    <tbody>

                        @forelse ($reservations as $reservation)

                            @php

                                $startAt = \Carbon\Carbon::parse(
                                    $reservation->start_at
                                );

                                $endAt = \Carbon\Carbon::parse(
                                    $reservation->end_at
                                );

                            @endphp


                            <tr>

                                {{-- ===============================
                                     Date
                                ================================ --}}
                                <td class="px-4">

                                    <div class="fw-semibold">
                                        {{ $startAt->format('M d, Y') }}
                                    </div>

                                    <small class="text-secondary">
                                        {{ $startAt->format('l') }}
                                    </small>

                                </td>


                                {{-- ===============================
                                     Time
                                ================================ --}}
                                <td>

                                    {{ $startAt->format('H:i') }}
                                    -
                                    {{ $endAt->format('H:i') }}

                                </td>


                                {{-- ===============================
                                     Teacher
                                ================================ --}}
                                <td>

                                    <div class="d-flex align-items-center">

                                        {{-- Teacher Image --}}
                                        @if (
                                            $reservation
                                                ->teacher
                                                ?->user
                                                ?->profile_image
                                        )

                                            <img
                                                src="{{ $reservation->teacher->user->profile_image }}"
                                                alt="Teacher"
                                                width="40"
                                                height="40"
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
                                                    width: 40px;
                                                    height: 40px;
                                                "
                                            >
                                                <i class="fa-solid fa-user"></i>
                                            </div>

                                        @endif


                                        {{-- Teacher Name --}}
                                        <span class="fw-semibold">

                                            {{
                                                $reservation
                                                    ->teacher
                                                    ?->user
                                                    ?->first_name
                                                ?? ''
                                            }}

                                            {{
                                                $reservation
                                                    ->teacher
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
                                <td class="text-center">

                                    @if (
                                        in_array(
                                            $reservation->status?->status_code,
                                            ['completed', 'awaiting_result']
                                        )
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

                                {{-- ===============================
                                    Review
                                ================================ --}}
                                <td class="text-center">

                                    @if (
                                        in_array(
                                            $reservation->status?->status_code,
                                            ['completed', 'awaiting_result']
                                        )
                                    )

                                        @if ($reservation->review)

                                           <span class="text-success fw-semibold">
                                                <i class="fa-solid fa-check me-1"></i>
                                                Reviewed
                                            </span>

                                        @else

                                            <a
                                                href="{{ route(
                                                    'students.reviews.create',
                                                    $reservation
                                                ) }}"
                                                class="btn btn-outline-primary btn-sm"
                                            >
                                                Write a Review
                                            </a>

                                        @endif

                                    @else

                                        <span class="text-secondary">
                                            -
                                        </span>

                                    @endif

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center py-5"
                                >

                                    <i
                                        class="
                                            fa-solid
                                            fa-book-open
                                            fa-2x
                                            text-secondary
                                            mb-3
                                        "
                                    ></i>

                                    <h5 class="fw-bold">
                                        No lesson history
                                    </h5>

                                    <p class="text-secondary mb-0">
                                        You don't have any completed lessons yet.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Pagination --}}
           <div class="d-flex justify-content-between align-items-center px-3 py-3">

                <small class="text-secondary">
                    {{ $reservations->firstItem() }}
                    -
                    {{ $reservations->lastItem() }}
                    /
                    {{ $reservations->total() }}
                </small>

                <div>
                    {{ $reservations->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>

            </div>

    </div>

</div>

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const bubble =
            document.getElementById('lessonMonkeyBubble');

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