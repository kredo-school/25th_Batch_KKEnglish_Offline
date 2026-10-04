<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Admin Profile
     */
    public function edit()
    {
        $user = auth()->user();

        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Profile基本情報を更新
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],
            'profile_image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048',
            ],
        ]);

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];

        /*
         * Profile Image
         */
        if ($request->hasFile('profile_image')) {

            // 古い画像がStorageに保存されている場合は削除
            if (
                $user->profile_image &&
                !str_starts_with($user->profile_image, 'http://') &&
                !str_starts_with($user->profile_image, 'https://')
            ) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // 新しい画像を保存
            $user->profile_image = $request
                ->file('profile_image')
                ->store('profile_images', 'public');
        }

        $user->save();

        return redirect()
            ->route('admin.profile.edit')
            ->with('success', 'Profile has been updated successfully.');
    }

    /**
     * Password変更
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => [
                'required',
                'current_password',
            ],

            'new_password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        $user = auth()->user();

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return redirect()
            ->route('admin.profile.edit')
            ->with('success', 'Password has been changed successfully.');
    }
}
