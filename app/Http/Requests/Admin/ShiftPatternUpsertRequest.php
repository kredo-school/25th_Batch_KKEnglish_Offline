<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ShiftPatternUpsertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // protected function prepareForValidation(): void
    // {
    //     $weekdays = collect($this->input('weekdays', []))
    //         ->map(fn ($v) => (int)$v)
    //         ->filter(fn ($v) => $v >= 0 && $v <= 6)
    //         ->unique()
    //         ->values();

    //     // 既存の rules 入力が無い（=新UI）時だけ自動生成
    //     if (!$this->has('rules') && $weekdays->isNotEmpty()) {
    //         $rules = $weekdays->map(fn ($w) => [
    //             'weekday'    => $w,
    //             'start_time' => $this->input('common_rule_start_time'),
    //             'end_time'   => $this->input('common_rule_end_time'),
    //             'lesson_type'=> $this->input('common_rule_lesson_type', 'both'),
    //         ])->all();

    //         $breaks = [];
    //         if ($this->filled('common_break_start_time') && $this->filled('common_break_end_time')) {
    //             $breaks = $weekdays->map(fn ($w) => [
    //                 'weekday'    => $w,
    //                 'start_time' => $this->input('common_break_start_time'),
    //                 'end_time'   => $this->input('common_break_end_time'),
    //                 'reason'     => $this->input('common_break_reason'),
    //             ])->all();
    //         }

    //         $this->merge([
    //             'rules' => $rules,
    //             'breaks' => $breaks,
    //         ]);
    //     }
    // }

    public function rules(): array
    {
        return [
            'pattern_code' => ['required','string','max:50'],
            'pattern_name' => ['required','string','max:255'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'end_day_offset' => ['required','integer','min:0','max:1'],
            'slot_minutes' => ['required','integer','in:30,60'],
            'display_order' => ['nullable','integer','min:0'],
            'is_active' => ['nullable','boolean'],

            // 新UI入力
            // 'weekdays' => ['nullable','array'],
            // 'weekdays.*' => ['integer','min:0','max:6'],
            // 'common_rule_start_time' => ['nullable'],
            // 'common_rule_end_time' => ['nullable'],
            // 'common_rule_lesson_type' => ['nullable','in:online,in_person,both'],
            // 'common_break_start_time' => ['nullable'],
            // 'common_break_end_time' => ['nullable'],
            // 'common_break_reason' => ['nullable','string','max:255'],

            // サービスが使う最終形
            // 'rules' => ['required','array','min:1'],
            // 'rules.*.weekday' => ['required','integer','min:0','max:6'],
            // 'rules.*.start_time' => ['required'],
            // 'rules.*.end_time' => ['required'],
            // 'rules.*.lesson_type' => ['required','in:online,in_person,both'],

            'breaks' => ['nullable','array'],
            // 'breaks.*.weekday' => ['required','integer','min:0','max:6'],
            'breaks.*.start_time' => ['nullable'],
            'breaks.*.end_time' => ['nullable'],
            'breaks.*.reason' => ['nullable','string','max:255'],
        ];
    }
}
