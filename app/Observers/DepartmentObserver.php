<?php

namespace App\Observers;

use App\Models\Department;
use App\Events\Department\DepartmentCreated;
use App\Events\Department\DepartmentUpdated;
use App\Events\Department\DepartmentDeleted;

class DepartmentObserver
{
    /**
     * Handle the Department "creating" event.
     *
     * @param  \App\Models\Department  $department
     * @return void
     */
    public function creating(Department $department)
    {
        // ...
    }

    /**
     * Handle the Department "created" event.
     *
     * @param  \App\Models\Department  $department
     * @return void
     */
    public function created(Department $department)
    {
        DepartmentCreated::dispatch($department);
    }

    /**
     * Handle the Department "updating" event.
     *
     * @param  \App\Models\Department  $department
     * @return void
     */
    public function updating(Department $department)
    {
        if($department->is_default) {
            Department::where('organization_id', $department->organization_id)
                ->where('id', '!=', $department->getKey())
                ->update([
                    'is_default' => false,
                ]);
        }
    }

    /**
     * Handle the Department "updated" event.
     *
     * @param  \App\Models\Department  $department
     * @return void
     */
    public function updated(Department $department)
    {
        $hasDefaultDepartment = Department::where('is_default', true)
            ->where('organization_id', $department->organization_id)
            ->exists();

        if(!$hasDefaultDepartment) {
            Department::where('organization_id', $department->organization_id)
                ->where('id', '!=', $department->getKey())
                ->first()
                ->update([
                    'is_default' => true,
                ]);
        } 

        DepartmentUpdated::dispatch($department);
    }

    /**
     * Handle the Department "deleting" event.
     *
     * @param  \App\Models\Department  $department
     * @return void
     */
    public function deleting(Department $department)
    {
        if(count($department->organization->departments) < 2) {
            throw new \Exception("Cannot delete the only department in the organization.");
            return false;
        }

        if(count($department->tickets) > 0) {
            throw new \Exception("Cannot delete a department with tickets.");
            return false;
        }

        if($department->is_default) {
            $department->organization->departments()->first()->update([
                'is_default' => true,
            ]);
        }
    }

    /**
     * Handle the Department "deleted" event.
     *
     * @param  \App\Models\Department  $department
     * @return void
     */
    public function deleted(Department $department)
    {
        DepartmentDeleted::dispatch($department);
    }    
}
