@extends('layouts.app')

@section('title', 'Teacher Details')

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | URLから受け取る条件
    |--------------------------------------------------------------------------
    |
    | 例:
    | ?teacher_id=2
    | &material_id=1
    | &date=2026-09-10
    |
    */

    $teacherId = request('teacher_id');
   $materialId = request('material_id') ?? request('material');
    $selectedDate = request('date');

    /*
    |--------------------------------------------------------------------------
    | ダミーTeacher Profile
    |--------------------------------------------------------------------------
    |
    | 先生情報は後でControllerから
    | $teacher を受け取る形に変更予定
    |
    */

    $teacherName = 'John Smith';
    $teacherNationality = 'Philippines';
    $teacherCareer = '5 years';
    $teacherSpecialty = 'Daily Conversation';
    $teacherCertification = 'TESOL';
    $teacherGraduationSchool = 'Cebu Normal University';

    $teacherAboutMe =
        "Hello! I'm John. I enjoy helping students improve "
        . "their English through practical conversation lessons.";

    /*
    |--------------------------------------------------------------------------
    | Material
    |--------------------------------------------------------------------------
    |
    | 今は表示確認用
    | material_id自体はURLから受け取る
    |
    */

    $selectedMaterial = 'Daily Conversation';

    /*
    |--------------------------------------------------------------------------
    | 週間表示の開始日
    |--------------------------------------------------------------------------
    |
    | selectedDateがある場合:
    | その日を含む週の月曜日
    |
    | selectedDateがない場合:
    | 今週の月曜日
    |
    */

    $baseDate = $selectedDate
        ? \Carbon\Carbon::parse($selectedDate)
        : now();

    $startOfWeek = $baseDate
        ->copy()
        ->startOfWeek(\Carbon\Carbon::MONDAY);

    $days = collect(range(0, 6))
        ->map(function ($i) use ($startOfWeek) {

            $date = $startOfWeek
                ->copy()
                ->addDays($i);

            return [
                'day' => $date->format('D'),
                'date' => $date->format('Y-m-d'),
                'display' => $date->format('m/d'),
            ];
        });

    /*
    |--------------------------------------------------------------------------
    | 時間
    |--------------------------------------------------------------------------
    |
    | 現在は06:00〜22:00を30分刻みで表示
    |
    */

    $times = [];

    $time = \Carbon\Carbon::createFromTime(6, 0);
    $endTime = \Carbon\Carbon::createFromTime(22, 0);

    while ($time < $endTime) {

        $times[] = $time->format('H:i');

        $time->addMinutes(30);
    }
@endphp

<div class="alert alert-warning">
    teacher_id: {{ $teacherId ?? 'null' }}<br>
    material_id: {{ $materialId ?? 'null' }}<br>
    date: {{ $selectedDate ?? 'null' }}
</div>


