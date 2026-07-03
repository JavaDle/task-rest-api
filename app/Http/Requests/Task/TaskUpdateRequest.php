<?php

namespace App\Http\Requests\Task;

use App\Enums\Task\TaskPriority;
use App\Enums\Task\TaskStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskUpdateRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],

            'category' => ['sometimes', 'nullable', 'string', 'max:255'],

            'status' => ['sometimes', 'nullable', Rule::enum(TaskStatus::class)],
            'priority' => ['sometimes', 'nullable', Rule::enum(TaskPriority::class)],

            'create_date' => ['sometimes', 'required', 'date'],
            'due_date' => ['sometimes', 'required', 'date'],
        ];
    }
}
