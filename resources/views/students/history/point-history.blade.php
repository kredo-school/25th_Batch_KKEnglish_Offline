@extends('layouts.app')

@section('title', 'Point History')

@section('content')

<div class="container-fluid">

    {{-- ===============================
         Title
    ================================ --}}
    <div class="d-flex justify-content-between align-items-start mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                Point History
            </h2>

            <p class="text-secondary mb-0">
                View your point usage and refunds.
            </p>

        </div>


        {{-- Current Points --}}
        <div
            class="border rounded px-3 py-2"
        >
            <span class="text-secondary small me-2">
                Current Points
            </span>

            <span class="fw-bold">
                 {{ number_format(auth()->user()->student->point_balance ?? 0) }} pt
            </span>
        </div>

    </div>


    {{-- ===============================
         Point History
    ================================ --}}
    <div class="card">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                Point Transactions
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
                                Type
                            </th>

                            <th class="py-3">
                                Lesson
                            </th>

                            <th class="py-3 text-end">
                                Points
                            </th>

                            <th class="py-3 pe-4">
                                Note
                            </th>

                        </tr>

                    </thead>


                    {{-- Body --}}
                    <tbody>


                        {{-- ===============================
                             Reservation Use
                        ================================ --}}
                        <tr>

                            <td class="px-4">

                                <div class="fw-semibold">
                                    Sep 15, 2026
                                </div>

                                <small class="text-secondary">
                                    10:15 AM
                                </small>

                            </td>


                            <td>

                                <span class="badge bg-light text-dark border">
                                    Reservation
                                </span>

                            </td>


                            <td>

                                <div class="fw-semibold">
                                    Daily Conversation
                                </div>

                                <small class="text-secondary">
                                    John Smith
                                </small>

                            </td>


                            <td class="text-end">

                                <span class="fw-bold text-danger">
                                    -300 pt
                                </span>

                            </td>


                            <td class="pe-4 text-secondary">
                                Lesson booking
                            </td>

                        </tr>


                        {{-- ===============================
                             Reservation Use
                        ================================ --}}
                        <tr>

                            <td class="px-4">

                                <div class="fw-semibold">
                                    Sep 14, 2026
                                </div>

                                <small class="text-secondary">
                                    02:30 PM
                                </small>

                            </td>


                            <td>

                                <span class="badge bg-light text-dark border">
                                    Reservation
                                </span>

                            </td>


                            <td>

                                <div class="fw-semibold">
                                    Grammar
                                </div>

                                <small class="text-secondary">
                                    Jane Doe
                                </small>

                            </td>


                            <td class="text-end">

                                <span class="fw-bold text-danger">
                                    -400 pt
                                </span>

                            </td>


                            <td class="pe-4 text-secondary">
                                Lesson booking
                            </td>

                        </tr>


                        {{-- ===============================
                             Refund
                        ================================ --}}
                        <tr>

                            <td class="px-4">

                                <div class="fw-semibold">
                                    Sep 14, 2026
                                </div>

                                <small class="text-secondary">
                                    04:10 PM
                                </small>

                            </td>


                            <td>

                                <span class="badge bg-light text-dark border">
                                    Refund
                                </span>

                            </td>


                            <td>

                                <div class="fw-semibold">
                                    Grammar
                                </div>

                                <small class="text-secondary">
                                    Jane Doe
                                </small>

                            </td>


                            <td class="text-end">

                                <span class="fw-bold text-success">
                                    +400 pt
                                </span>

                            </td>


                            <td class="pe-4 text-secondary">
                                Reservation cancelled
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- ===============================
         Back
    ================================ --}}
    <div class="mt-4">

        <a
            href="{{ route('student.history.test') }}"
            class="btn btn-outline-secondary"
        >
            Back
        </a>

    </div>

</div>

@endsection