<div class="container-fluid py-4">

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

            <div class="card">

                <div class="card-body p-4">

                    {{-- Teacher Image --}}
                    <div class="text-center mb-4">

                        <img
                            src="{{ asset('images/teacher1.jpg') }}"
                            alt="{{ $teacherName }}"
                            class="rounded-circle mb-3"
                            width="120"
                            height="120"
                            style="object-fit: cover;"
                        >

                        <h4 class="fw-bold mb-1">
                            {{ $teacherName }}
                        </h4>

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
                            {{ $teacherNationality }}
                        </p>

                    </div>


                    {{-- Teaching Experience --}}
                    <div class="mb-3">

                        <strong>
                            Teaching Experience
                        </strong>

                        <p class="mb-0">
                            {{ $teacherCareer }}
                        </p>

                    </div>


                    {{-- Specialty --}}
                    <div class="mb-3">

                        <strong>
                            Specialty
                        </strong>

                        <p class="mb-0">
                            {{ $teacherSpecialty }}
                        </p>

                    </div>


                    {{-- Certification --}}
                    <div class="mb-3">

                        <strong>
                            Certification
                        </strong>

                        <p class="mb-0">
                            {{ $teacherCertification }}
                        </p>

                    </div>


                    {{-- Graduation School --}}
                    <div class="mb-3">

                        <strong>
                            Graduation School
                        </strong>

                        <p class="mb-0">
                            {{ $teacherGraduationSchool }}
                        </p>

                    </div>


                    <hr>


                    {{-- About Me --}}
                    <div>

                        <strong>
                            About Me
                        </strong>

                        <p class="mt-2 mb-0">
                            {{ $teacherAboutMe }}
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

            <div class="card">

                <div class="card-body p-4">

                    {{-- Schedule Title --}}
                    <div class="mb-3">

                        <h4 class="fw-bold mb-1">
                            Weekly Schedule
                        </h4>

                        <p class="text-secondary mb-0">
                            You can view this teacher's availability for the week.
                        </p>

                    </div>


                    {{-- ===============================
                         Selected Conditions
                    ================================ --}}
                    <div class="alert alert-info">

                        <div class="row g-2">

                            {{-- Date --}}
                            <div class="col-md-6">

                                <span class="text-secondary">
                                    Selected Date:
                                </span>

                                @if ($selectedDate)

                                    <strong>
                                        {{
                                            \Carbon\Carbon::parse(
                                                $selectedDate
                                            )->format('M d, Y')
                                        }}
                                    </strong>

                                @else

                                    <span class="text-secondary">
                                        Not selected
                                    </span>

                                @endif

                            </div>


                            {{-- Material --}}
                            <div class="col-md-6">

                                <span class="text-secondary">
                                    Material:
                                </span>

                                <strong>
                                    {{ $selectedMaterial }}
                                </strong>

                            </div>

                        </div>

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
                            href="{{ route(
                                'reservations.teacher-detail.test',
                                [
                                    'teacher_id' => $teacherId,
                                    'material_id' => $materialId,
                                    'date' => $startOfWeek
                                        ->copy()
                                        ->subWeek()
                                        ->format('Y-m-d'),
                                ]
                            ) }}"
                            class="btn btn-outline-secondary btn-sm"
                        >
                            &lt; Previous
                        </a>


                        {{-- Week Range --}}
                        <h5 class="fw-bold mb-0">

                            {{ $days->first()['display'] }}

                            -

                            {{ $days->last()['display'] }}

                            {{ $startOfWeek->format('Y') }}

                        </h5>


                        {{-- Next Week --}}
                        <a
                            href="{{ route(
                                'reservations.teacher-detail.test',
                                [
                                    'teacher_id' => $teacherId,
                                    'material_id' => $materialId,
                                    'date' => $startOfWeek
                                        ->copy()
                                        ->addWeek()
                                        ->format('Y-m-d'),
                                ]
                            ) }}"
                            class="btn btn-outline-secondary btn-sm"
                        >
                            Next &gt;
                        </a>

                    </div>


                    {{-- ===============================
                         Loading
                    ================================ --}}
                    <div
                        id="scheduleLoading"
                        class="text-center py-4"
                    >

                        <div
                            class="spinner-border text-primary"
                            role="status"
                        >
                            <span class="visually-hidden">
                                Loading...
                            </span>
                        </div>

                        <p class="text-secondary mt-2 mb-0">
                            Loading schedule...
                        </p>

                    </div>


                    {{-- ===============================
                         Error
                    ================================ --}}
                    <div
                        id="scheduleError"
                        class="alert alert-danger d-none"
                    >
                        Failed to load schedule.
                    </div>


                    {{-- ===============================
                         Weekly Schedule Table
                    ================================ --}}
                    <div
                        id="scheduleTable"
                        class="table-responsive d-none"
                    >

                        <table
                            class="
                                table
                                table-bordered
                                text-center
                                align-middle
                            "
                        >

                            {{-- Header --}}
                            <thead class="table-light">

                                <tr>

                                    <th style="min-width: 85px;">
                                        Time
                                    </th>


                                    @foreach ($days as $day)

                                        @php
                                            $isPast =
                                                $day['date']
                                                < now()->format('Y-m-d');

                                            $isToday =
                                                $day['date']
                                                === now()->format('Y-m-d');

                                            $isSelected =
                                                $selectedDate
                                                === $day['date'];
                                        @endphp


                                        <th
                                            class="
                                                @if ($isPast)
                                                    table-secondary
                                                @elseif ($isSelected)
                                                    table-info
                                                @endif
                                            "
                                            style="min-width: 105px;"
                                        >

                                            <div class="fw-bold">
                                                {{ $day['day'] }}
                                            </div>

                                            <small class="text-secondary">
                                                {{ $day['display'] }}
                                            </small>


                                            @if ($isPast)

                                                <div class="mt-1">

                                                    <span
                                                        class="
                                                            badge
                                                            text-bg-secondary
                                                        "
                                                    >
                                                        Past
                                                    </span>

                                                </div>

                                            @elseif ($isSelected)

                                                <div class="mt-1">

                                                    <span
                                                        class="
                                                            badge
                                                            text-bg-primary
                                                        "
                                                    >
                                                        Selected
                                                    </span>

                                                </div>

                                            @elseif ($isToday)

                                                <div class="mt-1">

                                                    <span
                                                        class="
                                                            badge
                                                            text-bg-dark
                                                        "
                                                    >
                                                        Today
                                                    </span>

                                                </div>

                                            @endif

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
                                                $isPast =
                                                    $day['date']
                                                    < now()->format('Y-m-d');

                                                $isSelected =
                                                    $selectedDate
                                                    === $day['date'];
                                            @endphp


                                            <td
                                                id="slot-{{ $day['date'] }}-{{ str_replace(':', '-', $time) }}"
                                                class="
                                                    schedule-slot

                                                    @if ($isPast)
                                                        table-secondary
                                                    @elseif ($isSelected)
                                                        table-info
                                                    @endif
                                                "
                                                data-date="{{ $day['date'] }}"
                                                data-time="{{ $time }}"
                                            >

                                                @if ($isPast)

                                                    <span class="text-secondary">
                                                        -
                                                    </span>

                                                @else

                                                    <span class="text-secondary">
                                                        -
                                                    </span>

                                                @endif

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
                    <div
                        id="scheduleLegend"
                        class="
                            d-flex
                            gap-3
                            flex-wrap
                            mt-3
                            small
                            d-none
                        "
                    >

                        <div>

                            <span class="badge text-bg-primary">
                                Book
                            </span>

                            Available for booking

                        </div>


                        <div>

                            <span class="badge text-bg-success">
                                OPEN
                            </span>

                            Available on another day

                        </div>


                        <div class="text-secondary">
                            Reserved = already booked
                        </div>


                        <div class="text-secondary">
                            × = outside working hours
                        </div>


                        <div class="text-secondary">
                            Gray = past date
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     Booking Form
     Bookを押したときにBooking Confirmationへ送る
