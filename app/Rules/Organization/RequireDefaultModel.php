<?php

namespace App\Rules\Organization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Validation\Rule;

class RequireDefaultModel implements Rule
{
    /**
     * The Model that needs to be required a default by the organization.
     */
    private Model $model;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        // Check if the model has siblings in the organization.
        $hasOtherModels = ($this->model->where('organization_id', $this->model->organization_id)
            ->count() > 1);

        // Check if the model is being set to not default
        // and if the model has siblings.
        if(!$value && !$hasOtherModels) {
            return false;
        }
        
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $modelName = strtolower((new \ReflectionClass($this->model))->getShortName());
        return "Cannot set only {$modelName} to not default.";
    }
}
