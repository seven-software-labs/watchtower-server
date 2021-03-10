<?php

namespace App\Http\Requests\Department;

use App\Rules\Organization\RequireDefaultModel;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
{
    /**
     * The department that's going to be updated.
     */
    private $department;

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->department = $this->route()->parameter('department');
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
            'is_default' => ['required', 'boolean', new RequireDefaultModel($this->department)],
        ];
    }
}
