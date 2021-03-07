<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Organization;
use App\Http\Resources\DepartmentResource;
use App\Http\Requests\Department\CreateDepartmentRequest;
use App\Http\Requests\Department\DeleteDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Organization $organization): AnonymousResourceCollection
    {
        $departments = $organization->departments()
            ->paginate(15);

        return DepartmentResource::collection($departments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDepartmentRequest $request, Organization $organization): DepartmentResource
    {
        $department = Department::create($request->validated());

        return new DepartmentResource($department);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Organization $organization, Department $department): DepartmentResource
    {
        return new DepartmentResource($department);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Organization $organization, Department $department)
    {
        $department->update($request->validated());

        return new DepartmentResource($department->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteDepartmentRequest $request, Department $department): bool
    {
        return $department->delete();
    }
}
