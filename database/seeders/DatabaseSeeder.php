<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $organization = Organization::create([
            'name' => 'Watchtower Support',
            'subdomain' => 'eatchtowersupport',
        ]);

        $user = User::create([
            'name' => 'Watchtower Support',
            'email' => 'support@watchtowersupport.com',
            'password' => Hash::make('password'),
            'master_organization_id' => $organization->getKey(),
            'email_verified_at' => now(),
        ]);

        $user->assignRole([
            'operator',
        ]);
    }
}
