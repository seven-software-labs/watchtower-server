<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\DeleteUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;

class UserController extends Controller
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
    public function index(Organization $organization): AnonymousResourceCollection
    {
        return UserResource::collection($this->organization->users()->with('channels')->paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserRequest $request): UserResource
    {
        $user = User::create($request->validated());

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $user): UserResource
    {
        return new UserResource($user->load('channels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $user->update($request->validated());

        return new UserResource($user->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteUserRequest $request, User $user): bool
    {
        return $user->delete();
    }
}
