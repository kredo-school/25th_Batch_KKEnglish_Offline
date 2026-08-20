@php
    $user = auth()->user();

    // ログインユーザーのrole_id
    $roleId = $user?->role_id;

    // 1 = Student
    // 2 = Teacher
    // 3 = Admin
    if ($roleId == 1) {
        $viewMode = 'student';

    } elseif ($roleId == 2) {
        $viewMode = 'teacher';

    } elseif ($roleId == 3) {
        $viewMode = 'admin';

    } else {
        $viewMode = 'guest';
    }

@endphp


@php
    // Navbarの表示
    if ($viewMode == 'student') {

        $barClass = 'bg-info-subtle';
        $textClass = 'text-dark';
        $accountLabel = 'Student';
        $homeHref = route('student.dashboard');

    } elseif ($viewMode == 'teacher') {

        $barClass = 'bg-warning';
        $textClass = 'text-dark';
        $accountLabel = 'Teacher';
        $homeHref = route('teacher.dashboard');

    } elseif ($viewMode == 'admin') {

        $barClass = 'bg-dark';
        $textClass = 'text-white';
        $accountLabel = 'Admin';
        $homeHref = route('admin.dashboard');

    } else {

        $barClass = 'bg-white';
        $textClass = 'text-dark';
         $accountLabel = 'Account';
        $homeHref = '#';
    }
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>

<div id="app">

    {{-- Navbar --}}
    @include('components.navbar', [
        'user' => $user,
        'barClass' => $barClass,
        'textClass' => $textClass,
        'accountLabel' => $accountLabel,
        'homeHref' => $homeHref
    ])


    <div class="container-fluid px-0">
        <div class="row g-0">

            {{-- Sidebar --}}
            @auth
                <aside class="col-md-3 col-lg-2">
                    @include('components.sidebar', [
                        'viewMode' => $viewMode
                    ])
                </aside>
            @endauth


            {{-- 各ページの内容 --}}
            <main class="col py-4 px-4">
                @yield('content')
            </main>

        </div>
    </div>

</div>

</body>
</html>