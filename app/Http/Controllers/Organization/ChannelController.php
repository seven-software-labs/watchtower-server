<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\Organization;
use App\Models\Pivot\ChannelOrganization;
use App\Http\Resources\ChannelResource;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Organization $organization)
    {
        return ChannelResource::collection($organization->channels()->paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Channel  $channel
     * @return \Illuminate\Http\Response
     */
    public function show($channel_organization_id)
    {
        $channelOrganization = ChannelOrganization::find($channel_organization_id);
        $channel = $channelOrganization->organization->channels()->find($channelOrganization->channel_id);

        return $channel;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Channel  $channel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $channel_organization_id)
    {
        $channelOrganization = ChannelOrganization::findOrFail($channel_organization_id);
        
        $channelOrganization->update($request->all());

        return $channelOrganization;
    }

    /**
     * Attach a resource to the specified resource in storage.
     */
    public function attach(Request $request, Organization $organization)
    {
        $organization->channels()->attach($request->get('channel_id'), [
            'name' => $request->get('name', 'Undefined Nickname'),
            'settings' => collect($request->get('settings', []))->toJSON(),
            'is_active' => false,
        ]);

        return $organization->channels;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Channel  $channel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Channel $channel)
    {
        //
    }
}
