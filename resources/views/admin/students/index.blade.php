@extends('layouts.app')

@section('content')
<div class="container py-3">

    {{-- ============================================================
         ページタイトル
    ============================================================ --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Student Management</h1>
    </div>


    {{-- ============================================================
         メッセージ
    ============================================================ --}}
    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close">
            </button>
        </div>
    @endif

    {{-- ============================================================
         検索
    ============================================================ --}}
    <div class="card mb-3">
        <div class="card-header">
            Search
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.students.index') }}" class="row g-2">

                {{-- ID・名前 --}}
                <div class="col-md-5">
                    <label class="form-label">
                        ID・Name
                    </label>
                    <input type="text" name="keyword" class="form-control" value="{{ request('keyword') }}" placeholder="Student ID or Name">
                </div>

                {{-- Active --}}
                <div class="col-md-3">
                    <label class="form-label">
                        Active
                    </label>
                    <select name="active" class="form-select">

                        <option value="">
                            All
                        </option>

                        <option value="1"
                            @selected(request('active') === '1')>
                            Active
                        </option>
                        <option value="0" @selected(request('active') === '0')>
                            Inactive
                        </option>
                    </select>
                </div>

                {{-- ボタン --}}
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        Search
                    </button>
                    <a href="{{ route('admin.students.index') }}"
                        class="btn btn-outline-secondary">
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>


    {{-- ============================================================
         Student List
    ============================================================ --}}
    <div class="card">
        <div class="card-header">
            Student List
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>
                            Student ID
                        </th>
                        <th>
                            Name
                        </th>
                        <th>
                            Birthday
                        </th>
                        <th class="text-end">
                            Remaining Points
                        </th>
                        <th class="text-center">
                            Active
                        </th>
                        <th class="text-center">
                            View
                        </th>
                    </tr>
                </thead>

                <tbody>
                @forelse($students as $student)
                    @php
                        $name = trim(
                            ($student->user->first_name ?? '') .
                            ' ' .
                            ($student->user->last_name ?? '')
                        );

                        $pointBalance =
                            (int) ($student->calculated_point_balance ?? 0);
                    @endphp

                    <tr>
                        {{-- Student ID --}}
                        <td>
                            {{ $student->id }}
                        </td>

                        {{-- Name --}}
                        <td class="fw-semibold">
                            {{ $name ?: 'Name Not Registered' }}
                        </td>

                        {{-- Birthday --}}
                        <td>
                            {{ $student->birthday?->format('Y-m-d') ?? '-' }}
                        </td>

                        {{-- Point --}}
                        <td class="text-end fw-bold">
                            {{ number_format($pointBalance) }}
                            pt
                        </td>

                        {{-- Active --}}
                        <td class="text-center">
                            @if(($student->user->status ?? null) === 'active')
                                <span class="badge bg-success">
                                    ON
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    OFF
                                </span>
                            @endif
                        </td>

                        {{-- View --}}
                        <td class="text-center">
                            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-primary btn-sm">
                                View
                            </a>
                            <a href="{{ route('admin.students.profile', $student) }}" class="btn btn-outline-primary btn-sm ms-1">
                                Profile
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No students found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- ============================================================
         Pagination
    ============================================================ --}}
    <div class="mt-3">

        {{ $students->links() }}

    </div>

</div>
@endsection