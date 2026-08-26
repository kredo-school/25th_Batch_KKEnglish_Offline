@extends('layouts.app')

@section('title', 'Weekly Schedule Grid')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">Weekly Schedule (Drag Select)</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Admin only: teacher selector --}}
    @if(auth()->user()->role === 'admin')
        <form method="GET" class="row g-2 align-items-end mb-3">
            <input type="hidden" name="week_start" value="{{ $startOfWeek->toDateString() }}">
            <input type="hidden" name="view_start" value="{{ $viewStart }}">
            <input type="hidden" name="view_end" value="{{ $viewEnd }}">

            <div class="col-12 col-md-5 col-lg-4">
                <label class="form-label mb-1">Teacher</label>
                <select name="teacher_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- 講師を選択 --</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->teacher_id }}" @selected((int)$teacherId === (int)$t->teacher_id)>
                            {{ $t->name }} (ID: {{ $t->teacher_id }})
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    @endif

    {{-- Week navigation --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('teacher.schedules.create', [
            'week_start' => $startOfWeek->copy()->subWeek()->toDateString(),
            'teacher_id' => $teacherId,
            'view_start' => $viewStart,
            'view_end'   => $viewEnd,
        ]) }}" class="btn btn-outline-secondary btn-sm">
            ← Previous Week
        </a>

        <h5 class="fw-bold mb-0">
            {{ $startOfWeek->format('Y/m/d') }} - {{ $startOfWeek->copy()->addDays(6)->format('Y/m/d') }}
        </h5>

        <a href="{{ route('teacher.schedules.create', [
            'week_start' => $startOfWeek->copy()->addWeek()->toDateString(),
            'teacher_id' => $teacherId,
            'view_start' => $viewStart,
            'view_end'   => $viewEnd,
        ]) }}" class="btn btn-outline-secondary btn-sm">
            Next Week →
        </a>
    </div>

    {{-- Visible time range --}}
    <form method="GET" class="row g-2 align-items-end mb-3">
        <input type="hidden" name="week_start" value="{{ $startOfWeek->toDateString() }}">
        <input type="hidden" name="teacher_id" value="{{ $teacherId }}">

        <div class="col-auto">
            <label for="view_start" class="form-label mb-1">表示開始</label>
            <input type="time" id="view_start" name="view_start"
                   class="form-control form-control-sm"
                   value="{{ $viewStart }}" step="1800">
        </div>

        <div class="col-auto">
            <label for="view_end" class="form-label mb-1">表示終了</label>
            <input type="time" id="view_end" name="view_end"
                   class="form-control form-control-sm"
                   value="{{ $viewEnd }}" step="1800">
        </div>

        <div class="col-auto">
            <button type="submit" class="btn btn-outline-secondary btn-sm">反映</button>
        </div>
    </form>

    <div class="mb-2 small">
        <span class="badge text-bg-success">Selected</span>
        <span class="badge text-bg-primary">Existing(available)</span>
        <span class="badge text-bg-danger">Booked</span>
        <span class="badge text-bg-secondary">Closed</span>
    </div>

    <form method="POST" action="{{ route('teacher.schedules.storeGrid') }}" id="gridForm">
        @csrf
        <input type="hidden" name="week_start" value="{{ $startOfWeek->toDateString() }}">
        <input type="hidden" name="teacher_id" value="{{ $teacherId }}">
        <input type="hidden" name="view_start" value="{{ $viewStart }}">
        <input type="hidden" name="view_end" value="{{ $viewEnd }}">
        <div id="hiddenSlots"></div>

        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle small" id="scheduleGrid">
                <thead class="table-light">
                    <tr>
                        <th style="min-width:90px;">Time</th>
                        @foreach($days as $day)
                            <th style="min-width:120px;">
                                {{ $day->format('D') }}<br>
                                <span class="text-muted">{{ $day->format('m/d') }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                @foreach($times as $time)
                    <tr>
                        <th class="table-light">{{ $time }}</th>
                        @foreach($days as $day)
                            @php
                                $date = $day->toDateString();
                                $status = $existingMap[$date][$time] ?? null;
                                $class = '';
                                if ($status === 'available') $class = 'existing-available';
                                if ($status === 'booked') $class = 'existing-booked';
                                if ($status === 'closed') $class = 'existing-closed';
                            @endphp
                            <td class="slot {{ $class }}"
                                data-date="{{ $date }}"
                                data-time="{{ $time }}"
                                data-status="{{ $status ?? '' }}">
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save Selected Slots</button>
            <button type="button" class="btn btn-outline-secondary" id="clearSelection">Clear Selection</button>

            <a href="{{ route('teacher.schedules.index', [
                'week_start' => $startOfWeek->toDateString(),
                'teacher_id' => $teacherId,
                'view_start' => $viewStart,
                'view_end'   => $viewEnd,
            ]) }}" class="btn btn-outline-dark">
                Back
            </a>
        </div>
    </form>
</div>

<style>
    #scheduleGrid td.slot {
        cursor: pointer;
        user-select: none;
        height: 32px;
    }
    #scheduleGrid td.selected { background: #198754 !important; }
    #scheduleGrid td.existing-available { background: #0d6efd33; }
    #scheduleGrid td.existing-booked {
        background: #dc354544;
        cursor: not-allowed;
    }
    #scheduleGrid td.existing-closed {
        background: #6c757d55;
        cursor: not-allowed;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const grid = document.getElementById('scheduleGrid');
    const slots = Array.from(grid.querySelectorAll('td.slot'));
    const form = document.getElementById('gridForm');
    const hiddenSlots = document.getElementById('hiddenSlots');
    const clearBtn = document.getElementById('clearSelection');

    let dragging = false;
    let paintMode = 'select';

    const isLocked = (cell) => {
        const st = cell.dataset.status;
        return st === 'booked' || st === 'closed';
    };

    const toggleCell = (cell) => {
        if (isLocked(cell)) return;
        if (paintMode === 'select') cell.classList.add('selected');
        else cell.classList.remove('selected');
    };

    slots.forEach(cell => {
        cell.addEventListener('mousedown', (e) => {
            e.preventDefault();
            if (isLocked(cell)) return;
            dragging = true;
            paintMode = cell.classList.contains('selected') ? 'unselect' : 'select';
            toggleCell(cell);
        });

        cell.addEventListener('mouseover', () => {
            if (!dragging) return;
            toggleCell(cell);
        });
    });

    document.addEventListener('mouseup', () => dragging = false);

    clearBtn.addEventListener('click', () => {
        slots.forEach(c => c.classList.remove('selected'));
    });

    form.addEventListener('submit', (e) => {
        hiddenSlots.innerHTML = '';
        const selected = slots.filter(c => c.classList.contains('selected'));

        if (selected.length === 0) {
            e.preventDefault();
            alert('Please select at least one time slot.');
            return;
        }

        selected.forEach((cell, idx) => {
            const i1 = document.createElement('input');
            i1.type = 'hidden';
            i1.name = `cells[${idx}][date]`;
            i1.value = cell.dataset.date;

            const i2 = document.createElement('input');
            i2.type = 'hidden';
            i2.name = `cells[${idx}][time]`;
            i2.value = cell.dataset.time;

            hiddenSlots.appendChild(i1);
            hiddenSlots.appendChild(i2);
        });
    });
});
</script>
@endsection
