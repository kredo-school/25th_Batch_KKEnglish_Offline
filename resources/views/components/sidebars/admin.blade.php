<aside class="bg-light border-end min-vh-100">
    <nav class="px-2 py-3 fw-bold fs-5">

        <a href="{{ route('admin.dashboard') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('admin.dashboard')? 'bg-secondary-subtle fw-semibold': '' }}">
            Dashboard
        </a>

        <a href="{{ route('admin.teachers.index') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('admin.teachers.index')? 'bg-secondary-subtle fw-semibold': '' }}">
            Teacher management
        </a>

        <a href="{{ route('admin.materials.index') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
           {{ request()->routeIs('admin.materials.index')? 'bg-secondary-subtle fw-semibold': '' }}">
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
