@extends('layouts.app')

@section('title', 'Admin Profile')

@section('content')

<div class="container py-4">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Admin Profile</h2>

    <a
        href="{{ route('admin.dashboard') }}"
        class="btn btn-outline-secondary btn-sm"
    >
        <i class="fa-solid fa-angles-left me-1"></i>
        Back
    </a>
</div>


{{-- Success Message --}}
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif


{{-- Validation Errors --}}
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<div class="row">

    {{-- ==========================
         Basic Information
    =========================== --}}
    <div class="col-md-6 mb-4">

        <div class="card">

            <div class="card-header fw-bold">
                Basic Information
            </div>

            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route('admin.profile.update') }}"
                    enctype="multipart/form-data"
                >

                    @csrf
                    @method('PUT')


                    {{-- Profile Image --}}
                    <div class="mb-4">

                        <label class="form-label fw-semibold">
                            Profile Photo
                        </label>

                        <div class="mb-3">

                            @if($user->profile_image)

                                <img
                                    src="{{ str_starts_with($user->profile_image, 'http://') || str_starts_with($user->profile_image, 'https://')
                                        ? $user->profile_image
                                        : asset('storage/' . ltrim($user->profile_image, '/')) }}"
                                    alt="{{ $user->first_name }}"
                                    width="120"
                                    height="120"
                                    class="rounded-circle border"
                                    style="object-fit: cover;"
                                >

                            @else

                                <i
                                    class="fa-solid fa-circle-user text-secondary"
                                    style="font-size: 120px;"
                                ></i>

                            @endif

                        </div>

                        <input
                            type="file"
                            name="profile_image"
                            class="form-control @error('profile_image') is-invalid @enderror"
                            accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                        >

                        @error('profile_image')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-text">
                            JPEG, PNG, GIF or WebP. Maximum 2MB.
                        </div>

                    </div>


                    {{-- First Name --}}
                    <div class="mb-3">

                        <label
                            for="first_name"
                            class="form-label"
                        >
                            First Name
                        </label>

                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            value="{{ old('first_name', $user->first_name) }}"
                            class="form-control @error('first_name') is-invalid @enderror"
                            required
                        >

                        @error('first_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Last Name --}}
                    <div class="mb-3">

                        <label
                            for="last_name"
                            class="form-label"
                        >
                            Last Name
                        </label>

                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            value="{{ old('last_name', $user->last_name) }}"
                            class="form-control @error('last_name') is-invalid @enderror"
                            required
                        >

                        @error('last_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Email --}}
                    <div class="mb-3">

                        <label
                            for="email"
                            class="form-label"
                        >
                            Email
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            class="form-control @error('email') is-invalid @enderror"
                            required
                        >

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="fa-solid fa-floppy-disk me-1"></i>
                        Save Changes
                    </button>

                </form>

            </div>

        </div>

    </div>


    {{-- ==========================
         Password
    =========================== --}}
    <div class="col-md-6 mb-4">

        <div class="card">

            <div class="card-header fw-bold">
                Change Password
            </div>

            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route('admin.profile.password.update') }}"
                >

                    @csrf
                    @method('PUT')


                    {{-- Current Password --}}
                    <div class="mb-3">

                        <label
                            for="current_password"
                            class="form-label"
                        >
                            Current Password
                        </label>

                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            class="form-control @error('current_password') is-invalid @enderror"
                            required
                            autocomplete="current-password"
                        >

                        @error('current_password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- New Password --}}
                    <div class="mb-3">

                        <label
                            for="new_password"
                            class="form-label"
                        >
                            New Password
                        </label>

                        <input
                            type="password"
                            id="new_password"
                            name="new_password"
                            class="form-control @error('new_password') is-invalid @enderror"
                            required
                            autocomplete="new-password"
                        >

                        @error('new_password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-text">
                            Password must be at least 8 characters.
                        </div>

                    </div>


                    {{-- Confirm Password --}}
                    <div class="mb-3">

                        <label
                            for="new_password_confirmation"
                            class="form-label"
                        >
                            Confirm New Password
                        </label>

                        <input
                            type="password"
                            id="new_password_confirmation"
                            name="new_password_confirmation"
                            class="form-control"
                            required
                            autocomplete="new-password"
                        >

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="fa-solid fa-key me-1"></i>
                        Change Password
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>
```

</div>

@endsection
