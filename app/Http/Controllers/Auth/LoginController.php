<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    // ログイン後の処理
    protected function authenticated($request, $user)
    {
        if ($user->role->role_name === 'student') {
            return redirect()->route('student.dashboard');
        }

        if ($user->role->role_name === 'teacher') {
            return redirect()->route('teacher.dashboard');
        }

        if ($user->role->role_name === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect('/');
    }



}
