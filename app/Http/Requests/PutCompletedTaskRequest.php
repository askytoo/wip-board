<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class PutCompletedTaskRequest extends FormRequest
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
        // 完了のラベルを取得
        $completedLabel = Task::STATUS[3]['label'];

        return [
            'status' => 'required|in:' . $completedLabel,
        ];
    }

    public function attributes(): array
    {
        return [
        ];
    }
}
