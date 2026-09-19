@extends('layouts.app')

@section('title', 'Write a Review')

@section('content')

@php
    $startAt = \Carbon\Carbon::parse(
        $reservation->start_at
    );

    $endAt = \Carbon\Carbon::parse(
        $reservation->end_at
    );
@endphp


<div class="container py-4">


    {{-- ===============================
         Title
    ================================ --}}
    <div class="mb-4">

        <h2 class="fw-bold mb-1">
            Write a Review
        </h2>

        <p class="text-secondary mb-0">
            Share your feedback about this lesson.
        </p>

    </div>



    {{-- ===============================
         Success Message
    ================================ --}}
    @if (session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif



    {{-- ===============================
         Error Message
    ================================ --}}
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



    {{-- ===============================
         Lesson Information
    ================================ --}}
    <div class="card mb-4">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                Lesson Information
            </h5>

        </div>


        <div class="card-body p-4">


            {{-- ===============================
                 Teacher
            ================================ --}}
            <div class="row border-bottom py-3">

                <div class="col-md-3 fw-bold">
                    Teacher
                </div>


                <div class="col-md-9">

                    <div class="d-flex align-items-center">


                        {{-- Teacher Image --}}
                        @if (
                            $reservation
                                ->teacher
                                ?->user
                                ?->profile_image
                        )

                            <img
                                src="{{
                                    $reservation
                                        ->teacher
                                        ->user
                                        ->profile_image
                                }}"
                                alt="Teacher"
                                width="48"
                                height="48"
                                class="rounded-circle me-3"
                                style="object-fit: cover;"
                            >

                        @else

                            <div
                                class="
                                    rounded-circle
                                    bg-secondary-subtle
                                    d-flex
                                    align-items-center
                                    justify-content-center
                                    me-3
                                "
                                style="
                                    width: 48px;
                                    height: 48px;
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-user
                                        text-secondary
                                    "
                                ></i>

                            </div>

                        @endif


                        {{-- Teacher Name --}}
                        <div class="fw-semibold">

                            {{
                                $reservation
                                    ->teacher
                                    ?->user
                                    ?->first_name
                                ?? ''
                            }}

                            {{
                                $reservation
                                    ->teacher
                                    ?->user
                                    ?->last_name
                                ?? ''
                            }}

                        </div>

                    </div>

                </div>

            </div>



            {{-- ===============================
                 Date
            ================================ --}}
            <div class="row border-bottom py-3">

                <div class="col-md-3 fw-bold">
                    Date
                </div>

                <div class="col-md-9">

                    {{
                        $startAt->format(
                            'F j, Y'
                        )
                    }}

                </div>

            </div>



            {{-- ===============================
                 Time
            ================================ --}}
            <div class="row border-bottom py-3">

                <div class="col-md-3 fw-bold">
                    Time
                </div>

                <div class="col-md-9">

                    {{ $startAt->format('H:i') }}
                    -
                    {{ $endAt->format('H:i') }}

                </div>

            </div>



            {{-- ===============================
                 Material
            ================================ --}}
            <div class="row py-3">

                <div class="col-md-3 fw-bold">
                    Material
                </div>

                <div class="col-md-9">

                    <span
                        class="
                            badge
                            bg-secondary-subtle
                            text-dark
                            border
                        "
                    >

                        {{
                            $reservation
                                ->material
                                ?->name
                            ?? '-'
                        }}

                    </span>

                </div>

            </div>


        </div>

    </div>



    {{-- ===============================
         Review
    ================================ --}}
    <div class="card">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                Your Review
            </h5>

        </div>


        <div class="card-body p-4">


            {{-- ===============================
                 Review Form
            ================================ --}}
            <form
                action="{{ route(
                    'students.reviews.store',
                    $reservation
                ) }}"
                method="POST"
            >

                @csrf



                {{-- ===============================
                     Rating
                ================================ --}}
                <div class="mb-4">

                    <label class="form-label fw-bold">
                        Rating
                    </label>


                    <div
                        class="
                            d-flex
                            align-items-center
                            gap-3
                            flex-wrap
                        "
                    >


                        {{-- Stars --}}
                        <div
                            id="starRating"
                            class="
                                d-flex
                                gap-2
                                align-items-center
                            "
                            style="
                                font-size: 1.45rem;
                                cursor: pointer;
                            "
                        >

                            @for ($i = 1; $i <= 5; $i++)

                                <i
                                    class="
                                        {{ old('rating', 0) >= $i
                                            ? 'fa-solid'
                                            : 'fa-regular'
                                        }}
                                        fa-star
                                        rating-star
                                        text-warning
                                    "
                                    data-value="{{ $i }}"
                                ></i>

                            @endfor

                        </div>



                        {{-- Face / Text --}}
                        <div
                            class="
                                d-flex
                                align-items-center
                                gap-2
                            "
                        >

                            <span
                                id="ratingFace"
                                style="font-size: 1.4rem;"
                            >
                                😐
                            </span>

                            <span
                                id="ratingText"
                                class="
                                    text-secondary
                                    small
                                "
                            >
                                Select a rating
                            </span>

                        </div>

                    </div>



                    {{-- Hidden Rating --}}
                    <input
                        type="hidden"
                        name="rating"
                        id="ratingInput"
                        value="{{ old('rating') }}"
                    >


                    @error('rating')

                        <div class="text-danger small mt-2">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                {{-- ===============================
                     Comment
                ================================ --}}
                <div class="mb-4">

                    <label
                        for="comment"
                        class="form-label fw-bold"
                    >
                        Comment
                    </label>


                    <textarea
                        id="comment"
                        name="comment"
                        class="form-control"
                        rows="5"
                        maxlength="1000"
                        placeholder="Write your feedback..."
                    >{{ old('comment') }}</textarea>


                    <div
                        class="
                            form-text
                            text-end
                        "
                    >
                        Optional
                    </div>


                    @error('comment')

                        <div class="text-danger small mt-2">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                {{-- ===============================
                     Buttons
                ================================ --}}
                <div
                    class="
                        d-flex
                        justify-content-between
                    "
                >

                    <a
                        href="{{ route(
                            'students.history.index'
                        ) }}"
                        class="btn btn-outline-secondary"
                    >
                        Back
                    </a>


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Submit Review
                    </button>

                </div>


            </form>


        </div>

    </div>


