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
        $homeHref = route('students.dashboard');

    } elseif ($roleCode == 'teacher') {

        $barClass = 'teacher-navbar';
        $textClass = 'text-dark';
        $accountLabel = 'Teacher';
        $homeHref = route('teachers.dashboard');

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
        $homeHref = url('/');
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@7.3.2/css/flag-icons.min.css">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        .teacher-navbar {
            background-color: #FFD166 !important;
        }

        /* 画面全体のスクロールを止め、Flexboxで高さを100%に固定 */
        body {
            height: 100vh;
            overflow: hidden;
        }
        #app {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        #app > .container-fluid {
            flex-grow: 1;
            overflow: hidden;
        }
        /* html側の min-vh-100 を強制リセットして高さを親に合わせる */
        .row.align-items-stretch {
            height: 100%;
            min-height: 0 !important;
        }
        /* サイドバーを個別スクロール可能に（下部に余白を追加） */
        aside {
            height: 100%;
            overflow-y: auto;
            padding-bottom: 3rem; /* 下が見やすくなるように余白を追加 */
        }
        /* メインコンテンツを個別スクロール可能に（下部に余白を追加） */
        main {
            height: 100%;
            overflow-y: auto;
            padding-bottom: 3rem; /* 下が見やすくなるように余白を追加 */
        }
    </style>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/nouislider@15.8.1/dist/nouislider.min.css">

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
        <div class="row g-0 align-items-stretch min-vh-100">

            {{-- Sidebar --}}
@auth
    <aside class="col-md-3 col-lg-2 bg-light border-end">

        @if ($roleCode == 'student')
            {{-- 生徒ページの予約ページでは専用のサイドバーを表示　route判定--}}
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
            <main class="col py-4 px-4" style="min-width: 0;">
                @yield('content')
            </main>

        </div>
    </div>

</div>

</body>
</html>
