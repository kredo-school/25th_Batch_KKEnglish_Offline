@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

    {{-- タイトル --}}
    <div class="bg-light px-3 py-2 mb-3">
        <h4 class="mb-0 fw-bold">Teacher Profile</h4>
    </div>

    <div class="row g-4">

        {{-- 左：先生の基本情報 --}}
        <div class="col-md-4">

            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    {{-- 写真・名前 --}}
                    <div class="d-flex align-items-center mb-4">

                        <img src="{{ asset('images/teacher.jpg') }}"
                             alt="Teacher"
                             class="rounded-circle me-3"
                             width="80"
                             height="80"
                             style="object-fit: cover;">

                        <div>
                            <h4 class="fw-bold mb-1">
                                Teacher Name
                            </h4>

                            <p class="text-secondary mb-0">
                                English Teacher
                            </p>
                        </div>

                    </div>


                    {{-- プロフィール情報 --}}
                    <div class="mb-3">
                        <small class="text-secondary">Name</small>
                        <p class="mb-0">Teacher Name</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-secondary">Email</small>
                        <p class="mb-0">teacher@example.com</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-secondary">Nationality</small>
                        <p class="mb-0">Philippines</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-secondary">Teaching Experience</small>
                        <p class="mb-0">3 years</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-secondary">Specialty</small>
                        <p class="mb-0">Daily Conversation</p>
                    </div>

                </div>
            </div>

        </div>


        {{-- 右：先生の紹介 --}}
        <div class="col-md-8">

            {{-- 自己紹介 --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">
                        About Me
                    </h5>

                    <p class="mb-0">
                        Hello! I enjoy teaching English and helping students
                        improve their speaking skills.
                        Let's enjoy learning English together!
                    </p>

                </div>
            </div>


            {{-- レッスン情報 --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">
                        Available Lessons
                    </h5>

                    <div class="border rounded p-3 mb-3">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>
                                <h6 class="fw-bold mb-1">
                                    Daily English Conversation
                                </h6>

                                <p class="text-secondary mb-0">
                                    Beginner
                                </p>
                            </div>

                            <a href="#"
                               class="btn btn-outline-primary">
                                View
                            </a>

                        </div>

                    </div>


                    <div class="border rounded p-3">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>
                                <h6 class="fw-bold mb-1">
                                    Business English
                                </h6>

                                <p class="text-secondary mb-0">
                                    Intermediate
                                </p>
                            </div>

                            <a href="#"
                               class="btn btn-outline-primary">
                                View
                            </a>

                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>

</div>

@endsection