@extends('layouts.app')
@section('title', 'User Details')
@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">User Details</h2>
    <div class="card">
        <div class="card-body">
            <p><strong>ID:</strong> {{ $user->id }}</p>
            <p><strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Role:</strong> {{ $user->role->role_code ?? '-' }}</p>
            <p><strong>Status:</strong> {{ $user->status }}</p>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary btn-sm">Edit</a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
        </div>
    </div>
</div>
@endsection
