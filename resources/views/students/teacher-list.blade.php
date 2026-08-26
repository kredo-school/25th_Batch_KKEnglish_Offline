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

                        {{-- お気に入り --}}
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

                    {{-- バックエンド実装後 --}}
                    {{-- href="{{ route('teachers.show', $teacher->id) }}" に変更 --}}
                    <a href="{{ route('teacher.profile') }}"
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

                        {{-- お気に入り --}}
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

                    {{-- バックエンド実装後 --}}
                    {{-- href="{{ route('teachers.show', $teacher->id) }}" に変更 --}}
                    <a href="{{ route('teacher.profile') }}"
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

        {{-- Teacher 1 --}}
        <div class="col">

            <div class="card h-100">

                <img src="{{ asset('images/teacher1.jpg') }}"
                     alt="John"
                     class="card-img-top"
                     style="height: 180px; object-fit: cover;">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-2">

                        <h5 class="fw-bold mb-0">
                            Ken
                        </h5>

                         <i class="fa-regular fa-heart text-secondary"></i>

                    </div>

                    <p class="mb-1 small">
                        <span class="text-secondary">Nationality:</span>
                        Philippines
                    </p>

                    <p class="mb-3 small">
                        <span class="text-secondary">Specialty:</span>
                        Daily Conversation
                    </p>

                    <a href="{{ route('teacher.profile') }}"
                       class="btn btn-outline-primary btn-sm w-100">
                        View Profile
                    </a>

                </div>

            </div>

        </div>


        {{-- Teacher 2 --}}
        <div class="col">

            <div class="card h-100">

                <img src="{{ asset('images/teacher2.jpg') }}"
                     alt="Jane"
                     class="card-img-top"
                     style="height: 180px; object-fit: cover;">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-2">

                        <h5 class="fw-bold mb-0">
                            Kei
                        </h5>

                         <i class="fa-regular fa-heart text-secondary"></i>
                    </div>

                    <p class="mb-1 small">
                        <span class="text-secondary">Nationality:</span>
                        Philippines
                    </p>

                    <p class="mb-3 small">
                        <span class="text-secondary">Specialty:</span>
                        Grammar
                    </p>

                    <a href="{{ route('teacher.profile') }}"
                       class="btn btn-outline-primary btn-sm w-100">
                        View Profile
                    </a>

                </div>

            </div>

        </div>


        {{-- Teacher 3 --}}
        <div class="col">

            <div class="card h-100">

                <img src="{{ asset('images/teacher3.jpg') }}"
                     alt="Bob"
                     class="card-img-top"
                     style="height: 180px; object-fit: cover;">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-2">

                        <h5 class="fw-bold mb-0">
                            Bob
                        </h5>

                        <i class="fa-regular fa-heart text-secondary"></i>

                    </div>

                    <p class="mb-1 small">
                        <span class="text-secondary">Nationality:</span>
                        Philippines
                    </p>

                    <p class="mb-3 small">
                        <span class="text-secondary">Specialty:</span>
                        Pronunciation
                    </p>

                    <a href="{{ route('teacher.profile') }}"
                       class="btn btn-outline-primary btn-sm w-100">
                        View Profile
                    </a>

                </div>

            </div>

        </div>


        {{-- Teacher 4 --}}
        <div class="col">

            <div class="card h-100">

                <img src="{{ asset('images/teacher4.jpg') }}"
                     alt="Mary"
                     class="card-img-top"
                     style="height: 180px; object-fit: cover;">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-2">

                        <h5 class="fw-bold mb-0">
                            Mary
                        </h5>

                        <i class="fa-regular fa-heart text-secondary"></i>

                    </div>

                    <p class="mb-1 small">
                        <span class="text-secondary">Nationality:</span>
                        Philippines
                    </p>

                    <p class="mb-3 small">
                        <span class="text-secondary">Specialty:</span>
                        Business English
                    </p>

                    <a href="{{ route('teacher.profile') }}"
                       class="btn btn-outline-primary btn-sm w-100">
                        View Profile
                    </a>

                </div>

            </div>

        </div>


        {{-- Teacher 5 --}}
        <div class="col">

            <div class="card h-100">

                <img src="{{ asset('images/teacher5.jpg') }}"
                     alt="Mike"
                     class="card-img-top"
                     style="height: 180px; object-fit: cover;">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-2">

                        <h5 class="fw-bold mb-0">
                            Mike
                        </h5>

                        <i class="fa-regular fa-heart text-secondary"></i>

                    </div>

                    <p class="mb-1 small">
                        <span class="text-secondary">Nationality:</span>
                        Philippines
                    </p>

                    <p class="mb-3 small">
                        <span class="text-secondary">Specialty:</span>
                        Daily Conversation
                    </p>

                    <a href="{{ route('teacher.profile') }}"
                       class="btn btn-outline-primary btn-sm w-100">
                        View Profile
                    </a>

                </div>

            </div>

        </div>


        {{-- Teacher 6 --}}
        <div class="col">

            <div class="card h-100">

                <img src="{{ asset('images/teacher6.jpg') }}"
                     alt="Anna"
                     class="card-img-top"
                     style="height: 180px; object-fit: cover;">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-2">

                        <h5 class="fw-bold mb-0">
                            Anna
                        </h5>

                        <i class="fa-regular fa-heart text-secondary"></i>

                    </div>

                    <p class="mb-1 small">
                        <span class="text-secondary">Nationality:</span>
                        Philippines
                    </p>

                    <p class="mb-3 small">
                        <span class="text-secondary">Specialty:</span>
                        Kids English
                    </p>

                    <a href="{{ route('teacher.profile') }}"
                       class="btn btn-outline-primary btn-sm w-100">
                        View Profile
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection