@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
@php
    // Controllerから $weekStart (Carbon) が来る想定。無ければ今週開始日で代替
    $ws = isset($weekStart)
        ? \Carbon\Carbon::parse($weekStart)->startOfWeek(\Carbon\Carbon::MONDAY)
        : now()->startOfWeek(\Carbon\Carbon::MONDAY);

    $prev = $ws->copy()->subWeek()->toDateString();
    $curr = now()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString();
    $next = $ws->copy()->addWeek()->toDateString();
@endphp

<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            {{-- Hello Header --}}
            <div class="bg-light p-4 mb-3">
                <h2 class="fw-bold mb-1">Hello, Name</h2>
                <p class="text-secondary mb-0">{{ now()->format('Y-m-d') }}</p>
                <p class="fw-semibold mb-0">
                    <i class="fa-regular fa-clock me-1"></i>
                    {{ now()->format('H:i') }}
                </p>
            </div>

            {{-- Week Navigation --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="btn-group" role="group" aria-label="week navigation">
                    <a class="btn btn-outline-secondary"
                       href="{{ route('admin.dashboard', ['week_start' => $prev]) }}">
                        ← Prev
                    </a>
                    <a class="btn btn-outline-primary"
                       href="{{ route('admin.dashboard', ['week_start' => $curr]) }}">
                        This Week
                    </a>
                    <a class="btn btn-outline-secondary"
                       href="{{ route('admin.dashboard', ['week_start' => $next]) }}">
                        Next →
                    </a>
                </div>
                <div class="small text-secondary">
                    Display Week: {{ $ws->format('Y-m-d') }} Start / Week {{ $weekNo ?? $ws->isoWeek() }} ({{ $weekYear ?? $ws->isoWeekYear() }})
                </div>
            </div>

            {{-- Weekly Summary --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle mb-0">
                            <tr>
                                <th></th>
                                @foreach(($dashboardRows ?? []) as $row)
                                    <th>{{ $row['day_ja'] ?? '' }}</th>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="text-start">Date</th>
                                @foreach(($dashboardRows ?? []) as $row)
                                    <td>{{ $row['label'] ?? '' }}</td>
                                @endforeach
                            </tr>

                            <tr>
                                <th class="text-start">Total Available Lessons</th>
                                @foreach(($dashboardRows ?? []) as $row)
                                    <td>
                                        @if(($row['capacity'] ?? 0) > 0)
                                            <a href="{{ route('admin.dashboard.details', ['date' => $row['date'], 'type' => 'capacity']) }}">
                                                {{ $row['capacity'] }}
                                            </a>
                                        @else
                                            0
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            <tr>
                                <th class="text-start">Booked</th>
                                @foreach(($dashboardRows ?? []) as $row)
                                    <td>
                                        @if(($row['booked'] ?? 0) > 0)
                                            <a href="{{ route('admin.dashboard.details', ['date' => $row['date'], 'type' => 'booked']) }}">
                                                {{ $row['booked'] }}
                                            </a>
                                        @else
                                            0
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            <tr>
                                <th class="text-start">Auto-Booked Students</th>
                                @foreach(($dashboardRows ?? []) as $row)
                                    <td>
                                        @if(($row['auto_booked'] ?? 0) > 0)
                                            <a href="{{ route('admin.dashboard.details', ['date' => $row['date'], 'type' => 'auto_booked']) }}">
                                                {{ $row['auto_booked'] }}
                                            </a>
                                        @else
                                            0
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            <tr>
                                <th class="text-start">Working Teachers</th>
                                @foreach(($dashboardRows ?? []) as $row)
                                    <td>
                                        @if(($row['working_teachers'] ?? 0) > 0)
                                            <a href="{{ route('admin.dashboard.details', ['date' => $row['date'], 'type' => 'working_teachers']) }}">
                                                {{ $row['working_teachers'] }}
                                            </a>
                                        @else
                                            0
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Information --}}
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Announcements</h5>

                    @if(($announcements ?? collect())->isEmpty())
                        <p class="text-secondary mb-0">No announcements.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($announcements as $item)
                                <li class="list-group-item px-0">
                                    <div class="fw-semibold">{{ $item->title ?? '' }}</div>
                                    <div class="small text-secondary">{{ \Illuminate\Support\Str::limit($item->body ?? '', 120) }}</div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
