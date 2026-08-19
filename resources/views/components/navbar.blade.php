<nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">

        {{-- 左：ロゴ --}}
        <a class="navbar-brand" href="#">
          <img src="{{ asset('images/kkenglish-logo.png') }}"
         alt="KK English"
         height="55">
        </a>

        {{-- Home --}}
        <a href="#" class="nav-link text-dark">
            Home
        </a>

        {{-- 右：アカウント --}}
        <div class="dropdown ms-auto">

            <button class="btn border-0 dropdown-toggle d-flex align-items-center gap-2"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">

                <div class="d-flex align-items-center justify-content-center">
                 <i class="fa-solid fa-circle-user fa-2x"></i>
                </div>

                <span class="text-start">
                   Account Name
                </span>

            </button>

            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="#">
                        Profile
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="#">
                        Logout
                    </a>
                </li>
            </ul>

        </div>

    </div>
</nav>
