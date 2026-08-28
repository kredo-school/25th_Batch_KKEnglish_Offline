@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">Edit User</h2>

    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-control"
                       value="{{ old('first_name', $user->first_name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-control"
                       value="{{ old('last_name', $user->last_name) }}" required>
            </div>
        </div>

        <div class="mt-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <label class="form-label">Role</label>
                <select name="role_id" class="form-select" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}"
                            @selected(old('id', $user->role_id) == $role->id)>
                            {{ $role->role_code ?? $role->name ?? ('Role#'.$role->id) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="active" @selected(old('status', $user->status)==='active')>active</option>
                    <option value="inactive" @selected(old('status', $user->status)==='inactive')>inactive</option>
                </select>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </form>
</div>
@endsection
