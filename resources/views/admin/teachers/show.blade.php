@extends('layouts.app')
@section('title', 'Teacher Details')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Teacher Details</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen-to-square me-1"></i> Edit</a>
            <a href="{{ route('admin.teachers.materials.edit', $teacher) }}" class="btn btn-outline-info btn-sm">Materials</a>
            <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-angles-left"></i> Back to List</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <p><strong>Profile Picture:</strong>
                {{-- @php
                    if (!$teacher->cover_image) {$imageUrl = asset('images/no-image.png');
                    } elseif (Str::startsWith($teacher->cover_image, ['http://','https://'])){
                    } else {
                    // Laravelのstorageに保存された画像
                    $imageUrl = asset('storage/' . $material->cover_image);
                }
                @endphp --}}
                @if($teacher->user?->profile_image_url)
                    <img
                        src="{{ $teacher->user->profile_image_url }}"
                        alt="{{ $teacher->user->first_name }}"
                        width="100"
                        height="100"
                        class="rounded-circle me-4"
                        style="object-fit: cover;"
                    >
                @else
                    <i
                        class="fa-solid fa-circle-user fa-5x me-4 text-secondary"
                    ></i>
                @endif</p>
            <p><strong>Teacher ID:</strong> {{ $teacher->id }}</p>
            <p><strong>Name:</strong> {{ $teacher->user->last_name }} {{ $teacher->user->first_name }}</p>
            <p><strong>Email:</strong> {{ $teacher->user->email }}</p>
            <p><strong>Status:</strong> {{ $teacher->user->status }}</p>
            <hr>
            <p><strong>Nationality:</strong> {{ $teacher->user->nationality ?: '-' }}</p>
            <p><strong>Specialty:</strong> {{ $teacher->specialty ?: '-' }}</p>
            <p><strong>Career:</strong> {{ $teacher->career ?: '-' }}</p>
            <p><strong>Graduation School:</strong> {{ $teacher->graduation_school ?: '-' }}</p>
            <p><strong>Certification:</strong> {{ $teacher->certification ?: '-' }}</p>
            <p><strong>Biography:</strong><br>{{ $teacher->biography ?: '-' }}</p>
            <p><strong>About Me:</strong><br>{{ $teacher->about_me ?: '-' }}</p>
            <p><strong>Rating:</strong> {{ $teacher->rating_average }}</p>
            <p><strong>Points Consumed:</strong> {{ $teacher->point_consumed }}</p>

            @if($teacher->materials->isEmpty())
                <p class="text-secondary">No materials assigned.</p>
            @else
                <p><strong>Materials:</strong></p>
                <ul class="mb-2">
                    @foreach($teacher->materials as $material)
                        <li>{{ $material->name }}</li>
                    @endforeach
                </ul>
            @endif

            <a href="{{ route('admin.teachers.materials.edit', $teacher) }}" class="btn btn-outline-info btn-sm">
                Edit Materials
            </a>
        </div>
    </div>
</div>
@endsection
