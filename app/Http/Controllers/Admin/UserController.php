<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $q = User::query()->with('role');

        if ($request->filled('keyword')) {
            $kw = $request->string('keyword');
            $q->where(function ($x) use ($kw) {
                $x->where('email', 'like', "%{$kw}%")
                  ->orWhere('first_name', 'like', "%{$kw}%")
                  ->orWhere('last_name', 'like', "%{$kw}%")
                  ->orWhereRaw("CONCAT(last_name, first_name) LIKE ?", ["%{$kw}%"])
                  ->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", ["%{$kw}%"]);
            });
        }

        $users = $q->orderByDesc('id')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user): View
    {
        $roles = Role::orderBy('id')->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required','string','max:255'],
            'last_name' => ['required','string','max:255'],
            'email' => ['required','email','max:255'],
            'role_id' => ['required','exists:roles,id'],
            'status' => ['required','in:active,inactive'],
        ]);

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'ユーザーを更新しました。');
    }

    public function show(User $user): View
    {
        $user->load('role');
        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user): RedirectResponse
    {
        // 自分自身を削除不可（任意）
        if (auth()->id() === $user->id) {
            return back()->with('error', '自分自身は削除できません。');
        }

        // 物理削除ではなく停止に寄せる方が安全
        $user->update(['status' => 'inactive']);

        return redirect()->route('admin.users.index')
            ->with('success', 'ユーザーを停止しました。');
    }
}
