<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MaterialUpdateRequest extends MaterialStoreRequest
{
    // 同じルール・認可を使う
    /**
     * Determine if the user is authorized to make this request.
     */
    // authorize() メソッドは親クラスの MaterialStoreRequest のものを使用するため削除

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // MaterialStoreRequest のルールをそのまま使用する場合は空配列のままでよい
        ];
    }
}
