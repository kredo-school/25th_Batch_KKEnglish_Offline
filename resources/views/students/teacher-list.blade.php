@extends('layouts.app')

@section('title', 'Teachers')

@section('content')

<div class="container">

    <h2 class="fw-bold mb-4">
        Teachers
    </h2>


    @php

        $studentId = auth()->user()->student->id;

        $favoriteTeachers = $teachers->filter(function ($teacher) use ($studentId) {

            return $teacher
                ->teacherLikes
                ->contains(
                    'student_id',
                    $studentId
                );

        });

    @endphp


    {{-- ===============================
         Favorite Teachers
    ================================ --}}
    <h5 class="fw-bold mb-3">
        Favorite Teachers
    </h5>


    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-3 mb-5">

        @forelse ($favoriteTeachers as $teacher)

            <div class="col">

                <div class="card h-100">

                    {{-- Teacher Image --}}
                    <img
                        src="{{ $teacher->user->profile_image }}"
                        alt="{{ $teacher->user->first_name }}"
                        class="card-img-top"
                        style="
                            height: 180px;
                            object-fit: cover;
                        "
                    >


                    <div class="card-body d-flex flex-column">


                        {{-- ===============================
                             Name + Point + Favorite
                        ================================ --}}
                        <div class="d-flex justify-content-between align-items-start mb-2">

                            <div>

                                {{-- Name --}}
                                <h5
                                    class="fw-bold mb-1"
                                    style="min-height: 48px;"
                                >
                                    {{ $teacher->user->first_name }}
                                    {{ $teacher->user->last_name }}
                                </h5>


                                {{-- Lesson Point --}}
                                <span
                                    class="badge text-dark px-2 py-2"
                                    style="
                                        background-color: #f0c94d;
                                        font-family: Arial, sans-serif;
                                    "
                                >
                                    {{ number_format($teacher->point_consumed ?? 0) }} pt
                                </span>

                            </div>


                            {{-- ===============================
                                 Unlike
                            ================================ --}}
                            <form
                                method="POST"
                                action="{{ route('students.teachers.unlike', $teacher) }}"
                                class="ms-2"
                            >

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn p-0 border-0 bg-transparent"
                                    aria-label="Remove from favorites"
                                >
                                    <i
                                        class="
                                            fa-solid
                                            fa-heart
                                            text-danger
                                        "
                                    ></i>
                                </button>

                            </form>

                        </div>


                        {{-- ===============================
                             Nationality
                        ================================ --}}
                        <p class="mb-1 small">

                            <span class="text-secondary">
                                Nationality:
                            </span>

                            {{
                                $teacher
                                    ->user
                                    ->nationality
                                ?? '-'
                            }}

                        </p>


                        {{-- ===============================
                             Specialty
                        ================================ --}}
                        <p
                            class="mb-3 small"
                            style="min-height: 60px;"
                        >

                            <span class="text-secondary">
                                Specialty:
                            </span>

                            {{
                                $teacher->specialty
                                ?? '-'
                            }}

                        </p>


                        {{-- ===============================
                             View Profile
                        ================================ --}}
                        <a
                            href="{{ route('teachers.show', $teacher->id) }}"
                            class="
                                btn
                                btn-outline-primary
                                btn-sm
                                w-100
                                mt-auto
                            "
                        >
                            View Profile
                        </a>

                    </div>

                </div>

            </div>


        @empty

            <div class="col-12">

                <p class="text-secondary mb-0">
                    No favorite teachers yet.
                </p>

            </div>

        @endforelse

    </div>



    {{-- ===============================
         All Teachers
    ================================ --}}
    <h5 class="fw-bold mb-3">
        All Teachers
    </h5>


    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-3">

        @foreach ($teachers as $teacher)

            @php

                $teacherLike = $teacher
                    ->teacherLikes
                    ->firstWhere(
                        'student_id',
                        $studentId
                    );

            @endphp


            <div class="col">

                <div class="card h-100">

                    {{-- Teacher Image --}}
                    <img
                        src="{{ $teacher->user->profile_image }}"
                        alt="{{ $teacher->user->first_name }}"
                        class="card-img-top"
                        style="
                            height: 180px;
                            object-fit: cover;
                        "
                    >


                    <div class="card-body d-flex flex-column">


                        {{-- ===============================
                             Name + Point + Favorite
                        ================================ --}}
                        <div class="d-flex justify-content-between align-items-start mb-2">

                            <div>

                                {{-- Name --}}
                                <h5
                                    class="fw-bold mb-1"
                                    style="min-height: 48px;"
                                >
                                    {{ $teacher->user->first_name }}
                                    {{ $teacher->user->last_name }}
                                </h5>


                                {{-- Lesson Point --}}
                                <span
                                    class="badge text-dark px-2 py-2"
                                    style="
                                        background-color: #f0c94d;
                                        font-family: Arial, sans-serif;
                                    "
                                >
                                    {{ number_format($teacher->point_consumed ?? 0) }} pt
                                </span>

                            </div>


                            {{-- ===============================
                                 Favorite
                            ================================ --}}
                            @if ($teacherLike)

                                {{-- Unlike --}}
                                <form
                                    method="POST"
                                    action="{{ route('students.teachers.unlike', $teacher) }}"
                                    class="ms-2"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn p-0 border-0 bg-transparent"
                                        aria-label="Remove from favorites"
                                    >
                                        <i
                                            class="
                                                fa-solid
                                                fa-heart
                                                text-danger
                                            "
                                        ></i>
                                    </button>

                                </form>

                            @else

                                {{-- Like --}}
                                <form
                                    method="POST"
                                    action="{{ route('students.teacher.like', $teacher) }}"
                                    class="ms-2"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="btn p-0 border-0 bg-transparent"
                                        aria-label="Add to favorites"
                                    >
                                        <i
                                            class="
                                                fa-regular
                                                fa-heart
                                                text-secondary
                                            "
                                        ></i>
                                    </button>

                                </form>

                            @endif

                        </div>


                        {{-- ===============================
                             Nationality
                        ================================ --}}
                        <p class="mb-1 small">

                            <span class="text-secondary">
                                Nationality:
                            </span>

                            {{
                                $teacher
                                    ->user
                                    ->nationality
                                ?? '-'
                            }}

                        </p>


                        {{-- ===============================
                             Specialty
                        ================================ --}}
                        <p
                            class="mb-3 small"
                            style="min-height: 60px;"
                        >

                            <span class="text-secondary">
                                Specialty:
                            </span>

                            {{
                                $teacher->specialty
                                ?? '-'
                            }}

                        </p>


                        {{-- ===============================
                             View Profile
                        ================================ --}}
                        <a
                            href="{{ route('teachers.show', $teacher->id) }}"
                            class="
                                btn
                                btn-outline-primary
                                btn-sm
                                w-100
                                mt-auto
                            "
                        >
                            View Profile
                        </a>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

</div>

@endsection