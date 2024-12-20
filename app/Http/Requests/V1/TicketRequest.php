<?php

namespace App\Http\Requests\V1;

use App\Rules\LaneTransitionRule;
use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
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
            'user_id' => 'nullable|exists:users,id',
            'title' => 'required|string',
            'description' => 'required|string',
            'lane_id' => ['nullable', 'numeric', new LaneTransitionRule($this->route('ticket'))],
            'priority_id' => 'nullable|numeric',
            'attachments' => 'nullable|array',
        ];
    }
}
