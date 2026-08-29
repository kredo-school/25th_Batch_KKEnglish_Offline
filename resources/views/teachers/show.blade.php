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

                <img src="{{ asset('images/teacher.jpg') }}"
                     alt="Teacher"
                     class="rounded-circle me-3"
                     width="80"
                     height="80"
                     style="object-fit: cover;">

                <div>
                    <h4 class="fw-bold mb-1">Teacher Name</h4>
                    <p class="text-secondary mb-0">English Teacher</p>
                </div>

            </div>

            {{-- 先生情報 --}}
            <div class="mb-3">
                <strong>Nationality</strong>
                <p class="mb-0">Philippines</p>
            </div>

            <div class="mb-3">
                <strong>Teaching Experience</strong>
                <p class="mb-0">3 years</p>
            </div>

            <div class="mb-3">
                <strong>Specialty</strong>
                <p class="mb-0">Daily Conversation</p>
            </div>

            <hr>

            {{-- About Me --}}
            <div>

                <div class="d-flex align-items-center gap-3">
                    <strong>About Me</strong>

                    <button type="button"
                            class="btn btn-outline-secondary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#aboutMeModal">
                        Edit
                    </button>
                </div>

                 {{-- 実装後は {{ $teacher->about_me }} を表示 --}}
                <p class="mt-2 mb-0">
                    Hello! I enjoy teaching English and helping students
                    improve their speaking skills.
                    Let's enjoy learning English together!
                </p>

            </div>

        </div>
    </div>

</div>


{{-- About Me Edit Modal --}}
<div class="modal fade"
     id="aboutMeModal"
     tabindex="-1"
     aria-labelledby="aboutMeModalLabel"
     aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            {{-- Modal Header --}}
            <div class="modal-header">

                <h5 class="modal-title"
                    id="aboutMeModalLabel">
                    Edit About Me
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>


            {{-- Modal Body --}}
            <div class="modal-body">
                 {{-- 実装後は {{ old('about_me', $teacher->about_me) }} を表示 --}}
                <textarea class="form-control"
                          rows="5"
                          name="about_me">Hello! I enjoy teaching English and helping students improve their speaking skills. Let's enjoy learning English together!</textarea>

            </div>


            {{-- Modal Footer --}}
            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="button"
                        class="btn btn-primary">
                    Save
                </button>

            </div>

        </div>

    </div>

</div>

@endsection