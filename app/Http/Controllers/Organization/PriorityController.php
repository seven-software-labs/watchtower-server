<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Priority;
use App\Models\Organization;
use App\Http\Resources\PriorityResource;
use App\Http\Requests\Priority\CreatePriorityRequest;
use App\Http\Requests\Priority\DeletePriorityRequest;
use App\Http\Requests\Priority\UpdatePriorityRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;

class PriorityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Organization $organization): AnonymousResourceCollection
    {
        $priorities = $organization->priorities()
            ->paginate(15);

        return PriorityResource::collection($priorities);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePriorityRequest $request, Organization $organization): PriorityResource
    {
        $priority = Priority::create($request->validated());

        return new PriorityResource($priority);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Organization $organization, Priority $priority): PriorityResource
    {
        return new PriorityResource($priority);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePriorityRequest $request, Organization $organization, Priority $priority)
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
