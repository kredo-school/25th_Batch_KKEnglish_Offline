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
    <div
        id="selectionMessage"
        class="alert alert-light border"
    >
        Please select a material.
    </div>


    {{-- ===============================
         Selected Material
    ================================ --}}
    <div
        id="selectedConditions"
        class="mb-4 d-none"
    >

        <div
            class="
                bg-light
                border
                rounded
                px-3
                py-2
            "
        >

            <span class="text-secondary small me-2">
                Material
            </span>

            <span
                id="selectedMaterial"
                class="fw-semibold"
            >
            </span>

        </div>

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

                data-materials="{{
                    $teacher->materials
                        ->pluck('material_id')
                        ->implode(',')
                }}"

                data-schedule-id=""
                data-start-at=""
                data-end-at=""
            >

                <div class="card h-100 shadow-sm">


                    {{-- Teacher Image --}}
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

                            {{
                                $teacher->user?->first_name
                                ?? 'Teacher'
                            }}

                            {{
                                $teacher->user?->last_name
                                ?? ''
                            }}

                        </h5>


                        {{-- Nationality --}}
                        <p class="mb-1 small">

                            <span class="text-secondary">
                                Nationality:
                            </span>

                            {{
                                $teacher->user?->nationality
                                ?? '-'
                            }}

                        </p>


                        {{-- Specialty --}}
                        <p class="mb-3 small">

                            <span class="text-secondary">
                                Specialty:
                            </span>

                            {{
                                $teacher->specialty
                                ?? '-'
                            }}

                        </p>


                        <div class="mt-auto">

                            {{-- ===============================
                                 Method 1:
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
                                 Method 2:
                                 Direct Book
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


    {{-- ===============================
         Booking Form
    ================================ --}}
    <form
        id="bookingForm"
        action="{{ route('students.reservations.confirm') }}"
        method="POST"
        class="d-none"
    >

        @csrf


        <input
            type="hidden"
            name="teacher_id"
            id="bookingTeacherId"
        >


        <input
            type="hidden"
            name="material_id"
            id="bookingMaterialId"
        >


        <input
            type="hidden"
            name="schedule_id"
            id="bookingScheduleId"
        >


        <input
            type="hidden"
            name="start_at"
            id="bookingStartAt"
        >


        <input
            type="hidden"
            name="end_at"
            id="bookingEndAt"
        >

    </form>

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
        | 古いAPI結果を使用しない
        |--------------------------------------------------------------------------
        */

        let updateVersion = 0;


        /*
        |--------------------------------------------------------------------------
        | Selected Time
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


            if (
                !hour
                ||
                !minute
            ) {

                return '';

            }


            return (
                String(hour)
                    .padStart(
                        2,
                        '0'
                    )
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
                        method:
                            'GET',

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
        | 指定時間のSlot取得
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
                        slotTime
                        ===
                        time
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Material判定
        |--------------------------------------------------------------------------
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
        | Slot保存
        |--------------------------------------------------------------------------
        */

        function setBookingSlot(
            card,
            slot
        ) {

            if (!slot) {

                clearBookingSlot(
                    card
                );

                return;

            }


            card.dataset.scheduleId =
                slot.schedule_id
                ?? '';


            card.dataset.startAt =
                slot.start_at
                ?? '';


            card.dataset.endAt =
                slot.end_at
                ?? '';

        }


        /*
        |--------------------------------------------------------------------------
        | Slot削除
        |--------------------------------------------------------------------------
        */

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
        | View Schedule Button
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


            params.append(
                'material_id',
                material
            );


            /*
             * Dateが選択されていたら
             * その日を含む週を最初に表示
             */
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
                "{{ route('students.reservations.teacher-detail') }}"
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
        | Selected Material
        |--------------------------------------------------------------------------
        */

        function updateSelectedConditions(
            material
        ) {

            if (
                material
                &&
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
                            materialInput
                                .selectedIndex
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
                visibleCount
                ===
                0
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
                date
                &&
                time
            ) {

                selectionMessage.className =
                    'alert alert-success';


                selectionMessage.textContent =
                    'Available teachers for the selected date and time. You can book directly or view their schedule.';


                return;

            }


            /*
             * Material + Date
             */
            if (date) {

                selectionMessage.className =
                    'alert alert-light border';


                selectionMessage.textContent =
                    'These teachers have available lesson times on the selected date.';


                return;

            }


            /*
             * Material only
             */
            selectionMessage.className =
                'alert alert-light border';


            selectionMessage.textContent =
                'Select a teacher to view their weekly schedule, or choose a date and time for direct booking.';

        }


        /*
        |--------------------------------------------------------------------------
        | Teacher一覧更新
        |--------------------------------------------------------------------------
        */
    //    updateTeachers()がこの画面の中心
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
                material
            );


            /*
             * 条件変更時
             * 古いSlot情報を削除
             */
            teacherCards.forEach(
                function (card) {

                    clearBookingSlot(
                        card
                    );

                }
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
             *
             * 教材を教えられる先生を表示
             * View Schedule可能
             * Book不可
             * ===============================
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
                    visibleCount
                    ===
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


                return;

            }


            /*
             * ===============================
             * Material + Date
             * または
             * Material + Date + Time
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


                const matchMaterial =
                    matchesMaterial(
                        card,
                        material
                    );


                /*
                 * 教材を教えられない先生
                 */
                if (!matchMaterial) {

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
                        teacherId,
                        date,
                        material
                    );


                    continue;

                }


                try {

                    const data =
                        await fetchAvailability(
                            teacherId,
                            date
                        );


                    /*
                     * 条件変更後に返った
                     * 古いAPI結果は使用しない
                     */
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


                    let targetSlot =
                        null;


                    /*
                     * ===============================
                     * Date + Time
                     *
                     * 指定時間の空き確認
                     * ===============================
                     */
                    if (time) {

                        targetSlot =
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


                        if (isAvailable) {

                            setBookingSlot(
                                card,
                                targetSlot
                            );

                        } else {

                            clearBookingSlot(
                                card
                            );

                        }


                    /*
                     * ===============================
                     * Date Only
                     *
                     * その日に1枠でも
                     * 空きがあるか
                     * ===============================
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


                        clearBookingSlot(
                            card
                        );

                    }


                    /*
                     * Teacher表示
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
                     * View Schedule
                     */
                    updateViewScheduleButton(
                        card,
                        isAvailable,
                        teacherId,
                        date,
                        material
                    );


                    /*
                     * Direct Book
                     *
                     * Material
                     * Date
                     * Time
                     * 空きSlot
                     *
                     * 全部揃ったときのみ
                     */
                    const canBook =
                        Boolean(
                            material
                            &&
                            date
                            &&
                            time
                            &&
                            isAvailable
                            &&
                            card.dataset.scheduleId
                            &&
                            card.dataset.startAt
                            &&
                            card.dataset.endAt
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
                        teacherId,
                        date,
                        material
                    );

                }

            }


            /*
             * Teacher 0件
             */
            if (
                visibleCount
                ===
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

        }


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


                            const materialId =
                                materialInput
                                    ? materialInput.value
                                    : '';


                            const scheduleId =
                                card.dataset
                                    .scheduleId;


                            const startAt =
                                card.dataset
                                    .startAt;


                            const endAt =
                                card.dataset
                                    .endAt;


                            /*
                             * 必要情報確認
                             */
                            if (
                                !teacherId
                                ||
                                !materialId
                                ||
                                !scheduleId
                                ||
                                !startAt
                                ||
                                !endAt
                            ) {

                                console.error(
                                    '予約情報が不足しています。',
                                    {
                                        teacher_id:
                                            teacherId,

                                        material_id:
                                            materialId,

                                        schedule_id:
                                            scheduleId,

                                        start_at:
                                            startAt,

                                        end_at:
                                            endAt
                                    }
                                );


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


                            /*
                             * confirm()へ
                             */
                            bookingForm.submit();

                        }
                    );

                }
            );


        /*
        |--------------------------------------------------------------------------
        | Disabled View Schedule
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
