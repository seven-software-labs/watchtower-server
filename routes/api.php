<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['api']], function() {
    // Utility Routes
    Route::get('ping', function() {
        return 'pong';
    });

    // Me
    Route::get('me', [\App\Http\Controllers\MeController::class, 'index'])
        ->name('me');

    // General Resources
    Route::apiResources([
        'channels' => \App\Http\Controllers\ChannelController::class,
        'departments' => \App\Http\Controllers\DepartmentController::class,
        'messages' => \App\Http\Controllers\MessageController::class,
        'priorities' => \App\Http\Controllers\PriorityController::class,
        'statuses' => \App\Http\Controllers\StatusController::class,
        'services' => \App\Http\Controllers\ServiceController::class,
        'tickets' => \App\Http\Controllers\TicketController::class,
        'users' => \App\Http\Controllers\UserController::class,
    ]);

    // Organization Resources
    Route::group([], function() {
        Route::apiResources([
            'organizations' => \App\Http\Controllers\OrganizationController::class,
            'organizations.channels' => \App\Http\Controllers\Organization\ChannelController::class,
            'organizations.departments' => \App\Http\Controllers\Organization\DepartmentController::class,
            'organizations.messages' => \App\Http\Controllers\Organization\MessageController::class,
            'organizations.child-organizations' => \App\Http\Controllers\Organization\OrganizationController::class,
            'organizations.priorities' => \App\Http\Controllers\Organization\PriorityController::class,
            'organizations.statuses' => \App\Http\Controllers\Organization\StatusController::class,
            'organizations.tickets' => \App\Http\Controllers\Organization\TicketController::class,
            'organizations.users' => \App\Http\Controllers\Organization\UserController::class,
        ]);
    });
});
