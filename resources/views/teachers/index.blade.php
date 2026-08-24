@extends('layouts.app')

@section('title', 'Teachers')

@section('content')

<div class="container py-4">

    <h2 class="fw-bold mb-4">
        Teachers
    </h2>

    <div class="row">

        <div class="col-md-6 col-lg-4">

            <div class="card">

                <div class="card-body text-center">

                    {{-- 写真 --}}
                    <img src="{{ asset('images/teacher1.jpg') }}"
                         alt="John"
                         width="120"
                         height="120"
                         class="rounded-circle mb-3"
                         style="object-fit: cover;">

                    {{-- DB接続後 --}}
                    {{--
                    <img src="{{ asset('storage/' . $teacher->photo) }}"
                         alt="{{ $teacher->user->first_name }}">
                    --}}

                    {{-- 名前 --}}
                    <h5 class="fw-bold mb-2">
                        John
                    </h5>

                    {{-- DB接続後 --}}
                    {{--
                    {{ $teacher->user->first_name }}
                    {{ $teacher->user->last_name }}
                    --}}

                    {{-- 国籍 --}}
                    <p class="text-secondary mb-0">
                        Philippines
                    </p>

                    {{-- DB接続後 --}}
                    {{--
                    {{ $teacher->nationality }}
                    --}}

                </div>

            </div>

        </div>

    </div>

</div>

@endsection