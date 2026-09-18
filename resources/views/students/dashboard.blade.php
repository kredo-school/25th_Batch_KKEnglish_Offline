@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<style>
    #calendar a {
        color: #000;
        text-decoration: none;
    }
</style>

<div class="container-fluid">
    <div class="row">


        {{-- Dashboard --}}
      <div class="col-12">

         {{-- Hello Header --}}
        <div class="bg-light mb-4 d-flex justify-content-between align-items-center px-4 py-3">

         {{-- 名前・日付 --}}
      <div>
        <h2 class="fw-bold mb-1">
            Hello, {{ auth()->user()->first_name }}

        </h2>

        <p class="text-secondary mb-0">
           {{ now()->format('l, F j') }}
        </p>

         <p class="fw-semibold mb-0">
            <i class="fa-regular fa-clock me-1"></i>
            {{ now()->format('H:i') }}
          </p>

      </div>

    {{-- レッスン予約ボタン --}}
    <a href="{{ route('students.reservations.index') }}" class="btn btn-primary me-3">
        レッスンを予約
    </a>

</div>

            {{-- Main --}}
            <div class="row g-3">

                {{-- 左側：本日のレッスン --}}
                <div class="col-md-7">

                    <div class="card">
                        <div class="card-body">

                            {{-- タイトル＋日付切り替え --}}
                            <div class="d-flex align-items-center gap-4 mb-4">

                                <h5 class="mb-0">
                                    本日のレッスン
                                </h5>

                                <div class="d-flex align-items-center gap-3">

                                    <a href="#"
                                       class="text-dark text-decoration-none fw-bold">
                                        &lt; 前日
                                    </a>

                                    <span class="fw-bold">
                                        {{ now()->format('n月j日') }}
                                    </span>

                                    <a href="#"
                                       class="text-dark text-decoration-none fw-bold">
                                        翌日 &gt;
                                    </a>

                                </div>
                            </div>

                {{-- ===============================
                    Today's Lessons
                ================================ --}}
                @forelse ($todayLessons as $lesson)

                    @php
                        $startAt = \Carbon\Carbon::parse(
                            $lesson->start_at
                        );

                        $endAt = \Carbon\Carbon::parse(
                            $lesson->end_at
                        );
                    @endphp

                    <div class="border rounded p-3 mb-3">

                        {{-- Time --}}
                        <p class="mb-1 fw-bold">

                            {{ $startAt->format('H:i') }}

                            -

                            {{ $endAt->format('H:i') }}

                        </p>


                        {{-- Teacher --}}
                        <p class="mb-1">

                            {{
                                $lesson
                                    ->teacher
                                    ->user
                                    ->first_name
                            }}

                            {{
                                $lesson
                                    ->teacher
                                    ->user
                                    ->last_name
                            }}

                        </p>


                        {{-- Material --}}
                        <p class="text-secondary small mb-0">

                            {{
                                $lesson
                                    ->material
                                    ->name
                                ?? '-'
                            }}

                        </p>

                    </div>

                @empty

                    <div class="text-center py-4">

                        <i
                            class="
                                fa-regular
                                fa-calendar-check
                                fa-2x
                                text-secondary
                                mb-2
                            "
                        ></i>

                        <p class="text-secondary mb-0">
                            No lessons scheduled for today.
                        </p>

                    </div>

                @endforelse

                        </div>
                    </div>

                </div>

                {{-- 右側 --}}
                <div class="col-md-5">

                    {{-- ポイント・レベル --}}
                    <div class="row g-3 mb-3">

                        {{-- Point --}}
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body">
                                    保有ポイント
                                </div>
                            </div>
                        </div>

                        {{-- Level --}}
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body">
                                    現在のレベル
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Information --}}
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-0">
                                お知らせ
                            </h5>
                        </div>
                    </div>

                </div>

            </div>
        </div>


        {{-- Reservation Calendar --}}
<div class="card mt-4">
    <div class="card-body">

        <div class="d-flex align-items-center mb-3">
            <i class="fa-regular fa-calendar me-2"></i>

            <h5 class="mb-0">
                Reservation Calendar
            </h5>
        </div>

        <div id="calendar"></div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',

        locale: 'ja',

        headerToolbar: {
            left: 'title',
            center: '',
            right: 'prev,next'
        },

        height: 'auto'
    });

    calendar.render();
});
</script>


    </div>
</div>

@endsection
