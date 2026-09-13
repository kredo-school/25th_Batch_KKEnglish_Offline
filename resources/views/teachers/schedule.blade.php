@extends('layouts.app')

@section('title', 'My Schedule')

@section('content')

<div class="container py-4">

    {{-- ===============================
         Title
    ================================ --}}
    <div
        class="
            d-flex
            justify-content-between
            align-items-center
            mb-4
        "
    >

        <h2 class="fw-bold mb-0">
            My Schedule
        </h2>


        <button
            type="button"
            id="editBtn"
            class="btn btn-dark"
        >
            Edit Schedule
        </button>

    </div>



    {{-- ===============================
         Week Navigation
    ================================ --}}
    <div
        class="
            d-flex
            justify-content-between
            align-items-center
            mb-3
        "
    >

        {{-- Previous Week --}}
        <a
            href="{{ route('teachers.schedule', [
                'week_start' => $startOfWeek
                    ->copy()
                    ->subWeek()
                    ->format('Y-m-d')
            ]) }}"
            class="btn btn-outline-secondary btn-sm"
        >

            <i
                class="
                    fa-solid
                    fa-chevron-left
                    me-1
                "
            ></i>

            Previous Week

        </a>


        {{-- Week Range --}}
        <div class="text-center">

            <h5 class="fw-bold mb-0">

                {{ $days->first()->format('M d') }}

                -

                {{ $days->last()->format('M d, Y') }}

            </h5>

        </div>


        {{-- Next Week --}}
        <a
            href="{{ route('teachers.schedule', [
                'week_start' => $startOfWeek
                    ->copy()
                    ->addWeek()
                    ->format('Y-m-d')
            ]) }}"
            class="btn btn-outline-secondary btn-sm"
        >

            Next Week

            <i
                class="
                    fa-solid
                    fa-chevron-right
                    ms-1
                "
            ></i>

        </a>

    </div>



    {{-- ===============================
         Legend
    ================================ --}}
    <div
        class="
            d-flex
            gap-4
            flex-wrap
            mb-3
            small
        "
    >

        {{-- Available --}}
        <div class="d-flex align-items-center gap-2">

            <span
                class="
                    bg-danger-subtle
                    border
                    rounded
                "
                style="
                    width: 22px;
                    height: 22px;
                    display: inline-block;
                "
            ></span>

            Available

        </div>


        {{-- Booked --}}
        <div class="d-flex align-items-center gap-2">

            <span
                class="
                    bg-primary-subtle
                    border
                    rounded
                "
                style="
                    width: 22px;
                    height: 22px;
                    display: inline-block;
                "
            ></span>

            Booked

        </div>


        {{-- Unavailable --}}
        <div class="d-flex align-items-center gap-2">

            <span
                class="
                    bg-warning-subtle
                    border
                    rounded
                "
                style="
                    width: 22px;
                    height: 22px;
                    display: inline-block;
                "
            ></span>

            Unavailable

        </div>


        {{-- Past / Completed --}}
        <div class="d-flex align-items-center gap-2">

            <span
                class="
                    bg-secondary-subtle
                    border
                    rounded
                "
                style="
                    width: 22px;
                    height: 22px;
                    display: inline-block;
                "
            ></span>

            Past / Completed

        </div>


        {{-- Outside shift --}}
        <div class="d-flex align-items-center gap-2">

            <span
                class="
                    bg-light
                    border
                    rounded
                "
                style="
                    width: 22px;
                    height: 22px;
                    display: inline-block;
                "
            ></span>

            Outside shift

        </div>

    </div>



    {{-- ===============================
         Edit Message
    ================================ --}}
    <div
        id="editMessage"
        class="alert alert-warning d-none"
    >
        Select the available time slots you want to mark as unavailable.
    </div>



    {{-- ===============================
         Error Message
    ================================ --}}
    <div
        id="errorMessage"
        class="alert alert-danger d-none"
    >
    </div>



    {{-- ===============================
         Success Message
    ================================ --}}
    <div
        id="successMessage"
        class="alert alert-success d-none"
    >
    </div>



    {{-- ===============================
         Schedule Table
    ================================ --}}
    <div class="table-responsive">

        <table
            class="
                table
                table-bordered
                text-center
                align-middle
            "
        >

            <thead class="table-light">

                <tr>

                    <th style="width: 100px;">
                        Time
                    </th>


                    @foreach ($days as $day)

                        <th style="min-width: 120px;">

                            <div class="fw-bold">
                                {{ $day->format('D') }}
                            </div>

                            <small class="text-secondary">
                                {{ $day->format('m/d') }}
                            </small>

                        </th>

                    @endforeach

                </tr>

            </thead>


            <tbody>

                @foreach ($times as $time)

                    <tr>

                        {{-- Time --}}
                        <th class="table-light">

                            {{ $time }}

                        </th>


                        {{-- 7 Days --}}
                        @foreach ($days as $day)

                            <td
                                class="
                                    schedule-cell
                                    bg-light
                                "

                                data-date="{{ $day->format('Y-m-d') }}"

                                data-time="{{ $time }}"

                                data-working="false"

                                data-schedule-id=""

                                data-exception-id=""

                                data-original-unavailable="false"

                                style="
                                    height: 45px;
                                    cursor: default;
                                "
                            >
                            </td>

                        @endforeach

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>



    {{-- ===============================
         Edit Actions
    ================================ --}}
    <div
        id="editActions"
        class="
            d-none
            justify-content-end
            align-items-center
            gap-2
            mt-3
        "
    >

        {{-- Exception Type --}}
        <select
            id="exceptionType"
            class="form-select"
            style="max-width: 220px;"
        >

            <option value="">
                Select reason
            </option>

        </select>


        {{-- Cancel --}}
        <button
            type="button"
            id="cancelBtn"
            class="btn btn-outline-secondary"
        >
            Cancel
        </button>


        {{-- Save --}}
        <button
            type="button"
            id="saveBtn"
            class="btn btn-dark"
        >
            Save Schedule
        </button>

    </div>