========================================================= --}}
<form
    id="bookingForm"
    method="POST"
    action="{{ route('students.reservations.confirm') }}"
    class="d-none"
>

    @csrf

    <input
        type="hidden"
        name="teacher_id"
        id="bookingTeacherId"
        value="{{ $teacherId }}"
    >

    <input
        type="hidden"
        name="material_id"
        id="bookingMaterialId"
        value="{{ $materialId }}"
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


<script>
document.addEventListener('DOMContentLoaded', async function () {

    /*
    |--------------------------------------------------------------------------
    | BladeからJavaScriptへ
    |--------------------------------------------------------------------------
    */

    const teacherId = @json($teacherId);
    const materialId = @json($materialId);
    const selectedDate = @json($selectedDate);

    const days = @json(
        $days->pluck('date')->values()
    );


    /*
    |--------------------------------------------------------------------------
    | Elements
    |--------------------------------------------------------------------------
    */

    const loading =
        document.getElementById('scheduleLoading');

    const error =
        document.getElementById('scheduleError');

    const table =
        document.getElementById('scheduleTable');

    const legend =
        document.getElementById('scheduleLegend');


    /*
    |--------------------------------------------------------------------------
    | teacher_idがない場合
    |--------------------------------------------------------------------------
    */

    if (!teacherId) {

        loading.classList.add('d-none');

        error.classList.remove('d-none');

        error.textContent =
            'Teacher information is not available.';

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    function getTimeFromDateTime(dateTime) {

        if (!dateTime) {
            return null;
        }

        /*
         * APIの値が
         *
         * 2026-09-10 09:00:00
         *
         * または
         *
         * 2026-09-10T09:00:00...
         *
         * のどちらでも対応
         */

        const normalized =
            dateTime.replace('T', ' ');

        return normalized.substring(11, 16);
    }


    function getCell(date, time) {

        const formattedTime =
            time.replace(':', '-');

        return document.getElementById(
            `slot-${date}-${formattedTime}`
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Cell表示
    |--------------------------------------------------------------------------
    */

    function renderAvailableCell(
        cell,
        slot,
        date
    ) {

        /*
         * 選択日がある場合
         *
         * → 選択日だけBook可能
         *
         * 選択日がない場合
         *
         * → OPEN枠すべてBook可能
         */

        const canBook =
            !selectedDate
            || selectedDate === date;


        if (canBook) {

            cell.innerHTML = `
                <button
                    type="button"
                    class="
                        btn
                        btn-primary
                        btn-sm
                        w-100
                        book-slot-btn
                    "
                    data-schedule-id="${slot.schedule_id}"
                    data-start-at="${slot.start_at}"
                    data-end-at="${slot.end_at}"
                >
                    Book
                </button>
            `;

        } else {

            cell.innerHTML = `
                <span class="badge text-bg-success">
                    OPEN
                </span>
            `;

        }
    }


    function renderUnavailableCell(
        cell,
        slot
    ) {

        /*
         * student_conflictが
         * Availability APIに追加された場合
         *
         * 自分が同じ時間に
         * 別の先生を予約済みなら
         *
         * Already booked
         */

        if (slot.student_conflict === true) {

            cell.innerHTML = `
                <span class="text-danger small">
                    Already booked
                </span>
            `;

            return;
        }


        /*
         * 現状APIでは
         * available=falseの場合、
         *
         * 予約済み / Exception
         *
         * の区別が付かないため
         * Reservedとして表示
         */

        cell.innerHTML = `
            <span class="text-secondary small">
                Reserved
            </span>
        `;
    }


    /*
    |--------------------------------------------------------------------------
    | 1日分のAvailability取得
    |--------------------------------------------------------------------------
    */

    async function fetchAvailability(date) {

        const url =
            `/students/availability`
            + `?teacher_id=${encodeURIComponent(teacherId)}`
            + `&date=${encodeURIComponent(date)}`;


        const response =
            await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                },
            });


        if (!response.ok) {

            throw new Error(
                `Availability request failed: ${response.status}`
            );

        }


        return await response.json();
    }


    /*
    |--------------------------------------------------------------------------
    | 1日分を画面に反映
    |--------------------------------------------------------------------------
    */

    function renderDay(
        date,
        data
    ) {

        /*
         * 一旦その日の未来セルを
         * working hours外として × にする
         *
         * APIから返ってきたSlotだけ
         * あとで上書きする
         */

        document
            .querySelectorAll(
                `.schedule-slot[data-date="${date}"]`
            )
            .forEach(function (cell) {

                const cellDate =
                    cell.dataset.date;

                const today =
                    new Date()
                        .toLocaleDateString(
                            'en-CA'
                        );


                if (cellDate < today) {
                    return;
                }


                cell.innerHTML = `
                    <span class="text-secondary">
                        ×
                    </span>
                `;

            });


        /*
         * API response:
         *
         * {
         *   teacher_id: ...,
         *   date: ...,
         *   slots: [...]
         * }
         */

        const slots =
            data.slots ?? [];


        slots.forEach(function (slot) {

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


            if (slot.available === true) {

                renderAvailableCell(
                    cell,
                    slot,
                    date
                );

            } else {

                renderUnavailableCell(
                    cell,
                    slot
                );

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | 1週間分を取得
    |--------------------------------------------------------------------------
    */

    try {

        /*
         * 7日分を同時に取得
         */

        const results =
            await Promise.all(

                days.map(
                    async function (date) {

                        const data =
                            await fetchAvailability(
                                date
                            );

                        return {
                            date,
                            data,
                        };

                    }
                )

            );


        results.forEach(
            function (result) {

                renderDay(
                    result.date,
                    result.data
                );

            }
        );


        loading.classList.add('d-none');

        table.classList.remove('d-none');

        legend.classList.remove('d-none');


    } catch (err) {

        console.error(err);

        loading.classList.add('d-none');

        error.classList.remove('d-none');

        error.textContent =
            'Failed to load teacher availability.';

    }


    /*
    |--------------------------------------------------------------------------
    | Book Button
    |--------------------------------------------------------------------------
    |
    | BookボタンはAPI取得後に生成されるため、
    | event delegationを使用
    |
    */

    document.addEventListener(
        'click',
        function (event) {

            const button =
                event.target.closest(
                    '.book-slot-btn'
                );


            if (!button) {
                return;
            }


            /*
             * material_idがない場合は
             * Confirmationへ送れない
             */

            if (!materialId) {

                alert(
                    'Please select a material first.'
                );

                return;
            }


            /*
             * APIから受け取った値を
             * hidden inputにセット
             */

            document.getElementById(
                'bookingScheduleId'
            ).value =
                button.dataset.scheduleId;


            document.getElementById(
                'bookingStartAt'
            ).value =
                button.dataset.startAt;


            document.getElementById(
                'bookingEndAt'
            ).value =
                button.dataset.endAt;


            /*
             * 既存Booking Confirmationへ
             */

            document
                .getElementById(
                    'bookingForm'
                )
                .submit();

        }
    );

});
</script>

@endsection
