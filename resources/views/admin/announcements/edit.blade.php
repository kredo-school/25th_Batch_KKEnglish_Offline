@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">Edit Announcement</h1>
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
            {{-- 更新用フォーム（PUTメソッド） --}}
            <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-bold">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $announcement->title) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Target Audience</label>
                    <select name="target" class="form-select" required>
                        <option value="all" @selected(old('target', $announcement->target) === 'all')>All (Students & Teachers)</option>
                        <option value="students" @selected(old('target', $announcement->target) === 'students')>Students Only</option>
                        <option value="teachers" @selected(old('target', $announcement->target) === 'teachers')>Teachers Only</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Content</label>
                    <textarea name="content" class="form-control" rows="6" required>{{ old('content', $announcement->content) }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>

            <hr class="my-4">

            {{-- 削除用フォーム（DELETEメソッド） --}}
            <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="text-end" onsubmit="return confirm('本当にこのお知らせを削除しますか？この操作は元に戻せません。');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