</div>



<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        /*
        |--------------------------------------------------------------------------
        | Elements
        |--------------------------------------------------------------------------
        */

        const editBtn =
            document.getElementById(
                'editBtn'
            );


        const cancelBtn =
            document.getElementById(
                'cancelBtn'
            );


        const saveBtn =
            document.getElementById(
                'saveBtn'
            );


        const editActions =
            document.getElementById(
                'editActions'
            );


        const editMessage =
            document.getElementById(
                'editMessage'
            );


        const errorMessage =
            document.getElementById(
                'errorMessage'
            );


        const successMessage =
            document.getElementById(
                'successMessage'
            );


        const exceptionType =
            document.getElementById(
                'exceptionType'
            );


        const cells =
            document.querySelectorAll(
                '.schedule-cell'
            );


        /*
        |--------------------------------------------------------------------------
        | Current Week
        |--------------------------------------------------------------------------
        */

        const currentWeekStart =
            '{{ $startOfWeek->format('Y-m-d') }}';


        /*
        |--------------------------------------------------------------------------
        | CSRF Token
        |--------------------------------------------------------------------------
        */

        const token =
            document
                .querySelector(
                    'meta[name="csrf-token"]'
                )
                .getAttribute(
                    'content'
                );


        /*
        |--------------------------------------------------------------------------
        | State
        |--------------------------------------------------------------------------
        */

        let editing =
            false;


        let originalSchedule =
            [];



        /*
        |--------------------------------------------------------------------------
        | Initial Load
        |--------------------------------------------------------------------------
        */

        loadSchedule();



        /*
        |--------------------------------------------------------------------------
        | GET Schedule
        |--------------------------------------------------------------------------
        */

        async function loadSchedule() {

            try {

                const response =
                    await fetch(
                        '/teachers/schedule-exceptions'
                        +
                        '?week_start='
                        +
                        encodeURIComponent(
                            currentWeekStart
                        ),
                        {
                            method:
                                'GET',

                            headers: {
                                'Accept':
                                    'application/json'
                            },

                            credentials:
                                'same-origin'
                        }
                    );


                const data =
                    await response.json();


                if (!response.ok) {

                    showError(
                        data.message
                        ??
                        'Failed to load schedule.'
                    );

                    return;

                }


                /*
                 * TeacherSchedule
                 */
                applySchedules(
                    data.schedules
                    ?? []
                );


                /*
                 * ExceptionType
                 */
                applyExceptionTypes(
                    data.exception_types
                    ?? []
                );


            } catch (error) {

                console.error(
                    error
                );


                showError(
                    'Failed to load schedule.'
                );

            }

        }



        /*
        |--------------------------------------------------------------------------
        | TeacherSchedule
        |--------------------------------------------------------------------------
        */

        function applySchedules(
            schedules
        ) {

            /*
             * 全セル初期化
             */
            cells.forEach(
                function (cell) {

                    cell.dataset.working =
                        'false';


                    cell.dataset.scheduleId =
                        '';


                    cell.dataset.exceptionId =
                        '';


                    cell.dataset.originalUnavailable =
                        'false';


                    /*
                     * 状態ごとの色を全部リセット
                     */
                    cell.classList.remove(
                        'bg-danger-subtle',
                        'bg-primary-subtle',
                        'bg-secondary-subtle',
                        'bg-warning-subtle',
                        'table-secondary'
                    );


                    /*
                     * 初期状態
                     * Outside shift
                     */
                    cell.classList.add(
                        'bg-light'
                    );


                    cell.textContent =
                        '';


                    cell.style.cursor =
                        'default';

                }
            );


            /*
             * TeacherScheduleを反映
             */
            schedules.forEach(
                function (schedule) {


                    const date =
                        schedule
                            .available_date;


                    const startTime =
                        schedule
                            .start_time
                            .substring(
                                0,
                                5
                            );


                    const endTime =
                        schedule
                            .end_time
                            .substring(
                                0,
                                5
                            );


                    cells.forEach(
                        function (cell) {


                            /*
                             * 日付が違う
                             */
                            if (
                                cell.dataset.date
                                !== date
                            ) {

                                return;

                            }


                            const time =
                                cell.dataset.time;


                            /*
                             * シフト時間内
                             */
                            if (
                                time >= startTime
                                &&
                                time < endTime
                            ) {

                                cell.dataset.working =
                                    'true';


                                cell.dataset.scheduleId =
                                    schedule
                                        .schedule_id;


                                /*
                                 * シフト外の色を削除
                                 */
                                cell.classList.remove(
                                    'bg-light'
                                );


                                /*
                                 * 勤務時間は薄い赤
                                 */
                                cell.classList.add(
                                    'bg-danger-subtle'
                                );


                                cell.innerHTML = `
                                    <span class="small">
                                        Available
                                    </span>
                                `;

                            }

                        }
                    );


                    /*
                     * Reservation
                     */
                    applyReservations(
                        schedule
                    );


                    /*
                     * 登録済みException
                     */
                    applyExceptions(
                        schedule
                    );

                }
            );


            /*
             * Past
             */
            applyPastCells();

        }



        /*
        |--------------------------------------------------------------------------
        | Reservations
        |--------------------------------------------------------------------------
        */

        function applyReservations(
            schedule
        ) {

            const reservations =
                schedule.reservations
                ?? [];


            reservations.forEach(
                function (reservation) {


                    const statusCode =
                        reservation.status_code;


                    const reservationStart =
                        new Date(
                            reservation.start_at
                                .replace(
                                    ' ',
                                    'T'
                                )
                        );


                    const reservationEnd =
                        new Date(
                            reservation.end_at
                                .replace(
                                    ' ',
                                    'T'
                                )
                        );


                    cells.forEach(
                        function (cell) {


                            /*
                             * 日付が違うセルは対象外
                             */
                            if (
                                cell.dataset.date
                                !==
                                schedule.available_date
                            ) {

                                return;

                            }


                            /*
                             * セル開始時間
                             */
                            const cellStart =
                                new Date(
                                    `${cell.dataset.date}T${cell.dataset.time}:00`
                                );


                            /*
                             * セル終了時間
                             * 30分後
                             */
                            const cellEnd =
                                new Date(
                                    cellStart.getTime()
                                    +
                                    30
                                    *
                                    60
                                    *
                                    1000
                                );


                            /*
                             * 予約時間とセル時間が
                             * 重なっているか
                             */
                            const overlaps =
                                cellStart
                                <
                                reservationEnd
                                &&
                                cellEnd
                                >
                                reservationStart;


                            if (!overlaps) {

                                return;

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Booked
                            |--------------------------------------------------------------------------
                            |
                            | pending / confirmed
                            |
                            */

                            if (
                                statusCode
                                === 'pending'
                                ||
                                statusCode
                                === 'confirmed'
                            ) {

                                cell.classList.remove(
                                    'bg-danger-subtle',
                                    'bg-light',
                                    'bg-secondary-subtle',
                                    'bg-warning-subtle'
                                );


                                cell.classList.add(
                                    'bg-primary-subtle'
                                );


                                cell.innerHTML = `
                                    <span class="small fw-semibold">
                                        Booked
                                    </span>
                                `;


                                return;

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Completed
                            |--------------------------------------------------------------------------
                            */

                            if (
                                statusCode
                                === 'completed'
                            ) {

                                cell.classList.remove(
                                    'bg-danger-subtle',
                                    'bg-primary-subtle',
                                    'bg-light',
                                    'bg-warning-subtle'
                                );


                                cell.classList.add(
                                    'bg-secondary-subtle'
                                );


                                cell.innerHTML = `
                                    <span class="small fw-semibold">
                                        Completed
                                    </span>
                                `;

                            }

                        }
                    );

                }
            );

        }



        /*
        |--------------------------------------------------------------------------
        | Existing Exceptions
        |--------------------------------------------------------------------------
        */

        function applyExceptions(
            schedule
        ) {

            const exceptions =
                schedule.exceptions
                ?? [];


            exceptions.forEach(
                function (exception) {


                    /*
                     * activeのみ
                     */
                    if (
                        exception.status
                        !== 'active'
                    ) {

                        return;

                    }


                    const exceptionStart =
                        new Date(
                            exception.start_at
                                .replace(
                                    ' ',
                                    'T'
                                )
                        );


                    const exceptionEnd =
                        new Date(
                            exception.end_at
                                .replace(
                                    ' ',
                                    'T'
                                )
                        );


                    cells.forEach(
                        function (cell) {


                            /*
                             * 日付が違う
                             */
                            if (
                                cell.dataset.date
                                !==
                                schedule.available_date
                            ) {

                                return;

                            }


                            const cellStart =
                                new Date(
                                    `${cell.dataset.date}T${cell.dataset.time}:00`
                                );


                            const cellEnd =
                                new Date(
                                    cellStart.getTime()
                                    +
                                    30
                                    *
                                    60
                                    *
                                    1000
                                );


                            /*
                             * Exceptionとの重複判定
                             */
                            const overlaps =
                                cellStart
                                <
                                exceptionEnd
                                &&
                                cellEnd
                                >
                                exceptionStart;


                            if (!overlaps) {

                                return;

                            }


                            /*
                             * Booked / Completed は
                             * Unavailableで上書きしない
                             */
                            if (
                                cell.classList.contains(
                                    'bg-primary-subtle'
                                )
                                ||
                                (
                                    cell.classList.contains(
                                        'bg-secondary-subtle'
                                    )
                                    &&
                                    cell.textContent
                                        .trim()
                                    === 'Completed'
                                )
                            ) {

                                return;

                            }


                            cell.classList.remove(
                                'bg-danger-subtle',
                                'bg-light'
                            );


                            cell.classList.add(
                                'bg-warning-subtle'
                            );


                            cell.innerHTML = `
                                <span class="small">
                                    Unavailable
                                </span>
                            `;


                            cell.dataset.exceptionId =
                                exception.id;


                            cell.dataset.originalUnavailable =
                                'true';

                        }
                    );

                }
            );

        }



        /*
        |--------------------------------------------------------------------------
        | Past
        |--------------------------------------------------------------------------
        */

        function applyPastCells() {

            const now =
                new Date();


            cells.forEach(
                function (cell) {


                    const cellStart =
                        new Date(
                            `${cell.dataset.date}T${cell.dataset.time}:00`
                        );


                    /*
                     * 未来は対象外
                     */
                    if (
                        cellStart
                        >=
                        now
                    ) {

                        return;

                    }


                    /*
                     * Completedはそのまま
                     */
                    if (
                        cell.classList.contains(
                            'bg-secondary-subtle'
                        )
                        &&
                        cell.textContent
                            .trim()
                        === 'Completed'
                    ) {

                        return;

                    }


                    /*
                     * Past表示
                     */
                    cell.classList.remove(
                        'bg-danger-subtle',
                        'bg-primary-subtle',
                        'bg-warning-subtle',
                        'bg-light'
                    );


                    cell.classList.add(
                        'bg-secondary-subtle'
                    );


                    cell.innerHTML = `
                        <span class="small text-secondary">
                            Past
                        </span>
                    `;

                }
            );

        }



        /*
        |--------------------------------------------------------------------------
        | Exception Types
        |--------------------------------------------------------------------------
        */

        function applyExceptionTypes(
            types
        ) {

            exceptionType.innerHTML =
                `
                    <option value="">
                        Select reason
                    </option>
                `;


            types.forEach(
                function (type) {

                    const option =
                        document.createElement(
                            'option'
                        );


                    option.value =
                        type
                            .exception_type_id;


                    option.textContent =
                        type
                            .type_name;


                    exceptionType
                        .appendChild(
                            option
                        );

                }
            );

        }



        /*
        |--------------------------------------------------------------------------
        | Edit Schedule
        |--------------------------------------------------------------------------
        */

        editBtn.addEventListener(
            'click',
            function () {

                editing =
                    true;


                hideMessages();


                /*
                 * Cancel用に現在状態を保存
                 */
                originalSchedule =
                    [];


                cells.forEach(
                    function (cell) {

                        originalSchedule.push({

                            cell:
                                cell,

                            className:
                                cell.className,

                            html:
                                cell.innerHTML,

                            exceptionId:
                                cell.dataset
                                    .exceptionId,

                            originalUnavailable:
                                cell.dataset
                                    .originalUnavailable,

                        });


                        /*
                         * 編集可能条件
                         *
                         * 勤務時間内
                         * ＋
                         * Pastではない
                         * ＋
                         * Bookedではない
                         * ＋
                         * Completedではない
                         */
                        const isPast =
                            cell.classList.contains(
                                'bg-secondary-subtle'
                            );


                        const isBooked =
                            cell.classList.contains(
                                'bg-primary-subtle'
                            );


                        if (
                            cell.dataset.working
                            === 'true'
                            &&
                            !isPast
                            &&
                            !isBooked
                        ) {

                            cell.style.cursor =
                                'pointer';

                        }

                    }
                );


                editBtn.classList.add(
                    'd-none'
                );


                editActions
                    .classList
                    .remove(
                        'd-none'
                    );


                editActions
                    .classList
                    .add(
                        'd-flex'
                    );


                editMessage
                    .classList
                    .remove(
                        'd-none'
                    );

            }
        );



        /*
        |--------------------------------------------------------------------------
        | Cell Click
        |--------------------------------------------------------------------------
        */

        cells.forEach(
            function (cell) {

                cell.addEventListener(
                    'click',
                    function () {


                        /*
                         * 編集中ではない
                         */
                        if (!editing) {

                            return;

                        }


                        /*
                         * シフト外
                         */
                        if (
                            cell.dataset.working
                            !== 'true'
                        ) {

                            return;

                        }


                        /*
                         * Past / Completed
                         */
                        if (
                            cell.classList.contains(
                                'bg-secondary-subtle'
                            )
                        ) {

                            return;

                        }


                        /*
                         * Booked
                         */
                        if (
                            cell.classList.contains(
                                'bg-primary-subtle'
                            )
                        ) {

                            return;

                        }


                        const isUnavailable =
                            cell
                                .classList
                                .contains(
                                    'bg-warning-subtle'
                                );


                        /*
                         * Unavailable
                         * ↓
                         * Available
                         */
                        if (
                            isUnavailable
                        ) {

                            cell.classList.remove(
                                'bg-warning-subtle'
                            );


                            cell.classList.add(
                                'bg-danger-subtle'
                            );


                            cell.innerHTML = `
                                <span class="small">
                                    Available
                                </span>
                            `;

                        }

                        /*
                         * Available
                         * ↓
                         * Unavailable
                         */
                        else {

                            cell.classList.remove(
                                'bg-danger-subtle'
                            );


                            cell.classList.add(
                                'bg-warning-subtle'
                            );


                            cell.innerHTML = `
                                <span class="small">
                                    Unavailable
                                </span>
                            `;

                        }

                    }
                );

            }
        );



        /*
        |--------------------------------------------------------------------------
        | Cancel
        |--------------------------------------------------------------------------
        */

        cancelBtn.addEventListener(
            'click',
            function () {

                originalSchedule.forEach(
                    function (item) {

                        item.cell.className =
                            item.className;


                        item.cell.innerHTML =
                            item.html;


                        item.cell.dataset.exceptionId =
                            item.exceptionId;


                        item.cell.dataset.originalUnavailable =
                            item.originalUnavailable;

                    }
                );


                finishEditing();

            }
        );



        /*
        |--------------------------------------------------------------------------
        | Save
        |--------------------------------------------------------------------------
        */

        saveBtn.addEventListener(
            'click',
            async function () {

                hideMessages();


                const exceptionTypeId =
                    exceptionType.value;


                const createCells =
                    [];


                const cancelCells =
                    [];


                cells.forEach(
                    function (cell) {


                        /*
                         * シフト外は対象外
                         */
                        if (
                            cell.dataset.working
                            !== 'true'
                        ) {

                            return;

                        }


                        /*
                         * Past / Completedは対象外
                         */
                        if (
                            cell.classList.contains(
                                'bg-secondary-subtle'
                            )
                        ) {

                            return;

                        }


                        /*
                         * Bookedは対象外
                         */
                        if (
                            cell.classList.contains(
                                'bg-primary-subtle'
                            )
                        ) {

                            return;

                        }


                        const wasUnavailable =
                            cell.dataset
                                .originalUnavailable
                            === 'true';


                        const isUnavailable =
                            cell
                                .classList
                                .contains(
                                    'bg-warning-subtle'
                                );


                        /*
                         * Available
                         * ↓
                         * Unavailable
                         */
                        if (
                            !wasUnavailable
                            &&
                            isUnavailable
                        ) {

                            createCells.push(
                                cell
                            );

                        }


                        /*
                         * Unavailable
                         * ↓
                         * Available
                         */
                        if (
                            wasUnavailable
                            &&
                            !isUnavailable
                        ) {

                            cancelCells.push(
                                cell
                            );

                        }

                    }
                );


                /*
                 * 新しいException作成時は
                 * 理由必須
                 */
                if (
                    createCells.length
                    >
                    0
                    &&
                    !exceptionTypeId
                ) {

                    showError(
                        'Please select a reason.'
                    );

                    return;

                }


                /*
                 * 変更なし
                 */
                if (
                    createCells.length
                    ===
                    0
                    &&
                    cancelCells.length
                    ===
                    0
                ) {

                    finishEditing();

                    return;

                }


                saveBtn.disabled =
                    true;


                try {


                    /*
                     * 新規Exception
                     */
                    for (
                        const cell
                        of createCells
                    ) {

                        await createException(
                            cell,
                            exceptionTypeId
                        );

                    }


                    /*
                     * Exception解除
                     */
                    for (
                        const cell
                        of cancelCells
                    ) {

                        await cancelException(
                            cell
                        );

                    }


                    /*
                     * DBの最新状態を再取得
                     */
                    await loadSchedule();


                    finishEditing();


                    showSuccess(
                        'Schedule updated successfully.'
                    );


                } catch (error) {

                    console.error(
                        error
                    );


                    showError(
                        error.message
                        ??
                        'Failed to save schedule.'
                    );


                } finally {

                    saveBtn.disabled =
                        false;

                }

            }
        );



        /*
        |--------------------------------------------------------------------------
        | Create Exception
        |--------------------------------------------------------------------------
        */

        async function createException(
            cell,
            exceptionTypeId
        ) {

            const date =
                cell.dataset.date;


            const time =
                cell.dataset.time;


            /*
             * 開始日時
             */
            const start =
                new Date(
                    `${date}T${time}:00`
                );


            /*
             * 30分後
             */
            const end =
                new Date(
                    start.getTime()
                    +
                    30
                    *
                    60
                    *
                    1000
                );


            const endHour =
                String(
                    end.getHours()
                )
                .padStart(
                    2,
                    '0'
                );


            const endMinute =
                String(
                    end.getMinutes()
                )
                .padStart(
                    2,
                    '0'
                );


            const endTime =
                `${endHour}:${endMinute}:00`;


            const response =
                await fetch(
                    '/teachers/schedule-exceptions',
                    {
                        method:
                            'POST',

                        headers: {
                            'Content-Type':
                                'application/json',

                            'Accept':
                                'application/json',

                            'X-CSRF-TOKEN':
                                token,
                        },

                        credentials:
                            'same-origin',

                        body:
                            JSON.stringify({

                                schedule_id:
                                    Number(
                                        cell
                                            .dataset
                                            .scheduleId
                                    ),

                                exception_type_id:
                                    Number(
                                        exceptionTypeId
                                    ),

                                start_at:
                                    `${date} ${time}:00`,

                                end_at:
                                    `${date} ${endTime}`,

                            }),
                    }
                );


            const result =
                await response.json();


            if (!response.ok) {

                const firstError =
                    result.errors
                        ? Object.values(
                            result.errors
                        )[0]?.[0]
                        : null;


                throw new Error(
                    firstError
                    ??
                    result.message
                    ??
                    'Failed to create exception.'
                );

            }


            cell.dataset.exceptionId =
                result.exception.id
                ??
                result.exception
                    .schedule_exception_id;

        }



        /*
        |--------------------------------------------------------------------------
        | Cancel Exception
        |--------------------------------------------------------------------------
        */

        async function cancelException(
            cell
        ) {

            const exceptionId =
                cell.dataset
                    .exceptionId;


            if (!exceptionId) {

                return;

            }


            const response =
                await fetch(
                    '/teachers/schedule-exceptions/'
                    +
                    exceptionId,
                    {
                        method:
                            'DELETE',

                        headers: {
                            'Accept':
                                'application/json',

                            'X-CSRF-TOKEN':
                                token,
                        },

                        credentials:
                            'same-origin'
                    }
                );


            const result =
                await response.json();


            if (!response.ok) {

                const firstError =
                    result.errors
                        ? Object.values(
                            result.errors
                        )[0]?.[0]
                        : null;


                throw new Error(
                    firstError
                    ??
                    result.message
                    ??
                    'Failed to cancel exception.'
                );

            }


            cell.dataset.exceptionId =
                '';

        }



        /*
        |--------------------------------------------------------------------------
        | Finish Editing
        |--------------------------------------------------------------------------
        */

        function finishEditing() {

            editing =
                false;


            editBtn.classList.remove(
                'd-none'
            );


            editActions
                .classList
                .add(
                    'd-none'
                );


            editActions
                .classList
                .remove(
                    'd-flex'
                );


            editMessage
                .classList
                .add(
                    'd-none'
                );


            exceptionType.value =
                '';


            cells.forEach(
                function (cell) {

                    cell.style.cursor =
                        'default';

                }
            );

        }



        /*
        |--------------------------------------------------------------------------
        | Messages
        |--------------------------------------------------------------------------
        */

        function hideMessages() {

            errorMessage
                .classList
                .add(
                    'd-none'
                );


            successMessage
                .classList
                .add(
                    'd-none'
                );

        }


        function showError(
            message
        ) {

            errorMessage.textContent =
                message;


            errorMessage
                .classList
                .remove(
                    'd-none'
                );

        }


        function showSuccess(
            message
        ) {

            successMessage.textContent =
                message;


            successMessage
                .classList
                .remove(
                    'd-none'
                );

        }

    }
);

</script>

@endsection