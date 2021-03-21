<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrganizationResource;
use App\Http\Requests\Organization\CreateOrganizationRequest;
use App\Http\Requests\Organization\DeleteOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organization $organization): AnonymousResourceCollection
    {
        $organizations = Organization::query()
            ->where('parent_organization_id', $organization->getKey())
            ->orWhereIn('id', [
                $organization->getKey(),
            ])->paginate(15);

        return OrganizationResource::collection($organizations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrganizationRequest $request, Organization $organization): OrganizationResource
    {
        $organization->update($request->validated());

        return new OrganizationResource($organization->fresh());
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Organization $organization): OrganizationResource
    {
        return new OrganizationResource($organization);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrganizationRequest $request, Organization $organization): OrganizationResource
    {
        $organization->update($request->validated());

        return new OrganizationResource($organization->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteOrganizationRequest $request, Organization $organization): bool
    {
        return $organization->delete();
    }
}
