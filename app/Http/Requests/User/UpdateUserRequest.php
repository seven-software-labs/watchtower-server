<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'email' => ['sometimes', 'required', 'email'],
            'name' => ['sometimes', 'required', 'string'],
            'current_password' => ['sometimes'],
            'new_password' => ['sometimes', 'required', 'required_with:current_password', 'confirmed'],
            'new_password_confirmation' => ['sometimes', 'required'],
        ];
    }
}
