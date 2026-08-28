<aside class="bg-light border-end min-vh-100">
    <nav class="px-2 py-3 fw-bold fs-5">

        <a href="{{ route('admin.dashboard') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
            Dashboard
        </a>

        <a href="{{ route('admin.teachers.index') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
            Teacher management
        </a>

        <a href="{{ route('admin.materials.index') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
            Material management
        </a>

        <a href="#"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
            Student management
        </a>

        <a href="#"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
            Schedule management
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
            User management
        </a>

    </nav>
</aside>
