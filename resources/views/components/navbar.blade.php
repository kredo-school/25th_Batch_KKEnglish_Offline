@php
    $user = auth()->user();
    $roleId = (int) ($user->role_id ?? 0);

    // v2: ログインロールではなく「今見ている画面」を優先
    if (request()->routeIs('student.*')) {
        $viewMode = 'student';
    } elseif (request()->routeIs('teacher.*')) {
        $viewMode = 'teacher';
    } elseif (request()->routeIs('admin.*')) {
        $viewMode = 'admin';
    } else {
        $viewMode = match ($roleId) {
            1 => 'student',
            2 => 'teacher',
            3 => 'admin',
            default => 'guest',
        };
    }

    $roleConfig = match ($viewMode) {
        'student' => [
            'barClass' => 'bg-info-subtle',
            'textClass' => 'text-dark',
            'homeLabel' => 'Student Home',
            'accountLabel' => 'Student',
        ],
        'teacher' => [
            'barClass' => 'bg-warning',
            'textClass' => 'text-dark',
            'homeLabel' => 'Teacher Home',
            'accountLabel' => 'Teacher',
        ],
        'admin' => [
            'barClass' => 'bg-dark',
            'textClass' => 'text-white',
            'homeLabel' => 'Admin Home',
            'accountLabel' => 'Admin',
        ],
        default => [
            'barClass' => 'bg-white',
            'textClass' => 'text-dark',
            'homeLabel' => 'Home',
            'accountLabel' => 'Guest',
        ],
    };
@endphp

<nav class="navbar navbar-expand-lg border-bottom {{ $roleConfig['barClass'] }}">
    <div class="container">

        {{-- 左：ロゴ --}}
        <a class="navbar-brand" href="#">
            <img src="{{ asset('images/kkenglish-logo.png') }}"
                 alt="KK English"
                 height="55">
        </a>

        {{-- Home --}}
        <a href="#" class="nav-link {{ $roleConfig['textClass'] }}">
            {{ $roleConfig['homeLabel'] }}
        </a>

        {{-- adminが他画面表示中のバッジ --}}
        @if ($roleId === 3 && $viewMode !== 'admin')
            <span class="badge text-bg-warning ms-2">
                Admin viewing {{ ucfirst($viewMode) }}
            </span>
        @endif

        {{-- 右：アカウント --}}
        <div class="dropdown ms-auto">
            <button class="btn border-0 dropdown-toggle d-flex align-items-center gap-2 {{ $roleConfig['textClass'] }}"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">

                <div class="d-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-circle-user fa-2x"></i>
                </div>

                <span class="text-start">
                    {{ $user->name ?? $roleConfig['accountLabel'] }}
                </span>
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li><a class="dropdown-item" href="#">Logout</a></li>
            </ul>
        </div>

    </div>
</nav>
