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

            {{-- Card --}}
            <div class="card">
                <div class="card-body p-4">

                    {{-- Date --}}
                    <div class="row mb-4">
                        <div class="col-4 fw-bold">
                            Date
                        </div>
                        <div class="col-8">
                            September 2, 2026
                        </div>
                    </div>

                    {{-- Time --}}
                    <div class="row mb-4">
                        <div class="col-4 fw-bold">
                            Time
                        </div>
                        <div class="col-8">
                            10:00 AM - 11:00 AM
                        </div>
                    </div>

                    {{-- Teacher --}}
                    <div class="row mb-4">
                        <div class="col-4 fw-bold">
                            Teacher
                        </div>

                        <div class="col-8 d-flex align-items-center">
                            <img src="{{ asset('images/teacher.jpg') }}"
                                 alt="Teacher"
                                 width="60"
                                 height="60"
                                 class="rounded-circle me-3"
                                 style="object-fit: cover;">

                            <span>John</span>
                        </div>
                    </div>

                    {{-- Material --}}
                    <div class="row mb-4">
                        <div class="col-4 fw-bold">
                            Material
                        </div>
                        <div class="col-8">
                            Daily Conversation
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ url()->previous() }}"
                           class="btn btn-outline-secondary">
                            Back
                        </a>

                        <button type="button"
                                class="btn btn-primary">
                            Confirm Booking
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>

@endsection