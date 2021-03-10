<?php

namespace App\Observers;

use App\Models\Status;
use App\Events\Status\StatusCreated;
use App\Events\Status\StatusUpdated;
use App\Events\Status\StatusDeleted;

class StatusObserver
{
    /**
     * Handle the Status "creating" event.
     *
     * @param  \App\Models\Status  $status
     * @return void
     */
    public function creating(Status $status)
    {
        // ...
    }

    /**
     * Handle the Status "created" event.
     *
     * @param  \App\Models\Status  $status
     * @return void
     */
    public function created(Status $status)
    {
        StatusCreated::dispatch($status);
    }

    /**
     * Handle the Status "updating" event.
     *
     * @param  \App\Models\Status  $status
     * @return void
     */
    public function updating(Status $status)
    {
        if($status->is_default) {
            Status::where('organization_id', $status->organization_id)
                ->where('id', '!=', $status->getKey())
                ->update([
                    'is_default' => false,
                ]);
        }
    }

    /**
     * Handle the Status "updated" event.
     *
     * @param  \App\Models\Status  $status
     * @return void
     */
    public function updated(Status $status)
    {
        $hasDefaultStatus = Status::where('is_default', true)
            ->where('organization_id', $status->organization_id)
            ->exists();

        if(!$hasDefaultStatus) {
            Status::where('organization_id', $status->organization_id)
                ->where('id', '!=', $status->getKey())
                ->first()
                ->update([
                    'is_default' => true,
                ]);
        } 

        StatusUpdated::dispatch($status);
    }

    /**
     * Handle the Status "deleting" event.
     *
     * @param  \App\Models\Status  $status
     * @return void
     */
    public function deleting(Status $status)
    {
        if(count($status->organization->statuses) < 2) {
            throw new \Exception("Cannot delete the only status in the organization.");
            return false;
        }

        if(count($status->tickets) > 0) {
            throw new \Exception("Cannot delete a status with tickets.");
            return false;
        }

        if($status->is_default) {
            $status->organization->statuses()->first()->update([
                'is_default' => true,
            ]);
        }
    }

    /**
     * Handle the Status "deleted" event.
     *
     * @param  \App\Models\Status  $status
     * @return void
     */
    public function deleted(Status $status)
    {
        StatusDeleted::dispatch($status);
    }    
}
