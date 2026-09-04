@extends('layouts.app')

@section('title', 'Book a Lesson')

@section('content')

<div class="container-fluid py-4">

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
         Message
    ================================ --}}
    <div
        id="selectionMessage"
        class="alert alert-light border"
    >
        Please select a material.
    </div>


    {{-- ===============================
         Selected Conditions
    ================================ --}}
    <div
        id="selectedConditions"
        class="mb-4 d-none"
    >

        <span class="text-secondary me-2">
            Selected:
        </span>

        <span
            id="selectedDate"
            class="badge text-bg-light border me-1 d-none"
        >
        </span>

        <span
            id="selectedTime"
            class="badge text-bg-light border me-1 d-none"
        >
        </span>

        <span
            id="selectedMaterial"
            class="badge text-bg-light border me-1 d-none"
        >
        </span>

    </div>


    {{-- ===============================
         Teacher List
    ================================ --}}
    <div
        id="teacherList"
        class="
            row
            row-cols-1
            row-cols-md-2
            row-cols-lg-4
            g-4
        "
    >

        @foreach ($teachers as $teacher)

            <div
                class="col teacher-card d-none"

                data-teacher-id="{{ $teacher->id }}"

                data-materials="{{ $teacher->materials
                    ->pluck('material_id')
                    ->implode(',') }}"
            >

                <div class="card h-100 shadow-sm">


                    {{-- ===============================
                         Teacher Image
                    ================================ --}}
                    @if ($teacher->user && $teacher->user->profile_image)

                        <img
                            src="{{ $teacher->user->profile_image }}"
                            alt="{{ $teacher->user->first_name }}"
                            class="card-img-top"
                            style="
                                height: 180px;
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
                            style="height: 180px;"
                        >
                            No Image
                        </div>

                    @endif


                    <div
                        class="
                            card-body
                            d-flex
                            flex-column
                        "
                    >

                        {{-- Name --}}
                        <h5 class="fw-bold mb-2">

                            {{ $teacher->user?->first_name ?? 'Teacher' }}

                            {{ $teacher->user?->last_name ?? '' }}

                        </h5>


                        {{-- Nationality --}}
                        <p class="mb-1 small">

                            <span class="text-secondary">
                                Nationality:
                            </span>

                            {{ $teacher->user?->nationality ?? '-' }}

                        </p>


                        {{-- Specialty --}}
                        <p class="mb-3 small">

                            <span class="text-secondary">
                                Specialty:
                            </span>

                            {{ $teacher->specialty ?? '-' }}

                        </p>


                        <div class="mt-auto">

                            {{-- ===============================
                                 View Schedule
                            ================================ --}}
                            <a
                                href="#"
                                class="
                                    btn
                                    btn-outline-primary
                                    btn-sm
                                    w-100
                                    mb-2
                                    view-schedule-btn
                                    disabled
                                "
                                aria-disabled="true"
                            >
                                View Schedule
                            </a>


                            {{-- ===============================
                                 Book
                            ================================ --}}
                            <button
                                type="button"
                                class="
                                    btn
                                    btn-secondary
                                    btn-sm
                                    w-100
                                    book-btn
                                "
                                disabled
                            >
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
    <div
        id="noTeachers"
        class="text-center py-5 d-none"
    >

        <h5 class="fw-bold">
            No teachers available
        </h5>

        <p class="text-secondary">
            Please change your search conditions.
        </p>

    </div>

