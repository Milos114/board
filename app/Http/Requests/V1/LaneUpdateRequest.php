<?php

namespace App\Http\Requests\V1;

use App\Enums\LaneEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LaneUpdateRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                Rule::unique('lanes', 'name')->ignore($this->route('lane')->id),
                Rule::in(
                    [
                        LaneEnum::BACK_LOG->value,
                        LaneEnum::TO_DO->value,
                        LaneEnum::IN_PROGRESS->value,
                        LaneEnum::DONE->value
                    ]
                )
            ],
        ];
    }
}
