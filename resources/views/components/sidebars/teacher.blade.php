<aside class="bg-light border-end min-vh-100">
    <nav class="px-2 py-3 fw-bold fs-5">

        <a href="{{ route('teacher.dashboard') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('teacher.dashboard') ? 'bg-warning-subtle' : '' }}">
            Dashboard
        </a>

        <a href="#"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
            Booked lessons
        </a>

        <a href="#"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
            Lesson History
        </a>

        <a href="{{ route('materials.index') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
            Teaching Materials
        </a>

        <a href="#"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
            Notifications
        </a>

        <a href="{{ route('teacher.schedules.index') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
            Schedule Settings
        </a>

    </nav>
</aside>
