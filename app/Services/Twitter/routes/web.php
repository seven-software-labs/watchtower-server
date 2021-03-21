<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Twitter Web Routes
|--------------------------------------------------------------------------
|
*/

Route::group(['prefix' => 'twitter', 'middleware' => []], function() {

    // Request to authorize a Twitter account.
    Route::match(['GET', 'POST'], 'authorizeAccount', [\App\Services\Twitter\Controllers\TwitterController::class, 'authorizeAccount'])
        ->name('services.twitter.channels.authorize-account');

    // Save credentials for an authorized Twitter account.
    Route::match(['GET', 'POST'], 'confirmAccount', [\App\Services\Twitter\Controllers\TwitterController::class, 'confirmAccount'])
        ->name('services.twitter.channels.confirm-account');        

});