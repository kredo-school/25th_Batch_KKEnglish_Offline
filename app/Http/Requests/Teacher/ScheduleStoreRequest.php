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
            'available_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
            'status' => ['nullable', 'in:available,booked,closed'],
        ];
    }

    public function messages(): array
    {
        return [
            'available_date.required' => '利用可能日は必須です。',
            'available_date.date' => '利用可能日は有効な日付である必要があります。',
            'available_date.after_or_equal' => '利用可能日は今日以降である必要があります。',
            'start_time.required' => '開始時刻は必須です。',
            'start_time.date_format' => '開始時刻は有効な時刻である必要があります。',
            'end_time.required' => '終了時刻は必須です。',
            'end_time.date_format' => '終了時刻は有効な時刻である必要があります。',
            'end_time.after' => '終了時刻は開始時刻より後である必要があります。',
            'status.in' => 'ステータスは利用可能、予約済み、または閉鎖のいずれかである必要があります。',
        ];
    }
}
