@extends('layouts.app')

@section('title', 'My Lessons')

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
    | Teacher\ReservationController@index()
    | から
    | $upcomingReservations
    | を受け取る。
    |
    */

    $upcomingReservations = collect([

        (object) [
            'id' => 1,

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
        ],


        (object) [
            'id' => 2,

            'start_at' => '2026-09-12 13:30:00',
            'end_at' => '2026-09-12 14:00:00',

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


        (object) [
            'id' => 3,

            'start_at' => '2026-09-15 16:00:00',
            'end_at' => '2026-09-15 16:30:00',

            'student' => (object) [
                'user' => (object) [
                    'first_name' => 'Mika',
                    'last_name' => 'Sato',
                ],
            ],

            'material' => (object) [
                'name' => 'Pronunciation',
            ],

            'status' => (object) [
                'status_code' => 'pending',
            ],
        ],

    ]);

@endphp


<div class="container-fluid py-4">

    {{-- ===============================
         Title
    ================================ --}}
    <div class="mb-4">

        <h2 class="fw-bold mb-1">
            My Lessons
        </h2>

        <p class="text-secondary mb-0">
            View your upcoming scheduled lessons.
        </p>

    </div>


    {{-- ===============================
         Upcoming Lessons
    ================================ --}}
    <div class="card">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                Upcoming Lessons
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

                        @forelse ($upcomingReservations as $reservation)

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

                                {{-- Date --}}
                                <td class="px-4">

                                    <div class="fw-bold">

                                        {{ $startAt->format('M d, Y') }}

                                    </div>

                                    <small class="text-secondary">

                                        {{ $startAt->format('l') }}

                                    </small>

                                </td>


                                {{-- Time --}}
                                <td>

                                    {{ $startAt->format('h:i A') }}

                                    -

                                    {{ $endAt->format('h:i A') }}

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
                                    colspan="6"
                                    class="text-center py-5 text-secondary"
                                >

                                    No upcoming lessons.

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

        Teacher\ReservationController@index()

        から

        $upcomingReservations

        を取得して表示する。


        表示対象:

        ・ログイン中Teacherの予約
        ・未来の予約
        ・pending / confirmed


        Details:

        最終的には

        teachers.reservations.show

        に接続する。


        本日のレッスンは
        Teacher Dashboardに表示する。


        レッスン終了後は
        statusをcompletedに変更し、

        Lesson History側に
        表示する予定。

    --}}

</div>

@endsection