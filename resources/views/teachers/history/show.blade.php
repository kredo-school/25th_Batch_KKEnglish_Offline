@extends('layouts.app')

@section('title', 'Lesson History Details')

@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | Dummy Status
    |--------------------------------------------------------------------------
    |
    | 一覧画面から
    |
    | ?status=awaiting_result
    | ?status=completed
    | ?status=absent
    |
    | を受け取って表示を切り替える
    |
    | Controller完成後は削除
    |
    */

    $dummyStatus =
        request(
            'status',
            'awaiting_result'
        );


    /*
    |--------------------------------------------------------------------------
    | Dummy Data
    |--------------------------------------------------------------------------
    | Controller完成後は削除
    */

    $reservation = (object) [

        'start_at' =>
            '2026-09-17 10:00:00',

        'end_at' =>
            '2026-09-17 10:30:00',


        'student' => (object) [

            'user' => (object) [

                'first_name' =>
                    'John',

                'last_name' =>
                    'Smith',

            ],

        ],


        'material' => (object) [

            'name' =>
                'Grammar Beginner',

        ],


        /*
         * URLから受け取ったStatusを使用
         */
        'status' => (object) [

            'status_code' =>
                $dummyStatus,

        ],


        /*
         * Completedの場合に表示する
         * Lesson Record
         */
        'lessonRecord' => (object) [

            'subject' =>
                'Unit 3 / Page 25-30',

            'progress_note' =>
                'Practiced past tense and irregular verbs.',

        ],

    ];


    /*
    |--------------------------------------------------------------------------
    | Date / Time
    |--------------------------------------------------------------------------
    */

    $startAt =
        \Carbon\Carbon::parse(
            $reservation->start_at
        );


    $endAt =
        \Carbon\Carbon::parse(
            $reservation->end_at
        );


    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    $statusCode =
        $reservation
            ->status
            ->status_code;

@endphp



