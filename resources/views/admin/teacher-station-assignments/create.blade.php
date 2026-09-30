@extends('layouts.app')

@section('title', 'Create Station Assignment')

@section('content')

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Create Station Assignment</h2>
            <p class="text-muted mb-0">
                Assign a default station to a teacher for a specific period.
            </p>
        </div>

        <a href="{{ route('admin.teacher-station-assignments.index', ['menu' => 'station-assignment']) }}"
           class="btn btn-outline-secondary">
            <i class="fa-solid fa-angles-left"></i> Back
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST"
                  action="{{ route('admin.teacher-station-assignments.store') }}">

                @csrf

                {{-- Teacher --}}
                <div class="mb-3">
                    <label for="teacher_id" class="form-label fw-semibold">
                        Teacher
                    </label>

                    <select
                        name="teacher_id"
                        id="teacher_id"
                        class="form-select"
                        required
                    >
                        <option value="">Select Teacher</option>

                        @foreach ($teachers as $teacher)
                            <option
                                value="{{ $teacher->id }}"
                                @selected(old('teacher_id') == $teacher->id)
                            >
                                {{ $teacher->user->first_name ?? '' }}
                                {{ $teacher->user->last_name ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Station --}}
                <div class="mb-3">
                    <label for="station_id" class="form-label fw-semibold">
                        Station
                    </label>

                    <select
                        name="station_id"
                        id="station_id"
                        class="form-select"
                        required
                    >
                        <option value="">Select Station</option>

                        @foreach ($stations as $station)
                            <option
                                value="{{ $station->id }}"
                                @selected(old('station_id') == $station->id)
                            >
                                {{ $station->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Start Date --}}
                <div class="mb-3">
                    <label for="start_date" class="form-label fw-semibold">
                        Start Date
                    </label>

                    <input
                        type="date"
                        name="start_date"
                        id="start_date"
                        class="form-control"
                        value="{{ old('start_date') }}"
                        required
                    >
                </div>

                {{-- End Date --}}
                <div class="mb-4">
                    <label for="end_date" class="form-label fw-semibold">
                        End Date
                    </label>

                    <input
                        type="date"
                        name="end_date"
                        id="end_date"
                        class="form-control"
                        value="{{ old('end_date') }}"
                    >

                    <div class="form-text">
                        Leave blank if there is no end date.
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('admin.teacher-station-assignments.index', ['menu' => 'station-assignment']) }}"
                       class="btn btn-outline-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i>
                        Save
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

@endsection