<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignShiftPatternRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'teacher_ids' => ['required', 'array', 'min:1'],
            'teacher_ids.*' => ['required', 'integer', 'exists:teachers,id'],

            'shift_pattern_id' => ['required', 'integer', 'exists:shift_patterns,id'],

            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],

            'priority' => ['nullable', 'integer', 'min:0'],
            'replace_overlapping' => ['nullable', 'boolean'], // 重複期間を置換するか
        ];
    }
}