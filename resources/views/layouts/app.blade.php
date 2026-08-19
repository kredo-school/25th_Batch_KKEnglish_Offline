@php
    $user = auth()->user();
    $roleId = (int) ($user->role_id ?? 0);

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
<div id="app">
    @include('components.navbar', ['viewMode' => $viewMode, 'roleId' => $roleId])

    <div class="container-fluid">
        <div class="row">
            @auth
                <aside class="col-md-3 col-lg-2 px-0">
                    @include('components.sidebar', ['viewMode' => $viewMode, 'roleId' => $roleId])
                </aside>
            @endauth

            <main class="col py-4">
                @yield('content')
            </main>
        </div>
    </div>
</div>
</body>
</html>
