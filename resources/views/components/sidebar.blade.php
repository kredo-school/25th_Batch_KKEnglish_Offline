<aside class="w-64 bg-light border-r min-h-screen">
    {{-- <div class="p-4 font-bold text-xl">Menu</div> --}}
    <nav class="px-2 fw-bold fs-5">
        @php $roleId = (int) (auth()->user()->role_id ?? 0); @endphp

        {{-- student --}}
        @if ($roleId === 1)
            <a href="{{ route('student.dashboard') }}" class="block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Dashboard</a>
            <a href="" class="block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Book a lesson</a><br>
            <a href="" class="block px-3 py-2 rounded mb-1 text-dark text-decoration-none">My lessons</a><br>
            <a href="" class="block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Learning history</a><br>
            <a href="" class="block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Learning Progress</a><br>
            <a href="" class="block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Teaching Materials</a><br>
            <a href="" class="block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Notifications</a><br>
            <a href="" class="block px-3 py-2 rounded mb-1 text-dark text-decoration-none">User Guide</a><br>
            <a href="" class="block px-3 py-2 rounded mb-1 text-dark text-decoration-none">AI customized Lesson</a>

        {{-- teacher --}}
        @elseif ($roleId === 2)
            <a href="{{ route('teacher.dashboard') }}" class="block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Dashboard</a><br>
            <a href="" class="block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Booked lessons</a><br>
            <a href="" class="block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Lesson History</a><br>
            <a href="" class="block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Teaching Materials</a><br>
            <a href="" class="block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Notifications</a>

        {{-- admin --}}
        @elseif ($roleId === 3)
            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded mb-1  text-dark text-decoration-none text-decoration-none">Dashboard</a><br>
            <a href="" class="block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Teacher management</a><br>
            <a href="" class="block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Material management</a><br>
            <a href="" class="block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Student management</a><br>
            <a href="" class="block px-3 py-2 rounded mb-1 text-dark text-decoration-none">Schedule management</a>
        @else
            <p class="px-3 py-2 text-gray-400">No menu to show</p>
        @endif
    </nav>
</aside>
