@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Shift Pattern Management</h1>
        <a href="{{ route('admin.shift-patterns.create') }}" class="btn btn-primary btn-sm">＋ New Pattern</a>
    </div>

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
                        <th>Teacher</th>
                        <th></th>
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
                        <td>{{ $pattern->teachers_count ?? 0 }}</td>  {{-- 未解決 --}}
                        <td class="text-end">
                            <a href="{{ route('admin.shift-patterns.edit', $pattern) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                            <a href="{{ route('admin.shift-pattern-assignments.create', ['pattern_id' => $pattern->id]) }}" class="btn btn-outline-secondary btn-sm">Assign</a>
                            <form action="{{ route('admin.shift-patterns.destroy', $pattern) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No patterns found.</td></tr>
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
