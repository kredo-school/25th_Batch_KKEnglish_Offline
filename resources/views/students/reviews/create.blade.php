@extends('layouts.app')

@section('title', 'Write a Review')

@section('content')

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
         Lesson Information
    ================================ --}}
    <div class="card mb-4">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                Lesson Information
            </h5>

        </div>


        <div class="card-body p-4">

           {{-- Teacher --}}
            <div class="row border-bottom py-3">

                <div class="col-md-3 fw-bold">
                    Teacher
                </div>

                <div class="col-md-9">

                    <div class="d-flex align-items-center">

                        <img
                            src="https://via.placeholder.com/48"
                            alt="Teacher"
                            width="48"
                            height="48"
                            class="rounded-circle me-3"
                            style="object-fit: cover;"
                        >

                        <div class="fw-semibold">
                            Maria Santos
                        </div>

                    </div>

                </div>

            </div>

            {{-- Date --}}
            <div class="row border-bottom py-3">

                <div class="col-md-3 fw-bold">
                    Date
                </div>

                <div class="col-md-9">
                    September 18, 2026
                </div>

            </div>


            {{-- Time --}}
            <div class="row border-bottom py-3">

                <div class="col-md-3 fw-bold">
                    Time
                </div>

                <div class="col-md-9">
                    10:00 - 10:30
                </div>

            </div>


            {{-- Material --}}
            <div class="row py-3">

                <div class="col-md-3 fw-bold">
                    Material
                </div>

                <div class="col-md-9">
                    Grammar Beginner
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

            {{-- 今は表示確認用 --}}
            <form>

           {{-- Rating --}}
            <div class="mb-4">

                <label class="form-label fw-bold">
                    Rating
                </label>

                <div class="d-flex align-items-center gap-3">

                    {{-- Stars --}}
                    <div
                        id="starRating"
                        class="d-flex gap-2 align-items-center"
                        style="
                            font-size: 1.45rem;
                            cursor: pointer;
                        "
                    >

                        @for ($i = 1; $i <= 5; $i++)

                            <i
                                class="
                                    fa-regular
                                    fa-star
                                    rating-star
                                    text-warning
                                "
                                data-value="{{ $i }}"
                            ></i>

                        @endfor

                    </div>


                    {{-- Face / Text --}}
                    <div class="d-flex align-items-center gap-2">

                        <span
                            id="ratingFace"
                            style="font-size: 1.4rem;"
                        >
                            😐
                        </span>

                        <span
                            id="ratingText"
                            class="text-secondary small"
                        >
                            Select a rating
                        </span>

                    </div>

                </div>


                <input
                    type="hidden"
                    name="rating"
                    id="ratingInput"
                    value=""
                >

            </div>


                {{-- Comment --}}
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
                        placeholder="Write your feedback..."
                    ></textarea>

                </div>


                {{-- Buttons --}}
                <div class="d-flex justify-content-between">

                    <a
                        href="{{ route(
                            'students.history.index'
                        ) }}"
                        class="btn btn-outline-secondary"
                    >
                        Back
                    </a>


                    <button
                        type="button"
                        class="btn btn-primary"
                    >
                        Submit Review
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection

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


        stars.forEach(function (star) {

            star.addEventListener(
                'click',
                function () {

                    const rating =
                        Number(
                            this.dataset.value
                        );


                    ratingInput.value =
                        rating;


                    /*
                     * 星の表示
                     */
                    stars.forEach(
                        function (item) {

                            const value =
                                Number(
                                    item.dataset.value
                                );


                            if (
                                value <= rating
                            ) {

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


                    /*
                     * 顔文字と文字を変更
                     */
                    ratingFace.textContent =
                        ratingData[rating].face;

                    ratingText.textContent =
                        ratingData[rating].text;

                }
            );

        });

    }
);

</script>