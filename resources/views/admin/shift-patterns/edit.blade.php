@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Shift Pattern</h1>

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

    <form method="POST" action="{{ route('admin.shift-patterns.update', $shiftPattern) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Pattern Code</label>
            <input type="text" name="pattern_code" class="form-control"
                   value="{{ old('pattern_code', $shiftPattern->pattern_code) }}" required>
        </div>

        <div class="mb-3">
            <label>Pattern Name</label>
            <input type="text" name="pattern_name" class="form-control"
                   value="{{ old('pattern_name', $shiftPattern->pattern_name) }}" required>
        </div>

        <div class="row">
            <div class="col-md-3 mb-3">
                <label>Representative Start Time</label>
                <input type="text" name="start_time" class="form-control time-input"
                       value="{{ old('start_time', \Illuminate\Support\Str::of($shiftPattern->start_time)->substr(0,5)) }}" inputmode="numeric"
                        pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$"
                        maxlength="5"
                        list="time-options" required>
            </div>
            <div class="col-md-3 mb-3">
                <label>Representative End Time</label>
                <input type="text" name="end_time" class="form-control time-input"
                       value="{{ old('end_time', \Illuminate\Support\Str::of($shiftPattern->end_time)->substr(0,5)) }}" inputmode="numeric"
                        pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$"
                        maxlength="5"
                        list="time-options" required>
            </div>
            <div class="col-md-3 mb-3">
                <label>End Day Offset</label>
                <input type="number" min="0" max="1" name="end_day_offset" class="form-control"
                       value="{{ old('end_day_offset', $shiftPattern->end_day_offset) }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label>Slot Minutes</label>
                <select name="slot_minutes" class="form-control" required>
                    <option value="30" @selected((int)old('slot_minutes', $shiftPattern->slot_minutes)===30)>30</option>
                    <option value="60" @selected((int)old('slot_minutes', $shiftPattern->slot_minutes)===60)>60</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label>Display Order</label>
            <input type="number" min="0" name="display_order" class="form-control"
                   value="{{ old('display_order', $shiftPattern->display_order) }}">
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                   @checked((int)old('is_active', $shiftPattern->is_active)===1)>
            <label class="form-check-label" for="is_active">Active</label>
        </div>

        {{-- <hr>
        <h3>Weekly Rules</h3>
        <p class="text-muted">※Rows including in-person (in_person/both) should have the start time set to xx:00.</p>
        <table class="table" id="rules-table">
            <thead>
                <tr>
                    <th>Weekday (0=Sun...6=Sat)</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Type</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @php
                $defaultRules = $shiftPattern->rules->map(fn($r) => [
                    'weekday' => $r->weekday,
                    'start_time' => \Illuminate\Support\Str::of($r->start_time)->substr(0,5)->toString(),
                    'end_time' => \Illuminate\Support\Str::of($r->end_time)->substr(0,5)->toString(),
                    'lesson_type' => $r->lesson_type,
                ])->values()->all();

                $oldRules = old('rules', $defaultRules);
            @endphp

            @foreach($oldRules as $i => $r)
                <tr>
                    <td><input type="number" name="rules[{{ $i }}][weekday]" min="0" max="6" class="form-control" value="{{ $r['weekday'] ?? '' }}" required></td>
                    <td><input type="time" name="rules[{{ $i }}][start_time]" class="form-control" value="{{ $r['start_time'] ?? '' }}" required></td>
                    <td><input type="time" name="rules[{{ $i }}][end_time]" class="form-control" value="{{ $r['end_time'] ?? '' }}" required></td>
                    <td>
                        <select name="rules[{{ $i }}][lesson_type]" class="form-control" required>
                            <option value="online" @selected(($r['lesson_type'] ?? '')==='online')>online</option>
                            <option value="in_person" @selected(($r['lesson_type'] ?? '')==='in_person')>in_person</option>
                            <option value="both" @selected(($r['lesson_type'] ?? '')==='both')>both</option>
                        </select>
                    </td>
                    <td><button type="button" class="btn btn-outline-danger btn-sm remove-row">Delete</button></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="add-rule">＋ Add Rule</button> --}}
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
        <h3>Breaks</h3>
        <table class="table" id="breaks-table">
            <thead>
                <tr>
                    {{-- <th>Weekday</th> --}}
                    <th>Start</th>
                    <th>End</th>
                    <th>Reason</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @php
                $defaultBreaks = $shiftPattern->breaks->map(fn($b) => [
                    'weekday' => $b->weekday,
                    'start_time' => \Illuminate\Support\Str::of($b->start_time)->substr(0,5)->toString(),
                    'end_time' => \Illuminate\Support\Str::of($b->end_time)->substr(0,5)->toString(),
                    'reason' => $b->reason,
                ])->values()->all();

                $oldBreaks = old('breaks', $defaultBreaks);
            @endphp

            @foreach($oldBreaks as $i => $b)
                <tr>
                    {{-- <td><input type="number" name="breaks[{{ $i }}][weekday]" min="0" max="6" class="form-control" value="{{ $b['weekday'] ?? '' }}" required></td> --}}
                    <td><input type="text" name="breaks[{{ $i }}][start_time]" class="form-control time-input" value="{{ $b['start_time'] ?? '' }}" inputmode="numeric"
                    pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$"
                    maxlength="5"
                    list="time-options" required></td>
                    <td><input type="text" name="breaks[{{ $i }}][end_time]" class="form-control time-input" value="{{ $b['end_time'] ?? '' }}" inputmode="numeric"
                    pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$"
                    maxlength="5"
                    list="time-options" required></td>
                    <td><input type="text" name="breaks[{{ $i }}][reason]" class="form-control" value="{{ $b['reason'] ?? '' }}"></td>
                    <td><button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="fa-solid fa-trash me-1"></i> Delete</button></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="add-break"><i class="fa-solid fa-plus me-1"></i>Add Break</button>

        <div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.shift-patterns.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
