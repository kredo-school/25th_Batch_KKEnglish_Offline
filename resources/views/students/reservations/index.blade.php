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

                data-schedule-id=""
                data-start-at=""
                data-end-at=""
            >

                <div class="card h-100 shadow-sm">


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
                                height: 180px;
                                object-fit: cover;">

                    @else

                        <div
                            class="
                                bg-light
                                d-flex
                                justify-content-center
                                align-items-center
                                text-secondary"
                            style="height: 180px;">
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


    {{-- ===============================
         Booking Form
         Book押下時にConfirmへ送信
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
        | 指定時間のSlotを取得
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


                    /*
                     * 例
                     *
                     * 2026-09-10 09:00:00
                     *
                     * または
                     *
                     * 2026-09-10T09:00:00
                     *
                     * ↓
                     *
                     * 09:00
                     */

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
        | 選択した予約Slotをカードに保存
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
        | 保存していたSlotを削除
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
                material
                &&
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
                material
                &&
                date
                &&
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
                material
                &&
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
             * 条件が変更されたので
             * 前回保存したSlot情報を一旦削除
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


                        /*
                         * 日付・時間がないので
                         * Bookはできない
                         */
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
                 * Material判定
                 */
                const matchMaterial =
                    matchesMaterial(
                        card,
                        material
                    );


                /*
                 * 教材が一致しない先生は
                 * Availability APIを呼ばない
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


                    clearBookingSlot(
                        card
                    );


                    continue;

                }


                try {

                    /*
                     * Availability取得
                     */
                    const data =
                        await fetchAvailability(
                            teacherId,
                            date
                        );


                    /*
                     * 条件変更後に
                     * 古いAPI結果が返ってきた場合
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


                        /*
                         * 予約可能なら
                         *
                         * schedule_id
                         * start_at
                         * end_at
                         *
                         * をカードに保存
                         */
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


                        /*
                         * 時間未選択なので
                         * Book用Slotは保存しない
                         */
                        clearBookingSlot(
                            card
                        );

                    }


                    /*
                     * ===============================
                     * Teacher表示
                     * ===============================
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
                     * ===============================
                     * View Schedule
                     * ===============================
                     */

                    updateViewScheduleButton(
                        card,
                        isAvailable,
                        teacherId,
                        date,
                        material
                    );


                    /*
                     * ===============================
                     * Book
                     * ===============================
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
             * ===============================
             * Teacher 0件
             * ===============================
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
                             * 必要な予約情報が
                             * 全て揃っているか確認
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


                            /*
                             * Confirm Controllerへ送る値
                             */
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
                             * 確認用
                             */
                            console.log(
                                'Booking Confirm',
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


                            /*
                             * ReservationController
                             * confirm() へPOST
                             */
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