@extends('layouts.app')

@section('title', 'Lesson Details')

@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | Reservation Date / Time
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

@endphp


<div class="container-fluid">

    {{-- ===============================
         Title
    ================================ --}}
    <div class="mb-4">

        <h2 class="fw-bold mb-1">
            Lesson Details
        </h2>

        <p class="text-secondary mb-0">
            View lesson reservation details.
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
         Validation Errors
    ================================ --}}
    @if ($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ===============================
         Lesson Details
    ================================ --}}
    <div class="card">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                Reservation Details
            </h5>

        </div>


        <div class="card-body p-4">

            {{-- Date --}}
            <div class="row border-bottom py-3">

                <div class="col-md-4 fw-bold">
                    Date
                </div>

                <div class="col-md-8">

                    {{ $startAt->format('M d, Y') }}

                    <span class="text-secondary ms-2">
                        {{ $startAt->format('l') }}
                    </span>

                </div>

            </div>


            {{-- Time --}}
            <div class="row border-bottom py-3">

                <div class="col-md-4 fw-bold">
                    Time
                </div>

                <div class="col-md-8">

                    {{ $startAt->format('h:i A') }}

                    -

                    {{ $endAt->format('h:i A') }}

                </div>

            </div>


            {{-- Student --}}
            <div class="row border-bottom py-3">

                <div class="col-md-4 fw-bold">
                    Student
                </div>

                <div class="col-md-8 d-flex align-items-center">

                    @if (
                        $reservation->student
                        &&
                        $reservation->student->user
                    )

                        @if (
                            $reservation
                                ->student
                                ->user
                                ->profile_image
                        )

                            <img
                                src="{{ str_starts_with(
                                    $reservation->student->user->profile_image,
                                    'http'
                                )
                                    ? $reservation->student->user->profile_image
                                    : asset(
                                        'storage/'
                                        .
                                        $reservation->student->user->profile_image
                                    )
                                }}"
                                alt="{{ $reservation->student->user->first_name }}"
                                width="45"
                                height="45"
                                class="rounded-circle me-2"
                                style="object-fit: cover;"
                            >

                        @else

                            <i
                                class="
                                    fa-solid
                                    fa-circle-user
                                    me-2
                                    fa-2x
                                "
                            ></i>

                        @endif


                        <span>

                            {{ $reservation->student->user->first_name }}

                            {{ $reservation->student->user->last_name }}

                        </span>

                    @else

                        <span class="text-secondary">
                            -
                        </span>

                    @endif

                </div>

            </div>


            {{-- Material --}}
            <div class="row py-3">

                <div class="col-md-4 fw-bold">
                    Material
                </div>

                <div class="col-md-8">

                    {{
                        $reservation->material->name
                        ?? '-'
                    }}

                </div>

            </div>

        </div>

    </div>


    {{-- ===============================
         Lesson Record
    ================================ --}}
    <div class="card mt-4">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                Lesson Record
            </h5>

        </div>


        <div class="card-body p-4">

            <form
                method="POST"
                action="{{ route(
                    'teachers.reservations.result.update',
                    $reservation
                ) }}"
            >

                @csrf
                @method('PATCH')


                {{-- ===============================
                     Lesson Status
                ================================ --}}
                <div class="mb-4">

                    <label
                        for="lessonResult"
                        class="form-label fw-bold"
                    >
                        Lesson Status
                    </label>


                    <select
                        id="lessonResult"
                        name="result"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Select status
                        </option>


                        <option
                            value="completed"
                            @selected(
                                old('result')
                                ===
                                'completed'
                            )
                        >
                            Completed
                        </option>


                        <option
                            value="absent"
                            @selected(
                                old('result')
                                ===
                                'absent'
                            )
                        >
                            Absent
                        </option>

                    </select>

                </div>


                {{-- ===============================
                     Completed Only Fields
                ================================ --}}
                <div id="completedFields">


                    {{-- Subject --}}
                    <div class="mb-4">

                        <label
                            for="subject"
                            class="form-label fw-bold"
                        >
                            Subject
                        </label>

                      <div class="form-control bg-light">
                        {{ $reservation->material->name ?? '-' }}
                    </div>

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
                            name="progress_note"
                            class="form-control"
                            rows="4"
                            placeholder="Enter lesson progress or notes..."
                        >{{ old(
                            'progress_note',
                            $reservation
                                ->lessonRecord
                                ?->progress_note
                            ?? ''
                        ) }}</textarea>

                    </div>

                </div>


                {{-- ===============================
                     Save
                ================================ --}}
                <div class="text-end">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Save Lesson Record
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- ===============================
         Actions
    ================================ --}}
    <div class="mt-4">

        <a
            href="{{ route(
                'teachers.reservations.index'
            ) }}"
            class="btn btn-outline-secondary"
        >
            Back
        </a>

    </div>

</div>


{{-- ===============================
     JavaScript
================================ --}}
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


        const subject =
            document.getElementById(
                'subject'
            );


        const progressNote =
            document.getElementById(
                'progressNote'
            );


        /*
        |--------------------------------------------------------------------------
        | Completed / Absent 切り替え
        |--------------------------------------------------------------------------
        */

        function updateLessonFields() {

            if (
                lessonResult.value
                ===
                'completed'
            ) {

                /*
                 * Completedの場合
                 * Subject / Progress Noteを表示
                 */
                completedFields
                    .classList
                    .remove(
                        'd-none'
                    );


                subject.required =
                    true;


                progressNote.required =
                    true;


            } else {

                /*
                 * Absent または未選択の場合
                 * Subject / Progress Noteを非表示
                 */
                completedFields
                    .classList
                    .add(
                        'd-none'
                    );


                subject.required =
                    false;


                progressNote.required =
                    false;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Status変更時
        |--------------------------------------------------------------------------
        */

        lessonResult.addEventListener(
            'change',
            updateLessonFields
        );


        /*
        |--------------------------------------------------------------------------
        | 初期表示
        |--------------------------------------------------------------------------
        */

        updateLessonFields();

    }
);

</script>

@endsection