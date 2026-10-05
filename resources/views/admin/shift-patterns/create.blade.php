@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Shift Pattern</h1>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.shift-patterns.store') }}">
        @csrf

        <div class="mb-3">
            <label>Pattern Code</label>
            <input type="text" name="pattern_code" class="form-control" value="{{ old('pattern_code') }}" required>
        </div>

        <div class="mb-3">
            <label>Pattern Name</label>
            <input type="text" name="pattern_name" class="form-control" value="{{ old('pattern_name') }}" required>
        </div>

        <div class="row">
            <div class="col-md-3 mb-3">
                <label>Start Time</label>
                <input type="text" name="start_time" class="form-control time-input" value="{{ old('start_time') }}" placeholder="--:--"
                inputmode="numeric"
                pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$"
                maxlength="5"
                list="time-options" required>
            </div>
            <div class="col-md-3 mb-3">
                <label>End Time</label>
                <input type="text" name="end_time" class="form-control time-input" value="{{ old('end_time') }}" placeholder="--:--"
                inputmode="numeric"
                pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$"
                maxlength="5"
                list="time-options" required>
            </div>
            <div class="col-md-3 mb-3">
                <label>End Day Offset</label>
                <input type="number" min="0" max="1" name="end_day_offset" class="form-control" value="{{ old('end_day_offset', 0) }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label>slot_minutes</label>
                <select name="slot_minutes" class="form-control" required>
                    <option value="30" @selected(old('slot_minutes', 30)==30)>30</option>
                    <option value="60" @selected(old('slot_minutes')==60)>60</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label>Display Order</label>
            <input type="number" min="0" name="display_order" class="form-control" value="{{ old('display_order', 0) }}">
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', 1))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>

        {{-- <hr>
        <h3>勤務曜日（共通時間）</h3>
        <p class="text-muted mb-2">曜日を選ぶだけ。時間は1回入力で全選択曜日に適用します。</p>

        <div class="mb-3">
            @php
                $weekdayLabels = [0=>'Sun',1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat'];
                $oldWeekdays = old('weekdays', [1,2,3,4,5]);
            @endphp
            @foreach($weekdayLabels as $num => $label)
                <label class="me-3">
                    <input type="checkbox" name="weekdays[]" value="{{ $num }}"
                        @checked(collect($oldWeekdays)->map(fn($v)=>(int)$v)->contains($num))>
                    {{ $label }}
                </label>
            @endforeach
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <label>勤務 開始</label>
                <input type="time" name="common_rule_start_time" class="form-control"
                       value="{{ old('common_rule_start_time', '09:00') }}" required>
            </div>
            <div class="col-md-4">
                <label>勤務 終了</label>
                <input type="time" name="common_rule_end_time" class="form-control"
                       value="{{ old('common_rule_end_time', '18:00') }}" required>
            </div>
            <div class="col-md-4">
                <label>Lesson Type</label>
                <select name="common_rule_lesson_type" class="form-control" required>
                    <option value="online" @selected(old('common_rule_lesson_type')==='online')>online</option>
                    <option value="in_person" @selected(old('common_rule_lesson_type')==='in_person')>in_person</option>
                    <option value="both" @selected(old('common_rule_lesson_type','both')==='both')>both</option>
                </select>
            </div>
        </div> --}}
        <datalist id="time-options">
            <option value="06:00">
            <option value="06:30">
            <option value="07:00">
            <option value="07:30">
            <option value="08:00">
            <option value="08:30">
            <option value="09:00">
            <option value="09:30">
            <option value="10:00">
            <option value="10:30">
            <option value="11:00">
            <option value="11:30">
            <option value="12:00">
            <option value="12:30">
            <option value="13:00">
            <option value="13:30">
            <option value="14:00">
            <option value="14:30">
            <option value="15:00">
            <option value="15:30">
            <option value="16:00">
            <option value="16:30">
            <option value="17:00">
            <option value="17:30">
            <option value="18:00">
            <option value="18:30">
            <option value="19:00">
            <option value="19:30">
            <option value="20:00">
            <option value="20:30">
            <option value="21:00">
            <option value="21:30">
            <option value="22:00">
            <option value="22:30">
            <option value="23:00">
            <option value="23:30">
            <option value="00:00">
        </datalist>

        <hr>

        <h3>Break</h3>
        <!-- 追加される行を囲むコンテナを用意 -->
        <div id="breaks-container">
            <div class="row mb-4">
                <div class="col-md-3">
                    <label>Break Start</label>
                    <input type="text" name="breaks[0][start_time]" class="form-control time-input"
                        value="{{ old('breaks.0.start_time', '--:--') }}"
                        placeholder="13:00"
                        inputmode="numeric"
                        pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$"
                        maxlength="5"
                        list="time-options">
                </div>
                <div class="col-md-3">
                    <label>Break End</label>
                    <input type="text" name="breaks[0][end_time]" class="form-control time-input"
                        value="{{ old('breaks.0.end_time', '--:--') }}"
                        placeholder="14:00"
                        inputmode="numeric"
                        pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$"
                        maxlength="5"
                        list="time-options">
                </div>
                <div class="col-md-4">
                    <label>Reason</label>
                    <input type="text" name="breaks[0][reason]" class="form-control"
                        value="{{ old('breaks.0.reason') }}">
                </div>
                <!-- 削除ボタン用のスペース（最初の行は不要なら空のままでOK） -->
                <div class="col-md-2 d-flex align-items-end"></div>
            </div>
        </div>
            <small class="text-muted mt-2">
                ※If you do not use a break, leave the start and end times blank.
            </small>
            <div class="col-md-4">
                        <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="add-break"><i class="fa-solid fa-plus me-1"></i>Add Break</button>
            </div>
        </div> {{-- #breaks-container --}}

        <div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Save</button>
            <a href="{{ route('admin.shift-patterns.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
(() => {
    // テーブルではなくコンテナのIDを取得
    const breaksContainer = document.getElementById('breaks-container');
    const addBreakButton = document.getElementById('add-break');
    // Add Break
    addBreakButton.addEventListener('click', () => {
        // 現在の行数を取得
        const i = breaksContainer.querySelectorAll('.break-row').length;

        // <tr> ではなく <div> を作成
        const row = document.createElement('div');
        row.className = 'row mb-4 break-row';
        row.innerHTML = `
            <div class="col-md-3">

                <input
                    type="text"
                    name="breaks[${i}][start_time]"
                    class="form-control time-input"
                    placeholder="--:--"
                    inputmode="numeric"
                    pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$"
                    maxlength="5"
                    list="time-options"
                    required
                >
            </div>
            <div class="col-md-3">

                <input
                    type="text"
                    name="breaks[${i}][end_time]"
                    class="form-control time-input"
                    placeholder="--:--"
                    inputmode="numeric"
                    pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$"
                    maxlength="5"
                    list="time-options"
                    required
                >
            </div>
            <div class="col-md-4">

                <input type="text"
                       name="breaks[${i}][reason]"
                       class="form-control">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button"
                        class="btn btn-outline-danger btn-sm remove-row">
                    <i class="fa-solid fa-trash"></i> Delete
                </button>
            </div>
        `;
        breaksContainer.appendChild(row);
    });
    // Delete Break
    document.addEventListener('click', (e) => {
        const button = e.target.closest('.remove-row');
        if (!button) return;

        // tr ではなく div(.break-row) を削除
        button.closest('.break-row')?.remove();
    });
})();

// Time input
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.time-input').forEach(function (input) {

        input.addEventListener('input', function () {

            // 数字以外を削除
            let value = this.value.replace(/\D/g, '');

            // 4桁を超えない
            value = value.substring(0, 4);

            // 4桁になったら HH:MM に変換
            if (value.length === 4) {

                const hour = value.substring(0, 2);
                const minute = value.substring(2, 4);

                this.value = hour + ':' + minute;

            } else {

                this.value = value;
            }
        });

    });

});
</script>
@endsection
