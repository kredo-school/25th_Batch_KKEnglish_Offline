@extends('layouts.app')
@section('title', 'Teacher Management')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Teacher List</h2>
        <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">Teacher Register</a>
    </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Name/Email/Specialty">
        </div>
        <div class="col-auto"><button class="btn btn-outline-primary">Search</button></div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Specialty</th><th>Career</th><th>Status</th>
                    <th class="text-nowrap">Actions</th></tr></thead>
            <tbody>
            @forelse($teachers as $teacher)
                @php
                    $user = $teacher->user; // null safety
                    $status = $user->status ?? 'unknown';
                @endphp
                <tr>
                    <td>{{ $teacher->id }}</td>
                    <td>{{ $teacher->user->first_name ?? '' }} {{ $teacher->user->last_name ?? '' }}</td>
                    <td>{{ $teacher->user->email ?? '-' }}</td>
                    <td>{{ $teacher->specialty ?? '-' }}</td>
                    <td>{{ $teacher->career ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                            {{ $status }}
                        </span>
                    </td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.teachers.show', $teacher) }}" class="btn btn-sm btn-outline-secondary">Details</a>
                        <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <a href="{{ route('admin.teachers.materials.edit', $teacher) }}" class="btn btn-sm btn-outline-info">Materials</a>
                        {{-- @if($status === 'active')
                            <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('この講師アカウントを停止しますか？');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Deactivate</button>
                            </form>
                        @endif --}}
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No teacher data available.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $teachers->links() }}
</div>
@endsection
