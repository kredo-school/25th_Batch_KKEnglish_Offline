@extends('layouts.app')

@section('title', 'Upcoming Lessons')

@section('content')

<div class="container-fluid py-4">

    {{-- ===============================
         Title
    ================================ --}}
    <div class="mb-4">

        <h2 class="fw-bold mb-1">
            Upcoming Lessons
        </h2>

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
         Upcoming Lesson List
    ================================ --}}
    <div class="card shadow-sm">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                Reserved Lessons
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


                        {{-- ===============================
                             Lesson 1
                        ================================ --}}
                        <tr>

                            {{-- Date --}}
                            <td class="px-4">

                                <div class="fw-bold">
                                    Sep 10, 2026
                                </div>

                                <small class="text-secondary">
                                    Thursday
                                </small>

                            </td>


                            {{-- Time --}}
                            <td>

                                10:00 AM
                                -
                                10:30 AM

                            </td>


                            {{-- Teacher --}}
                            <td>

                                <div class="d-flex align-items-center">

                                    <img
                                        src="{{ asset('images/teacher1.jpg') }}"
                                        alt="John Smith"
                                        width="45"
                                        height="45"
                                        class="rounded-circle me-2"
                                        style="object-fit: cover;"
                                    >

                                    <div>

                                        <div class="fw-semibold">
                                            John Smith
                                        </div>

                                        <small class="text-secondary">
                                            Philippines
                                        </small>

                                    </div>

                                </div>

                            </td>


                            {{-- Material --}}
                            <td>

                                Daily Conversation

                            </td>


                            {{-- Status --}}
                            <td>

                                <span class="badge text-bg-primary">
                                    Confirmed
                                </span>

                            </td>


                            {{-- Cancel --}}
                            <td class="text-end pe-4">

                                <button
                                    type="button"
                                    class="
                                        btn
                                        btn-outline-danger
                                        btn-sm
                                    "
                                    data-bs-toggle="modal"
                                    data-bs-target="#cancelModal"
                                >
                                    Cancel
                                </button>

                            </td>

                        </tr>


                        {{-- ===============================
                             Lesson 2
                        ================================ --}}
                        <tr>

                            {{-- Date --}}
                            <td class="px-4">

                                <div class="fw-bold">
                                    Sep 12, 2026
                                </div>

                                <small class="text-secondary">
                                    Saturday
                                </small>

                            </td>


                            {{-- Time --}}
                            <td>

                                02:00 PM
                                -
                                02:30 PM

                            </td>


                            {{-- Teacher --}}
                            <td>

                                <div class="d-flex align-items-center">

                                    <img
                                        src="{{ asset('images/teacher2.jpg') }}"
                                        alt="Jane Doe"
                                        width="45"
                                        height="45"
                                        class="rounded-circle me-2"
                                        style="object-fit: cover;"
                                    >

                                    <div>

                                        <div class="fw-semibold">
                                            Jane Doe
                                        </div>

                                        <small class="text-secondary">
                                            Philippines
                                        </small>

                                    </div>

                                </div>

                            </td>


                            {{-- Material --}}
                            <td>

                                Grammar

                            </td>


                            {{-- Status --}}
                            <td>

                                <span class="badge text-bg-primary">
                                    Confirmed
                                </span>

                            </td>


                            {{-- Cancel --}}
                            <td class="text-end pe-4">

                                <button
                                    type="button"
                                    class="
                                        btn
                                        btn-outline-danger
                                        btn-sm
                                    "
                                    data-bs-toggle="modal"
                                    data-bs-target="#cancelModal"
                                >
                                    Cancel
                                </button>

                            </td>

                        </tr>


                        {{-- ===============================
                             Lesson 3
                        ================================ --}}
                        <tr>

                            {{-- Date --}}
                            <td class="px-4">

                                <div class="fw-bold">
                                    Sep 15, 2026
                                </div>

                                <small class="text-secondary">
                                    Tuesday
                                </small>

                            </td>


                            {{-- Time --}}
                            <td>

                                04:30 PM
                                -
                                05:00 PM

                            </td>


                            {{-- Teacher --}}
                            <td>

                                <div class="d-flex align-items-center">

                                    <img
                                        src="{{ asset('images/teacher3.jpg') }}"
                                        alt="Mary Jones"
                                        width="45"
                                        height="45"
                                        class="rounded-circle me-2"
                                        style="object-fit: cover;"
                                    >

                                    <div>

                                        <div class="fw-semibold">
                                            Mary Jones
                                        </div>

                                        <small class="text-secondary">
                                            Philippines
                                        </small>

                                    </div>

                                </div>

                            </td>


                            {{-- Material --}}
                            <td>

                                Business English

                            </td>


                            {{-- Status --}}
                            <td>

                                <span class="badge text-bg-primary">
                                    Confirmed
                                </span>

                            </td>


                            {{-- Cancel --}}
                            <td class="text-end pe-4">

                                <button
                                    type="button"
                                    class="
                                        btn
                                        btn-outline-danger
                                        btn-sm
                                    "
                                    data-bs-toggle="modal"
                                    data-bs-target="#cancelModal"
                                >
                                    Cancel
                                </button>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- ===============================
         Empty State
         予約0件の時に使用
    ================================ --}}

    {{--
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
                You don't have any upcoming reservations.
            </p>

            <a
                href="#"
                class="btn btn-primary"
            >
                Book a Lesson
            </a>

        </div>

    </div>
    --}}

</div>


{{-- ===============================
     Cancel Confirmation Modal
=============================== --}}
<div
    class="modal fade"
    id="cancelModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title fw-bold">
                    Cancel Reservation
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                >
                </button>

            </div>


            <div class="modal-body">

                Are you sure you want to cancel this lesson?

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal"
                >
                    Keep Reservation
                </button>


                <button
                    type="button"
                    class="btn btn-danger"
                >
                    Cancel Reservation
                </button>

            </div>

        </div>

    </div>

</div>

@endsection