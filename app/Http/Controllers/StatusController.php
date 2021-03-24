<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\StatusResource;
use App\Http\Requests\Status\CreateStatusRequest;
use App\Http\Requests\Status\DeleteStatusRequest;
use App\Http\Requests\Status\UpdateStatusRequest;
use App\Models\Status;
use App\Models\Organization;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;

class StatusController extends Controller
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
        $statuses = $this->organization->statuses()
            ->paginate(15);

        return StatusResource::collection($statuses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateStatusRequest $request): StatusResource
    {
        $status = Status::create($request->validated());

        return new StatusResource($status);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Status $status): StatusResource
    {
        return new StatusResource($status);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStatusRequest $request, Status $status)
    {
        $status->update($request->validated());

        return new StatusResource($status->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteStatusRequest $request, Status $status): bool
    {
        return $status->delete();
    }
}
