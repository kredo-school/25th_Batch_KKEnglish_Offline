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
                <label>Representative Start Time</label>
                <input type="time" name="start_time" class="form-control" value="{{ old('start_time', '09:00') }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label>Representative End Time</label>
                <input type="time" name="end_time" class="form-control" value="{{ old('end_time', '18:00') }}" required>
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

        <hr>
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
        </div>

        <hr>
        <h3>休憩（1箇所入力で全選択曜日に反映）</h3>
        <div class="row mb-4">
            <div class="col-md-4">
                <label>休憩 開始</label>
                <input type="time" name="common_break_start_time" class="form-control"
                       value="{{ old('common_break_start_time', '13:00') }}">
            </div>
            <div class="col-md-4">
                <label>休憩 終了</label>
                <input type="time" name="common_break_end_time" class="form-control"
                       value="{{ old('common_break_end_time', '14:00') }}">
            </div>
            <div class="col-md-4">
                <label>Reason</label>
                <input type="text" name="common_break_reason" class="form-control"
                       value="{{ old('common_break_reason', 'Lunch') }}">
            </div>
            <small class="text-muted mt-2">
                ※休憩を使わない場合は開始・終了を空欄にしてください。
            </small>
        </div>

        <div>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>
@endsection
