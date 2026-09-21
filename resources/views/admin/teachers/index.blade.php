{{--
    ================================================================
    【このファイルの役割】
    resources/views/admin/teachers/index.blade.php
    ================================================================

    Teacher Management の一覧画面です。

    このBladeでは計算処理をしていません。
    TeacherController@index が作った以下の値を表示するだけです。

    - booked             ：今日の予約数
    - slots_number       ：今日の teacher_schedules から計算したスロット数
    - shift_pattern_name ：今日の teacher_schedules に紐づくシフトパターン名
    - today              ：今日の勤務時間、または Off

    「Number of Slots がSeederの先生だけに出る」という問題を避けるため、
    Controller側で teacher_schedules を基準に値を作っています。
    ================================================================
--}}

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
        <div class="col-auto">
            <button class="btn btn-outline-primary">Search</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    {{-- 今日の予約件数 --}}
                    <th>Booked</th>
                    {{-- teacher_schedules の勤務時間から計算した予約可能枠数 --}}
                    <th>Number of Slots</th>
                    {{-- 今日の teacher_schedules に紐づくシフトパターン --}}
                    <th>Shift Pattern</th>
                    <th>Status</th>
                    {{-- 今日の勤務時間 --}}
                    <th>Today</th>
                    <th class="text-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($teachers as $teacher)
                @php
                    $user = $teacher->user;
                    $status = $user->status ?? 'unknown';
                @endphp
                <tr>
                    <td>{{ $teacher->id }}</td>
                    <td>{{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}</td>
                    <td>{{ $teacher->booked ?? 0 }}</td>
                    {{--
                        Controllerで重複時間をmergeした後のスロット数です。
                        Blade側では再計算しません。
                    --}}
                    <td>{{ $teacher->slots_number ?? 0 }}</td>
                    {{-- teacher_schedules → shift_patterns.pattern_name --}}
                    <td>{{ $teacher->shift_pattern_name ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                            {{ $status }}
                        </span>
                    </td>
                    {{-- teacher_schedules がなければ Controller が Off を設定します。 --}}
                    <td>{{ $teacher->today ?? '-' }}</td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.teachers.show', $teacher) }}" class="btn btn-sm btn-outline-secondary">Details</a>
                        {{-- <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-sm btn-outline-primary">Edit</a> --}}
                        <a href="{{ route('admin.teachers.materials.edit', $teacher) }}" class="btn btn-sm btn-outline-info">Materials</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No teacher data available.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $teachers->links() }}
</div>
@endsection
