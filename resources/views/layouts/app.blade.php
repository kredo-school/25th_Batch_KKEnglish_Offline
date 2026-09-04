@php
    $user = auth()->user();

    $roleCode = $user?->role?->role_code;
@endphp


@php
    // Navbarの表示
    if ($roleCode == 'student') {

        $barClass = 'bg-info-subtle';
        $textClass = 'text-dark';
        $accountLabel = 'Student';
        $homeHref = route('student.dashboard');

    } elseif ($roleCode == 'teacher') {

        $barClass = 'bg-warning';
        $textClass = 'text-dark';
        $accountLabel = 'Teacher';
        $homeHref = route('teacher.dashboard');

    } elseif ($roleCode == 'admin') {

        $barClass = 'bg-dark';
        $textClass = 'text-white';
        $accountLabel = 'Admin';
        $homeHref = \Illuminate\Support\Facades\Route::has('admin.dashboard')
        ? route('admin.dashboard')
        : url('/admins/dashboard');

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

  <title>{{ config('app.name') }} | @yield('title')</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


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
        <div class="row g-0 align-items-stretch">

            {{-- Sidebar --}}
@auth
    <aside class="col-md-3 col-lg-2">

        @if ($roleCode == 'student')
            {{-- 生徒ページの予約ページでは専用のサイドバーを表示　route判定--}}
            {{-- @if (request()->routeIs('students.teacher-list*')) --}}
            @if (request()->routeIs('students.reservations.index'))
                {{-- 生徒の予約画面専用サイドバー --}}
                @include('students.components.sidebar')
            @else
                {{-- 生徒の通常サイドバー --}}
                @include('components.sidebars.student')
            @endif

        @elseif ($roleCode == 'teacher')
            @include('components.sidebars.teacher')

        @elseif ($roleCode == 'admin')
            @include('components.sidebars.admin')
        @endif

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
