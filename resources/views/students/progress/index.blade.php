@extends('layouts.app')

@section('title', 'Learning Progress')

@section('content')

<div class="container-fluid py-4">

    <style>

        .progress-monkey-bubble {
            margin-right: 4px;

            background-color: #fff;
            border: 1px solid #d6d6d6;
            border-radius: 14px;

            padding: 7px 12px;

            font-size: 0.85rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .progress-monkey {
            width: 57px;
            height: 57px;

            object-fit: contain;

            margin-left: -8px;
            margin-top: -4px;

            flex-shrink: 0;
        }

    </style>


    {{-- ===============================
         Title
    ================================ --}}
    <div class="mb-4">

        <h2 class="fw-bold mb-1">
            Learning Progress
        </h2>

        <p class="text-secondary mb-0">
            See how much you've learned and keep going!
        </p>

    </div>


    {{-- ===============================
         Summary Cards
    ================================ --}}
    <div class="row g-3 mb-4">

        {{-- Total Lessons --}}
        <div class="col-md-4">

            <div class="card h-100">

                <div class="card-body">

                    <div class="text-secondary small mb-2">
                        Total Lessons
                    </div>

                    <div class="d-flex align-items-center gap-2">

                        <i class="fa-solid fa-book-open text-primary"></i>

                        <h3 class="fw-bold mb-0">
                            {{ $totalLessons }}
                        </h3>

                        <span class="text-secondary">
                            lessons
                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- This Month --}}
        <div class="col-md-4">

            <div class="card h-100">

                <div class="card-body">

                    <div class="text-secondary small mb-2">
                        This Month
                    </div>

                    <div class="d-flex align-items-center gap-2">

                        <i class="fa-regular fa-calendar text-success"></i>

                        <h3 class="fw-bold mb-0">
                            {{ $thisMonthLessons }}
                        </h3>

                        <span class="text-secondary">
                            lessons
                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- Study Time --}}
        <div class="col-md-4">

            <div class="card h-100">

                <div class="card-body">

                    <div class="text-secondary small mb-2">
                        Study Time
                    </div>

                    <div class="d-flex align-items-center gap-2">

                        <i class="fa-regular fa-clock text-warning"></i>

                        <h3 class="fw-bold mb-0">
                            {{ number_format($studyHours, 1) }}
                        </h3>

                        <span class="text-secondary">
                            hours
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ===============================
         Detail
    ================================ --}}
    <div class="row g-4">

        {{-- Current Level --}}
        <div class="col-lg-6">

            <div class="card h-100">

                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3">
                        Current Level
                    </h5>

                    <div class="d-flex align-items-center gap-3">

                        <div
                            class="
                                rounded-circle
                                bg-primary-subtle
                                text-primary
                                d-flex
                                align-items-center
                                justify-content-center
                                fw-bold
                            "
                            style="
                                width: 60px;
                                height: 60px;
                                font-size: 1.3rem;
                            "
                        >
                            {{ $currentLevel }}
                        </div>

                        <div>

                            <div class="fw-bold">
                                {{ $levelLabel }}
                            </div>

                            <div class="text-secondary small">
                                {{ $levelDescription }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Most Studied Material --}}
        <div class="col-lg-6">

            <div class="card h-100">

                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3">
                        Most Studied Material
                    </h5>

                    <div class="d-flex align-items-center gap-3">

                        <div
                            class="
                                rounded
                                bg-light
                                d-flex
                                align-items-center
                                justify-content-center
                            "
                            style="
                                width: 60px;
                                height: 60px;
                            "
                        >

                            <i class="fa-solid fa-book fa-lg text-secondary"></i>

                        </div>

                        <div>

                            <div class="fw-bold">
                                {{ $mostStudiedMaterialName }}
                            </div>

                            <div class="text-secondary small">
                                {{ $mostStudiedMaterialLessons }} lessons completed
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ===============================
         Monkey Message
    ================================ --}}
    <div
        class="
            d-flex
            align-items-center
            justify-content-center
            mt-4
        "
    >

        <div class="progress-monkey-bubble">
            {{ $monkeyMessage }}
        </div>

        <img
            src="{{ asset('images/kk-monkey.png') }}"
            alt="KK English Monkey"
            class="progress-monkey"
        >

    </div>

</div>

@endsection