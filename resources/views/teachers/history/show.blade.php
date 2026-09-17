@extends('layouts.app')

@section('title', 'Lesson History Details')

@section('content')

<div class="container py-4">

    <h2 class="fw-bold mb-4">
        Lesson History Details
    </h2>


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

            <div class="row mb-3">

                <div class="col-md-3 fw-bold">
                    Student
                </div>

                <div class="col-md-9">
                    John Smith
                </div>

            </div>


            <div class="row mb-3">

                <div class="col-md-3 fw-bold">
                    Date
                </div>

                <div class="col-md-9">
                    September 17, 2026
                </div>

            </div>


            <div class="row mb-3">

                <div class="col-md-3 fw-bold">
                    Time
                </div>

                <div class="col-md-9">
                    10:00 - 10:30
                </div>

            </div>


            <div class="row">

                <div class="col-md-3 fw-bold">
                    Material
                </div>

                <div class="col-md-9">
                    Grammar Beginner
                </div>

            </div>

        </div>

    </div>


    {{-- ===============================
         Lesson Record
    ================================ --}}
    <div class="card">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                Lesson Record
            </h5>

        </div>


        <div class="card-body p-4">

            {{-- 表示確認用 --}}
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


                {{-- Present Only --}}
                <div id="completedFields">

                    {{-- Material --}}
                    <div class="mb-4">

                        <label class="form-label fw-bold">
                            Material
                        </label>

                        <div class="form-control bg-light">
                            Grammar Beginner
                        </div>

                    </div>


                    {{-- Subject --}}
                    <div class="mb-4">

                        <label
                            for="subject"
                            class="form-label fw-bold"
                        >
                            Subject
                        </label>

                        <input
                            type="text"
                            id="subject"
                            class="form-control"
                            placeholder="e.g. Unit 3 / Page 25-30"
                        >

                    </div>


                    {{-- Progress Note --}}
                    <div class="mb-4">

                        <label
                            for="progressNote"
                            class="form-label fw-bold"
                        >
                            Progress Note
                        </label>

                        <textarea
                            id="progressNote"
                            class="form-control"
                            rows="4"
                            placeholder="Enter lesson progress or notes..."
                        ></textarea>

                    </div>

                </div>


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


    {{-- Back --}}
    <div class="mt-4">

        <a
            href="{{ url()->previous() }}"
            class="btn btn-outline-secondary"
        >
            Back
        </a>

    </div>

</div>


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


        function updateLessonFields() {

            if (
                lessonResult.value
                ===
                'completed'
            ) {

                completedFields
                    .classList
                    .remove(
                        'd-none'
                    );

            } else {

                completedFields
                    .classList
                    .add(
                        'd-none'
                    );

            }

        }


        lessonResult.addEventListener(
            'change',
            updateLessonFields
        );


        updateLessonFields();

    }
);

</script>

@endsection