    <nav class="px-2 py-3 fw-bold fs-5">

        <a href="{{ route('teacher.dashboard') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('teacher.dashboard') ? 'teacher-active' : '' }}">
            Dashboard
        </a>

        <a href="{{ route('teachers.schedule') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('teachers.schedule') ? 'teacher-active' : '' }}">
            My Schedule
        </a>

         <a href="{{ route('teachers.reservations.index') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('teachers.reservations.index') ? 'teacher-active' : '' }}">
            Upcoming Lessons
        </a>

        <a href="{{ route('teachers.history.index') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('teachers.history.index') ? 'teacher-active' : '' }}">
            Lesson History
        </a>

        <a href="#"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
            My Materials
        </a>

        <a href="#"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
            (Notifications)
        </a>

    </nav>

<style>
    .teacher-active {
        background-color:  rgba(255, 209, 102, 0.16);
    }
</style>

