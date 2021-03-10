<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Status;
use App\Models\Organization;
use App\Http\Resources\StatusResource;
use App\Http\Requests\Status\CreateStatusRequest;
use App\Http\Requests\Status\DeleteStatusRequest;
use App\Http\Requests\Status\UpdateStatusRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Organization $organization): AnonymousResourceCollection
    {
        $statuses = $organization->statuses()
            ->paginate(15);

        return StatusResource::collection($statuses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateStatusRequest $request, Organization $organization): StatusResource
    {
        $status = Status::create($request->validated());

        return new StatusResource($status);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Organization $organization, Status $status): StatusResource
    {
        return new StatusResource($status);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStatusRequest $request, Organization $organization, Status $status)
    {
        $status->update($request->validated());

        return new StatusResource($status->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteStatusRequest $request, Organization $organization, Status $status): bool
    {
        return $status->delete();
    }
}
