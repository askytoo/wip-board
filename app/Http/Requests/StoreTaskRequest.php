<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'max:1000',
            'deadline_date' => 'required|date|after_or_equal:today',
            'deadline_time' => 'required|date_format:H:i',
            'deadline' => 'required|after:now',
            'estimated_effort' => 'required|integer|between:0,255',
            'is_today_task' => 'boolean',
            'output' => 'required|string|max:255',
        ];
    }

    protected function prepareForValidation(): void
    {
        // 日時のデータを結合して、deadline としてバリデーションする
        $deadline = ($this->filled(['deadline_date', 'deadline_time'])) ? $this->deadline_date.' '.$this->deadline_time : '';
        $this->merge([
            'deadline' => $deadline,
        ]);
    }

    public function attributes(): array
    {
        return [
        ];
    }
}
