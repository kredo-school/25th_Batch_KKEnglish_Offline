@extends('layouts.app')

@section('title', 'Teacher Profile')

@section('content')

<div class="container-fluid py-4">

    {{-- タイトル --}}
    <div class="bg-light px-3 py-2 mb-3">
        <h4 class="mb-0 fw-bold">Teacher Profile</h4>
    </div>

    {{-- プロフィール --}}
    <div class="card">
        <div class="card-body p-4">

            {{-- 写真・名前 --}}
            <div class="d-flex align-items-center mb-4">

                <img src="{{ $teacher->user->profile_image }}"
                     alt="{{ $teacher->user->first_name }}"
                     class="rounded-circle me-3"
                     width="80"
                     height="80"
                     style="object-fit: cover;">

                <div>
                    <h4 class="fw-bold mb-1">
                        {{ $teacher->user->first_name }}
                        {{ $teacher->user->last_name }}
                    </h4>

                    <p class="text-secondary mb-0">
                        English Teacher
                    </p>
                </div>

            </div>


            {{-- 先生情報 --}}
            <div class="mb-3">
                <strong>Nationality</strong>
                <p class="mb-0">
                    {{ $teacher->user->nationality }}
                </p>
            </div>

            <div class="mb-3">
                <strong>Teaching Experience</strong>
                <p class="mb-0">
                    {{ $teacher->career }}
                </p>
            </div>

            <div class="mb-3">
                <strong>Specialty</strong>
                <p class="mb-0">
                    {{ $teacher->specialty }}
                </p>
            </div>

            <div class="mb-3">
                <strong>Certification</strong>
                <p class="mb-0">
                    {{ $teacher->certification }}
                </p>
            </div>

            <div class="mb-3">
                <strong>Graduation School</strong>
                <p class="mb-0">
                    {{ $teacher->graduation_school }}
                </p>
            </div>

            <hr>

            {{-- About Me --}}
            <div>
                <strong>About Me</strong>

                <p class="mt-2 mb-0">
                    {{ $teacher->about_me }}
                </p>
            </div>

        </div>
    </div>

</div>

@endsection
