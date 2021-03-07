<?php

namespace App\Http\Requests\Message;

use App\Models\MessageType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => ['required', 'string'],
            'message_type_id' => ['required', 'exists:message_types,id', Rule::notIn([MessageType::REPLY])],
        ];
    }
}
