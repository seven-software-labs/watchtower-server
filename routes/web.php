<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('test', function() {
    DB::table('channel_organization')
        ->update([
            'settings' => collect([
                'server' => 'imap.gmail.com',
                'port' => '993',
                'mode' => 'imap',
                'encryption' => 'ssl',
                'email' => 'yamato.takato@gmail.com',
                'password' => 'ULN922mx105',
            ])->toJSON(),
        ]);

    die();
    $channel = \App\Models\Channel::first();
    $organization = \App\Models\Organization::first();

    echo "Channel:";
    dump($channel);

    echo "Channel Settings:";
    dump($channel->channelSettings);

    echo "Organization:";
    dump($organization);

    echo "Organization Channels:";

    foreach($organization->channels as $channel) {
        echo "\n\n".$channel->name . ":";
        dump($channel);

        echo "Pivot: ";
        dump($channel->pivot);
    }

    die();
});

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');
