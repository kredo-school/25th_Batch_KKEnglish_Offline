@extends('layouts.app')

@section('title', 'Teacher Details')

@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | ダミー表示モード
    |--------------------------------------------------------------------------
    |
    | mode=material
    | → 教材だけ選択した状態
    |
    | mode=date
    | → 教材 + 日付を選択した状態
    |
    */

    $mode = request('mode', 'material');


    /*
    |--------------------------------------------------------------------------
    | 選択条件
    |--------------------------------------------------------------------------
    */

    $selectedMaterial = 'Daily Conversation';

    $selectedDate =
        $mode === 'date'
            ? '2026-09-04'
            : null;


    /*
    |--------------------------------------------------------------------------
    | 今日の日付
    |--------------------------------------------------------------------------
    |
    | 表示確認用として
    | 2026/09/03 に固定
    |
    */

    $today = '2026-09-03';


    /*
    |--------------------------------------------------------------------------
    | 1週間
    |--------------------------------------------------------------------------
    */

    $days = [

        [
            'day' => 'Tue',
            'date' => '2026-09-01',
            'display' => '09/01',
        ],

        [
            'day' => 'Wed',
            'date' => '2026-09-02',
            'display' => '09/02',
        ],

        [
            'day' => 'Thu',
            'date' => '2026-09-03',
            'display' => '09/03',
        ],

        [
            'day' => 'Fri',
            'date' => '2026-09-04',
            'display' => '09/04',
        ],

        [
            'day' => 'Sat',
            'date' => '2026-09-05',
            'display' => '09/05',
        ],

        [
            'day' => 'Sun',
            'date' => '2026-09-06',
            'display' => '09/06',
        ],

        [
            'day' => 'Mon',
            'date' => '2026-09-07',
            'display' => '09/07',
        ],

    ];


    /*
    |--------------------------------------------------------------------------
    | 時間
    |--------------------------------------------------------------------------
    */

    $times = [
        '09:00',
        '09:30',
        '10:00',
        '10:30',
        '11:00',
    ];


    /*
    |--------------------------------------------------------------------------
    | ダミースケジュール
    |--------------------------------------------------------------------------
    */

    $schedule = [

        '2026-09-01' => [
            '09:00' => 'open',
            '09:30' => 'closed',
            '10:00' => 'reserved',
            '10:30' => 'open',
            '11:00' => 'closed',
        ],

        '2026-09-02' => [
            '09:00' => 'open',
            '09:30' => 'open',
            '10:00' => 'open',
            '10:30' => 'closed',
            '11:00' => 'open',
        ],

        '2026-09-03' => [
            '09:00' => 'reserved',
            '09:30' => 'open',
            '10:00' => 'closed',
            '10:30' => 'open',
            '11:00' => 'reserved',
        ],

        '2026-09-04' => [
            '09:00' => 'open',
            '09:30' => 'open',
            '10:00' => 'unavailable',
            '10:30' => 'open',
            '11:00' => 'open',
        ],

        '2026-09-05' => [
            '09:00' => 'open',
            '09:30' => 'reserved',
            '10:00' => 'open',
            '10:30' => 'reserved',
            '11:00' => 'open',
        ],

        '2026-09-06' => [
            '09:00' => 'closed',
            '09:30' => 'closed',
            '10:00' => 'closed',
            '10:30' => 'closed',
            '11:00' => 'closed',
        ],

        '2026-09-07' => [
            '09:00' => 'open',
            '09:30' => 'open',
            '10:00' => 'reserved',
            '10:30' => 'open',
            '11:00' => 'closed',
        ],

    ];

@endphp


