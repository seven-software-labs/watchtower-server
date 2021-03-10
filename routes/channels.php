<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('ticket-channel', function() {
    return true;
});

Broadcast::channel('organization-{organizationId}-{model}-{modelId}-channel', function($user, $organizationId, $model, $modelId) {
    return true;
});

Broadcast::channel('organization-{organizationId}-{model}-channel', function($user, $organizationId, $model) {
    return true;
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
