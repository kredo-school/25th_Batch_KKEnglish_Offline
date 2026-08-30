@extends('layouts.app')
@section('title', 'Edit Teacher')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Edit Teacher</h2>
        <a href="{{ route('admin.teachers.show', $teacher) }}" class="btn btn-outline-secondary btn-sm">Details</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
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

    <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}">
        @csrf
        @method('PUT')

        <div class="card mb-3">
            <div class="card-header">Account Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control"
                               value="{{ old('first_name', $teacher->user->first_name ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control"
                               value="{{ old('last_name', $teacher->user->last_name ?? '') }}" required>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email', $teacher->user->email ?? '') }}" required>
                </div>

                <div class="mt-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="active" @selected(old('status', $teacher->user->status ?? 'active') === 'active')>active</option>
                        <option value="inactive" @selected(old('status', $teacher->user->status ?? 'active') === 'inactive')>inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Teacher Profile</div>
            <div class="card-body">
                <div class="mt-2">
                    <label class="form-label">Specialty</label>
                    <input type="text" name="specialty" class="form-control"
                           value="{{ old('specialty', $teacher->specialty) }}">
                </div>

                <div class="mt-3">
                    <label class="form-label">Career</label>
                    <input type="text" name="career" class="form-control"
                           value="{{ old('career', $teacher->career) }}">
                </div>

                <div class="mt-3">
                    <label class="form-label">Graduation School</label>
                    <input type="text" name="graduation_school" class="form-control"
                           value="{{ old('graduation_school', $teacher->graduation_school) }}">
                </div>

                <div class="mt-3">
                    <label class="form-label">Certification</label>
                    <input type="text" name="certification" class="form-control"
                           value="{{ old('certification', $teacher->certification) }}">
                </div>

                <div class="mt-3">
                    <label class="form-label">Biography</label>
                    <textarea name="biography" rows="4" class="form-control">{{ old('biography', $teacher->biography) }}</textarea>
                </div>

                <div class="mt-3">
                    <label class="form-label">About Me</label>
                    <textarea name="about_me" rows="4" class="form-control">{{ old('about_me', $teacher->about_me) }}</textarea>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </form>
</div>
@endsection
