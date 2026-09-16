@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">Create Announcement</h1>
        <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary btn-sm">Back to List</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.announcements.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Enter the announcement title" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Target Audience</label>
                    <select name="target" class="form-select" required>
                        <option value="all">All (Students & Teachers)</option>
                        <option value="students">Students Only</option>
                        <option value="teachers">Teachers Only</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Content</label>
                    <textarea name="content" class="form-control" rows="6" placeholder="Enter the announcement content" required>{{ old('content') }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Send Announcement</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
