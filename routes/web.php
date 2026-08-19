<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::view('/frontend-test', 'teachers.dashboard');

Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/students/dashboard', function () {
        return view('students.dashboard');
    })->name('student.dashboard');
});
Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/teachers/dashboard', function () {
        return view('teachers.dashboard');
    })->name('teacher.dashboard');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admins/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

