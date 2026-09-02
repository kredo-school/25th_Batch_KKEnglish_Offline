@extends('layouts.app')

@section('title', 'Book a Lesson')

@section('content')

<div class="container py-5">

    {{-- Title --}}
    <div class="text-center mb-5">

        <h2 class="fw-bold mb-3">
            Book a Lesson
        </h2>

    </div>


    <div class="row justify-content-center g-4">

        {{-- ===============================
             Find from My Schedule
        ================================ --}}
        <div class="col-md-5">

            <div class="card h-100 shadow-sm">

                <div class="card-body p-4 text-center">

                    <i class="fa-solid fa-calendar-days fa-2x mb-3"></i>

                    <h4 class="fw-bold mb-3">
                        Find from My Schedule
                    </h4>


                    {{-- 後でrouteに変更 --}}
                    <a href="#"
                       class="btn btn-outline-primary w-100">

                        View My Schedule

                    </a>

                </div>

            </div>

        </div>


        {{-- ===============================
             Search for a Lesson
        ================================ --}}
        <div class="col-md-5">

            <div class="card h-100 shadow-sm">

                <div class="card-body p-4 text-center">

                    <i class="fa-solid fa-magnifying-glass fa-2x mb-3"></i>

                    <h4 class="fw-bold mb-3">
                        Search for a Lesson
                    </h4>


                    {{-- 後で今の予約画面のrouteに変更 --}}
                    <a href="#"
                       class="btn btn-primary w-100">

                        Search for a Lesson

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection