<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'filter' => ['array', function ($attribute, $value, $fail) {
                $allowedKeys = ['search', 'state', 'user', 'assigned_user', 'priority'];
                foreach (array_keys($value) as $key) {
                    if (!in_array($key, $allowedKeys, true)) {
                        $fail("The {$attribute} field contains an invalid key: {$key}.");
                    }
                }
            }],
            'filter.search' => ['nullable', 'string'],
            'filter.state' => ['nullable', 'integer'],
            'filter.user' => ['nullable', 'integer'],
            'filter.assigned_user' => ['nullable', 'integer'],
            'filter.priority' => ['nullable', 'integer'],
        ];
    }
}
