<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\MaterialController;


// Test route for frontend testing
Route::view('/frontend-test', 'teachers.profile')->name('teacher.profile');
Route::view('/materials-test', 'materials.index')->name('teaching.materials');

// Public routes
Auth::routes();
Route::get('/', function () {
    return view('welcome');
});
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Student Routes
Route::middleware(['auth', 'role:student'])->group(function () {
    // Student Dashboard Route
    Route::get('/students/dashboard', function () {
        return view('students.dashboard');
    })->name('student.dashboard');

    // Student Profile Routes
    Route::get('/students/profile', [StudentProfileController::class, 'show'])->name('student.profile');
    Route::get('/students/profile/edit', [StudentProfileController::class, 'edit'])->name('student.profile.edit');
    Route::patch('/students/profile', [StudentProfileController::class, 'update'])->name('student.profile.update');

});

// Teacher Routes
Route::middleware(['auth', 'role:teacher'])->group(function () {
    // Teacher Dashboard Route
    Route::get('/teachers/dashboard', function () {
        return view('teachers.dashboard');
    })->name('teacher.dashboard');

});


// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin Dashboard Route
    Route::get('/admins/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    });

// Material Routes
Route::get('/materials', [MaterialController::class, 'index'])->name('materials.index');