<div class="container py-4">


    {{-- ===============================
         Title
    ================================ --}}
    <div class="mb-4">

        <h2 class="fw-bold mb-1">
            Lesson History Details
        </h2>

        <p class="text-secondary mb-0">

            @if (
                $statusCode
                === 'awaiting_result'
            )

                Enter the lesson result.

            @else

                View lesson details and record.

            @endif

        </p>

    </div>



    {{-- ===============================
         Lesson Information
    ================================ --}}
    <div class="card mb-4">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                Lesson Information
            </h5>

        </div>


        <div class="card-body p-4">


            {{-- Student --}}
            <div class="row border-bottom py-3">

                <div class="col-md-3 fw-bold">
                    Student
                </div>

                <div class="col-md-9">

                    {{
                        $reservation
                            ->student
                            ->user
                            ->first_name
                    }}

                    {{
                        $reservation
                            ->student
                            ->user
                            ->last_name
                    }}

                </div>

            </div>


            {{-- Date --}}
            <div class="row border-bottom py-3">

                <div class="col-md-3 fw-bold">
                    Date
                </div>

                <div class="col-md-9">

                    {{
                        $startAt
                            ->format(
                                'F d, Y'
                            )
                    }}

                </div>

            </div>


            {{-- Time --}}
            <div class="row border-bottom py-3">

                <div class="col-md-3 fw-bold">
                    Time
                </div>

                <div class="col-md-9">

                    {{
                        $startAt
                            ->format('H:i')
                    }}

                    -

                    {{
                        $endAt
                            ->format('H:i')
                    }}

                </div>

            </div>


            {{-- Material --}}
            <div class="row py-3">

                <div class="col-md-3 fw-bold">
                    Material
                </div>

                <div class="col-md-9">

                    {{
                        $reservation
                            ->material
                            ->name
                    }}

                </div>

            </div>

        </div>

    </div>



    {{-- =========================================================
         Awaiting Result
         未入力
    ========================================================== --}}
    @if (
        $statusCode
        === 'awaiting_result'
    )

        <div class="card">

            <div class="card-header bg-white py-3">

                <h5 class="fw-bold mb-0">
                    Lesson Record
                </h5>

                <p
                    class="
                        text-secondary
                        small
                        mb-0
                        mt-1
                    "
                >
                    Please enter the lesson result.
                </p>

            </div>


            <div class="card-body p-4">

                {{-- 表示確認用Form --}}
                <form>


                    {{-- Attendance --}}
                    <div class="mb-4">

                        <label
                            for="lessonResult"
                            class="form-label fw-bold"
                        >
                            Attendance
                        </label>


                        <select
                            id="lessonResult"
                            name="result"
                            class="form-select"
                        >

                            <option value="">
                                Select attendance
                            </option>

                            <option value="completed">
                                Present
                            </option>

                            <option value="absent">
                                Absent
                            </option>

                        </select>

                    </div>



                    {{-- ===============================
                         Present Only
                    ================================ --}}
                    <div
                        id="completedFields"
                        class="d-none"
                    >


                        {{-- Material --}}
                        <div class="mb-4">

                            <label
                                class="
                                    form-label
                                    fw-bold
                                "
                            >
                                Material
                            </label>


                            <div
                                class="
                                    form-control
                                    bg-light
                                "
                            >

                                {{
                                    $reservation
                                        ->material
                                        ->name
                                }}

                            </div>

                        </div>



                        {{-- Subject --}}
                        <div class="mb-4">

                            <label
                                for="subject"
                                class="
                                    form-label
                                    fw-bold
                                "
                            >
                                Subject
                            </label>


                            <input
                                type="text"
                                id="subject"
                                name="subject"
                                class="form-control"
                                placeholder="
                                    e.g. Unit 3 / Page 25-30
                                "
                            >

                        </div>



                        {{-- Progress Note --}}
                        <div class="mb-4">

                            <label
                                for="progressNote"
                                class="
                                    form-label
                                    fw-bold
                                "
                            >
                                Progress Note
                            </label>


                            <textarea
                                id="progressNote"
                                name="progress_note"
                                class="form-control"
                                rows="4"
                                placeholder="Enter lesson progress or notes..."
                            ></textarea>

                        </div>

                    </div>



                    {{-- Save --}}
                    <div class="text-end">

                        <button
                            type="button"
                            class="btn btn-primary"
                        >
                            Save Lesson Record
                        </button>

                    </div>

                </form>

            </div>

        </div>



    {{-- =========================================================
         Completed
         入力済みLesson Recordを閲覧
    ========================================================== --}}
    @elseif (
        $statusCode
        === 'completed'
    )

        <div class="card">

            <div class="card-header bg-white py-3">

                <div
                    class="
                        d-flex
                        justify-content-between
                        align-items-center
                    "
                >

                    <h5 class="fw-bold mb-0">
                        Lesson Record
                    </h5>


                    <span
                        class="
                            badge
                            text-bg-success
                        "
                    >
                        Completed
                    </span>

                </div>

            </div>


            <div class="card-body p-4">


                {{-- Attendance --}}
                <div class="row border-bottom py-3">

                    <div class="col-md-3 fw-bold">
                        Attendance
                    </div>

                    <div class="col-md-9">
                        Present
                    </div>

                </div>



                {{-- Material --}}
                <div class="row border-bottom py-3">

                    <div class="col-md-3 fw-bold">
                        Material
                    </div>

                    <div class="col-md-9">

                        {{
                            $reservation
                                ->material
                                ->name
                        }}

                    </div>

                </div>



                {{-- Subject --}}
                <div class="row border-bottom py-3">

                    <div class="col-md-3 fw-bold">
                        Subject
                    </div>

                    <div class="col-md-9">

                        {{
                            $reservation
                                ->lessonRecord
                                ->subject
                            ?? '-'
                        }}

                    </div>

                </div>



                {{-- Progress Note --}}
                <div class="row py-3">

                    <div class="col-md-3 fw-bold">
                        Progress Note
                    </div>

                    <div class="col-md-9">

                        {{
                            $reservation
                                ->lessonRecord
                                ->progress_note
                            ?? '-'
                        }}

                    </div>

                </div>

            </div>

        </div>



    {{-- =========================================================
         Absent
    ========================================================== --}}
    @elseif (
        $statusCode
        === 'absent'
    )

        <div class="card">

            <div class="card-header bg-white py-3">

                <div
                    class="
                        d-flex
                        justify-content-between
                        align-items-center
                    "
                >

                    <h5 class="fw-bold mb-0">
                        Lesson Result
                    </h5>


                    <span
                        class="
                            badge
                            text-bg-secondary
                        "
                    >
                        Absent
                    </span>

                </div>

            </div>


            <div class="card-body p-4">

                {{-- Attendance --}}
                <div class="row py-3">

                    <div class="col-md-3 fw-bold">
                        Attendance
                    </div>

                    <div class="col-md-9">
                        Absent
                    </div>

                </div>

            </div>

        </div>

    @endif



    {{-- ===============================
         Back
    ================================ --}}
    <div class="mt-4">

        <a
            href="{{ url()->previous() }}"
            class="btn btn-outline-secondary"
        >
            Back
        </a>

    </div>

</div>



{{-- =============================================================
     Awaiting ResultのときだけJSを使用
============================================================= --}}
@if (
    $statusCode
    === 'awaiting_result'
)

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const lessonResult =
            document.getElementById(
                'lessonResult'
            );


        const completedFields =
            document.getElementById(
                'completedFields'
            );


        /*
        |--------------------------------------------------------------------------
        | Attendanceによって入力欄を切り替える
        |--------------------------------------------------------------------------
        */

        function updateLessonFields() {

            /*
             * Present
             */
            if (
                lessonResult.value
                === 'completed'
            ) {

                completedFields
                    .classList
                    .remove(
                        'd-none'
                    );


                return;

            }


            /*
             * 未選択 / Absent
             */
            completedFields
                .classList
                .add(
                    'd-none'
                );

        }


        lessonResult.addEventListener(
            'change',
            updateLessonFields
        );


        /*
         * 初期表示
         */
        updateLessonFields();

    }
);

</script>

@endif


@endsection