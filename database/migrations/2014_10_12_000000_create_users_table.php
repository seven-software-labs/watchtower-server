<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->foreignId('organization_id')->nullable();
            $table->foreignId('master_organization_id')->nullable();
            $table->text('profile_photo_path')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        $this->seed();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }

    /**
     * Seed initial data.
     */
    public function seed()
    {
        $users = collect([
            [
                'name' => 'Jonathan Tordesillas',
                'email' => 'yamato.takato@gmail.com',
                'password' => Hash::make('password'),
                'organization_id' => 1,
                'email_verified_at' => now(),
            ],
        ]);

        $users->each(function($user) {
            User::create($user);
        });
    }
}
