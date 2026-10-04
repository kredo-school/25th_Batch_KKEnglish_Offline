@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h2 mb-0 fw-bold">Shift Pattern Management</h1>
        <a href="{{ route('admin.shift-patterns.create') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i>New Pattern</a>
    </div>

    {{-- 成功メッセージ --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
        </div>
    @endif

    {{-- 削除できない場合の警告 --}}
    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Warning</strong><br>
            {{ session('warning') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
        </div>
    @endif

    {{-- その他のエラー --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Pattern Name</th>
                        <th>Timezone</th>
                        <th>Slot(min)</th>
                        <th>Teachers</th>
                        <th>Past Shift</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($patterns as $pattern)
                    <tr>
                        <td>{{ $pattern->id }}</td>
                        <td>{{ $pattern->pattern_code }}</td>
                        <td>{{ $pattern->pattern_name }}</td>
                        <td>{{ $pattern->timezone ?? 'UTC' }}</td>
                        <td>{{ $pattern->slot_minutes ?? '-' }}</td>
                        <td>
                            @if (($pattern->teachers_count ?? 0) > 0)
                                <a href="{{ route('admin.shift-pattern-assignments.bulk-edit-by-pattern', ['shiftPattern' => $pattern->id]) }}" class="text-decoration-none fw-semibold">{{ $pattern->teachers_count }}
                                </a>
                            @else
                                0
                            @endif
                        </td>
                        <td class="text-center">
                            @if (($pattern->past_assignments_count ?? 0) > 0)
                                <i class="fa-solid fa-check text-success" title="Past shifts exist"></i>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.shift-patterns.edit', $pattern) }}" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen-to-square me-1"></i> Edit</a>
                            <a href="{{ route('admin.shift-pattern-assignments.create', ['pattern_id' => $pattern->id]) }}" class="btn btn-outline-secondary btn-sm">Assign</a>
                            <form action="{{ route('admin.shift-patterns.destroy', $pattern) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash me-1"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No patterns found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $patterns->links() }}
    </div>
</div>
@endsection
