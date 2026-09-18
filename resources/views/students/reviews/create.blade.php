@extends('layouts.app')

@section('title', 'Write a Review')

@section('content')

<div class="container py-4">

    <div class="mb-4">
        <h2 class="fw-bold mb-1">
            Write a Review
        </h2>

        <p class="text-secondary mb-0">
            Share your feedback about this lesson.
        </p>
    </div>


    {{-- Lesson Information --}}
    <div class="card mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">
                Lesson Information
            </h5>
        </div>

        <div class="card-body p-4">

            <div class="row border-bottom py-3">
                <div class="col-md-3 fw-bold">
                    Teacher
                </div>

                <div class="col-md-9">
                    Teacher Name
                </div>
            </div>


            <div class="row border-bottom py-3">
                <div class="col-md-3 fw-bold">
                    Date
                </div>

                <div class="col-md-9">
                    September 18, 2026
                </div>
            </div>


            <div class="row border-bottom py-3">
                <div class="col-md-3 fw-bold">
                    Time
                </div>

                <div class="col-md-9">
                    10:00 - 10:30
                </div>
            </div>


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


    {{-- Review --}}
    <div class="card">

        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">
                Your Review
            </h5>
        </div>

        <div class="card-body p-4">

            <form>

                {{-- Rating --}}
                <div class="mb-4">

                    <label class="form-label fw-bold">
                        Rating
                    </label>

                    <select
                        name="rating"
                        class="form-select"
                    >
                        <option value="">
                            Select rating
                        </option>

                        <option value="5">
                            ★★★★★
                        </option>

                        <option value="4">
                            ★★★★☆
                        </option>

                        <option value="3">
                            ★★★☆☆
                        </option>

                        <option value="2">
                            ★★☆☆☆
                        </option>

                        <option value="1">
                            ★☆☆☆☆
                        </option>
                    </select>

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


                <div class="text-end">

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

@endsection