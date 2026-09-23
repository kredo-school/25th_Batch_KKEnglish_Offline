@extends('layouts.app')

@section('title', 'Teacher Details')

@section('content')

    @php

        /*
    |--------------------------------------------------------------------------
    | Controllerから受け取った条件
    |--------------------------------------------------------------------------
    */

        // → 生徒が選んだ日,Selected表示に使用
        $selectedDate = $validated['date'] ?? null;

        // カレンダーの表示開始日
        $viewStart = $validated['view_start'] ?? null;

        /*
    |--------------------------------------------------------------------------
    | 7日間表示
    |--------------------------------------------------------------------------
    |
    | date がある場合
    | → 選択した日から7日間
    |
    | date がない場合
    | → 今日から7日間
    |
    */

        // カレンダーを作る基準日
        $startDate = $viewStart ? \Carbon\Carbon::parse($viewStart)->startOfDay() : now()->startOfDay();

        $days = collect(range(0, 6))->map(function ($i) use ($startDate) {
            $date = $startDate->copy()->addDays($i);

            return [
                'day' => $date->format('D'),

                'date' => $date->format('Y-m-d'),

                'display' => $date->format('m/d'),
            ];
        });

        /*
    |--------------------------------------------------------------------------
    | Previous表示判定
    |--------------------------------------------------------------------------
    */

        $today = now()->startOfDay();

        $canGoPrevious = $startDate->copy()->subDays(7)->gte($today);

        /*
    |--------------------------------------------------------------------------
    | 06:00〜22:00
    | 30分単位
    |--------------------------------------------------------------------------
    */

        $times = [];

        $time = \Carbon\Carbon::createFromTime(6, 0);

        $endTime = \Carbon\Carbon::createFromTime(22, 0);

        while ($time < $endTime) {
            $times[] = $time->format('H:i');

            $time->addMinutes(30);
        }

    @endphp


    <style>
        /*
                        |--------------------------------------------------------------------------
                        | 選択日の色
                        |--------------------------------------------------------------------------
                        */

        .selected-day-header {
            background-color: #e7eef7 !important;
        }

        .selected-day-cell {
            background-color: #f1f5fa !important;
        }


        /*
                        |--------------------------------------------------------------------------
                        | 左右カードの高さ
                        |--------------------------------------------------------------------------
                        */

        .teacher-profile-card,
        .schedule-card {
            height: 750px;
        }


        /*
                        |--------------------------------------------------------------------------
                        | Schedule Card
                        |--------------------------------------------------------------------------
                        */

        .schedule-card {
            overflow: hidden;
        }

        .schedule-card .card-body {
            display: flex;
            flex-direction: column;
            min-height: 0;
        }


        /*
                        |--------------------------------------------------------------------------
                        | Schedule Scroll
                        |--------------------------------------------------------------------------
                        |
                        | 横スクロールなし
                        | 縦スクロールのみ
                        |
                        */

        .schedule-scroll {
            flex: 1;
            min-height: 0;

            overflow-y: auto;
            overflow-x: hidden;
        }


        /*
                        |--------------------------------------------------------------------------
                        | Table
                        |--------------------------------------------------------------------------
                        |
                        | カード幅いっぱいにして
                        | 7日分を均等に表示
                        |
                        */

        .schedule-scroll table {
            width: 100%;
            table-layout: fixed;
        }


        /*
                        |--------------------------------------------------------------------------
                        | Time列
                        |--------------------------------------------------------------------------
                        */

        .schedule-scroll th:first-child,
        .schedule-scroll td:first-child {
            width: 65px;
        }


        /*
                        |--------------------------------------------------------------------------
                        | 7日分
                        |--------------------------------------------------------------------------
                        */

        .schedule-scroll th:not(:first-child),
        .schedule-scroll td:not(:first-child) {
            width: calc((100% - 65px) / 7);
        }


        /*
                        |--------------------------------------------------------------------------
                        | Cell
                        |--------------------------------------------------------------------------
                        */

        .schedule-scroll th,
        .schedule-scroll td {
            padding-left: 4px;
            padding-right: 4px;

            font-size: 0.85rem;

            overflow: hidden;
        }


        /*
                        |--------------------------------------------------------------------------
                        | Book Button
                        |--------------------------------------------------------------------------
                        */

        .schedule-scroll .book-slot-btn {
            padding-left: 3px;
            padding-right: 3px;

            font-size: 0.78rem;
        }


        /*
                        |--------------------------------------------------------------------------
                        | スケジュール表ヘッダー固定
                        |--------------------------------------------------------------------------
                        */

        .schedule-scroll thead th {
            position: sticky;
            top: 0;
            z-index: 3;

            background-color: #f8f9fa;
        }


        /*
                        |--------------------------------------------------------------------------
                        | Material表示
                        |--------------------------------------------------------------------------
                        */

        .material-summary {
            background-color: #f8f9fa;

            border: 1px solid #e9ecef;

            border-radius: 6px;
        }
    </style>



    <div class="container-fluid">

        {{-- ===============================
         Title
    ================================ --}}
        <div class="bg-light px-3 py-2 mb-4">

            <h4 class="mb-0 fw-bold">
                Teacher Details
            </h4>

        </div>


        <div class="row g-4">


            {{-- ===============================
             Left
             Teacher Profile
        ================================ --}}
            <div class="col-lg-4">

                <div class="card teacher-profile-card">

                    <div class="card-body p-4">


                        {{-- Teacher Image --}}
                        <div class="text-center mb-4">

                            <img src="{{ $teacher->user->profile_image }}" alt="{{ $teacher->user->first_name }}"
                                width="120" height="120" class="rounded-circle mb-3" style="object-fit: cover;">

                            {{-- Lesson Point --}}
                            <div class="d-flex justify-content-center align-items-center gap-2 mb-1">

                                <h4 class="fw-bold mb-0">
                                    {{ $teacher->user->first_name }}
                                    {{ $teacher->user->last_name }}
                                </h4>

                                <span class="badge text-dark px-2 py-2"
                                    style="
                            background-color: #f0c94d;
                            font-family: Arial, sans-serif;
                        ">
                                    {{ number_format($teacher->point_consumed ?? 0) }} pt
                                </span>
                            </div>

                            {{-- Rating --}}
                            <div class="
                            d-flex
                            justify-content-center
                            align-items-center
                            gap-1
                            mt-2
                        "
                                style="font-size: 14px;">
                                @if (($teacher->reviews_count ?? 0) > 0)
                                    <i class="fa-solid fa-star text-warning"></i>

                                    <span class="fw-semibold">
                                        {{ number_format($teacher->reviews_avg_rating, 1) }}
                                    </span>

                                    <span class="text-secondary">
                                        ({{ $teacher->reviews_count }})
                                    </span>
                                @else
                                    <span class="text-secondary">
                                        No reviews
                                    </span>
                                @endif
                            </div>



                            <p class="text-secondary mb-0">
                                English Teacher
                            </p>

                        </div>


                        <hr>


                        {{-- Nationality --}}
                        <div class="mb-3">

                            <strong>
                                Nationality
                            </strong>

                            <p class="mb-0">

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

                        </div>


                        {{-- Teaching Experience --}}
                        <div class="mb-3">

                            <strong>
                                Teaching Experience
                            </strong>

                            <p class="mb-0">

                                {{ $teacher->career ?? '-' }}

                            </p>

                        </div>


                        {{-- Specialty --}}
                        <div class="mb-3">

                            <strong>
                                Specialty
                            </strong>

                            <p class="mb-0">

                                {{ $teacher->specialty ?? '-' }}

                            </p>

                        </div>


                        {{-- Certification --}}
                        <div class="mb-3">

                            <strong>
                                Certification
                            </strong>

                            <p class="mb-0">

                                {{ $teacher->certification ?? '-' }}

                            </p>

                        </div>


                        {{-- Graduation School --}}
                        <div class="mb-3">

                            <strong>
                                Graduation School
                            </strong>

                            <p class="mb-0">

                                {{ $teacher->graduation_school ?? '-' }}

                            </p>

                        </div>


                        <hr>


                        {{-- About Me --}}
                        <div>

                            <strong>
                                About Me
                            </strong>

                            <p class="mt-2 mb-0">

                                {{ $teacher->about_me ?? '-' }}

                            </p>

                        </div>

                    </div>

                </div>

            </div>



            {{-- ===============================
             Right
             Weekly Schedule
        ================================ --}}
            <div class="col-lg-8">

                <div class="card schedule-card">

                    <div class="card-body p-4">


                        {{-- Schedule Title --}}
                        <div class="mb-3">

                            <h4 class="fw-bold mb-1">
                                Weekly Schedule
                            </h4>

                            <p class="text-secondary mb-0">
                                Select any available time slot to book a lesson.
                            </p>

                        </div>



                        {{-- ===============================
     Material Selection
================================ --}}
                        <div class="
        material-summary
        px-3
        py-3
        mb-4
    ">

                            <form method="GET" action="{{ route('students.reservations.teacher-detail') }}">

                                {{-- Teacher --}}
                                <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">


                                {{-- Selected Date --}}
                                @if ($selectedDate)
                                    <input type="hidden" name="date" value="{{ $selectedDate }}">
                                @endif


                                {{-- 現在表示中の週 --}}
                                <input type="hidden" name="view_start" value="{{ $startDate->format('Y-m-d') }}">


                                <label for="detailMaterial" class="form-label fw-bold mb-2">
                                    Material
                                </label>


                                <select id="detailMaterial" name="material_id" class="form-select"
                                    onchange="this.form.submit()">

                                    <option value="">
                                        Select Material
                                    </option>


                                    @foreach ($materials as $teacherMaterial)
                                        <option value="{{ $teacherMaterial->material_id }}" @selected($selectedMaterial && $selectedMaterial->material_id === $teacherMaterial->material_id)>
                                            {{ $teacherMaterial->name }}
                                        </option>
                                    @endforeach

                                </select>


                                @if (!$selectedMaterial)
                                    <div class="form-text">
                                        Select a material before booking a lesson.
                                    </div>
                                @endif

                            </form>

                        </div>


                        {{-- ===============================
                         7 Days Navigation
                    ================================ --}}
                        <div
                            class="
                            d-flex
                            justify-content-between
                            align-items-center
                            mb-3
                        ">


                            {{-- Previous --}}
                            @if ($canGoPrevious)
                                <a href="{{ route('students.reservations.teacher-detail', [
                                    'teacher_id' => $teacher->id,

                                    'material_id' => $selectedMaterial?->material_id,

                                    'date' => $selectedDate,

                                    'view_start' => $startDate->copy()->subDays(7)->format('Y-m-d'),

                                    'mode' => $selectedMaterial ? 'material' : 'teacher',
                                ]) }}"
                                    class="
                                    btn
                                    btn-outline-secondary
                                    btn-sm
                                ">

                                    <i
                                        class="
                                        fa-solid
                                        fa-chevron-left
                                        me-1
                                    "></i>

                                    Previous

                                </a>
                            @else
                                <span></span>
                            @endif



                            {{-- Date Range --}}
                            <h5 class="fw-bold mb-0">

                                {{ $startDate->format('M d') }}

                                -

                                {{ $startDate->copy()->addDays(6)->format('M d, Y') }}

                            </h5>



                            {{-- Next --}}
                            <a href="{{ route('students.reservations.teacher-detail', [
                                'teacher_id' => $teacher->id,

                                'material_id' => $selectedMaterial?->material_id,

                                'date' => $selectedDate,

                                'view_start' => $startDate->copy()->addDays(7)->format('Y-m-d'),

                                'mode' => $selectedMaterial ? 'material' : 'teacher',
                            ]) }}"
                                class="
                                btn
                                btn-outline-secondary
                                btn-sm
                            ">

                                Next

                                <i
                                    class="
                                    fa-solid
                                    fa-chevron-right
                                    ms-1
                                "></i>

                            </a>

                        </div>



                        {{-- ===============================
                         Loading
                    ================================ --}}
                        <div id="scheduleLoading" class="text-center py-4">

                            <div class="
                                spinner-border
                                spinner-border-sm
                                text-secondary
                            "
                                role="status">

                                <span class="visually-hidden">
                                    Loading...
                                </span>

                            </div>


                            <p
                                class="
                                text-secondary
                                mt-2
                                mb-0
                            ">
                                Loading schedule...
                            </p>

                        </div>



                        {{-- ===============================
                         Error
                    ================================ --}}
                        <div id="scheduleError"
                            class="
                            alert
                            alert-danger
                            d-none
                        ">

                            Failed to load schedule.

                        </div>



                        {{-- ===============================
                         Weekly Schedule Table
                    ================================ --}}
                        <div id="scheduleTable"
                            class="
                            schedule-scroll
                            d-none
                        ">

                            <table
                                class="
                                table
                                table-bordered
                                text-center
                                align-middle
                                mb-0
                            ">


                                {{-- Header --}}
                                <thead class="table-light">

                                    <tr>

                                        <th>
                                            Time
                                        </th>


                                        @foreach ($days as $day)
                                            @php

                                                $isPast = $day['date'] < now()->format('Y-m-d');

                                                $isToday = $day['date'] === now()->format('Y-m-d');

                                                $isSelected = $selectedDate === $day['date'];

                                            @endphp


                                            <th
                                                class="
                                                @if ($isPast) table-secondary
                                                @elseif ($isSelected)
                                                    selected-day-header @endif
                                            ">

                                                {{-- Status --}}
                                                @if ($isPast)
                                                    <div class="mb-1">

                                                        <span
                                                            class="
                                                            badge
                                                            text-bg-secondary
                                                        ">
                                                            Past
                                                        </span>

                                                    </div>
                                                @elseif ($isSelected)
                                                    <div class="mb-1">

                                                        <span
                                                            class="
                                                            badge
                                                            bg-light
                                                            text-secondary
                                                            border
                                                        ">
                                                            Selected
                                                        </span>

                                                    </div>
                                                @elseif ($isToday)
                                                    <div class="mb-1">

                                                        <span
                                                            class="
                                                            badge
                                                            bg-light
                                                            text-dark
                                                            border
                                                        ">
                                                            Today
                                                        </span>

                                                    </div>
                                                @endif


                                                {{-- Day --}}
                                                <div class="fw-bold">
                                                    {{ $day['day'] }}
                                                </div>


                                                {{-- Date --}}
                                                <small class="text-secondary">
                                                    {{ $day['display'] }}
                                                </small>

                                            </th>
                                        @endforeach

                                    </tr>

                                </thead>



                                {{-- Body --}}
                                <tbody>

                                    @foreach ($times as $time)
                                        <tr>


                                            {{-- Time --}}
                                            <th class="table-light">

                                                {{ $time }}

                                            </th>


                                            @foreach ($days as $day)
                                                @php

                                                    $isPast = $day['date'] < now()->format('Y-m-d');

                                                    $isSelected = $selectedDate === $day['date'];

                                                @endphp


                                                <td id="slot-{{ $day['date'] }}-{{ str_replace(':', '-', $time) }}"
                                                    class="
                                                    schedule-slot

                                                    @if ($isPast) table-secondary
                                                    @elseif ($isSelected)
                                                        selected-day-cell @endif
                                                "
                                                    data-date="{{ $day['date'] }}" data-time="{{ $time }}"
                                                    style="height: 46px;">

                                                    <span class="text-secondary">
                                                        -
                                                    </span>

                                                </td>
                                            @endforeach

                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>

                        </div>



                        {{-- ===============================
                         Legend
                    ================================ --}}
                        <div id="scheduleLegend"
                            class="
                            d-flex
                            gap-3
                            flex-wrap
                            mt-3
                            small
                            d-none
                        ">

                            <div>

                                <span
                                    class="
                                    badge
                                    text-bg-primary
                                ">
                                    Book
                                </span>

                                Available

                            </div>

                            <div class="text-secondary">
                                Already booked = your existing lesson
                            </div>


                            <div class="text-secondary">
                                × = unavailable
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    {{-- =========================================================
     Booking Form
========================================================= --}}
    {{-- Bookボタンを押したらJavaScriptから値を入れて送信する --}}
    <form id="bookingForm" method="POST" action="{{ route('students.reservations.confirm') }}" class="d-none">

        @csrf


        <input type="hidden" name="teacher_id" id="bookingTeacherId" value="{{ $teacher->id }}">


        <input type="hidden" name="material_id" id="bookingMaterialId"
            value="{{ $selectedMaterial?->material_id ?? '' }}">


        <input type="hidden" name="schedule_id" id="bookingScheduleId">


        <input type="hidden" name="start_at" id="bookingStartAt">


        <input type="hidden" name="end_at" id="bookingEndAt">

    </form>



    <script>
        // HTMLが全部読み込まれてからJavaScriptを実行する
        document.addEventListener(
            'DOMContentLoaded',
            async function() {


                /*
                |--------------------------------------------------------------------------
                | Blade → JavaScript
                |--------------------------------------------------------------------------
                */

                // BladeのPHP変数をJavaScriptに渡している
                const teacherId =
                    @json($teacher->id);


                const materialId =
                    @json($selectedMaterial?->material_id);


                // JavaScript配列に変換
                const days =
                    @json($days->pluck('date')->values());



                /*
                |--------------------------------------------------------------------------
                | Elements
                |--------------------------------------------------------------------------
                */

                const loading =
                    document.getElementById(
                        'scheduleLoading'
                    );


                const error =
                    document.getElementById(
                        'scheduleError'
                    );


                const table =
                    document.getElementById(
                        'scheduleTable'
                    );


                const legend =
                    document.getElementById(
                        'scheduleLegend'
                    );



                /*
                |--------------------------------------------------------------------------
                | Helper
                |--------------------------------------------------------------------------
                */

                function getTimeFromDateTime(
                    dateTime
                ) {

                    if (!dateTime) {
                        return null;
                    }


                    const normalized =
                        dateTime.replace(
                            'T',
                            ' '
                        );


                    return normalized.substring(
                        11,
                        16
                    );

                }

                // 日付 + 時間のHTMLのセルを探す
                function getCell(
                    date,
                    time
                ) {

                    const formattedTime =
                        time.replace(
                            ':',
                            '-'
                        );


                    return document.getElementById(
                        `slot-${date}-${formattedTime}`
                    );

                }



                /*
                |--------------------------------------------------------------------------
                | Available
                |--------------------------------------------------------------------------
                */

                function renderAvailableCell(
                    cell,
                    slot
                ) {

                    /*
                     * Materialが選択されているか
                     */
                    const canBook =
                        Boolean(
                            materialId
                        );


                    cell.innerHTML = `

        <button
            type="button"
            class="
                btn
                btn-outline-primary
                btn-sm
                w-100
                book-slot-btn
            "
            data-schedule-id="${slot.schedule_id}"
            data-start-at="${slot.start_at}"
            data-end-at="${slot.end_at}"
            ${canBook ? '' : 'disabled'}
        >
            ${
                canBook
                    ? 'Book'
                    : 'Select Material'
            }
        </button>

    `;
                }


                /*
                |--------------------------------------------------------------------------
                | Unavailable
                |--------------------------------------------------------------------------
                */

                function renderUnavailableCell(
                    cell,
                    slot
                ) {

                    /*
                     * 生徒自身が
                     * 同じ時間帯に予約を持っている
                     */
                    if (
                        slot.student_conflict ===
                        true
                    ) {

                        cell.innerHTML = `

            <span
                class="
                    text-danger
                    small
                "
            >
                Already booked
            </span>

        `;


                        return;
                    }


                    /*
                     * その他の予約不可
                     */
                    cell.innerHTML = `

        <span
            class="
                text-secondary
            "
        >
            ×
        </span>

    `;
                }

                /*
                |--------------------------------------------------------------------------
                | Availability API
                |--------------------------------------------------------------------------
                */

                //AvailabilityControllerを呼び出している
                // AvailabilityController→AvailabilityService→JSONが返ってくる

                async function fetchAvailability(
                    date
                ) {

                    const url =
                        `/students/availability` +
                        `?teacher_id=${encodeURIComponent(
                    teacherId
                )}` +
                        `&date=${encodeURIComponent(
                    date
                )}`;


                    const response =
                        await fetch(
                            url, {
                                headers: {
                                    'Accept': 'application/json',
                                },
                            }
                        );


                    if (!response.ok) {

                        throw new Error(
                            `Availability request failed: ${response.status}`
                        );

                    }


                    return await response.json();

                }



                /*
                |--------------------------------------------------------------------------
                | 1日分表示
                |--------------------------------------------------------------------------
                */

                function renderDay(
                    date,
                    data
                ) {

                    document
                        .querySelectorAll(
                            `.schedule-slot[data-date="${date}"]`
                        )
                        .forEach(function(cell) {

                            const cellDate =
                                cell.dataset.date;

                            const cellTime =
                                cell.dataset.time;

                            const cellDateTime =
                                new Date(
                                    `${cellDate}T${cellTime}:00`
                                );

                            const now =
                                new Date();


                            if (
                                cellDateTime <
                                now
                            ) {

                                cell.classList.add(
                                    'table-secondary'
                                );

                                cell.innerHTML = `
                        <span class="text-secondary small">
                            Past
                        </span>
                    `;

                                return;
                            }

                            cell.innerHTML = `

                        <span
                            class="
                                text-secondary
                            "
                        >
                            ×
                        </span>

                    `;

                        });



                    const slots =
                        data.slots ?? [];


                    slots.forEach(
                        function(slot) {


                            const time =
                                getTimeFromDateTime(
                                    slot.start_at
                                );


                            if (!time) {
                                return;
                            }


                            const cell =
                                getCell(
                                    date,
                                    time
                                );


                            if (!cell) {
                                return;
                            }

                            // Pastのセルは上書きしない
                            if (
                                cell.classList.contains(
                                    'table-secondary'
                                )
                            ) {
                                return;
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | BackendのavailableでBook表示を切り替える
                            |--------------------------------------------------------------------------
                            */

                            if (
                                slot.available ===
                                true
                            ) {

                                renderAvailableCell(
                                    cell,
                                    slot
                                );

                            } else {

                                renderUnavailableCell(
                                    cell,
                                    slot
                                );

                            }

                        }
                    );

                }



                /*
                |--------------------------------------------------------------------------
                | 7日分取得
                |--------------------------------------------------------------------------
                */

                try {

                    const results =
                        await Promise.all(

                            days.map(
                                async function(
                                    date
                                ) {

                                    const data =
                                        await fetchAvailability(
                                            date
                                        );


                                    return {
                                        date: date,

                                        data: data,
                                    };

                                }
                            )

                        );


                    results.forEach(
                        function(
                            result
                        ) {

                            renderDay(
                                result.date,
                                result.data
                            );

                        }
                    );


                    loading
                        .classList
                        .add(
                            'd-none'
                        );


                    table
                        .classList
                        .remove(
                            'd-none'
                        );


                    legend
                        .classList
                        .remove(
                            'd-none'
                        );


                } catch (err) {

                    console.error(
                        err
                    );


                    loading
                        .classList
                        .add(
                            'd-none'
                        );


                    error
                        .classList
                        .remove(
                            'd-none'
                        );


                    error.textContent =
                        'Failed to load teacher availability.';

                }



                /*
                |--------------------------------------------------------------------------
                | Book
                |--------------------------------------------------------------------------
                */

                document.addEventListener(
                    'click',
                    function(event) {


                        const button =
                            event.target.closest(
                                '.book-slot-btn'
                            );


                        if (!button) {
                            return;
                        }

                        if (!materialId) {

                            alert(
                                'Please select a material before booking.'
                            );

                            return;
                        }

                        document
                            .getElementById(
                                'bookingScheduleId'
                            )
                            .value =
                            button
                            .dataset
                            .scheduleId;


                        document
                            .getElementById(
                                'bookingStartAt'
                            )
                            .value =
                            button
                            .dataset
                            .startAt;


                        document
                            .getElementById(
                                'bookingEndAt'
                            )
                            .value =
                            button
                            .dataset
                            .endAt;


                        document
                            .getElementById(
                                'bookingForm'
                            )
                            .submit();

                    }
                );

            }
        );
    </script>

@endsection
