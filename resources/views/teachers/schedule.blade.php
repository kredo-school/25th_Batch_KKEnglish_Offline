@extends('layouts.app')

@section('title', 'My Schedule')

@section('content')

<div class="container py-4">

    {{-- タイトル --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">My Schedule</h2>

        <button id="editBtn" class="btn btn-dark">
            Edit Schedule
        </button>
    </div>


    {{-- $days / $times を作る処理は最終的にControllerへ移す --}}
    @php
        use Carbon\Carbon;

        // 表示確認用
        $startOfWeek = now()->startOfWeek(Carbon::MONDAY);

        $days = collect(range(0, 6))
            ->map(fn ($i) => $startOfWeek->copy()->addDays($i));

        $times = range(9, 18);
    @endphp


    {{-- 週表示 --}}
    <div class="text-center mb-3">
        <h5 class="fw-bold">
            {{ $days->first()->format('M d') }}
            -
            {{ $days->last()->format('M d, Y') }}
        </h5>
    </div>


    {{-- 編集中メッセージ --}}
    <div id="editMessage"
         class="alert alert-warning d-none">

        Click the time slots you are unavailable.

    </div>


    {{-- スケジュール --}}
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
                            {{ sprintf('%02d:00', $time) }}
                        </th>


                        {{-- 1週間分のセル --}}
                        @foreach ($days as $day)

                            <td
                                class="schedule-cell"
                                data-date="{{ $day->format('Y-m-d') }}"
                                data-time="{{ sprintf('%02d:00', $time) }}"
                                style="height: 60px;">
                            </td>

                        @endforeach

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>


    {{-- 編集モードのボタン --}}
    <div id="editActions"
         class="d-none justify-content-end gap-2 mt-3">

        <button id="cancelBtn"
                class="btn btn-outline-secondary">

            Cancel

        </button>

        <button id="saveBtn"
                class="btn btn-dark">

            Save Schedule

        </button>

    </div>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const editBtn = document.getElementById('editBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const saveBtn = document.getElementById('saveBtn');

    const editActions = document.getElementById('editActions');
    const editMessage = document.getElementById('editMessage');

    const cells = document.querySelectorAll('.schedule-cell');


    // 閲覧モード / 編集モード
    let editing = false;


    // 編集前の状態を保存
    let originalSchedule = [];


    // =====================================
    // Edit Schedule
    // =====================================
    editBtn.addEventListener('click', function () {

        editing = true;


        // Cancel用に現在の状態を保存
        originalSchedule = [];

        cells.forEach(function (cell) {

            originalSchedule.push({
                cell: cell,
                className: cell.className,
                text: cell.textContent
            });

        });


        editBtn.classList.add('d-none');

        editActions.classList.remove('d-none');
        editActions.classList.add('d-flex');

        editMessage.classList.remove('d-none');

    });


    // =====================================
    // セルをクリック
    // =====================================
    cells.forEach(function (cell) {

        cell.addEventListener('click', function () {

            // 閲覧モードでは何もしない
            if (!editing) {
                return;
            }


            // Unavailableの切り替え
            cell.classList.toggle('table-secondary');


            if (cell.classList.contains('table-secondary')) {

                cell.textContent = 'Unavailable';

            } else {

                cell.textContent = '';

            }

        });

    });


    // =====================================
    // Cancel
    // =====================================
    cancelBtn.addEventListener('click', function () {

        originalSchedule.forEach(function (item) {

            item.cell.className = item.className;

            item.cell.textContent = item.text;

        });


        finishEditing();

    });


    // =====================================
    // Save Schedule
    // =====================================
    saveBtn.addEventListener('click', function () {

        const schedules = [];


        document
            .querySelectorAll('.schedule-cell.table-secondary')
            .forEach(function (cell) {

                schedules.push({

                    date: cell.dataset.date,

                    time: cell.dataset.time,

                    status: 'unavailable'

                });

            });


        // 今は確認用
        // 後でControllerへ送信してDB更新する
        console.log(schedules);


        /*
            後でバックエンドへ送る想定

            例：

            [
                {
                    date: "2026-08-26",
                    time: "10:00",
                    status: "unavailable"
                },
                {
                    date: "2026-08-26",
                    time: "11:00",
                    status: "unavailable"
                }
            ]

        */


        finishEditing();

    });


    // =====================================
    // 編集終了
    // =====================================
    function finishEditing() {

        editing = false;


        editBtn.classList.remove('d-none');

        editActions.classList.add('d-none');
        editActions.classList.remove('d-flex');

        editMessage.classList.add('d-none');

    }

});

</script>

@endsection