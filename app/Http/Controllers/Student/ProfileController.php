<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        return view('students.profile');
    }
    public function edit()
    {
        return view('students.profile-edit');
    }

    public function update(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1048',
            // Add other fields as necessary
        ]);

        // Update the authenticated user's profile
        $user = auth()->user();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        // Update other fields as necessary
        $user->save();

        return redirect()->route('student.profile')->with('success', 'Profile updated successfully.');
    }
}
