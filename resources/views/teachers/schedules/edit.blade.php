@extends('layouts.app')

@section('title', 'Edit Schedule')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">Edit Schedule</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('teacher.schedules.update', $schedule) }}">
                @csrf
                @method('PUT')

                {{-- 戻り導線パラメータ保持 --}}
                <input type="hidden" name="week_start" value="{{ $backParams['week_start'] ?? request('week_start') }}">
                <input type="hidden" name="teacher_id" value="{{ $backParams['teacher_id'] ?? request('teacher_id') }}">
                <input type="hidden" name="view_start" value="{{ $backParams['view_start'] ?? request('view_start') }}">
                <input type="hidden" name="view_end" value="{{ $backParams['view_end'] ?? request('view_end') }}">

                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label for="available_date" class="form-label">Date</label>
                        <input type="date"
                               id="available_date"
                               name="available_date"
                               class="form-control"
                               value="{{ old('available_date', \Carbon\Carbon::parse($schedule->available_date)->format('Y-m-d')) }}"
                               required>
                    </div>

                    <div class="col-6 col-md-4">
                        <label for="start_time" class="form-label">Start</label>
                        <input type="time"
                               id="start_time"
                               name="start_time"
                               class="form-control"
                               value="{{ old('start_time', \Carbon\Carbon::parse($schedule->start_time)->format('H:i')) }}"
                               required>
                    </div>

                    <div class="col-6 col-md-4">
                        <label for="end_time" class="form-label">End</label>
                        <input type="time"
                               id="end_time"
                               name="end_time"
                               class="form-control"
                               value="{{ old('end_time', \Carbon\Carbon::parse($schedule->end_time)->format('H:i')) }}"
                               required>
                    </div>

                    @if(auth()->user()->role === 'admin')
                        <div class="col-12 col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-select">
                                @foreach(['available', 'booked', 'closed'] as $st)
                                    <option value="{{ $st }}" @selected(old('status', $schedule->status) === $st)>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update</button>

                    <a href="{{ route('teacher.schedules.index', [
                        'week_start' => $backParams['week_start'] ?? request('week_start'),
                        'teacher_id' => $backParams['teacher_id'] ?? request('teacher_id'),
                        'view_start' => $backParams['view_start'] ?? request('view_start'),
                        'view_end'   => $backParams['view_end'] ?? request('view_end'),
                    ]) }}" class="btn btn-outline-secondary">
                        Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
