<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;

class MeController extends Controller
{
    /**
     * Return the currently authenticated user as a resource.
     */
    public function index(): UserResource
    {
        $user = User::with(['organization'])->find(auth()->user()->id);

        return new UserResource($user);
    }

    /**
     * Update the currently authenticated user's profile.
     */
    public function updateProfile(UpdateUserRequest $request, User $user): UserResource
    {
        $user->update($request->validated());

        return new UserResource($user->fresh());
    }

    /**
     * Update the currently authenticated user's settings.
     */
    public function updateSettings(UpdateUserRequest $request, User $user): UserResource
    {
        $user->update($request->validated());

        return new UserResource($user->fresh());
    }
}
