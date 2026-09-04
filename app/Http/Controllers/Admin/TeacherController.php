<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $q = Teacher::query()->with('user');

        if ($request->filled('keyword')) {
            $kw = (string) $request->string('keyword');
            $q->where(function ($x) use ($kw) {
                $x->where('specialty', 'like', "%{$kw}%")
                  ->orWhere('career', 'like', "%{$kw}%")
                  ->orWhereHas('user', function ($u) use ($kw) {
                      $u->where('email', 'like', "%{$kw}%")
                        ->orWhere('first_name', 'like', "%{$kw}%")
                        ->orWhere('last_name', 'like', "%{$kw}%");
                  });
            });
        }

        $teachers = $q->latest()->paginate(12)->withQueryString();
        return view('admin.teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'last_name'  => ['required','string','max:100'],
            'first_name' => ['required','string','max:100'],
            'email'      => ['required','email','max:255','unique:users,email'],
            'password'   => ['required','string','min:8'],
            'specialty'  => ['nullable','string','max:255'],
            'career'     => ['nullable','string','max:255'],
            'biography'  => ['nullable','string'],
            'graduation_school' => ['nullable', 'string', 'max:255'],
            'certification'     => ['nullable', 'string', 'max:255'],
            'about_me'          => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data) {
            $teacherRoleId = Role::query()
                ->where('role_code', 'teacher')
                ->value('id');

            if (!$teacherRoleId) {
                throw ValidationException::withMessages([
                    'role' => 'teacherロールが見つかりません。rolesテーブルを確認してください。'
                ]);
            }
            $user = User::create([
                'last_name'  => $data['last_name'],
                'first_name' => $data['first_name'],
                'email'      => $data['email'],
                'password'   => Hash::make($data['password']),
                'role_id'    => $teacherRoleId,
                'status'     => 'active',
            ]);

            // 2) teacherプロフィール作成
            Teacher::create([
                'user_id'          => (string) $user->id, // 現在schemaがstringのため
                'specialty'        => $data['specialty'] ?? null,
                'career'           => $data['career'] ?? null,
                'biography'        => $data['biography'] ?? null,
                'graduation_school'=> $data['graduation_school'] ?? null,
                'certification'    => $data['certification'] ?? null,
                'about_me'         => $data['about_me'] ?? null,
            ]);
        });

        return redirect()->route('admin.teachers.index')->with('success', '講師を登録しました。');
    }

    public function edit(Teacher $teacher): \Illuminate\Contracts\View\View
    {
        $teacher->load('user');
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(\Illuminate\Http\Request $request, Teacher $teacher): \Illuminate\Http\RedirectResponse
    {
        $teacher->load('user');

        $data = $request->validate([
            'last_name'         => ['required','string','max:100'],
            'first_name'        => ['required','string','max:100'],
            'email'             => ['required','email','max:255', \Illuminate\Validation\Rule::unique('users','email')->ignore($teacher->user->id)],
            'specialty'         => ['nullable','string','max:255'],
            'career'            => ['nullable','string','max:255'],
            'biography'         => ['nullable','string'],
            'graduation_school' => ['nullable','string','max:255'],
            'certification'     => ['nullable','string','max:255'],
            'about_me'          => ['nullable','string'],
            'status'            => ['required','in:active,inactive'],
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($teacher, $data) {
            $teacher->user->update([
                'last_name'  => $data['last_name'],
                'first_name' => $data['first_name'],
                'email'      => $data['email'],
                'status'     => $data['status'],
            ]);

            $teacher->update([
                'specialty'         => $data['specialty'] ?? null,
                'career'            => $data['career'] ?? null,
                'biography'         => $data['biography'] ?? null,
                'graduation_school' => $data['graduation_school'] ?? null,
                'certification'     => $data['certification'] ?? null,
                'about_me'          => $data['about_me'] ?? null,
            ]);
        });

        return redirect()->route('admin.teachers.index')->with('success', '講師情報を更新しました。');
    }

    public function destroy(Teacher $teacher): \Illuminate\Http\RedirectResponse
    {
        $teacher->load('user');

        // 物理削除ではなく停止
        $teacher->user->update(['status' => 'inactive']);

        return redirect()->route('admin.teachers.index')->with('success', '講師アカウントを停止しました。');
    }

    public function show(Teacher $teacher): \Illuminate\Contracts\View\View
    {
        $teacher->load('user', 'materials'); //materialsを読み込む

        if (!$teacher->user) {
            return redirect()->route('admin.teachers.index')
                ->with('error', '紐づくユーザーが見つかりません。');
        }

        return view('admin.teachers.show', compact('teacher'));
    }
}
