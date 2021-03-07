<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\ChannelOrganization;
use App\Models\Organization;
use App\Http\Resources\ChannelOrganizationResource;
use App\Http\Requests\ChannelOrganization\CreateChannelOrganizationRequest;
use App\Http\Requests\ChannelOrganization\DeleteChannelOrganizationRequest;
use App\Http\Requests\ChannelOrganization\UpdateChannelOrganizationRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ChannelOrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organization $organization): AnonymousResourceCollection
    {
        $channelOrganizations = ChannelOrganization::where('organization_id', $organization->getKey())
            ->with('department', 'channel', 'organization')
            ->paginate(15);
        
        return ChannelOrganizationResource::collection($channelOrganizations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateChannelOrganizationRequest $request, Organization $organization): ChannelOrganizationResource
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
     */
    public function show(Organization $organization, ChannelOrganization $channelOrganization): ChannelOrganizationResource
    {
        return new ChannelOrganizationResource($channelOrganization);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChannelOrganizationRequest $request, Organization $organization, ChannelOrganization $channelOrganization): ChannelOrganizationResource
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
     */
    public function destroy(DeleteChannelOrganizationRequest $request, ChannelOrganization $channelOrganization): bool
    {
        return $channelOrganization->delete();
    }
}
