<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShiftPatternUpsertRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 必要ならGate/Policyに置き換え
        return auth()->check();
    }

    public function rules(): array
    {
        $patternId = $this->route('shift_pattern')?->id; // Route Model Binding想定

        return [
            'pattern_code' => [
                'required', 'string', 'max:50',
                Rule::unique('shift_patterns', 'pattern_code')->ignore($patternId),
            ],
            'pattern_name' => ['required', 'string', 'max:255'],

            // 親の既存カラム（互換維持）
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'end_day_offset' => ['required', 'integer', 'min:0', 'max:1'],
            'slot_minutes' => ['required', 'integer', Rule::in([30, 60])],
            'is_active' => ['required', 'boolean'],
            'display_order' => ['nullable', 'integer', 'min:0'],

            // 子: 週次ルール
            'rules' => ['required', 'array', 'min:1'],
            'rules.*.weekday' => ['required', 'integer', 'min:0', 'max:6'],
            'rules.*.start_time' => ['required', 'date_format:H:i'],
            'rules.*.end_time' => ['required', 'date_format:H:i'],
            'rules.*.lesson_type' => ['required', Rule::in(['online', 'in_person', 'both'])],

            // 子: 休憩
            'breaks' => ['nullable', 'array'],
            'breaks.*.weekday' => ['required', 'integer', 'min:0', 'max:6'],
            'breaks.*.start_time' => ['required', 'date_format:H:i'],
            'breaks.*.end_time' => ['required', 'date_format:H:i'],
            'breaks.*.reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $rules = $this->input('rules', []);
            foreach ($rules as $i => $row) {
                if (($row['start_time'] ?? null) >= ($row['end_time'] ?? null)) {
                    $validator->errors()->add("rules.$i.end_time", 'end_time は start_time より後である必要があります。');
                }
            }

            $breaks = $this->input('breaks', []);
            foreach ($breaks as $i => $row) {
                if (($row['start_time'] ?? null) >= ($row['end_time'] ?? null)) {
                    $validator->errors()->add("breaks.$i.end_time", 'end_time は start_time より後である必要があります。');
                }
            }
        });
    }
}