</div>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const stars =
            document.querySelectorAll(
                '.rating-star'
            );

        const ratingInput =
            document.getElementById(
                'ratingInput'
            );

        const ratingFace =
            document.getElementById(
                'ratingFace'
            );

        const ratingText =
            document.getElementById(
                'ratingText'
            );


        const ratingData = {

            1: {
                face: '😞',
                text: 'Poor'
            },

            2: {
                face: '🙁',
                text: 'Fair'
            },

            3: {
                face: '🙂',
                text: 'Good'
            },

            4: {
                face: '😊',
                text: 'Very Good'
            },

            5: {
                face: '😄',
                text: 'Excellent'
            }

        };


        /*
        |--------------------------------------------------------------------------
        | Rating Display
        |--------------------------------------------------------------------------
        */
        function updateRating(
            rating
        ) {

            stars.forEach(
                function (item) {

                    const value =
                        Number(
                            item.dataset.value
                        );


                    if (value <= rating) {

                        item.classList.remove(
                            'fa-regular'
                        );

                        item.classList.add(
                            'fa-solid'
                        );

                    } else {

                        item.classList.remove(
                            'fa-solid'
                        );

                        item.classList.add(
                            'fa-regular'
                        );

                    }

                }
            );


            if (
                ratingData[rating]
            ) {

                ratingFace.textContent =
                    ratingData[rating].face;

                ratingText.textContent =
                    ratingData[rating].text;

            } else {

                ratingFace.textContent =
                    '😐';

                ratingText.textContent =
                    'Select a rating';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Star Click
        |--------------------------------------------------------------------------
        */
        stars.forEach(
            function (star) {

                star.addEventListener(
                    'click',
                    function () {

                        const rating =
                            Number(
                                this.dataset.value
                            );


                        ratingInput.value =
                            rating;


                        updateRating(
                            rating
                        );

                    }
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Keep Old Rating
        |--------------------------------------------------------------------------
        */
        const oldRating =
            Number(
                ratingInput.value
            );


        if (oldRating) {

            updateRating(
                oldRating
            );

        }

    }
);

</script>

@endsection