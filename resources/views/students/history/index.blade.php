@extends('layouts.app')

@section('title', 'Lesson History')

@section('content')

<div class="container-fluid">

    {{-- ===============================
         Title
    ================================ --}}
    <div class="d-flex justify-content-between align-items-start mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                Lesson History
            </h2>

            <p class="text-secondary mb-0">
                View your completed lessons.
            </p>
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

                                    {{ $startAt->format('h:i A') }}
                                    -
                                    {{ $endAt->format('h:i A') }}

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
                                                    bg-light
                                                    d-flex
                                                    justify-content-center
                                                    align-items-center
                                                    text-secondary
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

                                {{-- ===============================
                                    Review
                                ================================ --}}
                                <td class="text-center">

                                    @if (
                                        $reservation
                                            ->status
                                            ?->status_code
                                        === 'completed'
                                    )

                                        <a
                                            href="{{ route(
                                               'students.reviews.create.test'
                                            ) }}"
                                            class="btn btn-outline-primary btn-sm"
                                        >
                                            Write a Review
                                        </a>

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
                                    colspan="5"
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

        </div>

    </div>

</div>

@endsection