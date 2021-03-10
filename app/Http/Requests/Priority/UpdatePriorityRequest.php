<?php

namespace App\Http\Requests\Priority;

use App\Rules\Organization\RequireDefaultModel;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePriorityRequest extends FormRequest
{
    /**
     * The priority that's going to be updated.
     */
    private $priority;

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->priority = $this->route()->parameter('priority');
    }

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
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string'],
            'is_default' => ['required', 'boolean', new RequireDefaultModel($this->priority)],
        ];
    }
}
