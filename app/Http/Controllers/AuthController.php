<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Create a new user account.
     */
    public function register(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', 'min:8'],
            'organization_name' => ['required', 'unique:organizations,name'],
        ]);

        $masterOrganization = Organization::create([
            'name' => $request->get('organization_name'),
            'subdomain' => str_replace("-", "", Str::slug($request->get('organization_name'))),
        ]);

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'master_organization_id' => $masterOrganization->getKey(),
        ]);

        $user->assignRole(['operator']);

        return $user->load('masterOrganization');
    }
}
