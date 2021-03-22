<?php

namespace App\Observers;

use App\Models\User;
use App\Events\User\UserCreated;
use App\Events\User\UserUpdated;
use App\Events\User\UserDeleted;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function creating(User $user)
    {
        if(!$user->password) {
            $user->password = Hash::make(Str::random());
        }
    }

    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        UserCreated::dispatch($user);
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        UserUpdated::dispatch($user);
    }

    /**
     * Handle the User "deleting" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleting(User $user)
    {
        if(count($user->tickets) > 0) {
            throw new \Exception("Cannot delete a user with tickets.");
            return false;
        }
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        UserDeleted::dispatch($user);
    }
}
