<nav class="navbar navbar-expand-lg border-bottom {{ $barClass }}">

    <div class="container">

        {{-- ===============================
             Logo
        ================================ --}}
        <a class="navbar-brand"
           href="{{ $homeHref }}">

            <img
                src="{{ asset('images/kkenglish-logo.png') }}"
                alt="KK English"
                height="55"
            >

        </a>


        {{-- ===============================
             Home
        ================================ --}}
        <a
            href="{{ $homeHref }}"
            class="nav-link {{ $textClass }}"
        >
            Home
        </a>


        {{-- ===============================
             Account
        ================================ --}}
        <div class="dropdown ms-auto">

            <button
                class="btn border-0 shadow-none dropdown-toggle d-flex align-items-center {{ $textClass }}"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >

                {{-- ===============================
                     Profile Image
                ================================ --}}
                @if($user?->profile_image)

                    <img
                        src="{{ asset('storage/' . $user->profile_image) }}"
                        alt="{{ $user->first_name }}"
                        width="40"
                        height="40"
                        class="rounded-circle me-2"
                        style="object-fit: cover;"
                    >

                @else

                    <i class="fa-solid fa-circle-user me-2 fa-2x"></i>

                @endif


                {{-- ===============================
                     Login User Name
                ================================ --}}
                <span>
                    {{ $user?->first_name }}
                </span>

            </button>


            {{-- ===============================
                 Dropdown Menu
            ================================ --}}
            <ul class="dropdown-menu dropdown-menu-end">

                {{-- ===============================
                     Profile
                ================================ --}}
                @if($roleCode == 'student')

                    <li>
                        <a
                            class="dropdown-item"
                            href="{{ route('student.profile') }}"
                        >
                            Profile
                        </a>
                    </li>

                @elseif($roleCode == 'teacher')

                    <li>
                        <a
                            class="dropdown-item"
                            href="{{ route('teachers.show', $user->teacher->id) }}"
                        >
                            Profile
                        </a>
                    </li>

                @endif


                {{-- Divider --}}
                <li>
                    <hr class="dropdown-divider">
                </li>


                {{-- ===============================
                     Logout
                ================================ --}}
                <li>

                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="dropdown-item"
                        >
                            Logout
                        </button>

                    </form>

                </li>

            </ul>

        </div>

    </div>

</nav>