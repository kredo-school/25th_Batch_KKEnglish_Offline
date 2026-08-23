<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{

    public function show()
    {
        $user = auth()->user();
        return view('students.profile')->with('user', $user);
    }
    public function edit()
    {
        $user = auth()->user();
        return view('students.profile-edit')->with('user', $user);
    }

    public function update(Request $request)
    {
        // Validate the request data
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
            'phone_number' => 'nullable|string|max:20',
            'nationality' => 'nullable|string|max:100',
            'gender' => 'nullable|string|max:20',
            'birthday' => 'nullable|date',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1048',
            'password' => 'nullable|string|min:8',
            // Add other fields as necessary
        ]);

        // Update the authenticated user's profile
        $user = auth()->user();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->phone_number = $request->input('phone_number');
        $user->nationality = $request->input('nationality');
        $user->gender = $request->input('gender');


        if( $request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        if( $request->hasFile('profile_image')) {
                        // 古い画像を削除したい場合
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $path = $request->file('profile_image')->store(
                'profile_images',
                'public'
            );
            $user->profile_image = $path;
        }

         $user->save();


        $user->student()->updateOrCreate(
            [],
            ['birthday' => $request->input('birthday')]
        );

        return redirect()->route('student.profile')->with('success', 'Profile updated successfully.');
    }
}
