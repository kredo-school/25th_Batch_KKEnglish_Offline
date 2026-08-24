<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ScheduleStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'teacher';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_at' => ['required', 'date', 'after_or_equal:now'],
            'end_at'   => ['required', 'date', 'after:start_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_at.required' => '開始日時は必須です。',
            'start_at.date' => '開始日時は有効な日付である必要があります。',
            'start_at.after_or_equal' => '開始日時は現在の日時以降である必要があります。',
            'end_at.required' => '終了日時は必須です。',
            'end_at.date' => '終了日時は有効な日付である必要があります。',
            'end_at.after' => '終了日時は開始日時より後である必要があります。',
        ];
    }
}
