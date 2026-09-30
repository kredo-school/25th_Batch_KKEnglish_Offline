@extends('layouts.app')

@section('title', 'Station Override')

@section('content')

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Station Override</h2>

        <a href="{{ route('admin.teacher-station-assignments.index') }}"
           class="btn btn-outline-secondary">
            <i class="fa-solid fa-angles-left"></i> Back
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- Override登録 --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold">
            Set Station Override
        </div>

        <div class="card-body">

            <form method="POST"
                  action="{{ route('admin.teacher-station-assignments.overrides.store') }}">

                @csrf

                <div class="row g-3">

                    {{-- Teacher --}}
                    <div class="col-md-4">
                        <label for="teacher_id" class="form-label fw-semibold">
                            Teacher
                        </label>

                        <select name="teacher_id"
                                id="teacher_id"
                                class="form-select"
                                required>

                            <option value="">
                                Select Teacher
                            </option>

                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}">
                                    {{ $teacher->user->first_name ?? '' }}
                                    {{ $teacher->user->last_name ?? '' }}
                                </option>
                            @endforeach

                        </select>
                    </div>


                    {{-- Lesson --}}
                    <div class="col-md-4">
                        <label for="reservation_id" class="form-label fw-semibold">
                            Lesson
                        </label>

                        <select name="reservation_id"
                                id="reservation_id"
                                class="form-select"
                                disabled
                                required>

                            <option value="">
                                First select a teacher
                            </option>

                        </select>
                    </div>


                    {{-- Station --}}
                    <div class="col-md-4">
                        <label for="station_id" class="form-label fw-semibold">
                            Station
                        </label>

                        <select name="station_id"
                                id="station_id"
                                class="form-select"
                                required>

                            <option value="">
                                Select Station
                            </option>

                            @foreach ($stations as $station)
                                <option value="{{ $station->id }}">
                                    {{ $station->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                </div>


                <div class="mt-4">
                    <button type="submit"
                            class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i>
                        Save Override
                    </button>
                </div>

            </form>

        </div>
    </div>


    {{-- Existing Overrides --}}
    <div class="card shadow-sm">

        <div class="card-header fw-bold">
            Existing Overrides
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>Teacher</th>
                            <th>Lesson</th>
                            <th>Student</th>
                            <th>Station</th>
                            <th width="100">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($overrides as $override)

                            @php
                                $teacher = $override->reservation->teacher;
                                $student = $override->reservation->student;
                                $reservation = $override->reservation;
                            @endphp

                            <tr>

                                <td>
                                    {{ $teacher->user->first_name ?? '' }}
                                    {{ $teacher->user->last_name ?? '' }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($reservation->start_at)->format('Y/m/d H:i') }}
                                </td>

                                <td>
                                    {{ $student->user->first_name ?? '' }}
                                    {{ $student->user->last_name ?? '' }}
                                </td>

                                <td>
                                    {{ $override->station->name }}
                                </td>

                                <td>
                                    <form method="POST"
                                          action="{{ route('admin.lesson-station-overrides.destroy', $override) }}"
                                          onsubmit="return confirm('Delete this override?');">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger">
                                            Delete
                                        </button>

                                    </form>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5"
                                    class="text-center text-muted py-4">
                                    No overrides found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const teacherSelect = document.getElementById('teacher_id');
    const reservationSelect = document.getElementById('reservation_id');

    const reservations = @json($reservations);


    teacherSelect.addEventListener('change', function () {

        const teacherId = String(this.value);

        reservationSelect.innerHTML = '';

        // Teacher未選択
        if (!teacherId) {

            reservationSelect.disabled = true;

            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'First select a teacher';

            reservationSelect.appendChild(option);

            return;
        }


        // 選択されたTeacherのLessonだけ取得
        const filteredReservations = reservations.filter(function (reservation) {

            return String(reservation.teacher_id) === teacherId;

        });


        reservationSelect.disabled = false;


        // Lessonがない場合
        if (filteredReservations.length === 0) {

            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'No lessons found';

            reservationSelect.appendChild(option);

            return;
        }


        // 最初の選択肢
        const defaultOption = document.createElement('option');

        defaultOption.value = '';
        defaultOption.textContent = 'Select Lesson';

        reservationSelect.appendChild(defaultOption);


        // Lessonを追加
        filteredReservations.forEach(function (reservation) {

            const option = document.createElement('option');

            option.value = reservation.id;

            const start = new Date(
                reservation.start_at
            );

            const dateText = start.toLocaleDateString(
                'ja-JP'
            );

            const timeText = start.toLocaleTimeString(
                'ja-JP',
                {
                    hour: '2-digit',
                    minute: '2-digit'
                }
            );

            option.textContent =
                dateText +
                ' ' +
                timeText +
                ' - ' +
                (reservation.student_name ?? 'Student');

            reservationSelect.appendChild(option);

        });

    });

});
</script>

@endsection