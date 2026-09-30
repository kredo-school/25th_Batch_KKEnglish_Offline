    <nav class="px-2 py-3 fw-bold fs-5">

        <a href="{{ route('students.dashboard') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('students.dashboard') ? 'student-active fw-semibold' : '' }}">
            Dashboard
        </a>

        <a href="{{ route('students.reservations.index') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('students.reservations.index') ? 'student-active fw-semibold' : '' }}">
            Book a Lesson
        </a>

         <a href="{{ route('students.reservations.upcoming') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('students.reservations.upcoming') ? 'student-active fw-semibold' : '' }}">
            Upcoming Lessons
        </a>

         <a href="{{ route('students.teacher-list') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('students.teacher-list','teachers.show') ? 'student-active fw-semibold' : '' }}">
            Teacher List
        </a>

        <a href="{{ route('students.history.index') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('students.history.index',
                                  'students.point-history.index',
                                  'students.reviews.create') ? 'student-active fw-semibold' : '' }}">
            Learning History
        </a>

        <a href="{{ route('materials.index') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('materials.*') ? 'student-active fw-semibold' : '' }}">
            Teaching Materials
        </a>

        <a href="{{ route('students.progress.test') }}"
            class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
            {{ request()->routeIs('students.progress.test') ? 'student-active fw-semibold' : '' }}">
            Learning Progress
        </a>

    </nav>

<style>
    .student-active {
        background-color: rgba(13, 202, 240, 0.10);
    }
</style>
