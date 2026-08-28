@extends('layouts.app')
@section('title', 'Teacher Registration')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">Teacher Registration</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.teachers.store') }}">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">First name</label>
                <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
            </div>
        </div>

        <div class="mt-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>

        <div class="mt-3">
            <label class="form-label">Initial Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mt-3">
            <label class="form-label">Specialty</label>
            <input type="text" name="specialty" class="form-control" value="{{ old('specialty') }}">
        </div>

        <div class="mt-3">
            <label class="form-label">Career</label>
            <input type="text" name="career" class="form-control" value="{{ old('career') }}">
        </div>

        <div class="mt-3">
            <label class="form-label">Biography</label>
            <textarea name="biography" rows="4" class="form-control">{{ old('biography') }}</textarea>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Register</button>
            <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </form>
</div>
@endsection