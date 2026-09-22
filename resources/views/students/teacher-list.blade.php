@extends('layouts.app')

@section('title', 'Teachers')

@section('content')

<div class="container">

    {{-- ===============================
         Title
    ================================ --}}
    <h2 class="fw-bold mb-4">
        Teachers
    </h2>


    {{-- ===============================
         Favorite Teachers
    ================================ --}}
    <h5 class="fw-bold mb-3">
        Favorite Teachers
    </h5>


    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3 mb-5">

        @forelse ($favoriteTeachers as $teacher)

            <div class="col">

                <div class="card h-100">


                    {{-- ===============================
                         Teacher Image
                    ================================ --}}
                    @if (
                        $teacher->user
                        &&
                        $teacher->user->profile_image
                    )

                        <img
                            src="{{ $teacher->user->profile_image }}"
                            alt="{{ $teacher->user->first_name }}"
                            class="card-img-top"
                            style="
                                height: 120px;
                                object-fit: cover;
                            "
                        >

                    @else

                        <div
                            class="
                                bg-light
                                d-flex
                                justify-content-center
                                align-items-center
                                text-secondary
                            "
                            style="height: 120px;"
                        >
                            No Image
                        </div>

                    @endif


                    <div class="card-body p-2 d-flex flex-column">


                        {{-- ===============================
                             Name + Favorite
                        ================================ --}}
                        <div
                            class="
                                d-flex
                                justify-content-between
                                align-items-start
                                mb-1
                            "
                        >

                            {{-- Name --}}
                            <h6 class="fw-bold mb-1 small">

                                {{
                                    $teacher->user?->first_name
                                    ?? 'Teacher'
                                }}

                            </h6>


                            {{-- Unlike --}}
                            <form
                                method="POST"
                                action="{{ route(
                                    'students.teachers.unlike',
                                    $teacher
                                ) }}"
                                class="ms-2"
                            >

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="
                                        btn
                                        p-0
                                        border-0
                                        bg-transparent
                                    "
                                    aria-label="Remove from favorites"
                                >
                                    <i
                                        class="
                                            fa-solid
                                            fa-heart
                                            text-danger
                                            small
                                        "
                                    ></i>
                                </button>

                            </form>

                        </div>


                        {{-- ===============================
                             Point
                        ================================ --}}
                        <span
                            class="
                                badge
                                text-dark
                                align-self-start
                                mb-1
                            "
                            style="
                                background-color: #f0c94d;
                                font-family: Arial, sans-serif;
                                font-size: 11px;
                            "
                        >
                            {{
                                number_format(
                                    $teacher->point_consumed
                                    ?? 0
                                )
                            }}
                            pt
                        </span>


                        {{-- ===============================
                             Nationality
                        ================================ --}}
                      <p
                            class="text-secondary mb-2"
                            style="font-size: 12px;"
                        >
                            @if ($teacher->user?->nationality === 'Philippines')

                            Philippines
                            <span class="fi fi-ph ms-1"></span>

                        @elseif ($teacher->user?->nationality === 'Japanese')

                            Japan
                            <span class="fi fi-jp ms-1"></span>

                        @else

                            {{ $teacher->user?->nationality ?? '-' }}

                        @endif

                        </p>

                        {{-- ===============================
                             View Profile
                        ================================ --}}
                        <a
                            href="{{ route(
                                'teachers.show',
                                $teacher->id
                            ) }}"
                            class="
                                btn
                                btn-outline-primary
                                btn-sm
                                w-100
                                mt-auto
                            "
                            style="font-size: 12px;"
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


    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">

        @foreach ($teachers as $teacher)

            @php

                $isFavorite =
                    $favoriteTeacherIds
                        ->contains(
                            $teacher->id
                        );

            @endphp


            <div class="col">

                <div class="card h-100">


                    {{-- ===============================
                         Teacher Image
                    ================================ --}}
                    @if (
                        $teacher->user
                        &&
                        $teacher->user->profile_image
                    )

                        <img
                            src="{{ $teacher->user->profile_image }}"
                            alt="{{ $teacher->user->first_name }}"
                            class="card-img-top"
                            style="
                                height: 120px;
                                object-fit: cover;
                            "
                        >

                    @else

                        <div
                            class="
                                bg-light
                                d-flex
                                justify-content-center
                                align-items-center
                                text-secondary
                            "
                            style="height: 120px;"
                        >
                            No Image
                        </div>

                    @endif


                    <div class="card-body p-2 d-flex flex-column">


                        {{-- ===============================
                             Name + Favorite
                        ================================ --}}
                        <div
                            class="
                                d-flex
                                justify-content-between
                                align-items-start
                                mb-1
                            "
                        >

                            {{-- Name --}}
                            <h6 class="fw-bold mb-1 small">

                                {{
                                    $teacher->user?->first_name
                                    ?? 'Teacher'
                                }}

                            </h6>


                            {{-- ===============================
                                 Favorite
                            ================================ --}}
                            @if ($isFavorite)

                                {{-- Unlike --}}
                                <form
                                    method="POST"
                                    action="{{ route(
                                        'students.teachers.unlike',
                                        $teacher
                                    ) }}"
                                    class="ms-2"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="
                                            btn
                                            p-0
                                            border-0
                                            bg-transparent
                                        "
                                        aria-label="Remove from favorites"
                                    >
                                        <i
                                            class="
                                                fa-solid
                                                fa-heart
                                                text-danger
                                                small
                                            "
                                        ></i>
                                    </button>

                                </form>


                            @else

                                {{-- Like --}}
                                <form
                                    method="POST"
                                    action="{{ route(
                                        'students.teacher.like',
                                        $teacher
                                    ) }}"
                                    class="ms-2"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="
                                            btn
                                            p-0
                                            border-0
                                            bg-transparent
                                        "
                                        aria-label="Add to favorites"
                                    >
                                        <i
                                            class="
                                                fa-regular
                                                fa-heart
                                                text-secondary
                                                small
                                            "
                                        ></i>
                                    </button>

                                </form>

                            @endif

                        </div>


                        {{-- ===============================
                             Point
                        ================================ --}}
                        <span
                            class="
                                badge
                                text-dark
                                align-self-start
                                mb-1
                            "
                            style="
                                background-color: #f0c94d;
                                font-family: Arial, sans-serif;
                                font-size: 11px;
                            "
                        >
                            {{
                                number_format(
                                    $teacher->point_consumed
                                    ?? 0
                                )
                            }}
                            pt
                        </span>

                        {{-- ===============================
                            Rating
                        =============================== --}}
                        <div
                            class="
                                d-flex
                                align-items-center
                                gap-1
                                mb-1
                            "
                            style="font-size: 12px;"
                        >
                            <i class="fa-solid fa-star text-warning"></i>

                            <span class="fw-semibold">
                                {{ number_format($teacher->reviews_avg_rating ?? 0, 1) }}
                            </span>

                            <span class="text-secondary">
                                ({{ $teacher->reviews_count ?? 0 }})
                            </span>
                        </div>


                        {{-- ===============================
                             Nationality
                        ================================ --}}
                       <p
                            class="text-secondary mb-2"
                            style="font-size: 12px;"
                        >
                             @if ($teacher->user?->nationality === 'Philippines')

                                    Philippines
                                    <span class="fi fi-ph ms-1"></span>

                                @elseif ($teacher->user?->nationality === 'Japanese')

                                    Japan
                                    <span class="fi fi-jp ms-1"></span>

                                @else

                                    {{ $teacher->user?->nationality ?? '-' }}

                                @endif

                        </p>


                        {{-- ===============================
                             View Profile
                        ================================ --}}
                        <a
                            href="{{ route(
                                'teachers.show',
                                $teacher->id
                            ) }}"
                            class="
                                btn
                                btn-outline-primary
                                btn-sm
                                w-100
                                mt-auto
                            "
                            style="font-size: 12px;"
                        >
                            View Profile
                        </a>

                    </div>

                </div>

            </div>

        @endforeach

    </div>


    {{-- ===============================
         Pagination
    ================================ --}}
    <div class="mt-4">

        {{ $teachers->links() }}

    </div>

</div>

@endsection