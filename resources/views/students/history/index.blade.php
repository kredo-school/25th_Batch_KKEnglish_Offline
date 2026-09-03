@extends('layouts.app')

@section('title', 'Lesson History')

@section('content')

<div class="container-fluid py-4">

    {{-- ===============================
         Title
    ================================ --}}
    <div class="bg-light px-3 py-2 mb-4">

        <h4 class="mb-0 fw-bold">
            Lesson History
        </h4>

    </div>


    {{-- ===============================
         Month Navigation
    ================================ --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        {{-- Previous --}}
        <a href="#"
           class="btn btn-outline-secondary btn-sm">

            <i class="fa-solid fa-chevron-left me-1"></i>

            Previous

        </a>


        {{-- Current Month --}}
        <div class="text-center">

            <h4 class="fw-bold mb-0">
                September 2026
            </h4>

            <small class="text-secondary">
                Monthly Lesson History
            </small>

        </div>


        {{-- Next --}}
        <a href="#"
           class="btn btn-outline-secondary btn-sm">

            Next

            <i class="fa-solid fa-chevron-right ms-1"></i>

        </a>

    </div>


    {{-- ===============================
         Monthly Summary
    ================================ --}}
    <div class="row g-3 mb-4">

        {{-- Total Lessons --}}
        <div class="col-md-4">

            <div class="card h-100">

                <div class="card-body">

                    <p class="text-secondary mb-1">
                        Total Lessons
                    </p>

                    <h3 class="fw-bold mb-0">
                        8
                    </h3>

                </div>

            </div>

        </div>


        {{-- Completed --}}
        <div class="col-md-4">

            <div class="card h-100">

                <div class="card-body">

                    <p class="text-secondary mb-1">
                        Completed
                    </p>

                    <h3 class="fw-bold mb-0">
                        7
                    </h3>

                </div>

            </div>

        </div>


        {{-- Cancelled --}}
        <div class="col-md-4">

            <div class="card h-100">

                <div class="card-body">

                    <p class="text-secondary mb-1">
                        Cancelled
                    </p>

                    <h3 class="fw-bold mb-0">
                        1
                    </h3>

                </div>

            </div>

        </div>

    </div>


    {{-- ===============================
         History List
    ================================ --}}
    <div class="card">

        {{-- Card Header --}}
        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                September Lessons
            </h5>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    {{-- ===============================
                         Table Header
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
                                Status
                            </th>

                        </tr>

                    </thead>


                    {{-- ===============================
                         Table Body
                    ================================ --}}
                    <tbody>

                        {{-- Lesson 1 --}}
                        <tr>

                            <td class="px-4">
                                Sep 02, 2026
                            </td>

                            <td>
                                09:00
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


                        {{-- Lesson 2 --}}
                        <tr>

                            <td class="px-4">
                                Sep 01, 2026
                            </td>

                            <td>
                                10:30
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


                        {{-- Lesson 3 --}}
                        <tr>

                            <td class="px-4">
                                Sep 01, 2026
                            </td>

                            <td>
                                13:00
                            </td>

                            <td>

                                <div class="d-flex align-items-center">

                                    <img
                                        src="{{ asset('images/teacher3.jpg') }}"
                                        alt="Bob Lee"
                                        class="rounded-circle me-2"
                                        width="40"
                                        height="40"
                                        style="object-fit: cover;"
                                    >

                                    <span>
                                        Bob Lee
                                    </span>

                                </div>

                            </td>

                            <td>
                                Beginner English
                            </td>

                            <td>

                                <span class="badge text-bg-secondary">
                                    Cancelled
                                </span>

                            </td>

                        </tr>


                        {{-- Lesson 4 --}}
                        <tr>

                            <td class="px-4">
                                Sep 01, 2026
                            </td>

                            <td>
                                15:00
                            </td>

                            <td>

                                <div class="d-flex align-items-center">

                                    <img
                                        src="{{ asset('images/teacher4.jpg') }}"
                                        alt="Mary Jones"
                                        class="rounded-circle me-2"
                                        width="40"
                                        height="40"
                                        style="object-fit: cover;"
                                    >

                                    <span>
                                        Mary Jones
                                    </span>

                                </div>

                            </td>

                            <td>
                                Business English
                            </td>

                            <td>

                                <span class="badge text-bg-success">
                                    Completed
                                </span>

                            </td>

                        </tr>


                        {{-- Lesson 5 --}}
                        <tr>

                            <td class="px-4">
                                Sep 01, 2026
                            </td>

                            <td>
                                16:30
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

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- ===============================
         Empty State
         履歴0件のときに使用
    ================================ --}}

    {{--
    <div class="card mt-4">

        <div class="card-body text-center py-5">

            <i class="fa-solid fa-book-open fa-2x text-secondary mb-3"></i>

            <h5 class="fw-bold">
                No lesson history
            </h5>

            <p class="text-secondary mb-0">
                There are no lessons for this month.
            </p>

        </div>

    </div>
    --}}


    {{-- ===============================
         TODO
    ================================ --}}

    {{--

        TODO:

        現在は表示確認用のダミーデータ。


        最終的には、

        ログイン中のstudent_id
        +
        選択された年月

        を使用して、

        reservationsテーブルから
        該当月の履歴を取得する。


        例:

        September 2026

        ↓

        2026-09-01
        〜
        2026-09-30

        の履歴を取得。


        Previous
        ↓
        August 2026


        Next
        ↓
        October 2026


        月が切り替わったら、

        ・Total Lessons
        ・Completed
        ・Cancelled
        ・履歴一覧

        をすべて同じ月の内容に変更する。


        Status:

        ・completed
        ・cancelled

    --}}

</div>

@endsection