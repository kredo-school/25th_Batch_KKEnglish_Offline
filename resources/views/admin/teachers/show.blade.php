@extends('layouts.app')
@section('title', 'Teacher Details')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Teacher Details</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-outline-primary btn-sm">Edit</a>
            <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary btn-sm">Back to List</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <p><strong>Teacher ID:</strong> {{ $teacher->id }}</p>
            <p><strong>Name:</strong> {{ $teacher->user->last_name }} {{ $teacher->user->first_name }}</p>
            <p><strong>Email:</strong> {{ $teacher->user->email }}</p>
            <p><strong>Status:</strong> {{ $teacher->user->status }}</p>
            <hr>
            <p><strong>Specialty:</strong> {{ $teacher->specialty ?: '-' }}</p>
            <p><strong>Career:</strong> {{ $teacher->career ?: '-' }}</p>
            <p><strong>Graduation School:</strong> {{ $teacher->graduation_school ?: '-' }}</p>
            <p><strong>Certification:</strong> {{ $teacher->certification ?: '-' }}</p>
            <p><strong>Biography:</strong><br>{{ $teacher->biography ?: '-' }}</p>
            <p><strong>About Me:</strong><br>{{ $teacher->about_me ?: '-' }}</p>
            <p><strong>Rating:</strong> {{ $teacher->rating_average }}</p>
            <p><strong>Points Consumed:</strong> {{ $teacher->point_consumed }}</p>
        </div>
    </div>
</div>
@endsection