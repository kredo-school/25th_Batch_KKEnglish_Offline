@extends('layouts.app')

@section('title', 'Book a Lesson')

@section('content')

    <div class="container-fluid">

        {{-- ===============================
        Title
        ================================ --}}
        <div class="mb-4">

            <h2 class="fw-bold mb-1">
                Available Teachers
            </h2>

            <p class="text-secondary mb-0">
                Select a material, date and time to find available teachers.
            </p>

        </div>


        {{-- ===============================
        Validation Errors
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
        Message
        ================================ --}}
        <div id="selectionMessage" class="alert alert-light border">
            Please select a material.
        </div>


        {{-- ===============================
        Selected Material
        ================================ --}}
        <div id="selectedConditions" class="mb-4 d-none">

            <div
                class="
                                bg-light
                                border
                                rounded
                                px-3
                                py-2
                            ">

                <span class="text-secondary small me-2">
                    Material
                </span>

                <span id="selectedMaterial" class="fw-semibold">
                </span>

            </div>

        </div>


        {{-- ===============================
        Teacher List
        ================================ --}}
        <div id="teacherList"
            class="
                            row
                            row-cols-1
                            row-cols-md-2
                            row-cols-lg-4
                            g-4
                        ">

            @foreach ($teachers as $teacher)
                <div class="col teacher-card d-none" data-teacher-id="{{ $teacher->id }}"
                    data-materials="{{ $teacher->materials->pluck('material_id')->implode(',') }}" data-schedule-id=""
                    data-start-at="" data-end-at="">

                    <div class="card h-100 shadow-sm">


                        {{-- Teacher Image --}}
                        @if ($teacher->user && $teacher->user->profile_image)
                            <img src="{{ $teacher->user->profile_image }}" alt="{{ $teacher->user->first_name }}"
                                class="card-img-top"
                                style="
                                                                                height: 180px;
                                                                                object-fit: cover;
                                                                            ">
                        @else
                            <div class="
                                                                                bg-light
                                                                                d-flex
                                                                                justify-content-center
                                                                                align-items-center
                                                                                text-secondary
                                                                            "
                                style="height: 180px;">

                                No Image

                            </div>
                        @endif


                        <div
                            class="
                                                            card-body
                                                            d-flex
                                                            flex-column
                                                        ">

                            {{-- Name + Lesson Point --}}
                            <div class="
                                                                d-flex
                                                                justify-content-between
                                                                align-items-start
                                                                mb-2
                                                            "
                                style="min-height: 40px;">

                                <h5 class="
                                                                    fw-bold
                                                                    mb-0
                                                                    pe-2
                                                                "
                                    style="
                                                                    line-height: 1.3;
                                                                ">
                                    {{ $teacher->user?->first_name ?? 'Teacher' }}

                                    {{ $teacher->user?->last_name ?? '' }}
                                </h5>


                                <span
                                    class="
                                                                    badge
                                                                    text-dark
                                                                    px-2
                                                                    py-2
                                                                    flex-shrink-0
                                                                "
                                    style="
                                                                    background-color: #f0c94d;
                                                                    font-family: Arial, sans-serif;
                                                                ">
                                    {{ number_format($teacher->point_consumed ?? 0) }} pt
                                </span>

                            </div>


                            {{-- Nationality --}}
                            <div class="mb-2 small" style="min-height: 24px;">
                                @if ($teacher->user?->nationality === 'Philippines')
                                    Philippines
                                    <span class="fi fi-ph ms-1"></span>
                                @elseif ($teacher->user?->nationality === 'Japanese')
                                    Japan
                                    <span class="fi fi-jp ms-1"></span>
                                @else
                                    {{ $teacher->user?->nationality ?? '-' }}
                                @endif

                            </div>


                            {{-- Rating --}}
                            <div
                                class="
                                    d-flex
                                    align-items-center
                                    gap-1
                                    border
                                    rounded
                                    px-2
                                    py-1
                                    mb-2
                                    align-self-start
                                "
                                style="
                                    font-size: 13px;
                                    min-height: 30px;
                                "
                            >
                                <i class="fa-solid fa-star text-warning"></i>

                                <span class="fw-semibold">
                                    {{ number_format($teacher->reviews_avg_rating ?? 3, 1) }}
                                </span>

                                <span class="text-secondary">
                                    ({{ $teacher->reviews_count ?? 0 }})
                                </span>
                            </div>


                            {{-- Specialty --}}
                            <div class="small mb-3" style="min-height: 48px;">

                                <span class="text-secondary">
                                    Specialty:
                                </span>

                                {{ $teacher->specialty ?? '-' }}

                            </div>


                            {{-- Buttons --}}
                            <div class="mt-auto">

                                <a href="#"
                                    class="
                                                                    btn
                                                                    btn-outline-primary
                                                                    btn-sm
                                                                    w-100
                                                                    mb-2
                                                                    view-schedule-btn
                                                                    disabled
                                                                "
                                    aria-disabled="true">
                                    View Schedule
                                </a>

                                <button type="button"
                                    class="
                                                                    btn
                                                                    btn-secondary
                                                                    btn-sm
                                                                    w-100
                                                                    book-btn
                                                                "
                                    disabled>
                                    Book
                                </button>

                            </div>

                        </div>

                    </div>

                </div>
            @endforeach

        </div>


        {{-- ===============================
        No Teachers
        ================================ --}}
        <div id="noTeachers" class="text-center py-5 d-none">

            <h5 class="fw-bold">
                No teachers available
            </h5>

            <p class="text-secondary">
                Please change your search conditions.
            </p>

        </div>


        {{-- ===============================
        Booking Form
        ================================ --}}
        <form id="bookingForm" action="{{ route('students.reservations.confirm') }}" method="POST" class="d-none">

            @csrf


            <input type="hidden" name="teacher_id" id="bookingTeacherId">


            <input type="hidden" name="material_id" id="bookingMaterialId">


            <input type="hidden" name="schedule_id" id="bookingScheduleId">


            <input type="hidden" name="start_at" id="bookingStartAt">


            <input type="hidden" name="end_at" id="bookingEndAt">

        </form>

    </div>



    <script src="https://cdn.jsdelivr.net/npm/nouislider@15.8.1/dist/nouislider.min.js"></script>
    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                /*
                |--------------------------------------------------------------------------
                | Sidebar Inputs
                |--------------------------------------------------------------------------
                */

                const keywordInput =
                    document.getElementById(
                        'reservationKeyword'
                    );


                const favoriteOnlyInput =
                    document.getElementById(
                        'reservationFavoriteOnly'
                    );


                const materialInput =
                    document.getElementById(
                        'reservationMaterial'
                    );


                const nationalityInput =
                    document.getElementById(
                        'reservationNationality'
                    );


                const minPointsInput =
                    document.getElementById(
                        'reservationMinPoints'
                    );


                const maxPointsInput =
                    document.getElementById(
                        'reservationMaxPoints'
                    );


                const pointRangeLabel =
                    document.getElementById(
                        'pointRangeLabel'
                    );


                const pointRangeSlider =
                    document.getElementById(
                        'pointRangeSlider'
                    );


                const minRatingInput =
                    document.getElementById(
                        'reservationMinRating'
                    );


                const ratingStars =
                    document.querySelectorAll(
                        '.rating-star'
                    );


                const selectedRatingText =
                    document.getElementById(
                        'selectedRatingText'
                    );


                const clearRatingBtn =
                    document.getElementById(
                        'clearRatingBtn'
                    );


                const dateInput =
                    document.getElementById(
                        'reservationDate'
                    );


                const hourInput =
                    document.getElementById(
                        'reservationHour'
                    );


                const minuteInput =
                    document.getElementById(
                        'reservationMinute'
                    );


                /*
                |--------------------------------------------------------------------------
                | Main Elements
                |--------------------------------------------------------------------------
                */

                const teacherCards =
                    document.querySelectorAll(
                        '.teacher-card'
                    );


                const noTeachers =
                    document.getElementById(
                        'noTeachers'
                    );


                const selectionMessage =
                    document.getElementById(
                        'selectionMessage'
                    );


                const selectedConditions =
                    document.getElementById(
                        'selectedConditions'
                    );


                const selectedMaterial =
                    document.getElementById(
                        'selectedMaterial'
                    );


                /*
                |--------------------------------------------------------------------------
                | Booking Form
                |--------------------------------------------------------------------------
                */

                const bookingForm =
                    document.getElementById(
                        'bookingForm'
                    );


                const bookingTeacherId =
                    document.getElementById(
                        'bookingTeacherId'
                    );


                const bookingMaterialId =
                    document.getElementById(
                        'bookingMaterialId'
                    );


                const bookingScheduleId =
                    document.getElementById(
                        'bookingScheduleId'
                    );


                const bookingStartAt =
                    document.getElementById(
                        'bookingStartAt'
                    );


                const bookingEndAt =
                    document.getElementById(
                        'bookingEndAt'
                    );


                /*
                |--------------------------------------------------------------------------
                | API Version
                |--------------------------------------------------------------------------
                */

                let updateVersion =
                    0;


                /*
                |--------------------------------------------------------------------------
                | Point Slider
                |--------------------------------------------------------------------------
                */

                if (
                    pointRangeSlider &&
                    typeof noUiSlider !==
                    'undefined'
                ) {

                    noUiSlider.create(
                        pointRangeSlider, {
                            start: [
                                50,
                                150
                            ],

                            connect: true,

                            step: 10,

                            range: {
                                min: 50,

                                max: 150
                            }
                        }
                    );


                    /*
                     * スライダー表示更新
                     */
                    pointRangeSlider
                        .noUiSlider
                        .on(
                            'update',
                            function(values) {

                                const minValue =
                                    Math.round(
                                        values[0]
                                    );


                                const maxValue =
                                    Math.round(
                                        values[1]
                                    );


                                minPointsInput.value =
                                    minValue;


                                maxPointsInput.value =
                                    maxValue;


                                pointRangeLabel.textContent =
                                    minValue +
                                    ' - ' +
                                    maxValue;
                            }
                        );


                    /*
                     * 操作終了時に検索
                     */
                    pointRangeSlider
                        .noUiSlider
                        .on(
                            'change',
                            function() {

                                updateTeachers();
                            }
                        );
                }


                /*
                |--------------------------------------------------------------------------
                | Rating表示
                |--------------------------------------------------------------------------
                */

                function updateRatingDisplay(
                    rating
                ) {

                    ratingStars.forEach(
                        function(star) {

                            const starRating =
                                Number(
                                    star.dataset.rating
                                );


                            if (
                                rating &&
                                starRating <=
                                Number(rating)
                            ) {

                                star.classList.remove(
                                    'text-secondary'
                                );

                                star.classList.add(
                                    'text-warning'
                                );

                            } else {

                                star.classList.remove(
                                    'text-warning'
                                );

                                star.classList.add(
                                    'text-secondary'
                                );
                            }
                        }
                    );


                    if (rating) {

                        selectedRatingText.textContent =
                            rating +
                            ' stars or more';

                    } else {

                        selectedRatingText.textContent =
                            'Any rating';
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Rating Click
                |--------------------------------------------------------------------------
                */

                ratingStars.forEach(
                    function(star) {

                        star.addEventListener(
                            'click',
                            function() {

                                const rating =
                                    this.dataset.rating;


                                minRatingInput.value =
                                    rating;


                                updateRatingDisplay(
                                    rating
                                );


                                updateTeachers();
                            }
                        );
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Rating Clear
                |--------------------------------------------------------------------------
                */

                if (clearRatingBtn) {

                    clearRatingBtn.addEventListener(
                        'click',
                        function() {

                            minRatingInput.value =
                                '';


                            updateRatingDisplay(
                                ''
                            );


                            updateTeachers();
                        }
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Selected Time
                |--------------------------------------------------------------------------
                */

                function getSelectedTime() {

                    const hour =
                        hourInput ?
                        hourInput.value :
                        '';


                    const minute =
                        minuteInput ?
                        minuteInput.value :
                        '';


                    if (
                        !hour ||
                        !minute
                    ) {

                        return '';
                    }


                    return (
                        String(hour)
                        .padStart(
                            2,
                            '0'
                        ) +
                        ':' +
                        minute
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Teacher Search API
                |--------------------------------------------------------------------------
                */

                async function fetchTeachers(
                    conditions
                ) {

                    const params =
                        new URLSearchParams();


                    Object.entries(
                        conditions
                    ).forEach(
                        function([key, value]) {

                            if (
                                value !== '' &&
                                value !== null &&
                                value !== undefined
                            ) {

                                params.append(
                                    key,
                                    value
                                );
                            }
                        }
                    );


                    const response =
                        await fetch(
                            "{{ route('students.teachers.search') }}" +
                            '?' +
                            params.toString(), {
                                method: 'GET',

                                headers: {
                                    'Accept': 'application/json',
                                },
                            }
                        );


                    if (!response.ok) {

                        throw new Error(
                            'Teacher Search API error: ' +
                            response.status
                        );
                    }


                    return await response.json();
                }


                /*
                |--------------------------------------------------------------------------
                | Booking Slot
                |--------------------------------------------------------------------------
                */

                function setBookingSlot(
                    card,
                    teacher
                ) {

                    card.dataset.scheduleId =
                        teacher.schedule_id ??
                        '';


                    card.dataset.startAt =
                        teacher.start_at ??
                        '';


                    card.dataset.endAt =
                        teacher.end_at ??
                        '';
                }


                function clearBookingSlot(
                    card
                ) {

                    card.dataset.scheduleId =
                        '';


                    card.dataset.startAt =
                        '';


                    card.dataset.endAt =
                        '';
                }


                /*
                |--------------------------------------------------------------------------
                | Book Button
                |--------------------------------------------------------------------------
                */

                function updateBookButton(
                    card,
                    enabled
                ) {

                    const button =
                        card.querySelector(
                            '.book-btn'
                        );


                    if (!button) {

                        return;
                    }


                    button.disabled = !enabled;


                    if (enabled) {

                        button.classList.remove(
                            'btn-secondary'
                        );

                        button.classList.add(
                            'btn-primary'
                        );

                    } else {

                        button.classList.remove(
                            'btn-primary'
                        );

                        button.classList.add(
                            'btn-secondary'
                        );
                    }
                }
                /*
                |--------------------------------------------------------------------------
                | Favorite Heart表示
                |--------------------------------------------------------------------------
                */

                function updateFavoriteButton(
                    card,
                    isFavorite
                ) {

                    const button =
                        card.querySelector(
                            '.favorite-heart-btn'
                        );


                    if (!button) {

                        return;
                    }


                    const icon =
                        button.querySelector(
                            '.favorite-heart-icon'
                        );


                    if (!icon) {

                        return;
                    }


                    /*
                     * data-favorite更新
                     */
                    button.dataset.favorite =
                        isFavorite ?
                        '1' :
                        '0';


                    button.setAttribute(
                        'aria-pressed',
                        isFavorite ?
                        'true' :
                        'false'
                    );


                    /*
                     * Favorite
                     */
                    if (isFavorite) {

                        icon.classList.remove(
                            'fa-regular',
                            'text-secondary'
                        );


                        icon.classList.add(
                            'fa-solid',
                            'text-danger'
                        );


                        return;
                    }


                    /*
                     * Not Favorite
                     */
                    icon.classList.remove(
                        'fa-solid',
                        'text-danger'
                    );


                    icon.classList.add(
                        'fa-regular',
                        'text-secondary'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | View Schedule
                |--------------------------------------------------------------------------
                */

                function updateViewScheduleButton(
                    card,
                    enabled,
                    teacherId,
                    date,
                    material
                ) {

                    const button =
                        card.querySelector(
                            '.view-schedule-btn'
                        );


                    if (!button) {

                        return;
                    }


                    if (!enabled) {

                        button.href =
                            '#';


                        button.classList.add(
                            'disabled'
                        );


                        button.setAttribute(
                            'aria-disabled',
                            'true'
                        );


                        return;
                    }


                    const params =
                        new URLSearchParams();


                    params.append(
                        'teacher_id',
                        teacherId
                    );


                    /*
                     * Materialが選択されている場合だけ渡す
                     */
                    if (material) {

                        params.append(
                            'material_id',
                            material
                        );
                    }


                    /*
                     * MaterialなしでもTeacher Detailへ行ける
                     */
                    params.append(
                        'mode',
                        material ?
                        'material' :
                        'teacher'
                    );

                    if (date) {

                        params.append(
                            'date',
                            date
                        );


                        params.append(
                            'view_start',
                            date
                        );
                    }


                    button.href =
                        "{{ route('students.reservations.teacher-detail') }}" +
                        '?' +
                        params.toString();


                    button.classList.remove(
                        'disabled'
                    );


                    button.removeAttribute(
                        'aria-disabled'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Selected Material
                |--------------------------------------------------------------------------
                */

                function updateSelectedConditions(
                    material
                ) {

                    if (
                        material &&
                        materialInput
                    ) {

                        selectedConditions
                            .classList
                            .remove(
                                'd-none'
                            );


                        selectedMaterial.textContent =
                            materialInput
                            .options[
                                materialInput.selectedIndex
                            ]
                            .text;


                        return;
                    }


                    selectedConditions
                        .classList
                        .add(
                            'd-none'
                        );


                    selectedMaterial.textContent =
                        '';
                }


                /*
                |--------------------------------------------------------------------------
                | Message
                |--------------------------------------------------------------------------
                */

                function updateMessage(
                    date,
                    time,
                    material,
                    visibleCount
                ) {

                    if (
                        visibleCount ===
                        0
                    ) {

                        selectionMessage.className =
                            'alert alert-warning';


                        selectionMessage.textContent =
                            'No teachers match the selected conditions.';


                        return;
                    }


                    if (!material) {

                        selectionMessage.className =
                            'alert alert-light border';


                        selectionMessage.textContent =
                            'Teachers matching your search conditions are shown below. Select a material before booking.';


                        return;
                    }


                    if (
                        date &&
                        time
                    ) {

                        selectionMessage.className =
                            'alert alert-success';


                        selectionMessage.textContent =
                            'Teachers available for the selected date and time.';


                        return;
                    }


                    if (date) {

                        selectionMessage.className =
                            'alert alert-light border';


                        selectionMessage.textContent =
                            'Teachers with available lesson times on the selected date.';


                        return;
                    }


                    selectionMessage.className =
                        'alert alert-light border';


                    selectionMessage.textContent =
                        'Choose a date and time for direct booking, or view the teacher schedule.';
                }


                /*
                |--------------------------------------------------------------------------
                | Teacher一覧更新
                |--------------------------------------------------------------------------
                */

                async function updateTeachers() {

                    const myVersion =
                        ++updateVersion;


                    /*
                     * Search Conditions
                     */

                    const keyword =
                        keywordInput ?
                        keywordInput.value.trim() :
                        '';


                    const favoriteOnly =
                        favoriteOnlyInput &&
                        favoriteOnlyInput.checked ?
                        '1' :
                        '';


                    const material =
                        materialInput ?
                        materialInput.value :
                        '';


                    const nationality =
                        nationalityInput ?
                        nationalityInput.value :
                        '';


                    const minPoints =
                        minPointsInput ?
                        minPointsInput.value :
                        '';


                    const maxPoints =
                        maxPointsInput ?
                        maxPointsInput.value :
                        '';


                    const minRating =
                        minRatingInput ?
                        minRatingInput.value :
                        '';


                    const date =
                        dateInput ?
                        dateInput.value :
                        '';


                    const time =
                        getSelectedTime();


                    /*
                     * Dateがなければ
                     * TimeはBackendへ送らない
                     */
                    const apiTime =
                        date ?
                        time :
                        '';


                    const conditions = {

                        keyword: keyword,

                        favorite_only: favoriteOnly,

                        material_id: material,

                        nationality: nationality,

                        min_points: minPoints,

                        max_points: maxPoints,

                        min_rating: minRating,

                        date: date,

                        time: apiTime,
                    };


                    updateSelectedConditions(
                        material
                    );


                    /*
                     * 一旦リセット
                     */
                    teacherCards.forEach(
                        function(card) {

                            card.classList.add(
                                'd-none'
                            );


                            clearBookingSlot(
                                card
                            );


                            updateBookButton(
                                card,
                                false
                            );


                            updateViewScheduleButton(
                                card,
                                false,
                                '',
                                '',
                                ''
                            );
                        }
                    );


                    try {

                        /*
                         * Backend検索
                         */
                        const data =
                            await fetchTeachers(
                                conditions
                            );


                        /*
                         * 古い検索結果なら使わない
                         */
                        if (
                            myVersion !==
                            updateVersion
                        ) {

                            return;
                        }


                        const teachers =
                            data.teachers ?? [];


                        /*
                         * Teacher IDをKeyにしたMap
                         */
                        const teacherMap =
                            new Map(
                                teachers.map(
                                    function(teacher) {

                                        return [
                                            String(
                                                teacher.id
                                            ),
                                            teacher
                                        ];
                                    }
                                )
                            );


                        let visibleCount =
                            0;


                        /*
                         * Blade上のTeacher Card
                         */
                        teacherCards.forEach(
                            function(card) {

                                const teacherId =
                                    String(
                                        card.dataset.teacherId
                                    );


                                const teacher =
                                    teacherMap.get(
                                        teacherId
                                    );

                                /*
                                 * Backend結果にいない
                                 */
                                if (!teacher) {

                                    return;
                                }

                                updateFavoriteButton(
                                    card,
                                    teacher.is_favorite ===
                                    true
                                );


                                /*
                                 * 表示
                                 */
                                card.classList.remove(
                                    'd-none'
                                );


                                visibleCount++;


                                /*
                                 * Slot
                                 */
                                if (
                                    teacher.schedule_id &&
                                    teacher.start_at &&
                                    teacher.end_at
                                ) {

                                    setBookingSlot(
                                        card,
                                        teacher
                                    );

                                } else {

                                    clearBookingSlot(
                                        card
                                    );
                                }


                                /*
                                 * View Schedule
                                 *
                                 * 現在のTeacher Detailでは
                                 * Material必須をやめる
                                 */

                                updateViewScheduleButton(
                                    card,
                                    true,
                                    teacherId,
                                    date,
                                    material
                                );


                                /*
                                 * Direct Booking
                                 */
                                const canBook =
                                    Boolean(
                                        material &&
                                        date &&
                                        time &&
                                        teacher.available ===
                                        true &&
                                        teacher.schedule_id &&
                                        teacher.start_at &&
                                        teacher.end_at
                                    );


                                updateBookButton(
                                    card,
                                    canBook
                                );
                            }
                        );


                        /*
                         * 0件表示
                         */
                        if (
                            visibleCount ===
                            0
                        ) {

                            noTeachers
                                .classList
                                .remove(
                                    'd-none'
                                );

                        } else {

                            noTeachers
                                .classList
                                .add(
                                    'd-none'
                                );
                        }


                        updateMessage(
                            date,
                            time,
                            material,
                            visibleCount
                        );


                    } catch (error) {

                        console.error(
                            'Teacher検索失敗',
                            error
                        );


                        noTeachers
                            .classList
                            .add(
                                'd-none'
                            );


                        selectionMessage.className =
                            'alert alert-danger';


                        selectionMessage.textContent =
                            'Failed to search teachers.';
                    }
                }
                /*
        |--------------------------------------------------------------------------
        | Favorite Toggle
        |--------------------------------------------------------------------------
        */

                document
                    .querySelectorAll(
                        '.favorite-heart-btn'
                    )
                    .forEach(
                        function(button) {

                            button.addEventListener(
                                'click',
                                async function() {

                                    const teacherId =
                                        this.dataset.teacherId;


                                    const isFavorite =
                                        this.dataset.favorite ===
                                        '1';


                                    /*
                                     * Favoriteなら unlike
                                     *
                                     * Favoriteでなければ like
                                     */
                                    const url =
                                        isFavorite ?
                                        '/students/teachers/' +
                                        teacherId +
                                        '/unlike'

                                        :
                                        '/students/teachers/' +
                                        teacherId +
                                        '/like';


                                    /*
                                     * 連打防止
                                     */
                                    this.disabled =
                                        true;


                                    try {

                                        const response =
                                            await fetch(
                                                url, {
                                                    method: 'POST',

                                                    headers: {

                                                        'Accept': 'application/json',

                                                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                                    }
                                                }
                                            );


                                        if (!response.ok) {

                                            throw new Error(
                                                'Favorite API error: ' +
                                                response.status
                                            );
                                        }


                                        /*
                                         * 表示反転
                                         */
                                        updateFavoriteButton(
                                            this.closest(
                                                '.teacher-card'
                                            ),
                                            !isFavorite
                                        );


                                        /*
                                         * Favorite Only検索中なら
                                         * Teacher一覧を再取得
                                         */
                                        if (
                                            favoriteOnlyInput &&
                                            favoriteOnlyInput.checked
                                        ) {

                                            await updateTeachers();
                                        }


                                    } catch (error) {

                                        console.error(
                                            'お気に入り更新失敗',
                                            error
                                        );


                                        alert(
                                            'お気に入りの更新に失敗しました。'
                                        );

                                    } finally {

                                        this.disabled =
                                            false;
                                    }
                                }
                            );
                        }
                    );

                /*
                |--------------------------------------------------------------------------
                | Direct Book
                |--------------------------------------------------------------------------
                */

                document
                    .querySelectorAll(
                        '.book-btn'
                    )
                    .forEach(
                        function(button) {

                            button.addEventListener(
                                'click',
                                function() {

                                    if (
                                        this.disabled
                                    ) {

                                        return;
                                    }


                                    const card =
                                        this.closest(
                                            '.teacher-card'
                                        );


                                    const teacherId =
                                        card.dataset.teacherId;


                                    const materialId =
                                        materialInput ?
                                        materialInput.value :
                                        '';


                                    const scheduleId =
                                        card.dataset.scheduleId;


                                    const startAt =
                                        card.dataset.startAt;


                                    const endAt =
                                        card.dataset.endAt;


                                    if (
                                        !teacherId ||
                                        !materialId ||
                                        !scheduleId ||
                                        !startAt ||
                                        !endAt
                                    ) {

                                        alert(
                                            '予約情報を取得できませんでした。もう一度時間を選択してください。'
                                        );


                                        return;
                                    }


                                    bookingTeacherId.value =
                                        teacherId;


                                    bookingMaterialId.value =
                                        materialId;


                                    bookingScheduleId.value =
                                        scheduleId;


                                    bookingStartAt.value =
                                        startAt;


                                    bookingEndAt.value =
                                        endAt;


                                    bookingForm.submit();
                                }
                            );
                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | Disabled Schedule Link
                |--------------------------------------------------------------------------
                */

                document.addEventListener(
                    'click',
                    function(event) {

                        const button =
                            event.target.closest(
                                '.view-schedule-btn'
                            );


                        if (
                            button &&
                            button.classList.contains(
                                'disabled'
                            )
                        ) {

                            event.preventDefault();
                        }
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Events
                |--------------------------------------------------------------------------
                */

                if (keywordInput) {

                    keywordInput.addEventListener(
                        'input',
                        updateTeachers
                    );
                }


                if (favoriteOnlyInput) {

                    favoriteOnlyInput.addEventListener(
                        'change',
                        updateTeachers
                    );
                }


                if (materialInput) {

                    materialInput.addEventListener(
                        'change',
                        updateTeachers
                    );
                }


                if (nationalityInput) {

                    nationalityInput.addEventListener(
                        'change',
                        updateTeachers
                    );
                }


                if (dateInput) {

                    dateInput.addEventListener(
                        'change',
                        updateTeachers
                    );
                }


                if (hourInput) {

                    hourInput.addEventListener(
                        'change',
                        updateTeachers
                    );
                }


                if (minuteInput) {

                    minuteInput.addEventListener(
                        'change',
                        updateTeachers
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Initial
                |--------------------------------------------------------------------------
                */

                updateRatingDisplay(
                    ''
                );


                updateTeachers();

            }
        );
    </script>

@endsection
