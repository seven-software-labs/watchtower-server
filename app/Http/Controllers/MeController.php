<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MeController extends Controller
{
    /**
     * Return the currently authenticated user as a resource.
     */
    public function index(): UserResource
    {
        $user = User::with(['organization', 'masterOrganization'])
            ->find(auth()->user()->id);

        return new UserResource($user);
    }

    /**
     * Update the currently authenticated user's profile.
     */
    public function updateProfile(UpdateUserRequest $request): UserResource
    {
        $user = User::find(auth()->user()->id);

        $user->update([
            'email' => $request->get('email'),
            'name' => $request->get('name'),
        ]);

        return new UserResource($user->fresh());
    }

    /**
     * Update the currently authenticated user's password.
     */
    public function updatePassword(UpdateUserRequest $request): UserResource
    {
        $user = User::find(auth()->user()->id);

        // Check if current password and stored password are equal.
        if (Hash::check($request->get('current_password'), $user->password)) {
            $user->update([
                'password' => Hash::make($request->get('password')),
            ]);    
        } else {
            throw new \Exception("Current password must match your current password.");
        }

        return new UserResource($user->fresh());
    }
}
