<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Http\Resources\OrganizationResource;
use App\Http\Requests\Organization\CreateOrganizationRequest;
use App\Http\Requests\Organization\DeleteOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;

class OrganizationController extends Controller
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
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): AnonymousResourceCollection
    {
        $organizations = $this->organization->organizations()->paginate(15);

        return OrganizationResource::collection($organizations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrganizationRequest $request): OrganizationResource
    {
        $this->organization->update($request->validated());

        return new OrganizationResource($this->organization->fresh());
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request): OrganizationResource
    {
        return new OrganizationResource($this->organization);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrganizationRequest $request): OrganizationResource
    {
        $this->organization->update($request->validated());

        return new OrganizationResource($this->organization->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteOrganizationRequest $request): bool
    {
        return $this->organization->delete();
    }
}
