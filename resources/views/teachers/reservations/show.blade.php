@extends('layouts.app')

@section('title', 'Lesson Details')

@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | Dummy Data
    |--------------------------------------------------------------------------
    |
    | 現在はフロント表示確認用。
    |
    | 最終的には
    | Teacher\ReservationController@show()
    |
    | から
    | $reservation
    | を受け取る。
    |
    */

    $reservation = (object) [

        'start_at' => '2026-09-10 09:00:00',
        'end_at' => '2026-09-10 09:30:00',

        'student' => (object) [
            'user' => (object) [
                'first_name' => 'Ayako',
                'last_name' => 'Kobayashi',
            ],
        ],

        'material' => (object) [
            'name' => 'Daily Conversation',
        ],

        'status' => (object) [
            'status_code' => 'confirmed',
        ],

    ];


    $startAt =
        \Carbon\Carbon::parse(
            $reservation->start_at
        );


    $endAt =
        \Carbon\Carbon::parse(
            $reservation->end_at
        );

@endphp


<div class="container py-4">

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
         Details
    ================================ --}}
    <div class="card">

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

                <div class="col-md-8">

                    <i class="fa-solid fa-circle-user me-2"></i>

                    {{ $reservation->student->user->first_name }}

                    {{ $reservation->student->user->last_name }}

                </div>

            </div>


            {{-- Material --}}
            <div class="row border-bottom py-3">

                <div class="col-md-4 fw-bold">
                    Material
                </div>

                <div class="col-md-8">

                    {{ $reservation->material->name }}

                </div>

            </div>


            {{-- Status --}}
            <div class="row py-3">

                <div class="col-md-4 fw-bold">
                    Status
                </div>

                <div class="col-md-8">

                    @if (
                        $reservation->status->status_code
                        === 'confirmed'
                    )

                        <span class="badge text-bg-primary">
                            Confirmed
                        </span>

                    @elseif (
                        $reservation->status->status_code
                        === 'pending'
                    )

                        <span class="badge text-bg-warning">
                            Pending
                        </span>

                    @else

                        <span class="badge text-bg-secondary">

                            {{
                                ucfirst(
                                    $reservation
                                        ->status
                                        ->status_code
                                )
                            }}

                        </span>

                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- ===============================
         Actions
    ================================ --}}
    <div class="mt-4">

        <a
            href="{{ route('teachers.reservations.test') }}"
            class="btn btn-outline-secondary"
        >
            Back
        </a>

    </div>


    {{-- ===============================
         TODO
    ================================ --}}
    {{--

        TODO:

        現在はダミーデータ。


        最終的には

        Teacher\ReservationController@show()

        から

        $reservation

        を受け取る。


        今後追加予定:

        ・Completed
        ・Absent

        などのレッスン結果登録。


        Completedになった場合は

        生徒側のLearning History

        先生側のLesson History

        に表示する。

    --}}

</div>

@endsection

