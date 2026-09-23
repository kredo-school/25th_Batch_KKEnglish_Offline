<?php

/**
 * ================================================================
 * 【このファイルの役割】
 * TeacherController.php
 * ================================================================
 *
 * 管理者の「Teacher List」を表示・登録・編集するControllerです。
 *
 * index() の勤務関連表示は、shift_patterns や assignment を直接計算するのではなく、
 * 最終データである teacher_schedules を基準にしています。
 *
 * Teacher List の各項目
 * - Booked          ：今日の予約件数
 * - Number of Slots ：今日の teacher_schedules から勤務可能時間を計算
 * - Shift Pattern   ：今日の teacher_schedules に紐づく pattern_name
 * - Today           ：今日の勤務時間
 *
 * 【重複時間】
 * teacher_schedules に古い重複データが残っていても、index() 側で時間をmergeしてから
 * Number of Slots を計算するため、画面上では重複分を二重カウントしない設計です。
 * ================================================================
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Role;
use App\Models\TeacherShiftPatternAssignment;
use App\Models\ShiftPattern;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Services\TeacherScheduleStatusService;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        // Teacher List では「今日」の勤務状況を表示します。
        $today = now()->toDateString();

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

        // ここで先に return してしまうと、下の勤務時間計算が実行されません。
        // 必ず最後の return view() まで処理を進めます。
        $teachers = $q->orderBy('id', 'desc')->paginate(12)->withQueryString();

        // 現在のページに表示されている先生だけを対象にします。
        $teacherIds = $teachers->getCollection()->pluck('id')->values();
        $assignments = TeacherShiftPatternAssignment::query()
            ->with('shiftPattern')
            ->whereIn('teacher_id', $teacherIds)
            ->where(function ($query) use ($today) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', $today);
            })
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $today);
            })
            ->orderByDesc('priority')
            ->get()
            ->groupBy('teacher_id');

        // ★重要：勤務情報の元データは teacher_schedules です。
        // Seeder の値を直接表示しているわけではありません。
        // teacher_schedules が存在しない先生は勤務時間・スロット数が0になります。
        // Teacher Managementの勤務情報は、生成済みteacher_schedulesを基準にする。
        $todaySchedules = DB::table('teacher_schedules as ts')
            ->leftJoin('shift_patterns as sp', 'sp.id', '=', 'ts.shift_pattern_id')
            ->whereIn('ts.teacher_id', $teacherIds)
            ->where('ts.available_date', $today)
            ->where('ts.status', 'confirmed')
            ->select(
                'ts.teacher_id',
                'ts.start_time',
                'ts.end_time',
                'ts.shift_pattern_id',
                'sp.pattern_name',
                'sp.slot_minutes'
            )
            ->orderBy('ts.teacher_id')
            ->orderBy('ts.start_time')
            ->get();

        // Booked は teacher_schedules ではなく reservations から取得します。
        // 「今日、その先生に何件予約が入っているか」を表示するためです。
        $bookedByTeacher = DB::table('reservations')
            ->whereIn('teacher_id', $teacherIds)
            ->whereDate('start_at', $today)
            ->whereNull('cancelled_at')
            ->select('teacher_id', DB::raw('COUNT(*) as booked_count'))
            ->groupBy('teacher_id')
            ->pluck('booked_count', 'teacher_id');

        $scheduleByTeacher = $todaySchedules->groupBy('teacher_id');

        $teachers->getCollection()->transform(function ($teacher) use ($scheduleByTeacher, $bookedByTeacher, $assignments) {
            $schedules = $scheduleByTeacher->get($teacher->id, collect());

            $teacher->booked = (int) $bookedByTeacher->get($teacher->id, 0);

            // inactive の先生は list 上ではオフ扱いにする
            $isInactive = $teacher->user?->status === 'inactive';

            if ($isInactive) {
                $teacher->slots_number = 0;
            } else {
                // Number of Slots の計算
                // teacher_schedules は「勤務時間帯」を持っています。
                // 例：09:00～13:00 → 4時間 → 30分単位なら8スロット。
                // ただし重複行があると二重計算になるため、先にmergeします。
                // 重複時間を除外してからSlot数を計算する。
                $periods = $schedules->map(function ($schedule) {
                    return [
                        'start' => Carbon::parse($schedule->start_time),
                        'end' => Carbon::parse($schedule->end_time),
                        'slot_minutes' => max((int) ($schedule->slot_minutes ?? 30), 1),
                    ];
                })->sortBy('start')->values()->all();

                $merged = [];
                foreach ($periods as $period) {
                    if (empty($merged)) {
                        $merged[] = $period;
                        continue;
                    }

                    $i = count($merged) - 1;
                    if ($period['start']->lte($merged[$i]['end'])) {
                        if ($period['end']->gt($merged[$i]['end'])) {
                            $merged[$i]['end'] = $period['end']->copy();
                        }
                    } else {
                        $merged[] = $period;
                    }
                }

                $teacher->slots_number = collect($merged)->sum(function ($period) {
                    return intdiv(
                        $period['start']->diffInMinutes($period['end']),
                        $period['slot_minutes']
                    );
                });
            }

            // Shift Pattern は assignment → shift_patterns から取得します。
            // 今日の teacher_schedules がなくても、Teacherに設定されている
            // Shift Patternを表示します。
            $teacherAssignments = $assignments->get($teacher->id, collect());
            if ($teacherAssignments->isNotEmpty()) {
                $patterns = $teacherAssignments
                    ->map(function ($assignment) {
                        return $assignment->shiftPattern?->pattern_name;
                    })
                    ->filter()
                    ->unique()
                    ->values();

                $teacher->shift_pattern_name = $patterns->isNotEmpty()
                    ? $patterns->implode(', ')
                    : null;
            } else {
                $patterns = $schedules->pluck('pattern_name')->filter()->unique()->values();
                $teacher->shift_pattern_name = $patterns->isNotEmpty()
                    ? $patterns->implode(', ')
                    : null;
            }

            // inactive または 今日の勤務予定がなければ Off と表示します。
            if ($isInactive || $schedules->isEmpty()) {
                $teacher->today = 'Off';
            } else {
                $ranges = $schedules->map(function ($schedule) {
                    return substr($schedule->start_time, 0, 5) . '–' . substr($schedule->end_time, 0, 5);
                })->unique()->values();

                $teacher->today = 'Working ' . $ranges->implode(', ');
            }

            return $teacher;
        });

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create(): View { return view('admin.teachers.create'); }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'last_name' => ['required','string','max:100'],
            'first_name' => ['required','string','max:100'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8'],
            'nationality' => ['nullable','string','max:255'],
            'specialty' => ['nullable','string','max:255'],
            'career' => ['nullable','string','max:255'],
            'biography' => ['nullable','string'],
            'graduation_school' => ['nullable','string','max:255'],
            'certification' => ['nullable','string','max:255'],
            'about_me' => ['nullable','string'],
        ]);
$previousStatus = (string) $teacher->user->status;
        DB::transaction(function () use (
    $teacher,
    $data,
    $previousStatus,
    $teacherScheduleStatusService
) {
            $teacherRoleId = Role::query()->where('role_code', 'teacher')->value('id');
            if (!$teacherRoleId) {
                throw ValidationException::withMessages(['role' => 'teacherロールが見つかりません。rolesテーブルを確認してください。']);
            }

            $user = User::create([
                'last_name' => $data['last_name'],
                'first_name' => $data['first_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => $teacherRoleId,
                'nationality' => $data['nationality'] ?? null,
                'status' => 'active',
            ]);

            Teacher::create([
                'user_id' => (string) $user->id,
                'specialty' => $data['specialty'] ?? null,
                'career' => $data['career'] ?? null,
                'biography' => $data['biography'] ?? null,
                'graduation_school' => $data['graduation_school'] ?? null,
                'certification' => $data['certification'] ?? null,
                'about_me' => $data['about_me'] ?? null,
            ]);
        });

        return redirect()->route('admin.teachers.index')->with('success', '講師を登録しました。');
    }

    public function show(Teacher $teacher): View
    {
        $teacher->load('user');

        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher): View
    {
        $teacher->load('user');

        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher, TeacherScheduleStatusService $teacherScheduleStatusService): RedirectResponse
    {
        $teacher->load('user');

        $data = $request->validate([
            'last_name' => ['required', 'string', 'max:100'],
            'first_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:255',
                \Illuminate\Validation\Rule::unique('users', 'email')
                    ->ignore($teacher->user->id),
            ],
            'nationality' => ['nullable', 'string', 'max:255'],
            'specialty' => ['nullable', 'string', 'max:255'],
            'career' => ['nullable', 'string', 'max:255'],
            'biography' => ['nullable', 'string'],
            'graduation_school' => ['nullable', 'string', 'max:255'],
            'certification' => ['nullable', 'string', 'max:255'],
            'about_me' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            'profile_image' => 'nullable|image|mimes:jpeg,png,gif|max:2048',
        ]);

        // ファイルアップロード処理
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $path = $file->store('profile-images', 'public');
            $data['profile_image'] = asset('storage/' . $path);
        }
$previousStatus = (string) $teacher->user->status;
        DB::transaction(function () use ($teacher, $data, $previousStatus, $teacherScheduleStatusService) {
            // User側の情報
            $teacher->user->update([
                'last_name' => $data['last_name'],
                'first_name' => $data['first_name'],
                'email' => $data['email'],
                'nationality' => $data['nationality'] ?? null,
                'status' => $data['status'],
                'profile_image' => $data['profile_image'] ?? $teacher->user->profile_image,
            ]);
            $teacherScheduleStatusService->syncForUserStatus(
    $teacher->user,
    $previousStatus,
    $data['status']
);

            // Teacher の有効・無効に合わせて勤務予定の状態を更新する
            // if ($data['status'] === 'inactive') {
            //     DB::table('teacher_schedules')
            //         ->where('teacher_id', $teacher->getKey())
            //         ->where('status', 'confirmed')
            //         ->update([
            //             'status' => 'draft',
            //             'updated_at' => now(),
            //         ]);
            // } else {
            //     DB::table('teacher_schedules')
            //         ->where('teacher_id', $teacher->getKey())
            //         ->where('status', 'draft')
            //         ->update([
            //             'status' => 'confirmed',
            //             'updated_at' => now(),
            //         ]);
            // }

            // Teacher側の情報
            $teacher->update([
                'specialty' => $data['specialty'] ?? null,
                'career' => $data['career'] ?? null,
                'biography' => $data['biography'] ?? null,
                'graduation_school' => $data['graduation_school'] ?? null,
                'certification' => $data['certification'] ?? null,
                'about_me' => $data['about_me'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.teachers.show', $teacher)
            ->with('success', 'Teacher updated successfully!');
    }
}
