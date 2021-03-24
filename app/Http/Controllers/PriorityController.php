<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PriorityResource;
use App\Http\Requests\Priority\CreatePriorityRequest;
use App\Http\Requests\Priority\DeletePriorityRequest;
use App\Http\Requests\Priority\UpdatePriorityRequest;
use App\Models\Priority;
use App\Models\Organization;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;

class PriorityController extends Controller
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
        $priorities = $this->organization->priorities()
            ->paginate(15);

        return PriorityResource::collection($priorities);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePriorityRequest $request): PriorityResource
    {
        $priority = Priority::create($request->validated());

        return new PriorityResource($priority);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Priority $priority): PriorityResource
    {
        return new PriorityResource($priority);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePriorityRequest $request, Priority $priority)
    {
        $priority->update($request->validated());

        return new PriorityResource($priority->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeletePriorityRequest $request, Priority $priority): bool
    {
        return $priority->delete();
    }
}
