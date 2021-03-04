<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class MeController extends Controller
{
    /**
     * Return the currently authenticated user as a resource.
     */
    public function index(): UserResource
    {
        return new UserResource(auth()->user());
    }
}