</div>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
        |--------------------------------------------------------------------------
        | Sidebar Inputs
        |--------------------------------------------------------------------------
        */

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

        const materialInput =
            document.getElementById(
                'reservationMaterial'
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

        const selectedDate =
            document.getElementById(
                'selectedDate'
            );

        const selectedTime =
            document.getElementById(
                'selectedTime'
            );

        const selectedMaterial =
            document.getElementById(
                'selectedMaterial'
            );


        /*
        |--------------------------------------------------------------------------
        | 古いAPI結果を使わないため
        |--------------------------------------------------------------------------
        */

        let updateVersion = 0;


        /*
        |--------------------------------------------------------------------------
        | Time
        |--------------------------------------------------------------------------
        */

        function getSelectedTime() {

            const hour =
                hourInput
                    ? hourInput.value
                    : '';

            const minute =
                minuteInput
                    ? minuteInput.value
                    : '';

            if (!hour || !minute) {

                return '';

            }

            return (
                String(hour)
                    .padStart(2, '0')
                +
                ':'
                +
                minute
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Availability API
        |--------------------------------------------------------------------------
        */

        async function fetchAvailability(
            teacherId,
            date
        ) {

            const params =
                new URLSearchParams({
                    teacher_id:
                        teacherId,

                    date:
                        date
                });

            const response =
                await fetch(
                    '/students/availability'
                    +
                    '?'
                    +
                    params.toString(),
                    {
                        method: 'GET',

                        headers: {
                            'Accept':
                                'application/json'
                        }
                    }
                );

            if (!response.ok) {

                throw new Error(
                    'Availability API error: '
                    +
                    response.status
                );

            }

            return await response.json();

        }


        /*
        |--------------------------------------------------------------------------
        | 指定時間のSlot
        |--------------------------------------------------------------------------
        */

        function findTargetSlot(
            slots,
            time
        ) {

            return slots.find(
                function (slot) {

                    if (!slot.start_at) {

                        return false;

                    }

                    const slotTime =
                        slot.start_at
                            .substring(
                                11,
                                16
                            );

                    return (
                        slotTime === time
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Material判定
        |--------------------------------------------------------------------------
        |
        | 例:
        |
        | selectedMaterial = "1"
        |
        | data-materials = "1,3,5"
        |
        | → true
        |
        */

        function matchesMaterial(
            card,
            material
        ) {

            if (!material) {

                return false;

            }

            const materials =
                card.dataset.materials
                    ? card.dataset.materials
                        .split(',')
                    : [];

            return materials.includes(
                String(material)
            );

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

            button.disabled =
                !enabled;

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


            if (material) {

                params.append(
                    'material',
                    material
                );

            }


            if (date) {

                params.append(
                    'date',
                    date
                );

                params.append(
                    'mode',
                    'date'
                );

            } else {

                params.append(
                    'mode',
                    'material'
                );

            }


            button.href =
                "{{ route('reservations.teacher-detail.test') }}"
                +
                '?'
                +
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
        | Selected Conditions
        |--------------------------------------------------------------------------
        */

        function updateSelectedConditions(
            date,
            time,
            material
        ) {

            const hasCondition =
                Boolean(
                    date ||
                    time ||
                    material
                );


            if (hasCondition) {

                selectedConditions
                    .classList
                    .remove(
                        'd-none'
                    );

            } else {

                selectedConditions
                    .classList
                    .add(
                        'd-none'
                    );

            }


            /*
             * Date
             */
            if (date) {

                selectedDate.textContent =
                    date;

                selectedDate
                    .classList
                    .remove(
                        'd-none'
                    );

            } else {

                selectedDate
                    .classList
                    .add(
                        'd-none'
                    );

            }


            /*
             * Time
             */
            if (time) {

                selectedTime.textContent =
                    time;

                selectedTime
                    .classList
                    .remove(
                        'd-none'
                    );

            } else {

                selectedTime
                    .classList
                    .add(
                        'd-none'
                    );

            }


            /*
             * Material
             */
            if (
                material &&
                materialInput
            ) {

                selectedMaterial.textContent =
                    materialInput
                        .options[
                            materialInput
                                .selectedIndex
                        ]
                        .text;

                selectedMaterial
                    .classList
                    .remove(
                        'd-none'
                    );

            } else {

                selectedMaterial
                    .classList
                    .add(
                        'd-none'
                    );

            }

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

            /*
             * Material未選択
             */
            if (!material) {

                selectionMessage.className =
                    'alert alert-light border';

                selectionMessage.textContent =
                    'Please select a material.';

                return;

            }


            /*
             * 0件
             */
            if (
                visibleCount === 0
            ) {

                selectionMessage.className =
                    'alert alert-warning';

                selectionMessage.textContent =
                    'No teachers are available for the selected conditions.';

                return;

            }


            /*
             * Material + Date + Time
             */
            if (
                material &&
                date &&
                time
            ) {

                selectionMessage.className =
                    'alert alert-success';

                selectionMessage.textContent =
                    'These teachers can teach the selected material and are available at the selected time.';

                return;

            }


            /*
             * Material + Date
             */
            if (
                material &&
                date
            ) {

                selectionMessage.className =
                    'alert alert-info';

                selectionMessage.textContent =
                    'These teachers can teach the selected material and have available slots on this date.';

                return;

            }


            /*
             * Material only
             */
            selectionMessage.className =
                'alert alert-info';

            selectionMessage.textContent =
                'These teachers can teach the selected material.';

        }


        /*
        |--------------------------------------------------------------------------
        | Teacher一覧更新
        |--------------------------------------------------------------------------
        */

        async function updateTeachers() {

            const myVersion =
                ++updateVersion;


            const date =
                dateInput
                    ? dateInput.value
                    : '';

            const time =
                getSelectedTime();

            const material =
                materialInput
                    ? materialInput.value
                    : '';


            updateSelectedConditions(
                date,
                time,
                material
            );


            /*
             * ===============================
             * Material未選択
             * ===============================
             */

            if (!material) {

                teacherCards.forEach(
                    function (card) {

                        card.classList.add(
                            'd-none'
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


                noTeachers
                    .classList
                    .add(
                        'd-none'
                    );


                updateMessage(
                    date,
                    time,
                    material,
                    0
                );

                return;

            }


            /*
             * ===============================
             * Material Only
             * ===============================
             *
             * 日付なしなら
             * 教材だけで先生を絞る
             *
             */

            if (!date) {

                let visibleCount =
                    0;


                teacherCards.forEach(
                    function (card) {

                        const teacherId =
                            card.dataset
                                .teacherId;


                        const matchMaterial =
                            matchesMaterial(
                                card,
                                material
                            );


                        if (matchMaterial) {

                            card.classList.remove(
                                'd-none'
                            );

                            visibleCount++;

                        } else {

                            card.classList.add(
                                'd-none'
                            );

                        }


                        updateBookButton(
                            card,
                            false
                        );


                        updateViewScheduleButton(
                            card,
                            matchMaterial,
                            teacherId,
                            '',
                            material
                        );

                    }
                );


                if (
                    visibleCount === 0
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


                return;

            }


            /*
             * ===============================
             * Material + Date
             * ===============================
             */

            let visibleCount =
                0;


            for (
                const card
                of teacherCards
            ) {

                const teacherId =
                    card.dataset
                        .teacherId;


                /*
                 * まず教材判定
                 */
                const matchMaterial =
                    matchesMaterial(
                        card,
                        material
                    );


                /*
                 * 教材が一致しない先生は
                 * Availabilityを呼ばない
                 */
                if (!matchMaterial) {

                    card.classList.add(
                        'd-none'
                    );


                    updateBookButton(
                        card,
                        false
                    );


                    updateViewScheduleButton(
                        card,
                        false,
                        teacherId,
                        date,
                        material
                    );


                    continue;

                }


                try {

                    /*
                     * Availability
                     */
                    const data =
                        await fetchAvailability(
                            teacherId,
                            date
                        );


                    if (
                        myVersion
                        !==
                        updateVersion
                    ) {

                        return;

                    }


                    const slots =
                        data.slots
                        ?? [];


                    let isAvailable =
                        false;


                    /*
                     * Date + Time
                     */
                    if (time) {

                        const targetSlot =
                            findTargetSlot(
                                slots,
                                time
                            );


                        isAvailable =
                            Boolean(
                                targetSlot
                                &&
                                targetSlot.available
                                ===
                                true
                            );


                    /*
                     * Date Only
                     */
                    } else {

                        isAvailable =
                            slots.some(
                                function (slot) {

                                    return (
                                        slot.available
                                        ===
                                        true
                                    );

                                }
                            );

                    }


                    /*
                     * 表示
                     */
                    if (isAvailable) {

                        card.classList.remove(
                            'd-none'
                        );

                        visibleCount++;

                    } else {

                        card.classList.add(
                            'd-none'
                        );

                    }


                    /*
                     * Schedule
                     */
                    updateViewScheduleButton(
                        card,
                        isAvailable,
                        teacherId,
                        date,
                        material
                    );


                    /*
                     * Book
                     */
                    const canBook =
                        Boolean(
                            material
                            &&
                            date
                            &&
                            time
                            &&
                            matchMaterial
                            &&
                            isAvailable
                        );


                    updateBookButton(
                        card,
                        canBook
                    );


                } catch (error) {

                    console.error(
                        'Availability取得失敗',
                        {
                            teacherId:
                                teacherId,

                            date:
                                date,

                            error:
                                error
                        }
                    );


                    card.classList.add(
                        'd-none'
                    );


                    updateBookButton(
                        card,
                        false
                    );

                }

            }


            /*
             * 先生0件
             */
            if (
                visibleCount === 0
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

        }


        /*
        |--------------------------------------------------------------------------
        | Book
        |--------------------------------------------------------------------------
        */

        document
            .querySelectorAll(
                '.book-btn'
            )
            .forEach(
                function (button) {

                    button.addEventListener(
                        'click',
                        function () {

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
                                card.dataset
                                    .teacherId;


                            const date =
                                dateInput
                                    ? dateInput.value
                                    : '';


                            const time =
                                getSelectedTime();


                            const material =
                                materialInput
                                    ? materialInput.value
                                    : '';


                            console.log(
                                'Book selected',
                                {
                                    teacher_id:
                                        teacherId,

                                    material_id:
                                        material,

                                    date:
                                        date,

                                    time:
                                        time
                                }
                            );

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
            function (event) {

                const button =
                    event.target.closest(
                        '.view-schedule-btn'
                    );


                if (
                    button
                    &&
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


        if (materialInput) {

            materialInput.addEventListener(
                'change',
                updateTeachers
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Initial
        |--------------------------------------------------------------------------
        */

        updateTeachers();

    }
);

</script>

@endsection