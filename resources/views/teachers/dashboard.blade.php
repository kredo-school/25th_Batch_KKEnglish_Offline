@extends('layouts.app')

@section('title', 'Teacher Dashboard')

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
    | Teacher\ReservationController
    | または DashboardController から
    |
    | $todayReservations
    |
    | を受け取る。
    |
    */

    $todayReservations = collect([

        (object) [
            'id' => 1,

            'start_at' => '2026-09-07 10:00:00',
            'end_at' => '2026-09-07 10:30:00',

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
        ],


        (object) [
            'id' => 2,

            'start_at' => '2026-09-07 14:00:00',
            'end_at' => '2026-09-07 14:30:00',

            'student' => (object) [
                'user' => (object) [
                    'first_name' => 'Taro',
                    'last_name' => 'Yamada',
                ],
            ],

            'material' => (object) [
                'name' => 'Business English',
            ],

            'status' => (object) [
                'status_code' => 'confirmed',
            ],
        ],

    ]);

@endphp


<div class="container-fluid py-4">

    {{-- ===============================
         Hello Header
    ================================ --}}
    <div class="bg-light p-4 mb-4">

        <h2 class="fw-bold mb-1">

            Hello,
            {{ auth()->user()->first_name }}

        </h2>


        <p class="text-secondary mb-1">

            {{ now()->format('l, F j') }}

        </p>


        <p class="fw-semibold mb-0">

            <i class="fa-regular fa-clock me-1"></i>

            {{ now()->format('H:i') }}

        </p>

    </div>


    {{-- ===============================
         Today's Lessons
    ================================ --}}
    <div class="card">

        <div class="card-header bg-white py-3">

            <div class="
                d-flex
                justify-content-between
                align-items-center
            ">

                <div>

                    <h5 class="fw-bold mb-1">
                        Today's Lessons
                    </h5>

                    <small class="text-secondary">
                        {{ now()->format('F j, Y') }}
                    </small>

                </div>


                {{-- My Lessons --}}
                <a
                    href="{{ route('teachers.reservations.index') }}"
                    class="btn btn-outline-primary btn-sm"
                >
                    View My Lessons
                </a>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    {{-- Header --}}
                    <thead class="table-light">

                        <tr>

                            <th class="px-4 py-3">
                                Time
                            </th>

                            <th class="py-3">
                                Student
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

                        @forelse ($todayReservations as $reservation)

                            @php

                                $startAt =
                                    \Carbon\Carbon::parse(
                                        $reservation->start_at
                                    );

                                $endAt =
                                    \Carbon\Carbon::parse(
                                        $reservation->end_at
                                    );

                            @endphp


                            <tr>

                                {{-- Time --}}
                                <td class="px-4">

                                    <div class="fw-bold">

                                        {{ $startAt->format('h:i A') }}

                                        -

                                        {{ $endAt->format('h:i A') }}

                                    </div>

                                </td>


                                {{-- Student --}}
                                <td>

                                    <i class="fa-solid fa-circle-user me-2"></i>

                                    {{ $reservation->student->user->first_name }}

                                    {{ $reservation->student->user->last_name }}

                                </td>


                                {{-- Material --}}
                                <td>

                                    {{ $reservation->material->name }}

                                </td>


                                {{-- Status --}}
                                <td>

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

                                </td>


                                {{-- Action --}}
                                <td class="text-end pe-4">

                                    {{-- 今はダミー --}}
                                    <a
                                        href="{{ route('teachers.reservations.show.test') }}"
                                        class="btn btn-outline-primary btn-sm"
                                    >
                                        Details
                                    </a>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="text-center py-5 text-secondary"
                                >

                                    No lessons scheduled for today.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- ===============================
         TODO
    ================================ --}}
    {{--

        TODO:

        現在は表示確認用のダミーデータ。


        最終的には

        ログイン中Teacherの

        今日の

        pending / confirmed

        の予約を取得して表示する。


        表示内容:

        ・Time
        ・Student
        ・Material
        ・Status
        ・Details


        Details:

        最終的には

        teachers.reservations.show

        に接続する。


        My Lessons:

        今日以降の予約一覧を表示する。


        Lesson終了後:

        statusをcompletedに変更し、

        生徒側のLearning Historyや

        先生側のLesson Historyに
        表示する予定。

    --}}

</div>

@endsection
