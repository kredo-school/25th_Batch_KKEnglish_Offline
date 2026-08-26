@extends('layouts.app')

@section('title', 'Weekly Schedule Grid')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">Weekly Schedule (Drag Select)</h2>

    @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if (session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    @php
        use Carbon\Carbon;
        $startOfWeek = request('week_start')
            ? Carbon::parse(request('week_start'))->startOfDay()
            : now()->startOfWeek(Carbon::MONDAY);

        $days = collect(range(0,6))->map(fn($i) => $startOfWeek->copy()->addDays($i));

        $start = Carbon::createFromTimeString('06:00');
        $end   = Carbon::createFromTimeString('22:00');
        $times = [];
        for ($t = $start->copy(); $t->lt($end); $t->addMinutes(30)) {
            $times[] = $t->format('H:i');
        }

        // Controllerから渡す想定:
        // $existingMap['Y-m-d']['HH:MM'] = 'available'|'booked'|'closed'
        $existingMap = $existingMap ?? [];
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('teacher.schedules.create', ['week_start' => $startOfWeek->copy()->subWeek()->toDateString()]) }}"
           class="btn btn-outline-secondary btn-sm">← Previous Week</a>
        <h5 class="fw-bold mb-0">
            {{ $startOfWeek->format('Y/m/d') }} - {{ $startOfWeek->copy()->addDays(6)->format('Y/m/d') }}
        </h5>
        <a href="{{ route('teacher.schedules.create', ['week_start' => $startOfWeek->copy()->addWeek()->toDateString()]) }}"
           class="btn btn-outline-secondary btn-sm">Next Week →</a>
    </div>

    <div class="mb-2 small">
        <span class="badge text-bg-success">Selected</span>
        <span class="badge text-bg-primary">Existing(available)</span>
        <span class="badge text-bg-danger">Booked</span>
        <span class="badge text-bg-secondary">Closed</span>
    </div>

    <form method="POST" action="{{ route('teacher.schedules.storeGrid') }}" id="gridForm">
        @csrf
        <input type="hidden" name="week_start" value="{{ $startOfWeek->toDateString() }}">
        <div id="hiddenSlots"></div>

        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle small" id="scheduleGrid">
                <thead class="table-light">
                    <tr>
                        <th style="min-width:90px;">Time</th>
                        @foreach($days as $day)
                            <th style="min-width:120px;">
                                {{ $day->format('D') }}<br><span class="text-muted">{{ $day->format('m/d') }}</span>
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
        </div>
    </form>
</div>

<style>
    #scheduleGrid td.slot { cursor: pointer; user-select: none; height: 32px; }
    #scheduleGrid td.selected { background: #198754 !important; }
    #scheduleGrid td.existing-available { background: #0d6efd33; }
    #scheduleGrid td.existing-booked { background: #dc354544; cursor: not-allowed; }
    #scheduleGrid td.existing-closed { background: #6c757d55; cursor: not-allowed; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const grid = document.getElementById('scheduleGrid');
    const slots = Array.from(grid.querySelectorAll('td.slot'));
    const form = document.getElementById('gridForm');
    const hiddenSlots = document.getElementById('hiddenSlots');
    const clearBtn = document.getElementById('clearSelection');

    let dragging = false;
    let paintMode = 'select'; // 'select' or 'unselect'

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
            const date = cell.dataset.date;
            const time = cell.dataset.time;
            const i1 = document.createElement('input');
            i1.type = 'hidden';
            i1.name = `cells[${idx}][date]`;
            i1.value = date;

            const i2 = document.createElement('input');
            i2.type = 'hidden';
            i2.name = `cells[${idx}][time]`;
            i2.value = time;

            hiddenSlots.appendChild(i1);
            hiddenSlots.appendChild(i2);
        });
    });
});
</script>
@endsection
