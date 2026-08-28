@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-3">User Management</h2>
    <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="keyword" class="form-control" placeholder="Search by name or email" value="{{ request('keyword') }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role->role_code ?? '-' }}</td>
                    <td>{{ $user->status ?? '-' }}</td>
                    <td><a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-secondary">Details</a>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('このユーザーを停止しますか？');">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-outline-danger">Suspend</button>
</form></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-muted">No users found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
    .
</div>

@endsection
