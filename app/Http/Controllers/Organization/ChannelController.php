<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChannelResource;
use App\Http\Requests\Channel\CreateChannelRequest;
use App\Http\Requests\Channel\DeleteChannelRequest;
use App\Http\Requests\Channel\UpdateChannelRequest;
use App\Models\Channel;
use App\Models\Message;
use App\Models\Organization;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Organization $organization): AnonymousResourceCollection
    {
        $channels = $organization->channels()
            ->paginate(15);

        return ChannelResource::collection($channels);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateChannelRequest $request, Organization $organization)
    {
        $channel = $organization->channels()
            ->create($request->validated());

        return new ChannelResource($channel);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Organization $organization, Channel $channel): ChannelResource
    {
        return new ChannelResource($channel);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChannelRequest $request, Organization $organization, Channel $channel)
    {
        $channel->update($request->validated());

        return new ChannelResource($channel->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteChannelRequest $request, Organization $organization, Channel $channel): bool
    {
        return $channel->delete();
    }

    /**
     * Receive a message through this channel.
     */
    public function receiveMessage(Request $request, Organization $organization, Channel $channel): Message
    {
        return $channel->service->receiveMessage($request);
    }
}