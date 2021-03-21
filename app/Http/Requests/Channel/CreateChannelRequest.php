<?php

namespace App\Http\Requests\Channel;

use Illuminate\Foundation\Http\FormRequest;

class CreateChannelRequest extends FormRequest
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
        if(!$this->filled('organization_id')) {
            $this->merge([
                'organization_id' => auth()->user()->organization_id,
            ]);
        }

        $this->merge([
            'settings' => Collect($this->get('settings'))->toJSON(),
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
            'name' => ['required'],
            'department_id' => ['exists:departments,id'],
            'organization_id' => ['exists:organizations,id'],
            'service_id' => ['exists:services,id'],
            'is_active' => ['required', 'boolean'],
            'settings' => ['required', 'json'],
        ];
    }
}
