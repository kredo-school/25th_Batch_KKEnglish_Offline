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


    /*
    |--------------------------------------------------------------------------
    | Previous Lesson Record
    |--------------------------------------------------------------------------
    |
    | 表示確認用ダミーデータ
    |
    | 今回予約しているMaterialについての
    | 最新LessonRecord 1件を想定
    |
    | Controller完成後は削除
    |
    */

    $previousLessonRecord = [

        'date' => 'Sep 15, 2026',

        'teacher' => 'Anna Cruz',

        'material' =>
            $reservation->material->name
            ?? 'Grammar Beginner',

        'subject' =>
            'Unit 3 / Page 25-30',

        'progress_note' =>
            'Practiced past tense. The student understood the basic structure but needs more practice with irregular verbs.',

    ];

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
         Reservation Details
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
         Previous Lesson Record
    ================================ --}}
    <div class="card mt-4">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                Previous Lesson Record
            </h5>

            <p class="text-secondary small mb-0 mt-1">
                Latest record for this material.
            </p>

        </div>


        <div class="card-body p-4">

            {{-- Material --}}
            <div class="row border-bottom py-3">

                <div class="col-md-4 fw-bold">
                    Material
                </div>

                <div class="col-md-8">

                    {{ $previousLessonRecord['material'] }}

                </div>

            </div>


            {{-- Last Lesson --}}
            <div class="row border-bottom py-3">

                <div class="col-md-4 fw-bold">
                    Last Lesson
                </div>

                <div class="col-md-8">

                    {{ $previousLessonRecord['date'] }}

                </div>

            </div>


            {{-- Teacher --}}
            <div class="row border-bottom py-3">

                <div class="col-md-4 fw-bold">
                    Teacher
                </div>

                <div class="col-md-8">

                    {{ $previousLessonRecord['teacher'] }}

                </div>

            </div>


            {{-- Subject --}}
            <div class="row border-bottom py-3">

                <div class="col-md-4 fw-bold">
                    Subject
                </div>

                <div class="col-md-8">

                    {{ $previousLessonRecord['subject'] }}

                </div>

            </div>


            {{-- Progress Note --}}
            <div class="row py-3">

                <div class="col-md-4 fw-bold">
                    Progress Note
                </div>

                <div class="col-md-8">

                    {{ $previousLessonRecord['progress_note'] }}

                </div>

            </div>

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

@endsection