<?php

namespace App\Http\Requests\Priority;

use Illuminate\Foundation\Http\FormRequest;

class CreatePriorityRequest extends FormRequest
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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'organization_id' => $this->get('organization_id', auth()->user()->organization->getKey()),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string'],
            'is_default' => ['required', 'boolean'],
            'organization_id' => ['required', 'exists:organizations,id'],
        ];
    }
}
