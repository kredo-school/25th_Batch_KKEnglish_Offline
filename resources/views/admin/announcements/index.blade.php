@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Announcements</h1>
        <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary btn-sm">+ Create New</a>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Target</th>
                        <th>Title</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- コントローラーから渡されたデータをループで表示 --}}
                    @forelse($announcements as $announcement)
                        <tr>
                            <td>{{ $announcement->created_at->format('Y-m-d') }}</td>
                            <td>
                                @if($announcement->target === 'all')
                                    <span class="badge bg-primary">All</span>
                                @elseif($announcement->target === 'students')
                                    <span class="badge bg-info">Students Only</span>
                                @elseif($announcement->target === 'teachers')
                                    <span class="badge bg-warning">Teachers Only</span>
                                @else
                                    <span class="badge bg-secondary">Unknown</span>
                                @endif
                            </td>
                            <td>{{ $announcement->title }}</td>
                            <td class="text-center">
                                {{-- 今後、編集・削除機能などを追加する場合はここのリンクを変更します --}}
                                <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                No announcements have been registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{-- ペジネーションの表示 --}}
    <div class="mt-3">
        {{ $announcements->links() }}
    </div>
</div>
@endsection
