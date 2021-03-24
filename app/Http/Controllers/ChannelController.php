<?php

namespace App\Http\Controllers;

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
     * The organization that holds the resources.
     */
    private Organization $organization;

    /**
     * Create a new ChannelController instance.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->organization = auth()->user()->masterOrganization;
            return $next($request);
        });
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $channels = $this->organization->channels()
            ->paginate(15);

        return ChannelResource::collection($channels);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateChannelRequest $request)
    {
        $channel = $this->organization->channels()
            ->create($request->validated());

        return new ChannelResource($channel);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Channel $channel): ChannelResource
    {
        $organizationChannel = $this->organization->channels->where('id', $channel->getKey())->first();

        return new ChannelResource($organizationChannel);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChannelRequest $request, Channel $channel)
    {
        $channel->update($request->validated());

        return new ChannelResource($channel->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteChannelRequest $request, Channel $channel): bool
    {
        return $channel->delete();
    }

    /**
     * Receive a message through this channel.
     */
    public function receiveMessage(Request $request, Channel $channel): Message
    {
        return $channel->service->receiveMessage($request);
    }
}