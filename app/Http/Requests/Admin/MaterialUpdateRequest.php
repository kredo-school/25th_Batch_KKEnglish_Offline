<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MaterialUpdateRequest extends MaterialStoreRequest
{
    public function authorize(): bool
    {
        // まず切り分け優先なら true
        // 本番は admin 判定に置き換え推奨
        return auth()->check()
        && auth()->user()->role
        && auth()->user()->role->role_code === 'admin';
    }

    public function rules(): array
    {
        // 親のバリデーションを継承
        return parent::rules();
    }
}