<div class="container-fluid py-4">

    {{-- ===============================
         Title
    ================================ --}}
    <div class="bg-light px-3 py-2 mb-4">

        <h4 class="mb-0 fw-bold">
            Teacher Details
        </h4>

    </div>


    {{-- ===============================
         ダミー表示切り替え
    ================================ --}}
    <div class="mb-4">

        <span class="text-secondary me-2">
            Test View:
        </span>


        {{-- 教材のみ --}}
        <a
            href="{{ route(
                'reservations.teacher-detail.test',
                ['mode' => 'material']
            ) }}"
            class="
                btn
                btn-sm
                {{ $mode === 'material'
                    ? 'btn-primary'
                    : 'btn-outline-primary'
                }}
            "
        >
            Material Only
        </a>


        {{-- 教材 + 日付 --}}
        <a
            href="{{ route(
                'reservations.teacher-detail.test',
                ['mode' => 'date']
            ) }}"
            class="
                btn
                btn-sm
                {{ $mode === 'date'
                    ? 'btn-primary'
                    : 'btn-outline-primary'
                }}
            "
        >
            Material + Date
        </a>

    </div>


    <div class="row g-4">

        {{-- ===============================
             Left
             Teacher Profile
        ================================ --}}
        <div class="col-lg-4">

            <div class="card h-100">

                <div class="card-body p-4">

                    {{-- 写真・名前 --}}
                    <div class="text-center mb-4">

                        <img
                            src="{{ asset('images/teacher1.jpg') }}"
                            alt="John Smith"
                            class="rounded-circle mb-3"
                            width="120"
                            height="120"
                            style="object-fit: cover;"
                        >

                        <h4 class="fw-bold mb-1">
                            John Smith
                        </h4>

                        <p class="text-secondary mb-0">
                            English Teacher
                        </p>

                    </div>


                    <hr>


                    {{-- Nationality --}}
                    <div class="mb-3">

                        <strong>
                            Nationality
                        </strong>

                        <p class="mb-0">
                            Philippines
                        </p>

                    </div>


                    {{-- Teaching Experience --}}
                    <div class="mb-3">

                        <strong>
                            Teaching Experience
                        </strong>

                        <p class="mb-0">
                            5 years
                        </p>

                    </div>


                    {{-- Specialty --}}
                    <div class="mb-3">

                        <strong>
                            Specialty
                        </strong>

                        <p class="mb-0">
                            Daily Conversation
                        </p>

                    </div>


                    {{-- Certification --}}
                    <div class="mb-3">

                        <strong>
                            Certification
                        </strong>

                        <p class="mb-0">
                            TESOL
                        </p>

                    </div>


                    {{-- Graduation School --}}
                    <div class="mb-3">

                        <strong>
                            Graduation School
                        </strong>

                        <p class="mb-0">
                            Cebu Normal University
                        </p>

                    </div>


                    <hr>


                    {{-- About Me --}}
                    <div>

                        <strong>
                            About Me
                        </strong>

                        <p class="mt-2 mb-0">

                            Hello! I'm John.

                            I enjoy helping students improve
                            their English through practical
                            conversation lessons.

                        </p>

                    </div>

                </div>

            </div>

        </div>


        {{-- ===============================
             Right
             Weekly Schedule
        ================================ --}}
        <div class="col-lg-8">

            <div class="card">

                <div class="card-body p-4">

                    {{-- Schedule Title --}}
                    <div class="mb-3">

                        <h4 class="fw-bold mb-1">
                            Weekly Schedule
                        </h4>

                        <p class="text-secondary mb-0">
                            You can view this teacher's availability for the week.
                        </p>

                    </div>


                    {{-- ===============================
                         Selected Conditions
                    ================================ --}}
                    <div class="alert alert-info">

                        <div class="row g-2">

                            {{-- Date --}}
                            <div class="col-md-6">

                                <span class="text-secondary">
                                    Selected Date:
                                </span>


                                @if($selectedDate)

                                    <strong>
                                        Sep 04, 2026
                                    </strong>

                                @else

                                    <span class="text-secondary">
                                        Not selected
                                    </span>

                                @endif

                            </div>


                            {{-- Material --}}
                            <div class="col-md-6">

                                <span class="text-secondary">
                                    Material:
                                </span>

                                <strong>
                                    {{ $selectedMaterial }}
                                </strong>

                            </div>

                        </div>

                    </div>


                    {{-- ===============================
                         Week Range
                    ================================ --}}
                    <div class="text-center mb-3">

                        <h5 class="fw-bold mb-0">
                            Sep 01 - Sep 07, 2026
                        </h5>

                    </div>


                    {{-- ===============================
                         Weekly Schedule Table
                    ================================ --}}
                    <div class="table-responsive">

                        <table class="
                            table
                            table-bordered
                            text-center
                            align-middle
                        ">

                            {{-- Header --}}
                            <thead class="table-light">

                                <tr>

                                    <th style="min-width: 90px;">
                                        Time
                                    </th>


                                    @foreach($days as $day)

                                        @php

                                            $isPast =
                                                $day['date'] < $today;

                                            $isToday =
                                                $day['date'] === $today;

                                            $isSelected =
                                                $selectedDate === $day['date'];

                                        @endphp


                                        <th
                                            class="
                                                @if($isPast)
                                                    table-secondary
                                                @elseif($isSelected)
                                                    table-info
                                                @endif
                                            "
                                            style="min-width: 105px;"
                                        >

                                            <div class="fw-bold">
                                                {{ $day['day'] }}
                                            </div>


                                            <small class="text-secondary">
                                                {{ $day['display'] }}
                                            </small>


                                            {{-- 過去 --}}
                                            @if($isPast)

                                                <div class="mt-1">

                                                    <span class="badge text-bg-secondary">
                                                        Past
                                                    </span>

                                                </div>


                                            {{-- 選択日 --}}
                                            @elseif($isSelected)

                                                <div class="mt-1">

                                                    <span class="badge text-bg-primary">
                                                        Selected
                                                    </span>

                                                </div>


                                            {{-- 今日 --}}
                                            @elseif($isToday)

                                                <div class="mt-1">

                                                    <span class="badge text-bg-dark">
                                                        Today
                                                    </span>

                                                </div>

                                            @endif

                                        </th>

                                    @endforeach

                                </tr>

                            </thead>


                            {{-- Body --}}
                            <tbody>

                                @foreach($times as $time)

                                    <tr>

                                        {{-- Time --}}
                                        <th class="table-light">
                                            {{ $time }}
                                        </th>


                                        @foreach($days as $day)

                                            @php

                                                $date =
                                                    $day['date'];

                                                $status =
                                                    $schedule[$date][$time]
                                                    ?? 'closed';

                                                $isPast =
                                                    $date < $today;

                                                $isSelected =
                                                    $selectedDate === $date;

                                            @endphp


                                            <td
                                                class="
                                                    @if($isPast)
                                                        table-secondary
                                                    @elseif($isSelected)
                                                        table-info
                                                    @endif
                                                "
                                            >

                                                {{-- ===============================
                                                     過去の日
                                                ================================ --}}
                                                @if($isPast)

                                                    <span class="text-secondary">
                                                        -
                                                    </span>


                                                {{-- ===============================
                                                     OPEN
                                                ================================ --}}
                                                @elseif($status === 'open')


                                                    {{-- ① Material Only --}}
                                                    @if($mode === 'material')

                                                        <button
                                                            type="button"
                                                            class="
                                                                btn
                                                                btn-primary
                                                                btn-sm
                                                                w-100
                                                            "
                                                        >
                                                            Book
                                                        </button>


                                                    {{-- ② Material + Date --}}
                                                    @elseif($isSelected)

                                                        <button
                                                            type="button"
                                                            class="
                                                                btn
                                                                btn-primary
                                                                btn-sm
                                                                w-100
                                                            "
                                                        >
                                                            Book
                                                        </button>


                                                    {{-- 他の日 --}}
                                                    @else

                                                        <span class="badge text-bg-success">
                                                            OPEN
                                                        </span>

                                                    @endif


                                                {{-- Reserved --}}
                                                @elseif($status === 'reserved')

                                                    <span class="text-secondary">
                                                        Reserved
                                                    </span>


                                                {{-- Unavailable --}}
                                                @elseif($status === 'unavailable')

                                                    <span class="text-secondary">
                                                        Unavailable
                                                    </span>


                                                {{-- Closed --}}
                                                @else

                                                    <span class="text-secondary">
                                                        ×
                                                    </span>

                                                @endif

                                            </td>

                                        @endforeach

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>


                    {{-- ===============================
                         Legend
                    ================================ --}}
                    <div class="
                        d-flex
                        gap-3
                        flex-wrap
                        mt-3
                        small
                    ">

                        <div>

                            <span class="badge text-bg-primary">
                                Book
                            </span>

                            Available for booking

                        </div>


                        <div>

                            <span class="badge text-bg-success">
                                OPEN
                            </span>

                            Available on another day

                        </div>


                        <div class="text-secondary">
                            Reserved = already booked
                        </div>


                        <div class="text-secondary">
                            × = outside working hours
                        </div>


                        <div class="text-secondary">
                            Gray = past date
                        </div>

                    </div>


                    {{-- ===============================
                         TODO
                    ================================ --}}
                    {{--

                        TODO:

                        現在はフロント確認用の
                        ダミーデータを表示している。


                        最終的にはController/APIから

                        ・teacher_id
                        ・material_id
                        ・selected_date

                        を受け取る。


                        ① Materialのみ選択

                        → 先生の1週間スケジュールを表示
                        → 今日以降のOPEN枠からBook可能


                        ② Material + Date

                        → 選択日の列を強調表示
                        → 他の日の空き状況も確認可能
                        → 選択日のOPEN枠だけBook可能


                        過去の日付については

                        → グレー表示
                        → Book不可

                    --}}

                </div>

            </div>

        </div>

    </div>

</div>

@endsection