<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\LessonController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Teacher\ScheduleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MaterialController as AdminMaterialController;
use App\Http\Controllers\Admin\TeacherController as AdminTeacherController;

// Test route for frontend testing
Route::view('/frontend-test', 'teachers.profile')->name('teacher.profile');
Route::view('/teachers-test', 'teachers.schedule')->name('teachers.schedule');
Route::view('/reservation-test', 'students.reservations.index')
    ->name('reservation.test');

// Public routes
Auth::routes();

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');


// 認証ユーザー共通（閲覧系）
Route::middleware(['auth'])->group(function () {

    // Material 閲覧
    Route::get('/materials', [MaterialController::class, 'index'])->name('materials.index');
    Route::get('/materials/{material}', [MaterialController::class, 'show'])->name('materials.show');
});

// Student Routes
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/students/dashboard', function () {
        return view('students.dashboard');
    })->name('student.dashboard');

    Route::get('/students/profile', [StudentProfileController::class, 'show'])->name('student.profile');
    Route::get('/students/profile/edit', [StudentProfileController::class, 'edit'])->name('student.profile.edit');
    Route::patch('/students/profile', [StudentProfileController::class, 'update'])->name('student.profile.update');
// Teacher list/profile（studentも閲覧可）
    Route::get('/teachers', [TeacherController::class, 'index'])->name('students.teacher-list');
    Route::get('/teachers/{id}', [TeacherController::class, 'show'])->whereNumber('id')->name('teachers.show');
    Route::post('/student/lessons/{reservation}/cancel', [LessonController::class, 'cancel'])
        ->name('student.lessons.cancel');
});

// Teacher/Admin 共通（Schedule編集）
Route::middleware(['auth', 'role:teacher,admin'])->group(function () {
    Route::get('/teachers/schedules', [ScheduleController::class, 'index'])->name('teacher.schedules.index');
    Route::get('/teachers/schedules/create', [ScheduleController::class, 'create'])->name('teacher.schedules.create');
        // グリッド選択保存（create画面から送信）
    Route::post('/teachers/schedules/grid', [ScheduleController::class, 'storeGrid'])->name('teacher.schedules.storeGrid');

    Route::post('/teachers/schedules', [ScheduleController::class, 'store'])->name('teacher.schedules.store');

    Route::get('/teachers/schedules/{schedule}/edit', [ScheduleController::class, 'edit'])->name('teacher.schedules.edit');
    Route::put('/teachers/schedules/{schedule}', [ScheduleController::class, 'update'])->name('teacher.schedules.update');
    Route::delete('/teachers/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('teacher.schedules.destroy');
});

// Teacher Dashboard
Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/teachers/dashboard', function () {
        return view('teachers.dashboard');
    })->name('teacher.dashboard');

Route::post('/teachers/schedules/grid', [ScheduleController::class, 'storeGrid'])
    ->name('teacher.schedules.storeGrid');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admins')
    ->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Material 編集
    Route::get('/materials', [AdminMaterialController::class, 'index'])->name('materials.index');
    Route::get('/materials/create', [AdminMaterialController::class, 'create'])->name('materials.create');
    Route::post('/materials', [AdminMaterialController::class, 'store'])->name('materials.store');
    Route::get('/materials/{material}', [AdminMaterialController::class, 'show'])->name('materials.show');
    Route::get('/materials/{material}/edit', [AdminMaterialController::class, 'edit'])->name('materials.edit');
    Route::put('/materials/{material}', [AdminMaterialController::class, 'update'])->name('materials.update');
    Route::delete('/materials/{material}', [AdminMaterialController::class, 'destroy'])->name('materials.destroy');
    Route::patch('/materials/{material}/suspend',[AdminMaterialController::class, 'suspend'])->name('materials.suspend');
});
