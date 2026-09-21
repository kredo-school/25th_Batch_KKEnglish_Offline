<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\PointTransaction;
use App\Models\TransactionType;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Student Management
     *
     * student Roleの人だけを一覧表示する
     */
    public function index(Request $request): View
    {
        /*
        |--------------------------------------------------------------------------
        | ① studentだけを取得
        |--------------------------------------------------------------------------
        |
        | Student
        |   ↓
        | User
        |   ↓
        | Role
        |   ↓
        | role_code = student
        |
        */

        $studentRoleId = Role::query()
            ->where('role_code', 'student')
            ->value('id');

        $query = Student::query()
            ->with('user')
            // ->withSum(
            //     'pointTransactions as calculated_point_balance',
            //     'point'
            // )
            ->where('students.user_id', '!=', null)
            ->whereHas('user', function ($userQuery) use ($studentRoleId) {
                $userQuery->where('role_id', $studentRoleId);
            })
            ->orderByDesc('students.id');

        /*
        |--------------------------------------------------------------------------
        | ② ID・名前検索
        |--------------------------------------------------------------------------
        */

        if ($request->filled('keyword')) {
            $keyword = trim(
                $request->string('keyword')->toString()
            );

            $query->where(function ($q) use ($keyword) {

                // Student ID
                if (is_numeric($keyword)) {
                    $q->orWhere(
                        'students.id',
                        (int) $keyword
                    );
                }

                // User ID
                if (is_numeric($keyword)) {
                    $q->orWhere(
                        'students.user_id',
                        (int) $keyword
                    );
                }

                // 名前
                $q->orWhereHas('user', function ($userQuery) use ($keyword) {
                    $userQuery
                        ->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%")
                        ->orWhere(
                            DB::raw(
                                "CONCAT(first_name, ' ', last_name)"
                            ),
                            'like',
                            "%{$keyword}%"
                        )
                        ->orWhere(
                            DB::raw(
                                "CONCAT(last_name, ' ', first_name)"
                            ),
                            'like',
                            "%{$keyword}%"
                        );
                });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | ③ Active / Inactive
        |--------------------------------------------------------------------------
        |
        | statusはusersテーブルで管理
        |
        */

        if ($request->filled('active')) {
            $status = $request->boolean('active')
                ? 'active'
                : 'inactive';

            $query->whereHas('user', function ($userQuery) use ($status) {
                $userQuery->where('status', $status);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | ④ ページネーション
        |--------------------------------------------------------------------------
        */

        $students = $query
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.students.index',
            compact('students')
        );
    }

    /**
     * Student詳細
     */
    public function show(Student $student): View
    {
        $student->load([
            'user',
            'pointTransactions' => function ($query) {
                $query
                    ->with([
                        'transactionType',
                        'creator',
                    ])
                    ->latest('created_at')
                    ->latest('transaction_id');
            },
        ]);

        /*
        |--------------------------------------------------------------------------
        | 現在のポイント
        |--------------------------------------------------------------------------
        |
        | point_transactionsの合計から計算
        |
        */

        $pointBalance = (int) $student->point_balance;

        return view(
            'admin.students.show',
            compact(
                'student',
                'pointBalance'
            )
        );
    }

        /**
     * Student プロフィール画面 (新規追加)
     */
    public function profile(Student $student): View
    {
        $student->load([
            'user',
            'pointTransactions',
        ]);

        $pointBalance = (int) $student->point_balance;

        return view(
            'admin.students.profile',
            compact('student', 'pointBalance')
        );
    }
    /**
     * ポイント付与画面
     */
    public function pointCreate(Student $student): View
    {
        $student->load('user');

        $types = TransactionType::query()
            ->whereIn('type_code', [
                'grant',
                'refund',
                'adjustment',
            ])
            ->orderBy('type_id')
            ->get();

        $pointBalance = (int) $student
            ->point_balance;

        return view(
            'admin.students.point-create',
            compact(
                'student',
                'types',
                'pointBalance'
            )
        );
    }

    /**
     * ポイント付与
     */
    public function pointStore(
        Request $request,
        Student $student
    ): RedirectResponse {
        $data = $request->validate([
            'transaction_type' => [
                'required',
                'integer',
                'exists:transaction_types,type_id',
            ],

            'point' => [
                'required',
                'integer',
                'min:1',
            ],

            'note' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | ① 取引種類を確認
        |--------------------------------------------------------------------------
        */

        $type = TransactionType::findOrFail(
            $data['transaction_type']
        );

        /*
        |--------------------------------------------------------------------------
        | ② ポイント付与用の種類だけ許可
        |--------------------------------------------------------------------------
        */

        if (!in_array(
            $type->type_code,
            [
                'grant',
                'refund',
                'adjustment',
            ],
            true
        )) {
            return back()
                ->withInput()
                ->withErrors([
                    'transaction_type' =>
                        'Choose a transaction type for granting points.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | ③ ポイント取引を登録
        |--------------------------------------------------------------------------
        |
        | students.point_balanceを直接変更しません。
        |
        | point_transactionsに
        |
        | +300
        |
        | のような記録を追加します。
        |
        */

        DB::transaction(function () use (
            $data,
            $student
        ) {

        /*
        |--------------------------------------------------------------------------
        | point_transactionsに履歴を登録
        |--------------------------------------------------------------------------
        */
            PointTransaction::create([
                'student_id' => $student->id,

                'transaction_type' =>
                    $data['transaction_type'],

                'point' =>
                    (int) $data['point'],

                'related_reservation_id' => null,

                'note' =>
                    $data['note'] ?? null,

                'created_by' =>
                    auth()->id(),

                'created_at' =>
                    now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | students.point_balanceを更新
            |--------------------------------------------------------------------------
            */

            $student->increment(
                'point_balance',
                (int) $data['point']
            );
        });

    /*
    |--------------------------------------------------------------------------
    | ④ Student詳細へ戻る
    |--------------------------------------------------------------------------
    */
        return redirect()
            ->route(
                'admin.students.show',
                $student
            )
            ->with(
                'status',
                $data['point'] . 'Points added.'
            );
    }
}
