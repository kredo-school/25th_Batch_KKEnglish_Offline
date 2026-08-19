@php
    $user = auth()->user();
    $roleId = (int) ($user->role_id ?? 0);

    // role別設定
    $roleConfig = match ($roleId) {
        1 => [ // student
            'barClass' => 'bg-info-subtle',   // 水色系（Bootstrap）
            'textClass' => 'text-dark',
            'homeLabel' => 'Student Home',
            'accountLabel' => 'Student',
        ],
        2 => [ // teacher
            'barClass' => 'bg-warning',       // オレンジ系
            'textClass' => 'text-dark',
            'homeLabel' => 'Teacher Home',
            'accountLabel' => 'Teacher',
        ],
        3 => [ // admin
            'barClass' => 'bg-dark',          // 黒
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
