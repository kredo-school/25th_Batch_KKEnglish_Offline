    @php

    $isOperationalStatusMenu = request('menu') === 'operational-status'
        || request()->routeIs('admin.operational-status.*');

    $isStationMenu = request('menu') === 'station'
        || request()->routeIs('admin.stations.*');

    $isScheduleMenu = !$isOperationalStatusMenu
        && !$isStationMenu
        && (
            request('menu') === 'schedule'
            || request()->routeIs('admin.schedules.*')
            || request()->routeIs('admin.shift-patterns.*')
            || request()->routeIs('admin.shift-pattern-assignments.*')
        );

    if ($isScheduleMenu) {
        $menu = 'schedule';
    } elseif ($isOperationalStatusMenu) {
        $menu = 'operational-status';
    } elseif ($isStationMenu) {
        $menu = 'station';
    } else {
        $menu = 'main';
    }

@endphp

<style>
    .admin-sidebar a,
    .admin-sidebar .admin-sidebar-title {
        white-space: nowrap;
        font-size: clamp(0.75rem, 1.2vw, 1rem);
    }

    .admin-sidebar .admin-sidebar-title {
        font-size: clamp(0.7rem, 1.1vw, 0.875rem);
    }
</style>

    <nav class="px-2 py-3 fw-bold fs-5 admin-sidebar">
        @if($menu === 'schedule')
            {{-- Schedule専用サイドバー --}}
            <a href="{{ route('admin.dashboard', ['menu' => 'main']) }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
                <i class="fa-solid fa-angles-left"></i> Back to Dashboard
            </a>

            <div class="px-3 py-2 mb-1 fw-semibold text-muted fs-6 bg-secondary-subtle "><i class="fa-solid fa-calendar-days"></i> Scheduler</div>

            {{-- 今日のスケジュール --}}
            <a href="{{ route('admin.schedules.index', ['menu' => 'schedule']) }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.schedules.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                Today's schedule
            </a>

            <hr>

            {{-- シフト作成 --}}
            <a href="{{ route('admin.shift-patterns.index', ['menu' => 'schedule']) }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.shift-patterns.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                Shift creation
            </a>

            {{-- 先生割り当て --}}
            <a href="{{ route('admin.shift-pattern-assignments.index', ['menu' => 'schedule']) }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.shift-pattern-assignments.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                Teacher assignment
            </a>

        @elseif($menu === 'operational-status')
            {{-- Schedule専用サイドバー --}}
            <a href="{{ route('admin.dashboard', ['menu' => 'main']) }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
                <i class="fa-solid fa-angles-left"></i> Back to Dashboard
            </a>

        <div class="px-3 py-2 mb-1 fw-semibold text-muted fs-6 bg-secondary-subtle ">
            <i class="fa-solid fa-square-poll-vertical"></i> Operational Status
        </div>

            {{-- Monthly Overview --}}
            <a href="{{ route('admin.operational-status.index', ['menu' => 'operational-status']) }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.operational-status.index') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                Monthly Overview
            </a>

            {{-- Weekly Overview --}}
            <a href="{{ route('admin.operational-status.matrix', ['menu' => 'operational-status']) }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.operational-status.matrix*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                Weekly Overview
            </a>

            <hr>

            {{-- 予想期間設定 --}}
            <a href="{{ route('admin.season-periods.index', ['menu' => 'operational-status']) }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.season-periods.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                Season settings
            </a>

            {{-- 予想予約数設定 --}}
            <a href="{{ route('admin.expected-reservations.edit', ['menu' => 'operational-status']) }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.expected-reservations.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                Expected reservations settings
            </a>

        @elseif($menu === 'station')

            {{-- Station List専用サイドバー --}}
            <a href="{{ route('admin.dashboard', ['menu' => 'main']) }}"
            class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
                <i class="fa-solid fa-angles-left"></i>
                Back to Dashboard
            </a>

            {{-- <div class="px-3 py-2 mb-1 fw-semibold text-muted fs-6 bg-secondary-subtle">
                Station Management
            </div> --}}

            {{-- Station List --}}
            <a href="{{ route('admin.stations.index',['menu' => 'station']) }}"
            class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
            {{ request()->routeIs('admin.stations.*') ? 'bg-secondary-subtle fw-semibold': '' }}">
                <i class="fa-solid fa-location-dot me-2"></i>
                Station List
            </a>

            {{-- Teacher Station Assignmentへ --}}
            <a href="{{ route('admin.teacher-station-assignments.index',['menu' => 'station']) }}"
            class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.teacher-station-assignments.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                <i class="fa-solid fa-chalkboard-user me-2"></i>
                Teacher Assignment
            </a>
        @else
            {{-- 通常サイドバー --}}
            <a href="{{ route('admin.dashboard') }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.dashboard') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                Dashboard
            </a>
            {{-- 稼働状況マトリクス --}}
            <a href="{{ route('admin.operational-status.index') }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.operational-status.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                <i class="fa-solid fa-square-poll-vertical"></i> Operational Status
            </a>

            {{-- Schedule management (クリックすると Schedule専用メニューに切り替わります) --}}
            <a href="{{ route('admin.schedules.index', ['menu' => 'schedule']) }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.schedules.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                <i class="fa-solid fa-calendar-days"></i> Scheduler
            </a>

            <a href="{{ route('admin.stations.index', ['menu' => 'station']) }}"
                class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.stations.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                <i class="fa-solid fa-location-dot me-2"></i>
                Station List
            </a>

            <hr>

            <a href="{{ route('admin.teachers.index') }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.teachers.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                <i class="fa-solid fa-person-chalkboard"></i> Teacher List
            </a>

            <a href="{{ route('admin.materials.index') }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.materials.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                <i class="fa-solid fa-book"></i> Material List
            </a>

            <a href="{{ route('admin.students.index') }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.students.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                <i class="fa-solid fa-user-pen"></i> Student List
            </a>

            {{-- User management --}}
            <a href="{{ route('admin.users.index') }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.users.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                <i class="fa-solid fa-circle-user"></i> User List
            </a>

            <hr>

            {{-- Announcement management --}}
            <a href="{{ route('admin.announcements.index') }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.announcements.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                <i class="fa-solid fa-bullhorn"></i> Announcement
            </a>
        @endif
    </nav>
