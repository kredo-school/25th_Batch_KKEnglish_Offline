@extends('layouts.app')

@section('title', 'Registered Schedules')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">Registered Schedules (Weekly Grid)</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Admin only: Teacher selector --}}
    @if(auth()->user()->role === 'admin')
        <form method="GET" class="row g-2 align-items-end mb-3">
            <input type="hidden" name="week_start" value="{{ $startOfWeek->toDateString() }}">
            <input type="hidden" name="view_start" value="{{ $viewStart }}">
            <input type="hidden" name="view_end" value="{{ $viewEnd }}">

            <div class="col-12 col-md-5 col-lg-4">
                <label class="form-label mb-1">Teacher</label>
                <select name="teacher_id" class="form-select" onchange="this.form.submit()">
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
        <a class="btn btn-outline-secondary btn-sm"
           href="{{ route('teacher.schedules.index', [
                'week_start' => $startOfWeek->copy()->subWeek()->toDateString(),
                'teacher_id' => $teacherId,
                'view_start' => $viewStart,
                'view_end'   => $viewEnd,
           ]) }}">
            ← Previous Week
        </a>

        <h5 class="fw-bold mb-0">
            {{ $startOfWeek->format('Y/m/d') }} - {{ $startOfWeek->copy()->addDays(6)->format('Y/m/d') }}
        </h5>

        <a class="btn btn-outline-secondary btn-sm"
           href="{{ route('teacher.schedules.index', [
                'week_start' => $startOfWeek->copy()->addWeek()->toDateString(),
                'teacher_id' => $teacherId,
                'view_start' => $viewStart,
                'view_end'   => $viewEnd,
           ]) }}">
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

        <div class="col-auto ms-auto">
            <a href="{{ route('teacher.schedules.create', [
                'week_start' => $startOfWeek->toDateString(),
                'teacher_id' => $teacherId,
                'view_start' => $viewStart,
                'view_end'   => $viewEnd,
            ]) }}" class="btn btn-primary btn-sm">
                + Edit by Grid
            </a>
        </div>
    </form>

    <div class="mb-2 small">
        <span class="badge text-bg-success">available</span>
        <span class="badge text-bg-danger">booked</span>
        <span class="badge text-bg-secondary">closed</span>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle small" id="scheduleIndexGrid">
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
                            $sid = $cellScheduleIdMap[$date][$time] ?? null;
                        @endphp
                        <td>
                            @if($status)
                                @if($status === 'available')
                                    <span class="badge bg-success">available</span>
                                @elseif($status === 'booked')
                                    <span class="badge bg-danger">booked</span>
                                @else
                                    <span class="badge bg-secondary">{{ $status }}</span>
                                @endif

                                @if($sid)
                                    <div class="mt-1 d-flex justify-content-center gap-1 flex-wrap">
                                        <a href="{{ route('teacher.schedules.edit', $sid) }}"
                                           class="btn btn-outline-primary btn-sm py-0 px-1">E</a>

                                        <form method="POST"
                                              action="{{ route('teacher.schedules.destroy', $sid) }}"
                                              onsubmit="return confirm('削除しますか？')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm py-0 px-1">D</button>
                                        </form>
                                    </div>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
