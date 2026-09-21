<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        $roleCode = $user->role->role_code;

        if ($roleCode === 'student') {
            return redirect()->route('students.dashboard');
        }

        if ($roleCode === 'teacher') {
            return redirect()->route('teacher.dashboard');
        }

        if ($roleCode === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect('/');
    }
}
