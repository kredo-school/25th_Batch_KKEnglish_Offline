<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleExceptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'schedule_id' => [
                'required',
                'integer',
                'exists:teacher_schedules,schedule_id',
            ],
            'exception_type_id' => [
                'required',
                'integer',
                'exists:exception_types,exception_type_id',
            ],
            'start_at' => [
                'required',
                'date',
                'after:now',
            ],
            'end_at' => [
                'required',
                'date',
                'after:start_at',
            ],
            'reason' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'schedule_id.required' => '勤務シフトを選択してください。',
            'schedule_id.exists' => '選択した勤務シフトが存在しません。',
            'exception_type_id.required' => '休日理由を選択してください。',
            'exception_type_id.exists' => '選択した休日理由が存在しません。',
            'start_at.required' => '開始日時を入力してください。',
            'start_at.after' => '未来の日時を入力してください。',
            'end_at.required' => '終了日時を入力してください。',
            'end_at.after' => '終了日時は開始日時より後にしてください。',
            'reason.max' => '理由は1000文字以内で入力してください。',
        ];
    }
}
