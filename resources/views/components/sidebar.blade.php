<aside class="bg-light border-end min-vh-100">
    <nav class="px-2 py-3 fw-bold fs-5">
        @php
            $user = auth()->user();
            $roleId = (int) ($user->role_id ?? 0);

            // v7の分岐スタイルは維持しつつ、閲覧中ページを優先
            // adminがstudent/teacherページを見る場合に対応
            if (request()->routeIs('student.*')) {
                $effectiveRoleId = 1;
            } elseif (request()->routeIs('teacher.*')) {
                $effectiveRoleId = 2;
            } elseif (request()->routeIs('admin.*')) {
                $effectiveRoleId = 3;
            } else {
                $effectiveRoleId = $roleId;
            }
        @endphp

        {{-- admin横断閲覧の見える化 --}}
        @if ($roleId === 3 && $effectiveRoleId !== 3)
            <div class="alert alert-warning py-1 px-2 small mb-2">
                Adminとして
                {{ $effectiveRoleId === 1 ? 'Student' : 'Teacher' }}
                画面を表示中
            </div>
        @endif

        {{-- student --}}
        @if ($effectiveRoleId === 1)
            <a href="{{ route('student.dashboard') }}" class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Dashboard</a>
            <a href="#" class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Book a lesson</a>
            <a href="#" class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">My lessons</a>
            <a href="#" class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Learning history</a>
            <a href="#" class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Learning Progress</a>
            <a href="#" class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Teaching Materials</a>
            <a href="#" class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Notifications</a>
            <a href="#" class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">User Guide</a>
            <a href="#" class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">AI customized Lesson</a>

        {{-- teacher --}}
        @elseif ($effectiveRoleId === 2)
            <a href="{{ route('teacher.dashboard') }}" class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Dashboard</a>
            <a href="#" class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Booked lessons</a>
            <a href="#" class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Lesson History</a>
            <a href="#" class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Teaching Materials</a>
            <a href="#" class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Notifications</a>

        {{-- admin --}}
        @elseif ($effectiveRoleId === 3)
            <a href="{{ route('admin.dashboard') }}" class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Dashboard</a>
            <a href="#" class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Teacher management</a>
            <a href="#" class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Material management</a>
            <a href="#" class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Student management</a>
            <a href="#" class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Schedule management</a>
        @else
            <p class="px-3 py-2 text-muted">No menu to show</p>
        @endif
    </nav>
</aside>
