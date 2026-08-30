@extends('layouts.app')

@section('title', 'Teachers')

@section('content')

<div class="container py-4">

    <h2 class="fw-bold mb-4">
        Teachers
    </h2>


    {{-- Favorite Teachers --}}
    <h5 class="fw-bold mb-3">
        Favorite Teachers
    </h5>

    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-3 mb-5">

        {{-- Favorite Teacher 1 --}}
        <div class="col">

            <div class="card h-100">

                <img src="{{ asset('images/teacher1.jpg') }}"
                     alt="John"
                     class="card-img-top"
                     style="height: 180px; object-fit: cover;">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-2">

                        <h5 class="fw-bold mb-0">
                            John
                        </h5>

                        <i class="fa-solid fa-heart text-danger"></i>

                    </div>

                    <p class="mb-1 small">
                        <span class="text-secondary">Nationality:</span>
                        Philippines
                    </p>

                    <p class="mb-3 small">
                        <span class="text-secondary">Specialty:</span>
                        Daily Conversation
                    </p>

                    <a href="#"
                       class="btn btn-outline-primary btn-sm w-100">
                        View Profile
                    </a>

                </div>

            </div>

        </div>


        {{-- Favorite Teacher 2 --}}
        <div class="col">

            <div class="card h-100">

                <img src="{{ asset('images/teacher2.jpg') }}"
                     alt="Jane"
                     class="card-img-top"
                     style="height: 180px; object-fit: cover;">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-2">

                        <h5 class="fw-bold mb-0">
                            Jane
                        </h5>

                        <i class="fa-solid fa-heart text-danger"></i>

                    </div>

                    <p class="mb-1 small">
                        <span class="text-secondary">Nationality:</span>
                        Philippines
                    </p>

                    <p class="mb-3 small">
                        <span class="text-secondary">Specialty:</span>
                        Grammar
                    </p>

                    <a href="#"
                       class="btn btn-outline-primary btn-sm w-100">
                        View Profile
                    </a>

                </div>

            </div>

        </div>

    </div>


    {{-- All Teachers --}}
    <h5 class="fw-bold mb-3">
        All Teachers
    </h5>

    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-3">

        @foreach ($teachers as $teacher)

            <div class="col">

                <div class="card h-100">

                    <img src="{{ $teacher->user->profile_image }}"
                         alt="{{ $teacher->user->first_name }}"
                         class="card-img-top"
                         style="height: 180px; object-fit: cover;">

                    <div class="card-body d-flex flex-column">

                        <div class="d-flex justify-content-between align-items-start mb-2">

                            <h5 class="fw-bold mb-0"
                                style="min-height: 48px;">
                                {{ $teacher->user->first_name }}
                                {{ $teacher->user->last_name }}
                            </h5>

                            <i class="fa-regular fa-heart text-secondary ms-2"></i>

                        </div>

                        <p class="mb-1 small">
                            <span class="text-secondary">
                                Nationality:
                            </span>

                            {{ $teacher->user->nationality }}
                        </p>

                        <p class="mb-3 small"
                           style="min-height: 60px;">
                            <span class="text-secondary">
                                Specialty:
                            </span>

                            {{ $teacher->specialty }}
                        </p>

                        <a href="{{ route('teachers.show', $teacher->id) }}"
                           class="btn btn-outline-primary btn-sm w-100 mt-auto">
                            View Profile
                        </a>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

</div>

@endsection
