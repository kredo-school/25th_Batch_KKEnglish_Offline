<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ScheduleUpdateRequest extends FormRequest
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
            'available_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time'     => ['required', 'date_format:H:i'],
            'end_time'       => ['required', 'date_format:H:i', 'after:start_time'],
            'status'         => ['nullable', 'in:available,booked,closed'],
        ];
    }
    public function messages(): array
    {
        return [
            'available_date.required' => '利用可能日は必須です。',
            'available_date.date' => '利用可能日は有効な日付である必要があります。',
            'available_date.after_or_equal' => '過去の日付は指定できません。',
            'end_time.after' => '終了時間は開始時間より後にしてください。',
            'status.in' => 'ステータスは利用可能、予約済み、または閉鎖のいずれかである必要があります。',
        ];
    }
}
