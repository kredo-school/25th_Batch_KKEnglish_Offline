<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Teacher\ScheduleController;


// Test route for frontend testing
Route::view('/frontend-test', 'teachers.profile')->name('teacher.profile');
Route::view('/teachers-test', 'teachers.index')->name('teachers.test');




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

    Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
    Route::get('/teachers/{id}', [TeacherController::class, 'show'])->name('teachers.show');

    // Teacher Schedule Routes
    Route::get('/teachers/schedules/create', [ScheduleController::class, 'create'])->name('teacher.schedules.create');
    Route::post('/teachers/schedules', [ScheduleController::class, 'store'])->name('teacher.schedules.store');
    Route::get('/teachers/{id}', [TeacherController::class, 'show'])->name('teacher.profile');
});


// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin Dashboard Route
    Route::get('/admins/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    });

// Material Routes
// 閲覧（ログインユーザー全員）
Route::middleware(['auth'])->group(function () {
    Route::get('/materials', [MaterialController::class, 'index'])->name('materials.index');
    Route::get('/materials/{material}', [MaterialController::class, 'show'])->name('materials.show');
});

// 編集（adminのみ）
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/materials/create', [MaterialController::class, 'create'])->name('materials.create');
    Route::post('/materials', [MaterialController::class, 'store'])->name('materials.store');
    Route::get('/materials/{material}/edit', [MaterialController::class, 'edit'])->name('materials.edit');
    Route::put('/materials/{material}', [MaterialController::class, 'update'])->name('materials.update');
    Route::delete('/materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');
});
