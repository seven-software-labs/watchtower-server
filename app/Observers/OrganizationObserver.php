<?php

namespace App\Observers;

use App\Models\Organization;
use App\Events\Organization\OrganizationCreated;
use App\Events\Organization\OrganizationDeleted;
use App\Events\Organization\OrganizationUpdated;

class OrganizationObserver
{
    /**
     * Handle the Organization "creating" event.
     *
     * @param  \App\Models\Organization  $organization
     * @return void
     */
    public function creating(Organization $organization)
    {
        //
    }

    /**
     * Handle the Organization "created" event.
     *
     * @param  \App\Models\Organization  $organization
     * @return void
     */
    public function created(Organization $organization)
    {
        $organization->setupOrganization();
        OrganizationCreated::dispatch($organization);
    }

    /**
     * Handle the Organization "updating" event.
     *
     * @param  \App\Models\Organization  $organization
     * @return void
     */
    public function updating(Organization $organization)
    {
        //
    }

    /**
     * Handle the Organization "updated" event.
     *
     * @param  \App\Models\Organization  $organization
     * @return void
     */
    public function updated(Organization $organization)
    {
        OrganizationUpdated::dispatch($organization);
    }

    /**
     * Handle the Organization "deleting" event.
     *
     * @param  \App\Models\Organization  $organization
     * @return void
     */
    public function deleting(Organization $organization)
    {
        //
    }

    /**
     * Handle the Organization "deleted" event.
     *
     * @param  \App\Models\Organization  $organization
     * @return void
     */
    public function deleted(Organization $organization)
    {
        OrganizationDeleted::dispatch($organization);
    }
}
