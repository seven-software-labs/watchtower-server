<?php

namespace App\Services\Twitter\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Http\Controllers\Controller;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class TwitterController extends Controller
{
    /**
     * The consumer key for Twitter.
     */
    private string $consumerKey;

    /**
     * The consumer secret for Twitter.
    */
    private string $consumerSecret;

    /**
     * Create a new instance of TwitterController.
     */
    public function __construct()
    {
        $this->consumerKey = 'Sc7cPoAwOaEvksBgnxJUKII0f';
        $this->consumerSecret = 'ZbyYzzjEnTkfFyYlNj9VdNkmSr40dckv9nEcCND9nSQQF9cRsa';
    }

    /**
     * Get a TwitterOAuth Connection.
     */
    private function getConnection($oauthVerifier = null, $oauthToken = null): TwitterOAuth
    {
        return new TwitterOAuth($this->consumerKey, $this->consumerSecret, $oauthVerifier, $oauthToken);
    }

    /**
     * Request for authorization.
     */
    public function authorizeAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'channel_id' => ['required', 'exists:channels,id'],
        ]);

        $connection = $this->getConnection();
    
        $temporaryCredentials = $connection->oauth('oauth/request_token', [
            "oauth_callback" => route('services.twitter.channels.confirm-account', ['channel_id' => $request->get('channel_id')]),
        ]);

        session([
            'oauth_token' => $temporaryCredentials['oauth_token'],
            'oauth_token_secret' => $temporaryCredentials['oauth_token_secret'],
        ]);

        $redirectPath = $connection->url("oauth/authorize", [
            "oauth_token" => session('oauth_token'),
        ]);

        return redirect()->to($redirectPath);
    }

    public function confirmAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'channel_id' => ['required', 'exists:channels,id'],
        ]);

        $channel = Channel::findOrFail($request->get('channel_id'));
        $connection = $this->getConnection();

        $credentials = $connection->oauth("oauth/access_token", [
            'oauth_verifier' => $request->get('oauth_verifier'),
            'oauth_token' => $request->get('oauth_token'),
        ]);

        $channel->update([
            'settings' => collect(array_merge($channel->settings->toArray(), [
                'access_token' => $credentials['oauth_token'],
                'access_token_secret' => $credentials['oauth_token_secret'],
            ]))->toJSON(),
        ]);
        
        $redirectPath = "http://localhost:3000/settings/channels/{$request->get('channel_id')}/edit";

        return redirect()->to($redirectPath);
    }
}
