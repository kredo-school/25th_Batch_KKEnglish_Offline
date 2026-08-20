@php
    $roleConfig = match ($viewMode ?? 'guest') {
        'student' => [
            'barClass' => 'bg-info-subtle',
            'textClass' => 'text-dark',
            'homeLabel' => 'Student Home',
            'accountLabel' => 'Student',
            'homeHref' => route('student.dashboard'),
        ],
        'teacher' => [
            'barClass' => '',
            'barStyle' => 'background-color:#f59e0b;', // オレンジ固定
            'textClass' => 'text-dark',
            'homeLabel' => 'Teacher Home',
            'accountLabel' => 'Teacher',
            'homeHref' => route('teacher.dashboard'),
        ],
        'admin' => [
            'barClass' => 'bg-dark',
            'textClass' => 'text-white',
            'homeLabel' => 'Admin Home',
            'accountLabel' => 'Admin',
            'homeHref' => route('admin.dashboard'),
        ],
        default => [
            'barClass' => 'bg-white',
            'textClass' => 'text-dark',
            'homeLabel' => 'Home',
            'accountLabel' => 'Guest',
            'homeHref' => '#',
        ],
    };

    $barStyle = $roleConfig['barStyle'] ?? '';
@endphp

<nav class="navbar navbar-expand-lg border-bottom {{ $roleConfig['barClass'] }}" style="{{ $barStyle }}">
    <div class="container">

        <a class="navbar-brand" href="#">
            <img src="{{ asset('images/kkenglish-logo.png') }}" alt="KK English" height="55">
        </a>

        <a href="{{ $roleConfig['homeHref'] }}" class="nav-link {{ $roleConfig['textClass'] }}">
            {{ $roleConfig['homeLabel'] }}
        </a>

        @if (($roleId ?? 0) === 3 && ($viewMode ?? 'admin') !== 'admin')
            <span class="badge text-bg-warning ms-2">Admin viewing {{ ucfirst($viewMode) }}</span>
        @endif

        <div class="dropdown ms-auto">
            <button class="btn border-0 dropdown-toggle d-flex align-items-center gap-2 {{ $roleConfig['textClass'] }}"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-circle-user fa-2x"></i>
                <span>{{ auth()->user()->name ?? $roleConfig['accountLabel'] }}</span>
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">Logout</button>
                    </form>
                </li>
            </ul>
        </div>

    </div>
</nav>
