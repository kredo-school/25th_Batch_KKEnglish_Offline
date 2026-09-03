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
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ShiftPatternController;
use App\Http\Controllers\Admin\ShiftPatternAssignmentController;
use App\Http\Controllers\Teacher\ScheduleExceptionController;
use App\Http\Controllers\Student\AvailabilityController;

// Test route for frontend testing
Route::view('/reservation-test', 'students.reservations.index')
->name('reservation.test');
Route::view('/reservation-confirm-test','students.reservations.confirm')
->name('reservation.confirm.test');
Route::view('/reservation-teacher-detail-test','students.reservations.teacher-detail')
->name('reservations.teacher-detail.test');
Route::view('/student-history-test','students.history.index')
->name('student.history.test');


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

    Route::get('/teachers/{id}', [TeacherController::class, 'show'])->whereNumber('id')->name('teachers.show');
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
    Route::post('/student/lessons/{reservation}/cancel', [LessonController::class, 'cancel'])
        ->name('student.lessons.cancel');
// Teacher reservations/
    Route::get('/students/availability', [AvailabilityController::class, 'index'])->name('students.availability.index');
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

     // My Schedule 画面
    Route::view('/teachers/schedule', 'teachers.schedule')->name('teachers.schedule');

    Route::get('/teachers/schedule-exceptions', [ScheduleExceptionController::class, 'index'])->name('teachers.schedule-exceptions.index');
    Route::post('/teachers/schedule-exceptions', [ScheduleExceptionController::class, 'store'])->name('teacher.schedule-exceptions.store');
    Route::delete('/teachers/schedule-exceptions/{scheduleException}', [ScheduleExceptionController::class, 'destroy'])->name('teacher.schedule-exceptions.destroy');

Route::post('/teachers/schedules/grid', [ScheduleController::class, 'storeGrid'])
    ->name('teacher.schedules.storeGrid');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admins')
    ->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('admin.dashboard');

    // Material 編集
    Route::get('/materials', [AdminMaterialController::class, 'index'])->name('materials.index');
    Route::get('/materials/create', [AdminMaterialController::class, 'create'])->name('materials.create');
    Route::post('/materials', [AdminMaterialController::class, 'store'])->name('materials.store');
    Route::get('/materials/{material}', [AdminMaterialController::class, 'show'])->name('materials.show');
    Route::get('/materials/{material}/edit', [AdminMaterialController::class, 'edit'])->name('materials.edit');
    Route::put('/materials/{material}', [AdminMaterialController::class, 'update'])->name('materials.update');
    Route::delete('/materials/{material}', [AdminMaterialController::class, 'destroy'])->name('materials.destroy');
    Route::patch('/materials/{material}/suspend',[AdminMaterialController::class, 'suspend'])->name('materials.suspend');

    // User 編集
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Teacher Management
    Route::get('/teachers', [AdminTeacherController::class, 'index'])->name('teachers.index');
    Route::get('/teachers/create', [AdminTeacherController::class, 'create'])->name('teachers.create');
    Route::post('/teachers', [AdminTeacherController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/{teacher}', [AdminTeacherController::class, 'show'])->name('teachers.show');
    Route::get('/teachers/{teacher}/edit', [AdminTeacherController::class, 'edit'])->name('teachers.edit');
    Route::put('/teachers/{teacher}', [AdminTeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{teacher}', [AdminTeacherController::class, 'destroy'])->name('teachers.destroy');

    // Shift Pattern Management
    Route::resource('shift-patterns', ShiftPatternController::class)
        ->only(['index', 'create', 'store', 'edit', 'update']);

    // Shift Pattern Assignment
    Route::get('/shift-pattern-assignments/create', [ShiftPatternAssignmentController::class, 'create'])
        ->name('shift-pattern-assignments.create');
    Route::post('/shift-pattern-assignments', [ShiftPatternAssignmentController::class, 'store'])
        ->name('shift-pattern-assignments.store');
});
