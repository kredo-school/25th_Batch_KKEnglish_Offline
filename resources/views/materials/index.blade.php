@extends('layouts.app')

@section('title', 'Teaching Materials')

@section('content')

<div class="container py-4">

    <h2 class="fw-bold mb-4">Teaching Materials</h2>

    {{-- 教材一覧 --}}
    <div class="row g-4">

        {{-- 教材1 --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
               <div class="card-body d-flex align-items-center">

                    <img src="{{ asset('images/material1.jpg') }}"
                         alt="Daily Conversation"
                         width="90"
                         height="90"
                         class="rounded me-3"
                         style="object-fit: cover;">

                    <div>

                         <span class="badge bg-primary mb-2">
                            Beginner
                        </span>

                        <h5 class="fw-bold mb-2">
                            Daily Conversation
                        </h5>

                        <p class="text-secondary mb-0">
                            Practice useful English expressions for everyday situations.
                        </p>
                    </div>

                </div>
            </div>
        </div>


        {{-- 教材2 --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">

                      <img src="{{ asset('images/material2.jpg') }}"
                         alt="Business English"
                         width="90"
                         height="90"
                         class="rounded me-3"
                         style="object-fit: cover;">

                     <div>
                        <span class="badge bg-success mb-2">
                            Intermediate
                        </span>

                        <h5 class="fw-bold mb-2">
                            Business English
                        </h5>

                        <p class="text-secondary mb-0">
                            Learn practical expressions for business situations.
                        </p>
                    </div>

                </div>
            </div>
        </div>


        {{-- 教材3 --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
               <div class="card-body d-flex align-items-center">

                    <img src="{{ asset('images/material3.jpg') }}"
                         alt="Free Discussion"
                         width="90"
                         height="90"
                         class="rounded me-3"
                         style="object-fit: cover;">

                    <div>
                        <span class="badge bg-warning text-dark mb-2">
                            Advanced
                        </span>

                        <h5 class="fw-bold mb-2">
                            Free Discussion
                        </h5>

                        <p class="text-secondary mb-0">
                            Improve your speaking skills through discussions.
                        </p>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>

@endsection