    @php
        // クエリパラメータ ?menu=schedule が指定されているか、またはスケジュール関連のルートの場合に 'schedule' メニューにする
        $isScheduleMenu = request('menu') === 'schedule'
            || request()->routeIs('admin.schedules.*')
            || request()->routeIs('admin.shift-patterns.*')
            || request()->routeIs('admin.shift-pattern-assignments.*');

        $menu = $isScheduleMenu ? 'schedule' : 'main';
    @endphp

    <nav class="px-2 py-3 fw-bold fs-5">
        @if($menu === 'schedule')
            {{-- Schedule専用サイドバー --}}
            <a href="{{ route('admin.dashboard', ['menu' => 'main']) }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
                <i class="fa-solid fa-angles-left"></i> Back to Dashboard
            </a>

            <div class="px-3 py-2 mb-1 fw-semibold text-muted fs-6">Schedule management</div>

            {{-- 週間予定表 --}}
            <a href="{{ route('admin.schedules.index', ['menu' => 'schedule']) }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.schedules.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                Today's schedule
            </a>

            {{-- 稼働状況マトリクス --}}
            <a href="{{ route('admin.schedules.matrix', ['menu' => 'schedule']) }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.schedules.matrix*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                Operational Status
            </a>

            {{-- 予想期間設定 --}}
            {{-- <a href="{{ route('admin.season-periods.index', ['menu' => 'schedule']) }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.season-periods.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                Season settings
            </a> --}}

            {{-- 予想予約数設定 --}}
            {{-- <a href="{{ route('admin.expected-reservations.edit', ['menu' => 'schedule']) }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.expected-reservations.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                Expected reservations settings
            </a> --}}

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
        @else
            {{-- 通常サイドバー --}}
            <a href="{{ route('admin.dashboard') }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.dashboard') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                Dashboard
            </a>

            <a href="{{ route('admin.teachers.index') }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.teachers.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                Teacher List
            </a>

            <a href="{{ route('admin.materials.index') }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.materials.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                Material List
            </a>

            <a href="{{ route('admin.students.index') }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.students.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                Student List
            </a>

            {{-- Schedule management (クリックすると Schedule専用メニューに切り替わります) --}}
            <a href="{{ route('admin.schedules.index', ['menu' => 'schedule']) }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.schedules.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                Schedule management
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.users.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                User List
            </a>

            <a href="{{ route('admin.announcements.index') }}"
               class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.announcements.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
                Announcement
            </a>
        @endif
    </nav>
