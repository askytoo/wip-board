<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class PutInProgressTaskRequest extends FormRequest
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
        // 進行中のラベルを取得
        $inProgressLabel = Task::STATUS[1]['label'];

        return [
            'status' => 'required|in:' . $inProgressLabel,
        ];
    }

    public function attributes(): array
    {
        return [
        ];
    }
}
