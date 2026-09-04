<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;

class ReservationController extends Controller
{
    public function index(): View
    {
        $teachers = Teacher::query()
            ->with('user')
            ->get();

        return view(
            'students.reservations.index',
            compact('teachers')
        );
    }
}
