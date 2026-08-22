@extends('layouts.app')

@section('content')

<div class="container py-4">

    <h2 class="fw-bold mb-4">Teaching Materials</h2>

    {{-- 教材一覧 --}}
    <div class="row g-4">

        {{-- 教材1 --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">

                    <h5 class="fw-bold">
                        Daily Conversation
                    </h5>

                    <p class="text-secondary">
                        Practice useful English expressions for everyday situations.
                    </p>

                    <p class="mb-0">
                        <strong>Category:</strong> Conversation
                    </p>

                </div>
            </div>
        </div>


        {{-- 教材2 --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">

                    <h5 class="fw-bold">
                        Business English
                    </h5>

                    <p class="text-secondary">
                        Learn practical expressions for business situations.
                    </p>

                    <p class="mb-0">
                        <strong>Category:</strong> Business
                    </p>

                </div>
            </div>
        </div>


        {{-- 教材3 --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">

                    <h5 class="fw-bold">
                        Free Discussion
                    </h5>

                    <p class="text-secondary">
                        Improve your speaking skills through discussions.
                    </p>

                    <p class="mb-0">
                        <strong>Category:</strong> Discussion
                    </p>

                </div>
            </div>
        </div>

    </div>

</div>

@endsection