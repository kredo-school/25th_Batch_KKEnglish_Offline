@extends('layouts.app')

@section('title', 'Lesson History')

@section('content')

<div class="container-fluid py-4">

    {{-- Title --}}
    <div class="mb-4">

        <h2 class="fw-bold mb-1">
            Lesson History
        </h2>

        <p class="text-secondary mb-0">
            View your completed lessons.
        </p>

    </div>


    {{-- History List --}}
    <div class="card">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                Completed Lessons
            </h5>

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
                                Teacher
                            </th>

                            <th class="py-3">
                                Material
                            </th>

                            <th class="py-3">
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        {{-- Dummy Lesson 1 --}}
                        <tr>

                            <td class="px-4">
                                Sep 02, 2026
                            </td>

                            <td>
                                09:00 - 09:30
                            </td>

                            <td>

                                <div class="d-flex align-items-center">

                                    <img
                                        src="{{ asset('images/teacher1.jpg') }}"
                                        alt="John Smith"
                                        class="rounded-circle me-2"
                                        width="40"
                                        height="40"
                                        style="object-fit: cover;"
                                    >

                                    <span>
                                        John Smith
                                    </span>

                                </div>

                            </td>

                            <td>
                                Daily Conversation
                            </td>

                            <td>

                                <span class="badge text-bg-success">
                                    Completed
                                </span>

                            </td>

                        </tr>


                        {{-- Dummy Lesson 2 --}}
                        <tr>

                            <td class="px-4">
                                Sep 01, 2026
                            </td>

                            <td>
                                10:30 - 11:00
                            </td>

                            <td>

                                <div class="d-flex align-items-center">

                                    <img
                                        src="{{ asset('images/teacher2.jpg') }}"
                                        alt="Jane Doe"
                                        class="rounded-circle me-2"
                                        width="40"
                                        height="40"
                                        style="object-fit: cover;"
                                    >

                                    <span>
                                        Jane Doe
                                    </span>

                                </div>

                            </td>

                            <td>
                                Grammar
                            </td>

                            <td>

                                <span class="badge text-bg-success">
                                    Completed
                                </span>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- Empty State --}}
    {{--
    <div class="card">

        <div class="card-body text-center py-5">

            <i class="fa-solid fa-book-open fa-2x text-secondary mb-3"></i>

            <h5 class="fw-bold">
                No lesson history
            </h5>

            <p class="text-secondary mb-0">
                You don't have any completed lessons yet.
            </p>

        </div>

    </div>
    --}}

</div>

@endsection