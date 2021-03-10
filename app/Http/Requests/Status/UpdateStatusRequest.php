<?php

namespace App\Http\Requests\Status;

use App\Rules\Organization\RequireDefaultModel;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
{
    /**
     * The status that's going to be updated.
     */
    private $status;

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->status = $this->route()->parameter('status');
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
            'is_default' => ['required', 'boolean', new RequireDefaultModel($this->status)],
        ];
    }
}
