<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

class CreateMessageRequest extends FormRequest
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
            'message_type_id' => ['required', 'exists:message_types,id'],
            'ticket_id' => ['required', 'exists:tickets,id'],
            'user_id' => ['required', 'exists:users,id'],
            'channel_id' => ['nullable', 'exists:channels,id'],
        ];
    }
}
