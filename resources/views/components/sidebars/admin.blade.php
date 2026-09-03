<aside class="bg-light border-end min-vh-100">
    @php
    $menu = request('menu', 'main'); // main | schedule
@endphp

    <nav class="px-2 py-3 fw-bold fs-5">
         @if($menu === 'schedule')
        {{-- Schedule専用サイドバー --}}
        <a href="{{ url('/admins/dashboard?menu=main') }}"
   class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
    ← Back
</a>

        <div class="px-3 py-2 mb-1 fw-semibold text-muted">Schedule management</div>

        <a href="{{ route('admin.shift-patterns.index', ['menu' => 'schedule']) }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.shift-patterns.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
            Shift creation
        </a>

        <a href="{{ route('admin.shift-pattern-assignments.create', ['menu' => 'schedule']) }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none {{ request()->routeIs('admin.shift-pattern-assignments.*') ? 'bg-secondary-subtle fw-semibold' : '' }}">
            Teacher assignment
        </a>
    @else
        {{-- 通常サイドバー --}}
        <a href="{{ \Illuminate\Support\Facades\Route::has('admin.dashboard') ? route('admin.dashboard') : url('/admins/dashboard') }}"
   class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none
   {{ request()->routeIs('admin.dashboard') ? 'bg-secondary-subtle fw-semibold' : '' }}">
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

        {{-- Schedule management (parent) --}}
<a href="{{ url('/admins/dashboard?menu=schedule') }}"
   class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
    Schedule management
</a>

        <a href="{{ route('admin.users.index') }}"
           class="d-block px-3 py-2 rounded mb-1 text-dark text-decoration-none">
            User management
        </a>
    @endif
    </nav>
</aside>
