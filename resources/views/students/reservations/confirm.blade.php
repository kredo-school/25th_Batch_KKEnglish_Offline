@extends('layouts.app')

@section('title', 'Booking Confirmation')

@section('content')

<div class="container py-4">

    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">

            {{-- Title --}}
            <h2 class="fw-bold mb-2">
                Booking Confirmation
            </h2>

            <p class="text-secondary mb-4">
                Please confirm your lesson details.
            </p>


            {{-- Validation Errors --}}
            @if ($errors->any())

                <div class="alert alert-danger">

                    <ul class="mb-0">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- Card --}}
            <div class="card">

                <div class="card-body p-4">


                    {{-- Date --}}
                    <div class="row mb-4">

                        <div class="col-4 fw-bold">
                            Date
                        </div>

                        <div class="col-8">

                            {{ \Carbon\Carbon::parse(
                                $validated['start_at']
                            )->format('F d, Y') }}

                        </div>

                    </div>


                    {{-- Time --}}
                    <div class="row mb-4">

                        <div class="col-4 fw-bold">
                            Time
                        </div>

                        <div class="col-8">

                            {{ \Carbon\Carbon::parse(
                                $validated['start_at']
                            )->format('h:i A') }}

                            -

                            {{ \Carbon\Carbon::parse(
                                $validated['end_at']
                            )->format('h:i A') }}

                        </div>

                    </div>


                    {{-- Teacher --}}
                    <div class="row mb-4">

                        <div class="col-4 fw-bold">
                            Teacher
                        </div>


                        <div class="col-8 d-flex align-items-center">


                            {{-- Teacher Image --}}
                            @if (
                                $teacher->user
                                &&
                                $teacher->user->profile_image
                            )

                                <img
                                    src="{{ $teacher->user->profile_image }}"
                                    alt="{{ $teacher->user->first_name }}"
                                    width="60"
                                    height="60"
                                    class="rounded-circle me-3"
                                    style="object-fit: cover;"
                                >

                            @else

                                <div
                                    class="
                                        rounded-circle
                                        bg-light
                                        d-flex
                                        justify-content-center
                                        align-items-center
                                        text-secondary
                                        me-3
                                    "
                                    style="
                                        width: 60px;
                                        height: 60px;
                                    "
                                >
                                    No Image
                                </div>

                            @endif


                            {{-- Teacher Name --}}
                            <span>

                                {{ $teacher->user?->first_name ?? 'Teacher' }}

                                {{ $teacher->user?->last_name ?? '' }}

                            </span>

                        </div>

                    </div>


                    {{-- Material --}}
                    <div class="row mb-4">

                        <div class="col-4 fw-bold">
                            Material
                        </div>

                        <div class="col-8">

                            {{ $material->name }}

                        </div>

                    </div>


                    <hr>


                    {{-- ===============================
                         Booking Form
                    ================================ --}}
                    <form
                        action="{{ route('students.reservations.store') }}"
                        method="POST"
                    >

                        @csrf


                        {{-- Teacher --}}
                        <input
                            type="hidden"
                            name="teacher_id"
                            value="{{ $validated['teacher_id'] }}"
                        >


                        {{-- Material --}}
                        <input
                            type="hidden"
                            name="material_id"
                            value="{{ $validated['material_id'] }}"
                        >


                        {{-- Schedule --}}
                        <input
                            type="hidden"
                            name="schedule_id"
                            value="{{ $validated['schedule_id'] }}"
                        >


                        {{-- Start --}}
                        <input
                            type="hidden"
                            name="start_at"
                            value="{{ $validated['start_at'] }}"
                        >


                        {{-- End --}}
                        <input
                            type="hidden"
                            name="end_at"
                            value="{{ $validated['end_at'] }}"
                        >


                        <div class="d-flex justify-content-between mt-4">


                            {{-- Back --}}
                            <a
                                href="{{ route('students.reservations.index') }}"
                                class="btn btn-outline-secondary"
                            >
                                Back
                            </a>


                            {{-- Confirm --}}
                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Confirm Booking
                            </button>


                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection