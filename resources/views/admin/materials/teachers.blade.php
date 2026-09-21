@extends('layouts.app')

@section('title', 'Assign Teachers')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Assign Teachers</h2>

        <a href="{{ route('admin.materials.show', $material) }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-angles-left"></i>
            Back
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">

            <div class="d-flex align-items-center gap-3">

                <img
                    src="{{ $material->cover_image
                        ? asset('storage/' . $material->cover_image)
                        : asset('images/no-image.png') }}"
                    alt="{{ $material->name }}"
                    width="90"
                    height="90"
                    class="rounded border"
                    style="object-fit: cover;"
                >

                <div>
                    <h4 class="fw-bold mb-1">
                        {{ $material->name }}
                    </h4>

                    <div class="text-secondary">
                        Material ID: {{ $material->material_id }}
                    </div>

                    <div class="mt-2">
                        <span class="badge bg-primary">
                            {{ $material->level ?? 'N/A' }}
                        </span>
                    </div>
                </div>

            </div>

        </div>
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

    <form method="POST"
          action="{{ route('admin.materials.teachers.update', $material) }}">

        @csrf
        @method('PUT')

        <div class="card">

            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <h5 class="fw-bold mb-1">
                            Teachers
                        </h5>

                        <small class="text-secondary">
                            Select the teachers who can teach this material.
                        </small>
                    </div>

                    <div>
                        <button type="button"
                                class="btn btn-outline-secondary btn-sm"
                                id="selectAllTeachers">
                            Select All
                        </button>

                        <button type="button"
                                class="btn btn-outline-secondary btn-sm"
                                id="clearAllTeachers">
                            Clear All
                        </button>
                    </div>

                </div>
            </div>

            <div class="card-body">

                @forelse ($teachers as $teacher)

                    @php
                        $teacherName = trim(
                            ($teacher->user->first_name ?? '') . ' ' .
                            ($teacher->user->last_name ?? '')
                        );

                        if ($teacherName === '') {
                            $teacherName = 'Teacher #' . $teacher->id;
                        }
                    @endphp

                    <div class="form-check border rounded p-3 mb-2">

                        <input
                            class="form-check-input teacher-checkbox ms-0 me-2"
                            type="checkbox"
                            name="teacher_ids[]"
                            value="{{ $teacher->id }}"
                            id="teacher_{{ $teacher->id }}"
                            {{ in_array($teacher->id, $selectedTeacherIds) ? 'checked' : '' }}
                        >

                        <label
                            class="form-check-label"
                            for="teacher_{{ $teacher->id }}"
                        >
                            <strong>
                                {{ $teacherName }}
                            </strong>

                            <span class="text-secondary ms-2">
                                Teacher ID: {{ $teacher->id }}
                            </span>
                        </label>

                    </div>

                @empty

                    <div class="alert alert-light border text-muted mb-0">
                        No teachers found.
                    </div>

                @endforelse

            </div>

            <div class="card-footer bg-white">

                <div class="d-flex align-items-center gap-2">

                    <button type="submit"
                            class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Save Assignments
                    </button>

                    <a href="{{ route('admin.materials.show', $material) }}"
                       class="btn btn-outline-secondary">
                        Cancel
                    </a>

                </div>

            </div>

        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
    document.getElementById('selectAllTeachers')?.addEventListener('click', function () {
        document.querySelectorAll('.teacher-checkbox').forEach(function (checkbox) {
            checkbox.checked = true;
        });
    });

    document.getElementById('clearAllTeachers')?.addEventListener('click', function () {
        document.querySelectorAll('.teacher-checkbox').forEach(function (checkbox) {
            checkbox.checked = false;
        });
    });
</script>
@endpush
