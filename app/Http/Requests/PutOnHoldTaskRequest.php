<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class PutOnHoldTaskRequest extends FormRequest
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
        // 保留中のラベルを取得
        $onHoldLabel = Task::STATUS[2]['label'];

        return [
            'status' => 'required|in:' . $onHoldLabel,
        ];
    }

    public function attributes(): array
    {
        return [
        ];
    }
}
