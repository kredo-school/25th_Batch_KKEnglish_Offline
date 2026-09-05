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
        <div class="bg-light p-4 mb-4 d-flex justify-content-between align-items-center">

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
    <a href="{{ route('students.reservations.index') }}" class="btn btn-primary">
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

                            {{-- レッスン1 --}}
                            <div class="border rounded p-3 mb-3">
                                <p class="mb-1 fw-bold">
                                    10:00 - 10:50
                                </p>

                                <p class="mb-0">
                                    Sarah Mitchell
                                </p>
                            </div>

                            {{-- レッスン2 --}}
                            <div class="border rounded p-3">
                                <p class="mb-1 fw-bold">
                                    14:00 - 14:50
                                </p>

                                <p class="mb-0">
                                    Sarah Mitchell
                                </p>
                            </div>

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
