<?php

namespace App\Observers;

use App\Models\Priority;
use App\Events\Priority\PriorityCreated;
use App\Events\Priority\PriorityUpdated;
use App\Events\Priority\PriorityDeleted;

class PriorityObserver
{
    /**
     * Handle the Priority "creating" event.
     *
     * @param  \App\Models\Priority  $priority
     * @return void
     */
    public function creating(Priority $priority)
    {
        // ...
    }

    /**
     * Handle the Priority "created" event.
     *
     * @param  \App\Models\Priority  $priority
     * @return void
     */
    public function created(Priority $priority)
    {
        PriorityCreated::dispatch($priority);
    }

    /**
     * Handle the Priority "updating" event.
     *
     * @param  \App\Models\Priority  $priority
     * @return void
     */
    public function updating(Priority $priority)
    {
        if($priority->is_default) {
            Priority::where('organization_id', $priority->organization_id)
                ->where('id', '!=', $priority->getKey())
                ->update([
                    'is_default' => false,
                ]);
        }
    }

    /**
     * Handle the Priority "updated" event.
     *
     * @param  \App\Models\Priority  $priority
     * @return void
     */
    public function updated(Priority $priority)
    {
        $hasDefaultPriority = Priority::where('is_default', true)
            ->where('organization_id', $priority->organization_id)
            ->exists();

        if(!$hasDefaultPriority) {
            Priority::where('organization_id', $priority->organization_id)
                ->where('id', '!=', $priority->getKey())
                ->first()
                ->update([
                    'is_default' => true,
                ]);
        } 

        PriorityUpdated::dispatch($priority);
    }

    /**
     * Handle the Priority "deleting" event.
     *
     * @param  \App\Models\Priority  $priority
     * @return void
     */
    public function deleting(Priority $priority)
    {
        if(count($priority->organization->priorities) < 2) {
            throw new \Exception("Cannot delete the only priority in the organization.");
            return false;
        }

        if(count($priority->tickets) > 0) {
            throw new \Exception("Cannot delete a priority with tickets.");
            return false;
        }
        
        if($priority->is_default) {
            $priority->organization->priorities()->first()->update([
                'is_default' => true,
            ]);
        }
    }

    /**
     * Handle the Priority "deleted" event.
     *
     * @param  \App\Models\Priority  $priority
     * @return void
     */
    public function deleted(Priority $priority)
    {
        PriorityDeleted::dispatch($priority);
    }    
}