(() => {

    const breaksTbody = document.querySelector('#breaks-table tbody');
    const addBreakButton = document.getElementById('add-break');

    // Add Break
    addBreakButton.addEventListener('click', () => {

        const i = breaksTbody.querySelectorAll('tr').length;

        const tr = document.createElement('tr');

        tr.innerHTML = `
            <td>
                <input
                    type="text"
                    name="breaks[${i}][start_time]"
                    class="form-control time-input"
                    placeholder="1200"
                    inputmode="numeric"
                    pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$"
                    maxlength="5"
                    list="time-options"
                    required
                >
            </td>

            <td>
                <input
                    type="text"
                    name="breaks[${i}][end_time]"
                    class="form-control time-input"
                    placeholder="1300"
                    inputmode="numeric"
                    pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$"
                    maxlength="5"
                    list="time-options"
                    required
                >
            </td>

            <td>
                <input type="text"
                       name="breaks[${i}][reason]"
                       class="form-control">
            </td>

            <td>
                <button type="button"
                        class="btn btn-outline-danger btn-sm remove-row">
                    <i class="fa-solid fa-trash me-1"></i>
                    Delete
                </button>
            </td>
        `;

        breaksTbody.appendChild(tr);
    });


    // Delete Break
    document.addEventListener('click', (e) => {

        const button = e.target.closest('.remove-row');

        if (!button) {
            return;
        }

        button.closest('tr')?.remove();
    });

})();
// Time input

document.addEventListener('input', function (e) {

    if (!e.target.classList.contains('time-input')) {
        return;
    }

    let value = e.target.value.replace(/\D/g, '');

    value = value.substring(0, 4);

    if (value.length === 4) {

        const hour = parseInt(value.substring(0, 2), 10);
        const minute = parseInt(value.substring(2, 4), 10);

        // 正しい時刻の場合だけ変換
        if (hour <= 23 && minute <= 59) {

            e.target.value =
                String(hour).padStart(2, '0')
                + ':'
                + String(minute).padStart(2, '0');
        } else {

            e.target.value = value;
        }

    } else {

        e.target.value = value;
    }
});
</script>
@endsection
