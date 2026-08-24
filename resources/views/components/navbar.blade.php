<nav class="navbar navbar-expand-lg border-bottom {{ $barClass }}">

    <div class="container">

        {{-- ロゴ --}}
        <a class="navbar-brand" href="{{ $homeHref }}">
            <img src="{{ asset('images/kkenglish-logo.png') }}"
                 alt="KK English"
                 height="55">
        </a>

        {{-- Home --}}
        <a href="{{ $homeHref }}"
           class="nav-link {{ $textClass }}">
            Home
        </a>

        {{-- Account --}}
        <div class="dropdown ms-auto">

            <button class="btn border-0 shadow-none dropdown-toggle d-flex align-items-center {{ $textClass }}"
                    data-bs-toggle="dropdown">

                <i class="fa-solid fa-circle-user me-2 fa-2x"></i>
                {{ $user?->name ?? $accountLabel }}

            </button>

            <ul class="dropdown-menu dropdown-menu-end">

                {{-- Profile --}}
                <li>
                    @if($user->role=='student')
                        <a class="dropdown-item" href="{{ route('student.profile') }}">
                            Profile
                        </a>
                    @elseif($user->role=='teacher')
                        <a class="dropdown-item" href="{{ route('teacher.profile') }}">
                            Profile
                        </a>
                    @endif
                </li>

                {{-- Logout --}}
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit" class="dropdown-item">
                            Logout
                        </button>
                    </form>
                </li>

            </ul>

        </div>

    </div>

</nav>
