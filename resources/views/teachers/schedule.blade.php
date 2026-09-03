@extends('layouts.app')

@section('title', 'My Schedule')

@section('content')

<div class="container py-4">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">


    {{-- ===============================
         Title
    ================================ --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold mb-0">
            My Schedule
        </h2>

        <button type="button"
                id="editBtn"
                class="btn btn-dark">

            Edit Schedule

        </button>

    </div>


    @php

        use Carbon\Carbon;

        /*
        |--------------------------------------------------------------------------
        | 表示する週
        |--------------------------------------------------------------------------
        |
        | URLに week_start がある
        | → 指定された週を表示
        |
        | ない
        | → 今週を表示
        |
        */

        $startOfWeek =
            request('week_start')
                ? Carbon::parse(
                    request('week_start')
                )->startOfWeek(Carbon::MONDAY)
                : now()->startOfWeek(Carbon::MONDAY);


        /*
        |--------------------------------------------------------------------------
        | 前週・翌週
        |--------------------------------------------------------------------------
        */

        $previousWeek =
            $startOfWeek
                ->copy()
                ->subWeek()
                ->format('Y-m-d');


        $nextWeek =
            $startOfWeek
                ->copy()
                ->addWeek()
                ->format('Y-m-d');


        /*
        |--------------------------------------------------------------------------
        | 月曜日〜日曜日
        |--------------------------------------------------------------------------
        */

        $days = collect(range(0, 6))
            ->map(
                fn ($i) =>
                    $startOfWeek
                        ->copy()
                        ->addDays($i)
            );


        /*
        |--------------------------------------------------------------------------
        | 06:00〜22:00
        | 30分単位
        |--------------------------------------------------------------------------
        */

        $times = collect();

        $time =
            Carbon::createFromTime(6, 0);

        $endTime =
            Carbon::createFromTime(22, 0);


        while ($time < $endTime) {

            $times->push(
                $time->format('H:i')
            );

            $time->addMinutes(30);

        }

    @endphp


    {{-- ===============================
         Week Navigation
    ================================ --}}
    <div class="
        d-flex
        justify-content-between
        align-items-center
        mb-3
    ">

        {{-- Previous Week --}}
        <a
            href="{{ url()->current() }}?week_start={{ $previousWeek }}"
            class="btn btn-outline-secondary btn-sm"
        >

            <i class="fa-solid fa-chevron-left me-1"></i>

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
            href="{{ url()->current() }}?week_start={{ $nextWeek }}"
            class="btn btn-outline-secondary btn-sm"
        >

            Next Week

            <i class="fa-solid fa-chevron-right ms-1"></i>

        </a>

    </div>


    {{-- ===============================
         Message
    ================================ --}}
    <div id="editMessage"
         class="alert alert-warning d-none">

        Click the time slots you are unavailable.

    </div>


    {{-- エラー表示 --}}
    <div id="errorMessage"
         class="alert alert-danger d-none">
    </div>


    {{-- 成功表示 --}}
    <div id="successMessage"
         class="alert alert-success d-none">
    </div>


    {{-- ===============================
         Schedule Table
    ================================ --}}
    <div class="table-responsive">

        <table class="table table-bordered text-center align-middle">

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

                        {{-- 時間 --}}
                        <th class="table-light">

                            {{ $time }}

                        </th>


                        {{-- 1週間分 --}}
                        @foreach ($days as $day)

                            <td
                                class="schedule-cell bg-light"

                                data-date="{{ $day->format('Y-m-d') }}"

                                data-time="{{ $time }}"

                                {{-- 勤務時間内か --}}
                                data-working="false"

                                {{-- TeacherSchedule ID --}}
                                data-schedule-id=""

                                {{-- ScheduleException ID --}}
                                data-exception-id=""

                                {{-- 編集開始時の状態 --}}
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
    <div id="editActions"
         class="
            d-none
            justify-content-end
            align-items-center
            gap-2
            mt-3
         ">


        {{-- 休み理由 --}}
        <select id="exceptionType"
                class="form-select"
                style="max-width: 220px;">

            <option value="">
                Select reason
            </option>

            {{-- GETで取得したExceptionTypeを
                 JavaScriptから追加する --}}

        </select>


        {{-- Cancel --}}
        <button type="button"
                id="cancelBtn"
                class="btn btn-outline-secondary">

            Cancel

        </button>


        {{-- Save --}}
        <button type="button"
                id="saveBtn"
                class="btn btn-dark">

            Save Schedule

        </button>

    </div>

</div>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
         * ===============================
         * Elements
         * ===============================
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
         * ===============================
         * 表示中の週
         * ===============================
         *
         * Bladeで作った
         * $startOfWeek をJSでも使用
         *
         */

        const currentWeekStart =
            '{{ $startOfWeek->format('Y-m-d') }}';



        /*
         * ===============================
         * CSRF Token
         * ===============================
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
         * 閲覧 / 編集モード
         */
        let editing =
            false;


        /*
         * Cancel用
         */
        let originalSchedule =
            [];


        /*
         * ===============================
         * Initial Load
         * ===============================
         */

        loadSchedule();



        /*
         * ===============================
         * GET
         *
         * TeacherSchedule
         * +
         * ScheduleException
         * +
         * ExceptionType
         *
         * を取得
         *
         * 表示中のweek_startも送る
         * ===============================
         */

        async function loadSchedule() {

            hideMessages();


            try {

                const response =
                    await fetch(
                        '/teachers/schedule-exceptions'
                        + '?week_start='
                        + encodeURIComponent(
                            currentWeekStart
                        ),
                        {
                            headers: {

                                'Accept':
                                    'application/json'

                            }
                        }
                    );


                const data =
                    await response.json();


                if (!response.ok) {

                    showError(
                        data.message
                        ?? 'Failed to load schedule.'
                    );

                    return;

                }


                /*
                 * 確定勤務を表示
                 */
                applySchedules(
                    data
                );


                /*
                 * 休み理由をselectへ
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
         * ===============================
         * TeacherScheduleを画面へ反映
         * ===============================
         */

        function applySchedules(
            data
        ) {

            /*
             * 最初に全部勤務外にする
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


                    cell.classList.add(
                        'bg-light'
                    );


                    cell.classList.remove(
                        'table-secondary'
                    );


                    cell.textContent =
                        '';


                    cell.style.cursor =
                        'default';

                }
            );


            /*
             * TeacherSchedule
             */
            data.schedules.forEach(
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
                             * 勤務時間内
                             *
                             * 例:
                             *
                             * 06:00〜12:00
                             *
                             * 06:00 ○
                             * ...
                             * 11:30 ○
                             * 12:00 ×
                             */
                            if (
                                time >= startTime
                                &&
                                time < endTime
                            ) {

                                cell.dataset.working =
                                    'true';


                                /*
                                 * APIの主キー名に合わせる
                                 */
                                cell.dataset.scheduleId =
                                    schedule
                                        .schedule_id;


                                cell.classList.remove(
                                    'bg-light'
                                );

                            }

                        }
                    );


                    /*
                     * 登録済み休みを表示
                     */
                    applyExceptions(
                        schedule,
                        date
                    );

                }
            );

        }



        /*
         * ===============================
         * Existing Exceptions
         * ===============================
         */

        function applyExceptions(
            schedule,
            date
        ) {

            if (
                !schedule.exceptions
            ) {

                return;

            }


            schedule.exceptions.forEach(
                function (exception) {


                    /*
                     * cancelledは表示しない
                     */
                    if (
                        exception.status
                        &&
                        exception.status
                            !== 'active'
                    ) {

                        return;

                    }


                    /*
                     * start_at
                     *
                     * 2026-09-03 07:00:00
                     *
                     * ↓
                     *
                     * 07:00
                     */
                    const startAt =
                        exception
                            .start_at;


                    const time =
                        startAt
                            .substring(
                                11,
                                16
                            );


                    const cell =
                        document
                            .querySelector(
                                '.schedule-cell'
                                + `[data-date="${date}"]`
                                + `[data-time="${time}"]`
                            );


                    if (!cell) {

                        return;

                    }


                    cell.classList.add(
                        'table-secondary'
                    );


                    cell.textContent =
                        'Unavailable';


                    cell.dataset.exceptionId =
                        exception.id
                        ??
                        exception
                            .schedule_exception_id;


                    cell.dataset.originalUnavailable =
                        'true';

                }
            );

        }



        /*
         * ===============================
         * Exception Types
         * ===============================
         */

        function applyExceptionTypes(
            types
        ) {

            exceptionType.innerHTML =
                '<option value="">'
                + 'Select reason'
                + '</option>';


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
         * ===============================
         * Edit Schedule
         * ===============================
         */

        editBtn.addEventListener(
            'click',
            function () {

                editing =
                    true;


                hideMessages();


                /*
                 * Cancel用
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

                            text:
                                cell.textContent,

                            exceptionId:
                                cell.dataset
                                    .exceptionId,

                            originalUnavailable:
                                cell.dataset
                                    .originalUnavailable

                        });

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


                /*
                 * 勤務時間内だけ
                 * pointerにする
                 */
                cells.forEach(
                    function (cell) {

                        if (
                            cell.dataset
                                .working
                            === 'true'
                        ) {

                            cell.style.cursor =
                                'pointer';

                        }

                    }
                );

            }
        );



        /*
         * ===============================
         * Cell Click
         * ===============================
         */

        cells.forEach(
            function (cell) {

                cell.addEventListener(
                    'click',
                    function () {


                        /*
                         * 閲覧モード
                         */
                        if (!editing) {

                            return;

                        }


                        /*
                         * 勤務時間外
                         */
                        if (
                            cell.dataset
                                .working
                            !== 'true'
                        ) {

                            return;

                        }


                        /*
                         * Available
                         * ⇅
                         * Unavailable
                         */
                        cell.classList.toggle(
                            'table-secondary'
                        );


                        if (
                            cell
                                .classList
                                .contains(
                                    'table-secondary'
                                )
                        ) {

                            cell.textContent =
                                'Unavailable';

                        } else {

                            cell.textContent =
                                '';

                        }

                    }
                );

            }
        );



        /*
         * ===============================
         * Cancel
         * ===============================
         */

        cancelBtn.addEventListener(
            'click',
            function () {

                originalSchedule.forEach(
                    function (item) {

                        item.cell.className =
                            item.className;


                        item.cell.textContent =
                            item.text;


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
         * ===============================
         * Save Schedule
         * ===============================
         */

        saveBtn.addEventListener(
            'click',
            async function () {

                hideMessages();


                /*
                 * 休み理由
                 */
                const exceptionTypeId =
                    exceptionType.value;


                /*
                 * 変更されたセル
                 */
                const createCells =
                    [];


                const cancelCells =
                    [];


                cells.forEach(
                    function (cell) {

                        const wasUnavailable =
                            cell.dataset
                                .originalUnavailable
                            === 'true';


                        const isUnavailable =
                            cell
                                .classList
                                .contains(
                                    'table-secondary'
                                );


                        /*
                         * Available
                         * ↓
                         * Unavailable
                         *
                         * POST
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
                         *
                         * DELETE
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
                 * 新しい休みを登録する場合
                 * 理由必須
                 */
                if (
                    createCells.length > 0
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
                    createCells.length === 0
                    &&
                    cancelCells.length === 0
                ) {

                    finishEditing();

                    return;

                }


                /*
                 * Saveボタン連打防止
                 */
                saveBtn.disabled =
                    true;


                try {

                    /*
                     * 新規Unavailable
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
                     * Unavailable解除
                     */
                    for (
                        const cell
                        of cancelCells
                    ) {

                        await cancelException(
                            cell
                        );

                    }


                    showSuccess(
                        'Schedule updated successfully.'
                    );


                    /*
                     * DB最新状態を再取得
                     */
                    await loadSchedule();


                    finishEditing();


                } catch (error) {

                    console.error(
                        error
                    );


                    showError(
                        error.message
                        ?? 'Failed to save schedule.'
                    );


                } finally {

                    saveBtn.disabled =
                        false;

                }

            }
        );



        /*
         * ===============================
         * POST
         *
         * ScheduleException登録
         * ===============================
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
             * 30分後をend_atにする
             */
            const start =
                new Date(
                    `${date}T${time}:00`
                );


            const end =
                new Date(
                    start.getTime()
                    +
                    30 * 60 * 1000
                );


            const endHour =
                String(
                    end.getHours()
                ).padStart(
                    2,
                    '0'
                );


            const endMinute =
                String(
                    end.getMinutes()
                ).padStart(
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
                                token

                        },

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
                                    `${date} ${endTime}`

                            })

                    }
                );


            const result =
                await response.json();


            if (!response.ok) {

                throw new Error(
                    result.message
                    ?? 'Failed to create exception.'
                );

            }


            /*
             * DBで作成された
             * ScheduleException ID
             */
            cell.dataset.exceptionId =
                result.exception.id
                ??
                result.exception
                    .schedule_exception_id;

        }



        /*
         * ===============================
         * DELETE
         *
         * ScheduleException解除
         * ===============================
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
                    '/teachers/'
                    + 'schedule-exceptions/'
                    + exceptionId,
                    {
                        method:
                            'DELETE',

                        headers: {

                            'Accept':
                                'application/json',

                            'X-CSRF-TOKEN':
                                token

                        }

                    }
                );


            const result =
                await response.json();


            if (!response.ok) {

                throw new Error(
                    result.message
                    ?? 'Failed to cancel exception.'
                );

            }


            cell.dataset.exceptionId =
                '';

        }



        /*
         * ===============================
         * Finish Editing
         * ===============================
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
         * ===============================
         * Messages
         * ===============================
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