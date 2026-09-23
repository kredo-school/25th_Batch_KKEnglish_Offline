    <nav class="px-2 py-3 fw-bold fs-5">

        <a href="{{ route('students.dashboard') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('students.dashboard') ? 'student-active fw-semibold' : '' }}">
            Dashboard
        </a>

        <a href="{{ route('students.reservations.index') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('students.reservations.index') ? 'student-active fw-semibold' : '' }}">
            Book a lesson
        </a>

         <a href="{{ route('students.reservations.upcoming') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('students.reservations.upcoming') ? 'student-active fw-semibold' : '' }}">
            My lessons
        </a>

         <a href="{{ route('students.teacher-list') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('students.teacher-list') ? 'student-active fw-semibold' : '' }}">
            Teacher list
        </a>

        <a href="{{ route('students.history.index') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('student.history.index') ? 'student-active fw-semibold' : '' }}">
            Learning history
        </a>

        <a href="{{ route('materials.index') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('materials.*') ? 'student-active fw-semibold' : '' }}">
            Teaching Materials
        </a>

        <a href="#"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
            (Learning Progress)
        </a>

        <a href="#"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
            (Notifications)
        </a>

    </nav>

<style>
    .student-active {
        background-color: rgba(13, 202, 240, 0.10);
    }
</style>
