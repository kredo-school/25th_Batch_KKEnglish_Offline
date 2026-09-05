@extends('layouts.app')

@section('title', 'Teacher Profile')

@section('content')

<div class="container py-4">

    {{-- ===============================
         Title
    ================================ --}}
    <div class="mb-4">

        <h2 class="fw-bold mb-1">
            Teacher Profile
        </h2>

        <p class="text-secondary mb-0">
            Teacher information and introduction
        </p>

    </div>


    {{-- ===============================
         Success Message
    ================================ --}}
    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ===============================
         Profile Card
    ================================ --}}
    <div class="card shadow-sm">

        <div class="card-body p-4">


            {{-- ===============================
                 Profile Header
            ================================ --}}
            <div class="d-flex align-items-center mb-4 pb-4 border-bottom">

                {{-- Profile Image --}}
                @if($teacher->user->profile_image)

                    <img
                        src="{{ asset('storage/' . $teacher->user->profile_image) }}"
                        alt="{{ $teacher->user->first_name }}"
                        width="100"
                        height="100"
                        class="rounded-circle me-4"
                        style="object-fit: cover;"
                    >

                @else

                    <i
                        class="fa-solid fa-circle-user fa-5x me-4 text-secondary"
                    ></i>

                @endif


                {{-- Name --}}
                <div>

                    <h3 class="fw-bold mb-1">
                        {{ $teacher->user->first_name }}
                        {{ $teacher->user->last_name }}
                    </h3>

                    <p class="text-secondary mb-0">
                        English Teacher
                    </p>

                </div>

            </div>


            {{-- ===============================
                 Nationality
            ================================ --}}
            <div class="row border-bottom py-3">

                <div class="col-md-3 fw-bold text-secondary">
                    Nationality
                </div>

                <div class="col-md-9">
                    {{ $teacher->user->nationality ?? '-' }}
                </div>

            </div>


            {{-- ===============================
                 Teaching Background
            ================================ --}}
            <div class="row border-bottom py-3">

                <div class="col-md-3 fw-bold text-secondary">
                    Teaching Background
                </div>

                <div class="col-md-9">
                    {{ $teacher->career ?? '-' }}
                </div>

            </div>


            {{-- ===============================
                 Specialty
            ================================ --}}
            <div class="row border-bottom py-3">

                <div class="col-md-3 fw-bold text-secondary">
                    Specialty
                </div>

                <div class="col-md-9">
                    {{ $teacher->specialty ?? '-' }}
                </div>

            </div>


            {{-- ===============================
                 About Me
            ================================ --}}
            <div class="row py-3">

                <div class="col-md-3 fw-bold text-secondary">
                    About Me
                </div>

                <div class="col-md-9">

                    <div class="d-flex justify-content-between align-items-start">

                        {{-- About Me Text --}}
                        <p class="mb-0">
                            {{ $teacher->about_me ?? '-' }}
                        </p>


                        {{-- Edit Button
                             先生本人だけ表示
                        --}}
                        @if(
                            auth()->user()->role->role_code === 'teacher'
                            && auth()->user()->teacher->id === $teacher->id
                        )

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-secondary ms-3"
                                data-bs-toggle="modal"
                                data-bs-target="#editAboutMeModal"
                            >
                                <i class="fa-solid fa-pen me-1"></i>
                                Edit
                            </button>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ==================================================
     Edit About Me Modal
     先生本人だけ表示
================================================== --}}
@if(
    auth()->user()->role->role_code === 'teacher'
    && auth()->user()->teacher->id === $teacher->id
)

<div
    class="modal fade"
    id="editAboutMeModal"
    tabindex="-1"
    aria-labelledby="editAboutMeModalLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            {{-- ===============================
                 Form
            ================================ --}}
            <form
                action="#"
                method="POST"
            >

                @csrf
                @method('PATCH')


                {{-- ===============================
                     Modal Header
                ================================ --}}
                <div class="modal-header">

                    <h5
                        class="modal-title fw-bold"
                        id="editAboutMeModalLabel"
                    >
                        Edit About Me
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>

                </div>


                {{-- ===============================
                     Modal Body
                ================================ --}}
                <div class="modal-body">

                    <label
                        for="about_me"
                        class="form-label fw-bold"
                    >
                        About Me
                    </label>

                    <textarea
                        id="about_me"
                        name="about_me"
                        class="form-control @error('about_me') is-invalid @enderror"
                        rows="6"
                    >{{ old('about_me', $teacher->about_me) }}</textarea>


                    {{-- Error Message --}}
                    @error('about_me')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror


                    {{-- Character Guide --}}
                    <div class="form-text">
                        Maximum 1000 characters.
                    </div>

                </div>


                {{-- ===============================
                     Modal Footer
                ================================ --}}
                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        disabled
                    >
                        Save
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endif


{{-- ==================================================
     Validation Error時
     Modalを自動で再表示
================================================== --}}
@if($errors->has('about_me'))

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const modalElement =
                document.getElementById('editAboutMeModal');

            if (modalElement) {

                const modal =
                    new bootstrap.Modal(modalElement);

                modal.show();

            }

        });
    </script>

@endif

@endsection