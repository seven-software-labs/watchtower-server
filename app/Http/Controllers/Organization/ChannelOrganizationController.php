<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\ChannelOrganization;
use App\Models\Organization;
use App\Http\Resources\ChannelOrganizationResource;
use Illuminate\Http\Request;

class ChannelOrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Organization $organization)
    {
        $channelOrganizations = ChannelOrganization::where('organization_id', $organization->getKey())
            ->with('department', 'channel', 'organization')
            ->paginate(15);
        
        return ChannelOrganizationResource::collection($channelOrganizations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Organization $organization)
    {
        $channelOrganization = ChannelOrganization::create([
            'name' => $request->get('name'),
            'channel_id' => $request->get('channel_id'),
            'department_id' => $request->get('department_id'),
            'organization_id' => $organization->getKey(),
            'settings' => collect($request->get('settings', []))->toJSON(),
            'is_active' => $request->get('is_active'),
        ]);

        return new ChannelOrganizationResource($channelOrganization);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ChannelOrganization  $channelOrganization
     * @return \Illuminate\Http\Response
     */
    public function show(Organization $organization, ChannelOrganization $channelOrganization)
    {
        return new ChannelOrganizationResource($channelOrganization);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ChannelOrganization  $channelOrganization
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Organization $organization, ChannelOrganization $channelOrganization)
    {
        $channelOrganization->update([
            'name' => $request->get('name'),
            'channel_id' => $request->get('channel_id'),
            'department_id' => $request->get('department_id'),
            'settings' => collect($request->get('settings', []))->toJSON(),
            'is_active' => $request->get('is_active'),
        ]);

        return new ChannelOrganizationResource($channelOrganization->fresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ChannelOrganization  $channelOrganization
     * @return \Illuminate\Http\Response
     */
    public function destroy(ChannelOrganization $channelOrganization)
    {
        // ...
    }
}
